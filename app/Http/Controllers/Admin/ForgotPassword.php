<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use DB;
use Hash;
use Carbon;
use Helper;
use App\Models\Employee;
use App\Models\EmailTemplate;
use App\Models\EmailSmsLog;
use Password;
use Illuminate\Support\Str;
use App\Mail\SendEmail;
use App\Mail\SendEmailViaQueue;
use Storage, Mail;

class ForgotPassword extends Controller
{
    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLinkRequestForm() {
        return view('admin.auth.forgot-password');
    }

    protected function broker() {
        return Password::broker('admins');
    }
    
	public function sendResetLinkEmail(Request $request){
		$user = Employee::where('email', $request->email)->first();
		if ( !$user ) return redirect()->back()->withErrors(['error' => '404']);
		
		$token = Str::random(30);//str_random(15);
		//create a new token to be sent to the user. 
		DB::table('password_resets')->insert([
			'email' => $request->email,
			'token' => $token, //change 60 to any length you want
			'created_at' => Carbon::now()
		]);
	
		$url = url(route('admin.password.reset', [
                'token' => $token,
                'email' => $request->email,
            ], false));
			
		$template = EmailTemplate::where('external_key','=',"forgetpassword")->first();
		$shortcodes = Helper::shortcodes($user);
		$shortcodes['{{#reset_password_button}}'] = '<a href="'.$url.'" rel="noopener" target="_blank"><button type="button">Reset Password</button></a>';
		$shortcodes['{{#user_name}}'] = $user->name;
		$shortcodes['{{#reset_password_link}}'] = $url;
		
		$data['subject'] = $template->parseSubject($shortcodes);
		$data['messagecontent'] = $template->parseContent($shortcodes);
		$data['from_email'] = Helper::getSetting('mail_from');
		$data['from_name'] =  Helper::getSetting('mail_from_name');
		
		$to = [
					'email' => $user->email,
					'name' => $user->name,
			];
		$when = now()->addMinutes(1);
				//Mail::to($to)->later($when, new SendEmailViaQueue($template->email_template_id, $shortcodes));
				$message_resp = Mail::send('emails.email', $data, function($message)use($data,$to) {
					$message->subject($data['subject']);
					if(isset($data['from_email']) && isset($data['from_name'])){
					  $message->from($data['from_email'], $data['from_name']);
					}
					$message->to($to['email']);
				});
				$log = new EmailSmsLog;
				$log->user_id = $user->id;
				$log->client_id = $user->organization_id;
				$log->client_number = $user->mobileno;
				$log->client_email = $user->email;
				$log->company_id = $user->company_id;
				$log->template_id = $template->email_template_id;
				$log->type = 'Email';
				$log->response = '1';
				$log->save();
		return back()->with(['success' => __('We have e-mailed your password reset link!')]);
		
		/* $status = Password::sendResetLink(
			$request->only('email')
		);

		return $status === Password::RESET_LINK_SENT
			? back()->with(['success' => __($status)])
			: back()->withErrors(['email' => __($status)]); */
	}
	
	public function resetPassword(Request $request)
 {
	 $token = $request->token;
	 $password = $request->password;
     $tokenData = DB::table('password_resets')
     ->where('token', $token)->first();

     $user = Employee::where('email', $tokenData->email)->first();
	 
     if ( !$user ) return redirect()->to('home'); //or wherever you want
		
     $user->password = Hash::make($password);
     $user->update(); //or $user->save();

     //do we log the user directly or let them login and try their password for the first time ? if yes 
     //Auth::login($user);

    // If the user shouldn't reuse the token later, delete the token 
    DB::table('password_resets')->where('email', $user->email)->delete();


    return redirect(route('admin.login'))->with('success', 'Password updated successfully');
	
 }
}
