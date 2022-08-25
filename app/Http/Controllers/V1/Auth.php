<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Models\User;
use App\Models\Company;
use App\Models\Subscriptions;
use App\Models\Permissions;
use App\Models\LoginHistory;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Helper;
use Carbon\Carbon;
use Storage, Mail;
use Illuminate\Support\Facades\Password;


class Auth extends Controller
{
    // use ThrottlesLogins;
	
	public function getAuthPermissionRole()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        // print_r($user->permissions);die;
        return response()->json([
            'status' => 'SUCCESS',
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'roles' => $user->roles
        ]);
    }

    public function authenticate(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];

        $message = [
            'email.required' => 'Username is required.',
            'password.required' => 'Password is required.'
        ];

        /* if (!$this->verifyCaptcha($request->gResponse)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Please verify recaptcha.',
            ]);
        } */

        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        if (is_numeric($request->get('email'))) {
            $credentials = [
                'mobileno' => $request->get('email'),
                'password' => $request->get('password'),
            ];
        } else {
            $credentials = $request->only('email', 'password');
        }

        $user = User::where('email', $request->email)->orWhere('mobileno', $request->email)->first();
        if ($user && $user->user_status == 'INACTIVE') {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Your Account deactivated.',
            ]);
        }

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
				
                $user = User::where('email', $request->email)->first();
                $attempLeft = 6;
                if ($user) {
                    $totalAttempts = $user->noOfLoginAttempts + 1;
                    $user->noOfLoginAttempts = $totalAttempts;
                    if ($totalAttempts > 5) {
                        $user->isLocked = 1;
                        $user->lockedDate = date("Y-m-d H:i:s");
                    }

                    $user->save();

                    if ($totalAttempts > 5) {
                        return response()->json([
                            'status' => 'FAIL',
                            'message' => 'Account locked due to multiple failed attempts. Contact authority to unlock.',
                        ]);
                    }
                    $attempLeft -= $totalAttempts;

                    return response()->json([
                        'status' => 'FAIL',
                        'message' => '' . $attempLeft . ' attempt left to enter the correct email and password.'
                    ]);
                }

                return response()->json([
                    'status' => 'FAIL',
                    'message' => 'Enter correct email and password.'
                ]);
            }
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Technical issue suspected.Try again later.'
            ]);
        }

        $user = JWTAuth::user();

        if ($user->isLocked == 1) {
            //JWTAuth::invalidate($token);
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Account locked due to multiple failed attempts.Contact authority to unlock.'
            ]);
        }

        $ipAddress = (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR']);

        $history = new LoginHistory;
        $history->user_id = $user->id;
        $history->ipaddress = $ipAddress;
        $history->save();
        
        $company_details = $users_count = null;
        
        if($user->company_id){
            $company_details = Company::find($user->company_id);
            $expiry_date = $company_details->expiry_date;

            if($expiry_date){
                if($expiry_date <= date('Y-m-d')){
                    $start_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                    $end_date   = Carbon::createFromFormat('Y-m-d', $expiry_date);
                    $different_days = $start_date->diffInDays($end_date);
                    if($different_days >= 15){
                        return response()->json([
                            'status' => 'FAIL',
                            'message' => 'Your subscription has been expired. please renew.'
                        ]);
                    }
                }
            }
            $users_count = User::where('company_id',$user->company_id)->count();
        }

        $user->token = $token;
        $user->noOfLogins = $user->noOfLogins + 1;
        $user->noOfLoginAttempts = 0;
        $user->save();
        $roleId = isset($user->roles[0]) ? $user->roles[0]->id : null;
        if($roleId)
        {
            $permission = $this->getRoleWisePermission($roleId);
            $userPermission = $this->userWisePermission($user->id);

            $user->permission = $permission['permission'];

            // $user_permission_ar = array_merge_recursive($permission['permission'], $userPermission['permission']);

            // $super_unique = $this->super_unique($user_permission_ar,'name');

            // $user->user_permission = $userPermission['permission'];         
            $user->user_permission = $permission['permission'];         
        }
        $company_data['product_type'] = $user->organizationName->product_service;
        $user->company_data = $company_data;
        unset($user->organizationName);
        $userData = $user->toArray();
		$uclient = Helper::get_client_info($user->organization_id);
        $userData['industry_type'] = $uclient ? $uclient->industry_id : null;

        $userData['users_count']        = $users_count;
        $userData['company_details']    = $company_details;
        $userData['picture']    = 'images/'.$userData['picture'];
        
        return response()->json([
            'status' => 'SUCCESS',
            'token' => $token,
            'user' => $userData,
            'message' => 'Login successful'
        ]);
    }

    public function super_unique_multi($array,$key)
    {
       $temp_array = [];
       foreach ($array as &$v) {
           if (!isset($temp_array[$v[$key]]))
           $temp_array[$v[$key]] =& $v;
       }
       $array = array_values($temp_array);
       return $array;

    }

    public function super_unique($array,$key)
    {
       $temp_array = [];
       foreach ($array as $v => $key_value) {
            $tt = $this->super_unique_multi(array_values($key_value), $key);
            $temp_array[$v] = $tt;
       }
       return $temp_array;
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|unique:users,email|max:255|email',
            'mobileno' => 'required|digits:10',
            'password' => 'required|min:8|max:12',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'mobileno' => $request->get('mobileno'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('user', 'token'),
            'message' => 'Registration successful.'
        ]);
    }

    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'status' => 'FAIL',
                    'message' => 'User not found.'
                ]);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        $user = User::where('id', $user->id)->first();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => ['user' => $user],
            'message' => ''
        ]);
    }

    public function logout(Request $request)
    {
        $this->validate($request, ['token' => 'required']);
        $user = JWTAuth::parseToken()->authenticate();
        if ($user) {
            $user->token = null;
            $user->save();
        }

        try {
            JWTAuth::invalidate($request->input('token'));
            return response()->json([
                'status' => 'SUCCESS',
                'message' => "You have successfully logged out."
            ]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Failed to logout, please try again.'
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:8|max:12',
            'password_confirmation' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());

            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $user->password = Hash::make($request->new_password);
        /* if (isset($request->type) && $request->type == 'FIRST_LOGIN_ATTEMPT') {
            $user->noOfLogins = $user->noOfLogins + 1;
        } */
        $user->save();

        //Add Action Log
        Helper::addActionLog($user->id, 'USER', $user->id, 'CHANGEPASSWORD', [], []);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Password has been changed successfully.'
        ]);

    }

    public function forgotpassword(Request $request)
    {
        /* if (!$this->verifyCaptcha($request->gResponse)) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Please verify recaptcha.',
            ]);
        } */

        if (is_numeric($request->get('email'))) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|digits:10',
            ], [
                'email.required' => 'Mobile no is requird.',
                'email.digits' => 'Mobile no is invalid.',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
        }

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString //$validator->errors()->first()
            ]);
        }

        $user = User::where('email', $request->only('email'))
            ->orWhere('mobileno', $request->only('email'))
            ->first();

        if ($user && $user->user_status == 'INACTIVE') {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Your account is deactivated.'
            ]);
        }

        if ($user) {
           
            $token = (string) \Str::uuid();

            $user->forgetPasswordToken = $token;
            $user->save();
	        $is_sent=0; 

            if($user->company_id){
                $company_details = Company::find($user->company_id);
                $expiry_date = $company_details->expiry_date;
                $used_emails = $company_details->used_email;

                if($expiry_date){
                    if($expiry_date <= date('Y-m-d')){
                        $start_date = Carbon::createFromFormat('Y-m-d', date('Y-m-d'));
                        $end_date   = Carbon::createFromFormat('Y-m-d', $expiry_date);
                        $different_days = $start_date->diffInDays($end_date);
                        if($different_days >= 15){
                            return response()->json([
                                'status' => 'FAIL',
                                'message' => 'Your subscription has been expired. please renew.'
                            ]);
                        }
                    }
                } 
            }	

            $template = $this->getTemplate(Helper::getSetting('app_link').'reset-password/'. $token);//http://blueflower.in/rkcrm/

            $receiverEmail = $user->email;
            $subject = "Reset Password Notification";
            $data['messagecontent'] = $template;
            $data['subject'] = $subject;

            //Helper::storeEmailHistory(1, $user->id, $user->id, $user->email, null, null, $data['subject'], $template, false);
            Helper::storeEmailHistory(1, $user->id, $user->organization_id, $user->email, null, null, $data['subject'], $template, false);
            
            $is_sent = 1;

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'We have sent you a reset password link. Please check your email account for further instructions.',
                'is_email_sent' => $is_sent,
            ]);

        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Your account is not associated with us.'
            ]);
        }
    }

    private function getTemplate($url)
    {
	$html = "You are receiving this email because we received a password reset request for your account.\n";
	$html .= "<a href=".$url.">Reset Password</a>\n";
	$html .= "This password reset link will expire in 60 minutes.\n";
	$html .= "If you did not request a password reset, no further action is required.\n";
	$html .= "Regards,
		RKADMIN";
	return $html;
    }

    public function sendOTP()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        Helper::generateOTP($user, 15);

        $shortcodes = Helper::shortcodes($user);
        Helper::sendSMS(1, [$user->mobileno], $shortcodes, $user);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'OTP sent successfully.'
        ]);
    }

    public function verifyOTP(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'otp' => 'required|digits:6',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());

            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        if ($user->otp == $request->otp && $user->otp_expired_at > Carbon::now()) {
            $user->otp = null;
            $user->otp_expired_at = null;
            $user->save();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'OTP has been verified successfully.'
            ]);
        } elseif ($user->otp != $request->otp) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'OTP is invalid.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'OTP has expired.'
            ]);
        }
    }

    public function resetpassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'    => 'required',
            'password' => 'required|min:8|max:12',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());

            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $user = User::where('forgetPasswordToken', $request->token)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->forgetPasswordToken = null;
            $user->save();

            //Add Action Log
            Helper::addActionLog($user->id, 'USER', $user->id, 'RESETPASSWORD', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Your password has been set successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Token has been expired. Please try again.'
            ]);
        }
    }
    public function profileUpdate(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            //'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            //'mobileno' => 'required|digits:10|unique:users,mobileno,' . $user->user_id . ',user_id',
            //'name' => 'required|string',
           
        ], [
            'email.unique' => 'Email already exist.',
            'mobileno.unique' => 'Mobile no already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
		$userimage = $user->picture;

        if (isset($request->picture) && isset($request->picture['base64'])) {
            $user->picture = $userimage = Helper::createImageFromBase64($request->picture['base64']);
        }

        $pData = [
            'name' => \trim($request->get('name')),
            'email' => $request->email,
            'mobileno' => $request->mobileno,
            'picture' => $userimage,
            'alt_mobileno' => \trim($request->alt_mobileno),
            'designation' => \trim($request->designation),
            'city' => \trim($request->city),
            'country_id' => (int) $request->country_id,
            'state_id' => $request->state_id,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => \trim($request->address_line_2),
            'pincode' => \trim($request->pincode),
            'facebook' => ($request->facebook) ,
            'twitter' => \trim($request->twitter),
            'instagram' => \trim($request->instagram),
            'website' => \trim($request->website),
            'gender' => (empty($request->gender) ? null : $request->gender),
            'dob' => $request->dob,
        ];

        $oldData = $user->toArray();

        $request->user()->update($pData);

        $newData = $request->user()->toArray();
		
        //Add Action Log
        Helper::addActionLog($user->id, 'USER', $user->id, 'UPDATEPROFILE', $oldData, $newData);
		
		$user = User::where('id', $user->id)->first();
		
        $roleId = isset($user->roles[0]) ? $user->roles[0]->id : null;
        
        if($roleId)
        {
            $permission = $this->getRoleWisePermission($roleId);
            $userPermission = $this->userWisePermission($user->id);

            $user->permission = $permission['permission'];

            // $user_permission_ar = array_merge_recursive($permission['permission'], $userPermission['permission']);

            // $super_unique = $this->super_unique($user_permission_ar,'name');

            // $user->user_permission = $userPermission['permission'];
            $user->user_permission = $permission['permission'];        
        }
        $company_data['product_type'] = $user->organizationName->product_service;
        $user->company_data = $company_data;
        unset($user->organizationName);
        $userData = $user->toArray();
		$uclient = Helper::get_client_info($user->organization_id);
        $userData['industry_type'] = $uclient ? $uclient->industry_id : null;

        $company_details = $users_count = null;
        if($user->company_id){
            $company_details = Company::find($user->company_id);
            $users_count = User::where('company_id',$user->company_id)->count();
        }
        $userData['users_count'] = $users_count;
        $userData['company_details'] = $company_details;
		$userData['picture']    = 'images/'.$userData['picture'];

        return response()->json([
            'status' => 'SUCCESS',
			'data' => ['user' => $userData],
            'message' => "Profile information has been updated successfully."
        ]);
    }

    public function checkStatus(Request $request)
    {
        $token = $request->token;
        $user = JWTAuth::parseToken()->authenticate();

        if ($user && $token !== '' && $user->token == $token) {
            return response()->json([
                'status' => 'SUCCESS',
            ]);
        }

        return response()->json([
            'status' => 'FAIL',
            'message' => "You are logged in with new device."
        ]);
    }

    public function verifyCaptcha($gresponse)
    {
        $googleConfig = config('global.google');
        $post_data = http_build_query(
            array(
                'secret' => $googleConfig['recaptcha']['site_secret'],
                'response' => $gresponse,
                'remoteip' => (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR'])
            )
        );
        $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $post_data
        ));
        $context  = stream_context_create($opts);
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        $result = json_decode($response);

        if (!$result->success || empty($result->success)) {
            return false;
        }

        return true;
    }

    public function getUserPermission($roleId)
    {
        $permissionId = \DB::table("role_has_permissions")->where("role_id", $roleId)
        ->pluck('role_has_permissions.permission_id')
        ->toArray();
        $data = [];
        $permission = collect(Permissions::where('guard_name','api')->whereIn('id',$permissionId)->where('deleted',0)->get(['name']))->map(function($q,$i) use(&$data){
            $name = explode(': ',$q->name);
            $nameValue = $name[0];
            if(!empty($name[1]))
            {
                $data[str_replace(' ','',$name[0])][]['name'] = str_replace(' ','',$name[1]);
                return $data;
            }
        })->toArray();
        return ['permission'=>$data];
    }

    public function userPermission($userId)
    {
        $permissionId = \DB::table("model_has_permissions")->where("model_id",$userId)
        ->pluck('permission_id')
        ->toArray();

        $permission = collect(Permissions::where('guard_name','api')->whereIn('id',$permissionId)->where('deleted',0)->get(['name']))->map(function($q,$i) use(&$data){
            $name = explode(': ',$q->name);
            $nameValue = $name[0];
            if(!empty($name[1]))
            {
                $data[str_replace(' ','',$name[0])][]['name'] = str_replace(' ','',$name[1]);
                return $data;
            }
        })->toArray();

        return ['permission'=>$data];
    }

    public function getRoleWisePermission($roleId)
    {
        $permissionId = \DB::table("role_has_permissions")->where("role_id", $roleId)
        ->pluck('role_has_permissions.permission_id')
        ->toArray();
        $data = [];
        $permission = collect(Permissions::where('guard_name','api')->whereIn('id',$permissionId)->where('deleted',0)->get(['name']))->map(function($q,$i) use(&$data){
            $name = explode(': ',$q->name);
            $nameValue = $name[0];
            if(!empty($name[1]))
            {
                $data[str_replace(' ','',$name[0])][]['name'] = str_replace(' ','',$name[1]);
                return $data;
            }
        })->toArray();
        return ['permission'=>$data];
    }

    public function userWisePermission($userId)
    {
        $permissionId = \DB::table("model_has_permissions")->where("model_id",$userId)
        ->pluck('permission_id')
        ->toArray();

        $permission = collect(Permissions::where('guard_name','api')->whereIn('id',$permissionId)->where('deleted',0)->get(['name']))->map(function($q,$i) use(&$data){
            $name = explode(': ',$q->name);
            $nameValue = $name[0];
            if(!empty($name[1]))
            {
                $data[str_replace(' ','',$name[0])][]['name'] = str_replace(' ','',$name[1]);
                return $data;
            }
        })->toArray();

        return ['permission'=>$data];
    }
}
