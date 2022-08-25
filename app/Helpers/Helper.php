<?php

namespace App\Helpers;

use App\Mail\SendEmail;
use App\Mail\SendEmailViaQueue;
use Storage, Mail;
use Carbon\Carbon;

use App\Models\ActionLog;
use App\Models\EmailSmsLog;
use App\Models\Client;
use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use App\Models\ContactSection;
use App\Models\ProductSection;
use App\Models\EmployeeSection;
use App\Models\ContactField;
use App\Models\ProductField;
use App\Models\EmployeeField;
use App\Models\FieldValue;
use App\Models\ContactValue;
use App\Models\ProductValue;
use App\Models\EmployeeValue;
use App\Models\ClientPlan;
use App\Models\Contacts;
use App\Models\Products;
use App\Models\InterestedProduct;
use App\Models\EmailHistory;
use App\Models\CompanyPermission;
use App\Models\EmailTemplate;
use App\Models\Subscriptions;
use App\Models\Country;
use App\Models\State;
use App\Models\CompanyType;
use App\Models\IndustryType;
use App\Models\SmsTemplate;
use App\Models\SmsSettings;
use Spatie\Valuestore\Valuestore;
use Auth;

class Helper
{
    public static function getSetting($key)
    {
        $valuestore = Valuestore::make(storage_path('app/settings.json'));
        return $valuestore->get($key); //
    }

    public function replace_view_status($status, $type = 'ACTIVE', $default = '-')
    {
        $status_array = [];
        switch ($type) {
            case 'ACTIVE':
                $status_array = config('global.status_array');
                break;
        }

        return (isset($status_array[$status]) ? $status_array[$status] : $default);
    }

    public function get_new_filename($file)
    {
        $actual_name = \Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-');
        $original_name = $actual_name;
        $extension = $file->getClientOriginalExtension();
        $i = 1;
        while ($exists = Storage::has($actual_name . "." . $extension)) {
            $actual_name = (string) $original_name . $i;
            $i++;
        }
        return $actual_name . "." . $extension;
    }

	public function encrypt($string)
    {
        return $string;
        return $this->encrypt_decrypt("E", $string);
    }

    public function decrypt($string)
    {
        return $string;
        return $this->encrypt_decrypt("D", $string);
    }

	private function encrypt_decrypt($action, $string)
    {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = env('APP_KEY');
        $secret_iv = 'RKCRM';

        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'E') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } else {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

	public function getRecordOrder($sortOrder)
    {
        switch ($sortOrder) {
            case 'descend':
                $sortOrder = 'DESC';
                break;
            case 'ascend':
                $sortOrder = 'ASC';
                break;
            default:
                $sortOrder = 'DESC';
                break;
        }
        return $sortOrder;
    }

	public function paginationData($request, $sortField = false)
    {
        if (!$request->size) {
            $request->size = 10;
        }
        if (!$request->sortField && !$sortField) {
            $request->sortField = 'created_at';
        }
        if ($sortField) {
            $request->sortField = $sortField;
        }
        if (!$request->sortOrder) {
            $request->sortOrder = "DESC";
        } else {
            $request->sortOrder = $this->getRecordOrder($request->sortOrder);
        }
        return $request;
    }

    public function createImageFromBase64($file)
    {
        $extension = explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];   // .jpg .png .pdf
        $replace = substr($file, 0, strpos($file, ',') + 1);
        // find substring fro replace here eg: data:image/png;base64,
        $newFile = str_replace($replace, '', $file);
        $newFile = str_replace(' ', '+', $newFile);
        $fileName = \Str::random(10) . '.' . $extension;

        Storage::put('images/'.$fileName, base64_decode($newFile));

        return $fileName;
    }

    public function createDocFromBase64($file, $old_filename)
    {
        $info = pathinfo($old_filename);
        $extension = $info['extension'];

        // $extension = explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];   // .jpg .png .pdf
        $replace = substr($file, 0, strpos($file, ',') + 1);
        // find substring fro replace here eg: data:image/png;base64,
        $newFile = str_replace($replace, '', $file);
        $newFile = str_replace(' ', '+', $newFile);
        $fileName = \Str::random(10) . '.' . $extension;

        Storage::put('doc/'.$fileName, base64_decode($newFile));

        return $fileName;
    }

    public function createBase64FromImage($imageName)
    {
        if (Storage::has($imageName)) {
            $image_parts = explode(".", $imageName);
            $img_extension = $image_parts[1];
            $imageString = 'data:image/' . $img_extension . ';base64,' . base64_encode(Storage::get($imageName));
            return $imageString;
        }
        return $imageString = null;
    }

    public function generateOTP($user, $min)
    {
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expired_at = Carbon::now()->addMinutes($min);
        $user->save();

        return true;
    }

    public function transformShortcodeValue($item, $obj)
    {
        $column = $item['column'];
        $shortcode = $item['shortcode'];

        if (strpos($column, '->') !== false) {
            $properties = explode("->", $column);
            $value = $shortcode;
            if (is_array($properties) && count($properties)) {
                $tmpObj = true;
                foreach ($properties as $key) {
                    if ($tmpObj && !is_object($tmpObj)) {
                        $tmpObj = (isset($obj->{$key}) ? $obj->{$key} : false);
                        if (!$tmpObj) break;
                    } else if (is_object($tmpObj)) {
                        $tmpObj = (isset($tmpObj->{$key}) ? $tmpObj->{$key} : false);
                        if (!$tmpObj) break;
                    }
                }
                //$value = (!empty($tmpObj) ? $tmpObj : $shortcode);//remove comment if required
                $value = (!empty($tmpObj) ? $tmpObj : '');
            }
        } else {
            //$value = ($obj && isset($obj->{$column}) ? $obj->{$column} : $shortcode);//remove comment if required
            $value = ($obj && isset($obj->{$column}) ? $obj->{$column} : '');
        }

        switch ($column) {
            case 'login_url':
                $value = '#';
                break;
        }
        return $value;
    }

    function getApiUrl($mobile_numbers, $msg)
    {
        $sms_setting = SmsSettings::find(1);

        $parameters = json_decode($sms_setting->parameters);

        $url = $sms_setting->api_url . "?";

        foreach($parameters as $parameter) {
            $url .= $parameter->key ."=". $parameter->value ."&";
        }

        $url .= $sms_setting->mobile_param ."=". implode(",", $mobile_numbers) . "&";
        $url .= $sms_setting->msg_param ."=". $msg;

        return $url;
    }

    function sendSMS($sms_template_id, $mobile_no = array(), $shortcode_data, $user, $msg = false)
    {
        //Find Template
        $smsTemplate = SmsTemplate::find($sms_template_id);
        if ($smsTemplate) {
            $body = $smsTemplate->parseContent($shortcode_data);

            if($msg == false) {
                $msg = urlencode($body);
            } else {
                $msg = urlencode($msg);
            }

            $sms_setting = SmsSettings::find(1);
            $mobile_numbers = $this->filterMobileNumbers($mobile_no);

            if (count($mobile_numbers) > 0) {

                $url = $this->getApiUrl($mobile_numbers, $msg);

                $sms = curl_init();
                curl_setopt_array($sms, array(
                    // CURLOPT_URL => 'http://ui.netsms.co.in/API/SendSMS.aspx?APIkey=BFhOOeTfQtahCkESTWjBMt3Tpu&SenderID=WeCare&SMSType=2&Mobile=' . implode(",", $mobile_numbers) . '&MsgText=' . $msg,
                    // CURLOPT_URL => $sms_setting['final_url']. '&'.$sms_setting['mobile_param'].'=' . implode(",", $mobile_numbers) . '&'.$sms_setting['msg_param'].'=' . $msg,
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                ));

                $result = curl_exec($sms);

                $splited = explode('|',$result);

                if(!empty($splited[0]) && $splited[0] == 'ok') {
                    return true;
                } else {
                    return false;
                }
            }
            return false;
        } else {
            return false;
        }
    }

    function filterMobileNumbers($mobiles)
    {
        $mobiles = array_filter($mobiles, function ($m) {
            return (strlen($m) > 8);
        });
        return array_map('trim', $mobiles);
    }

    function filterEmails($emails)
    {
        $emails = array_filter($emails, function ($email) {
            return (filter_var(trim($email), FILTER_VALIDATE_EMAIL));
        });
        return array_map('trim', $emails);
    }

   public function sendEmail($user, $template_id, $shortcodes = [], $queue = false, $minute = 1)
    {
        if (!$template_id || !$user) return false;

        //Disable for Mayank
        if($user->user_id == 8) return true;

        if ($queue) {
            $when = now()->addMinutes($minute);
            try {
                Mail::to($user)->later($when, new SendEmailViaQueue($template_id, $shortcodes));

                $log = new EmailSmsLog;
                $log->user_id = $user->user_id;
                $log->template_id = $template_id;
                $log->type = 'EMAIL';
                $log->response = null;
                $log->save();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            try {
                Mail::to($user)->send(new SendEmail($template_id, $shortcodes));

                $log = new EmailSmsLog;
                $log->user_id = $user->user_id;
                $log->template_id = $template_id;
                $log->type = 'EMAIL';
                $log->response = null;
                $log->save();
                return true;
            } catch (\Exception $e) {
                return $e;
            }
        }

        return false;
    }

    public function shortcodes($user = false)
    {
        if (!$user) {
            return [];
        }

        $shortcodes = config()->get('shortcodes.magic_keywords');

        $keywords = [];

        $magic_keyword_keys = array_map(function ($item) {
            return $item['shortcode'];
        }, $shortcodes);

        $magic_keyword_values = [];

        $magic_keyword_values = array_map(function ($item) use ($user) {
            return $this->transformShortcodeValue($item, $user);
        }, $shortcodes);

        if (count($magic_keyword_values) == count($magic_keyword_keys)) {
            $keywords = array_combine($magic_keyword_keys, $magic_keyword_values);
        }

        return $keywords;
    }

    public function profileProgress($u)
    {
        $progress = 0;

        $profiles = [
            'photo' => 15,
            'alt_mobileno' => 10,
            'address' => 15,
            'pincode' => 10,
            'city' => 10,
            'state_id' => 10,
            'country_id' => 10,
            'emr_contact_person' => 10,
            'emr_contact_number' => 10,
        ];

        foreach ($profiles as $column => $perc) {
            if ($u->{$column} != '') {
                $progress += $perc;
            }
        }

        return $this->decimal($progress);
    }

    public function decimal($number, $decimal = 2)
    {
        $number = (float) $number;
        if (strpos($number, ".") === true) {
            //return sprintf('%0.2f', $number);
        }
        return round(number_format($number, $decimal));
    }

    public function decimalNumber($nunber, $zero = 2, $dot = '.')
    {
        return number_format($nunber, $zero, $dot,'');
    }

	public function addActionLog($user_id, $module, $module_id, $action, $old = [], $new = [])
    {
        $log = new ActionLog;
        $log->user_id = $user_id;
        $log->module = $module;
        $log->module_id = $module_id;
        $log->action = $action;
        if (!empty($old)) {
            $log->oldData = $old;
        }
        if (!empty($new)) {
            $log->newData = $new;
        }
        $log->save();
    }
	function get_client_info($cid){
		return Client::find($cid);
	}

	function getRows($file)
    {
        $replace = substr($file, 0, strpos($file, ',') + 1);
        $newFile = str_replace($replace, '', $file);
        $newFile = str_replace(' ', '+', $newFile);
        $rows = explode("\n", base64_decode($newFile));
        $array = array_map('str_getcsv', $rows);
        return $array;
    }

    // function checkUserLimit($companyId)
    // {
    //     $company = Company::find($companyId);
    //     $status = 1;
    //     if($company->plan_id)
    //     {
    //         $noOfUsers = Plan::find($company->plan_id);
    //         if(!$noOfUsers)
    //         {
    //             return ['status'=>2];
    //         }
    //         $noOfUsers = $noOfUsers->no_of_users;
    //         $usersCount = User::where('company_id',$companyId)->count();
    //         $status = 1;
    //         if(($usersCount + 1) > $noOfUsers)
    //         {
    //            $status = 0;
    //         }
    //     }
    //     return ['status'=>$status];
    // }

    function checkUserLimit($companyId)
    {
        $company = Company::find($companyId);
        $status = 0;

        if($company)
        {
            $subscription = Subscriptions::where('company_id',$companyId)->get()->count();

            $noOfUsers = $company->no_of_users;
            if(!$subscription)
            {
                return ['status'=>2];
            }

            $usersCount = User::where('company_id',$companyId)->count();

            $status = 1;
            if($usersCount >= $noOfUsers)
            {
               $status = 0;
            }
        }
        return ['status'=>$status];
    }

    // get section model to model id wise
    function sectionModel($modelId)
    {
        $sectionModel = null;
        switch ($modelId) {
            case 'contact':
                $sectionModel = new ContactSection;
                break;
            case 'product':
                $sectionModel = new ProductSection;
                break;
            case 'employee':
                $sectionModel = new EmployeeSection;
                break;
        }
        return $sectionModel;
    }

    // get field model to model id wise
    function fieldModel($modelId)
    {
        $fieldModel = null;
        switch ($modelId) {
            case 'contact':
                $fieldModel = new ContactField;
                break;
            case 'product':
                $fieldModel = new ProductField;
                break;
            case 'employee':
                $fieldModel = new EmployeeField;
                break;
        }
        return $fieldModel;
    }

    // get field value model to model id wise
    function fieldValueModel($modelId)
    {
        $fieldValueModel = null;
        switch ($modelId) {
            case 'contact':
            $fieldValueModel = new ContactValue;
                break;
            case 'product':
            $fieldValueModel = new ProductValue;
                break;
            case 'employee':
            $fieldValueModel = new EmployeeValue;
                break;
        }
        return $fieldValueModel;
    }

	function fieldPrimaryIdColumn($modelId)
    {
        $primaryId = null;
        switch ($modelId) {
            case 'contact':
				$primaryId = 'contact_id';
				break;
            case 'product':
				$primaryId = 'product_id';
                break;
            case 'employee':
				$primaryId = 'employee_id';
                break;
        }
        return $primaryId;
    }

    // get field value
    function getFieldValue($type,$typeId)
    {
        $fieldValue = FieldValue::where('type',$type)->where('type_id',$typeId)->pluck('values')->toArray();
        // $fieldValue = implode('<br />',$fieldValue);
        return $fieldValue;
    }

    // get primary company contact
    function getCompanyContact($companyId)
    {
        $contact = User::where('company_id',$companyId)->where('company_contact_type','1')->orderBy('updated_at','DESC')->first();
        return $contact;
    }

    // get company Data
    function getCompany($companyId){
        return Company::find($companyId);
    }

    function getPlanData($clientId,$companyId,$type){
       $plans = ClientPlan::where('client_id',$clientId)->where('company_id',$companyId);
       if($type == 1)
       {
            $plans = $plans->with('plan')->get()->pluck('plan.name')->toArray();
       }else{
            $plans = $plans->pluck('final_amount')->toArray();
       }
       $plans = implode(',',$plans);
       return $plans;
    }

    function getPlanSubscriptionsData($clientId,$companyId,$type){

        $subscriptions = Subscriptions::where('client_id',$clientId)->where('company_id',$companyId)->pluck('id')->toArray();
        $plans = ClientPlan::whereIn('subscription_id',$subscriptions);
        if($type == 1)
        {
             $plans = $plans->with('plan')->get()->pluck('plan.name')->toArray();
			 $plans = implode(',',$plans);
        }else{
             //$plans = $plans->pluck('final_amount')->toArray();
			 $plans = Subscriptions::where('client_id',$clientId)->where('company_id',$companyId)->sum('final_amount');
        }
        return $plans;
     }


    function getModelName($modelId)
    {
        $modelName = null;
        switch ($modelId) {
            case 'contact':
                $modelName = new Contacts;
                break;
            case 'product':
                $modelName = new Products;
                break;
            case 'employee':
                $modelName = new User;
                break;
        }
        return $modelName;
    }

    public function interestedProductAssign($contactId,$leadId)
    {
        $products = [];
        $columnType = $contactId ? 'contact_id' : 'lead_id';
        $id = $contactId ? $contactId : $leadId;
        $interestedProduct = collect(InterestedProduct::where($columnType,$id)->get(['id','product_id','contact_id','lead_id']))->map(function($q) use(&$products){
            if(!empty($q->getProductData)){
                $q->getProductData->ipid = $q->id;
            }
            $products[] = $q->getProductData;
            return $products;
        });
        return $products;
    }

    public function storeEmailHistory($templateId,$senderId,$receiverId,$receiverEmail,$ccMail=null,$bccMail=null,$subject=null,$content=null,$from_data)
    {
        // Get Instance
        $user = auth()->user();
        $emailTemplate = EmailTemplate::find($templateId);

        // Forgot password
		if($user){
		$company_id = $user->company_id;
		}else{
			$user = User::find($senderId);
			$company_id = $user->company_id;
		}

        // Save Email Log
        $log = new EmailSmsLog;
        $log->user_id = $senderId;
        $log->client_id = $receiverId;
        $log->client_number = "";
        $log->client_email = $receiverEmail;
        $log->company_id = $company_id;//$user->company_id ?? 0;
        $log->template_id = $templateId;
        $log->type = 'Email';
        $log->response = '1';

        if($emailTemplate)
        {
            if(!$subject){
                $subject = $emailTemplate->subject;
            }
            if(!$content){
                $content = $emailTemplate->content;
            }
            $data['messagecontent'] = $content;
            $data['subject'] = $subject;

            Mail::send('emails.email', $data, function($message) use($receiverEmail,$ccMail,$bccMail,$subject, $from_data){
                $message->to($receiverEmail);
                if($ccMail){
                    $message->cc( explode(',',$ccMail));
                }
                if($bccMail){
                    $message->bcc(explode(',',$bccMail));
                }
                $message->subject($subject);

                if($from_data){
                    $message->from($from_data['from_email'], $from_data['from_name']);
                }else{
                    $message->from(self::getSetting('mail_from'), self::getSetting('mail_from_name'));
                }
            });
            if (Mail::failures()) {
                $log->response = 0;
            }else{
                $log->response = 1;
            }
            $log->msg = $content ?? $emailTemplate->content;
            $log->save();
        }
        return true;
    }


    // -- Commented on (19-11-2021)

    // public function storeEmailHistory($templateId,$type,$typeId,$senderId,$receiverId,$receiverEmail,$ccMail=null,$bccMail=null,$subject=null,$content=null,$from_data)
    // {
    //     $emailHistory = new EmailHistory();
    //     $emailHistory->email_template_id = $templateId;
    //     $emailHistory->type  = $type;
    //     $emailHistory->type_id = $typeId;
    //     $emailHistory->sender_id = $senderId;
    //     $emailHistory->receiver_id = $receiverId;
    //     $emailHistory->receiver_email = $receiverEmail;
    //     $emailHistory->is_send = 0;
    //     $emailTemplate = EmailTemplate::find($templateId);
    //     if($emailTemplate)
    //     {
    //         if(!$subject){
    //             $subject = $emailTemplate->subject;
    //         }
    //         if(!$content){
    //             $content = $emailTemplate->content;
    //         }
    //         $data['messagecontent'] = $content;
    //         $data['subject'] = $subject;

    //         Mail::send('emails.email', $data, function($message) use($receiverEmail,$ccMail,$bccMail,$subject, $from_data){
    //             $message->to($receiverEmail);
    //             if($ccMail){
    //                 $message->cc($ccMail);
    //             }
    //             if($bccMail){
    //                 $message->bcc($bccMail);
    //             }
    //             $message->subject($subject);

    //             if(isset($from_data)){
    //                 $message->from($from_data['from_email'], $from_data['from_name']);
    //             }else{
    //                 $message->from('info@unicepts.in','Trinity Unicepts Pvt. Ltd.');
    //             }
    //          });
    //          if (Mail::failures()) {
    //             $emailHistory->is_send = 0;
    //          }else{
    //             $emailHistory->is_send = 1;
    //          }
    //     }
    //     $emailHistory->save();
    //     return true;
    // }

    public function getPermissionIds($companyId)
    {
        $assignedPermssion = CompanyPermission::where('company_id',$companyId)->pluck('permission_id')->toArray();
        return $assignedPermssion;
    }

    public function getColumnStructure($tableName)
    {
        //$states = \DB::select(\DB::raw("SELECT `state` FROM `country_pincode` GROUP BY state"))->map(function($q)
        $states = State::select(['name','state_id'])->where('country_id', 101)->where('deleted', 0)->get()->map(function($q)
		{
            $t['value'] = strval($q->name);
            $t['title'] = $q->state;
            return $t;
        });

        $countries = Country::select(['name','country_id'])->where('deleted', 0)->get()->map(function($q){
            $t['value'] = strval($q->country_id);
            $t['title'] = $q->name;
            return $t;
        });

        $companyType = CompanyType::select(['id','name'])->get()->map(function($q){
            $t['value'] = strval($q->id);
            $t['title'] = $q->name;
            return $t;
        });

        $industries = IndustryType::select(['id','name'])->get()->map(function($q){
            $t['value'] = strval($q->id);
            $t['title'] = $q->name;
            return $t;
        });


        $establishYears = app('App\Http\Controllers\Admin\ClientsController')->establishYears();

        $year = [];
        foreach($establishYears as $key => $value){
            $year_new['value'] = strval($key);
            $year_new['title'] = strval($value);
            array_push($year,$year_new);
        }

        $data = [];
        if($tableName == 'leads'){
           $data['fields']['is_completed']['label'] = 'Is Completed';
           $data['fields']['is_completed']['type'] = 'boolean';
           $data['fields']['is_completed']['valueSources'] = ['value'];
           $data['fields']['is_completed']['operators'] = ['equal'];

           $data['fields']['lead_name']['label'] = 'Lead Name';
           $data['fields']['lead_name']['type'] = 'text';
           $data['fields']['lead_name']['valueSources'] = ['value'];

           $data['fields']['stage_id']['label'] = 'Stage';
           $data['fields']['stage_id']['type'] = 'select';
           $data['fields']['stage_id']['valueSources'] = ['value'];
           $data['fields']['stage_id']['fieldSettings']['listValues'] = [['value'=>'1','title'=>'Cold'],['value'=>'2','title'=>'Warm'],['value'=>'3','title'=>'Hot'],['value'=>'4','title'=>'Converted'],['value'=>'5','title'=>'Closed'],['value'=>'6','title'=>'Cros-sell/up-sell']];

           $data['fields']['lead_source']['label'] = 'Lead Source';
           $data['fields']['lead_source']['type'] = 'text';
           $data['fields']['lead_source']['valueSources'] = ['value'];

           $data['fields']['customer_name']['label'] = 'Customer Name';
           $data['fields']['customer_name']['type'] = 'text';
           $data['fields']['customer_name']['valueSources'] = ['value'];
        }

        if($tableName == 'contacts'){
           $data['fields']['name']['label'] = 'Name';
           $data['fields']['name']['type'] = 'text';
           $data['fields']['name']['valueSources'] = ['value'];
        }

        $data['fields']['company_name']['label'] = 'Company Name';
        $data['fields']['company_name']['type'] = 'text';
        $data['fields']['company_name']['valueSources'] = ['value'];

        $data['fields']['email']['label'] = 'Email';
        $data['fields']['email']['type'] = 'text';
        $data['fields']['email']['valueSources'] = ['value'];

        $data['fields']['cc_email']['label'] = 'CC Email';
        $data['fields']['cc_email']['type'] = 'text';
        $data['fields']['cc_email']['valueSources'] = ['value'];

        $data['fields']['bcc_email']['label'] = 'BCC Email';
        $data['fields']['bcc_email']['type'] = 'text';
        $data['fields']['bcc_email']['valueSources'] = ['value'];

        $data['fields']['secondary_email']['label'] = 'Secondary Email';
        $data['fields']['secondary_email']['type'] = 'text';
        $data['fields']['secondary_email']['valueSources'] = ['value'];

        $data['fields']['city']['label'] = 'City';
        $data['fields']['city']['type'] = 'text';
        $data['fields']['city']['valueSources'] = ['value'];

        $data['fields']['turnover']['label'] = 'Turnover';
        $data['fields']['turnover']['type'] = 'number';
        $data['fields']['turnover']['valueSources'] = ['value'];

        $data['fields']['gst_no']['label'] = 'GST Number';
        $data['fields']['gst_no']['type'] = 'number';
        $data['fields']['gst_no']['valueSources'] = ['value'];

        $data['fields']['no_of_employees']['label'] = 'No Of Employees';
        $data['fields']['no_of_employees']['type'] = 'number';
        $data['fields']['no_of_employees']['valueSources'] = ['value'];

        $data['fields']['website']['label'] = 'Website';
        $data['fields']['website']['type'] = 'text';
        $data['fields']['website']['valueSources'] = ['value'];

        $data['fields']['postcode']['label'] = 'Postcode';
        $data['fields']['postcode']['type'] = 'text';
        $data['fields']['postcode']['valueSources'] = ['value'];

        $data['fields']['mobile_no']['label'] = 'Mobile Number';
        $data['fields']['mobile_no']['type'] = 'number';
        $data['fields']['mobile_no']['valueSources'] = ['value'];
        // $data['fields']['mobile_no']['fieldSettings']['min'] = '10';
        // $data['fields']['mobile_no']['fieldSettings']['max'] = '10';

        $data['fields']['pan_no']['label'] = 'Pan Number';
        $data['fields']['pan_no']['type'] = 'text';
        $data['fields']['pan_no']['valueSources'] = ['value'];

        $data['fields']['state']['label'] = 'State';
        $data['fields']['state']['type'] = 'select';
        $data['fields']['state']['valueSources'] = ['value'];
        $data['fields']['state']['fieldSettings']['listValues'] = $states;

        $data['fields']['country']['label'] = 'Country';
        $data['fields']['country']['type'] = 'select';
        $data['fields']['country']['valueSources'] = ['value'];
        $data['fields']['country']['fieldSettings']['listValues'] = $countries;

        $data['fields']['company_type_id']['label'] = 'Company Type';
        $data['fields']['company_type_id']['type'] = 'select';
        $data['fields']['company_type_id']['valueSources'] = ['value'];
        $data['fields']['company_type_id']['fieldSettings']['listValues'] = $companyType;

        $data['fields']['industry_id']['label'] = 'Industry';
        $data['fields']['industry_id']['type'] = 'select';
        $data['fields']['industry_id']['valueSources'] = ['value'];
        $data['fields']['industry_id']['fieldSettings']['listValues'] = $industries;

        $data['fields']['established_in']['label'] = 'Established In';
        $data['fields']['established_in']['type'] = 'select';
        $data['fields']['established_in']['valueSources'] = ['value'];
        $data['fields']['established_in']['fieldSettings']['listValues'] = $year;

        return $data;
    }

    public function usersType($type=null)
    {
        $type_array = array(
            1 => 'Admin',
            2 => 'Normal',
            3 => 'Dealer',
            4 => 'Distributor'
        );

        if($type){
           return $type_array[$type];
        }else{
            return $type_array;
        }
    }

    public function emailTemplateCss()
    {
        return '/* -------------------------------------
                    GLOBAL RESETS
                ------------------------------------- */

                /*All the styling goes here*/

                img {
                border: none;
                -ms-interpolation-mode: bicubic;
                max-width: 100%;
                }

                body {
                background-color: #f6f6f6;
                font-family: sans-serif;
                -webkit-font-smoothing: antialiased;
                font-size: 14px;
                line-height: 1.4;
                margin: 0;
                padding: 0;
                -ms-text-size-adjust: 100%;
                -webkit-text-size-adjust: 100%;
                }

                table {
                border-collapse: separate;
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
                border: none !important;
                width: 100%; }
                table td {
                    font-family: sans-serif;
                    font-size: 14px;
                    vertical-align: top;
                }

                /* -------------------------------------
                    BODY & CONTAINER
                ------------------------------------- */

                .body {
                background-color: #f6f6f6;
                width: 100%;
                }

                /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
                .container {
                display: block;
                margin: 0 auto !important;
                /* makes it centered */
                max-width: 580px;
                padding: 10px;
                border: 0px;
                }

                /* This should also be a block element, so that it will fill 100% of the .container */
                .content {
                box-sizing: border-box;
                display: block;
                margin: 0 auto;
                max-width: 580px;
                padding: 10px;
                border: 0px;
                }

                /* -------------------------------------
                    HEADER, FOOTER, MAIN
                ------------------------------------- */
                .main {
                background: #ffffff;
                border-radius: 3px;
                width: 100%;
                }

                .wrapper {
                box-sizing: border-box;
                padding: 20px;
                }

                .content-block {
                padding-bottom: 10px;
                padding-top: 10px;
                }

                .footer {
                clear: both;
                margin-top: 10px;
                text-align: center;
                width: 100%;
                }
                .footer td,
                .footer p,
                .footer span,
                .footer a {
                    color: #999999;
                    font-size: 12px;
                    text-align: center;
                }

                /* -------------------------------------
                    TYPOGRAPHY
                ------------------------------------- */
                h1,
                h2,
                h3,
                h4 {
                color: #000000;
                font-family: sans-serif;
                font-weight: 400;
                line-height: 1.4;
                margin: 0;
                margin-bottom: 30px;
                }

                h1 {
                font-size: 35px;
                font-weight: 300;
                text-align: center;
                text-transform: capitalize;
                }

                p,
                ul,
                ol {
                font-family: sans-serif;
                font-size: 14px;
                font-weight: normal;
                margin: 0;
                margin-bottom: 15px;
                }
                p li,
                ul li,
                ol li {
                    list-style-position: inside;
                    margin-left: 5px;
                }

                a {
                color: #3498db;
                text-decoration: underline;
                }

                /* -------------------------------------
                    BUTTONS
                ------------------------------------- */
                .btn {
                box-sizing: border-box;
                width: 100%; }
                .btn > tbody > tr > td {
                    padding-bottom: 15px; }
                .btn table {
                    width: auto;
                }
                .btn table td {
                    background-color: #ffffff;
                    border-radius: 5px;
                    text-align: center;
                }
                .btn a {
                    background-color: #ffffff;
                    border: solid 1px #3498db;
                    border-radius: 5px;
                    box-sizing: border-box;
                    color: #3498db;
                    cursor: pointer;
                    display: inline-block;
                    font-size: 14px;
                    font-weight: bold;
                    margin: 0;
                    padding: 12px 25px;
                    text-decoration: none;
                    text-transform: capitalize;
                }

                .btn-primary table td {
                background-color: #3498db;
                }

                .btn-primary a {
                background-color: #3498db;
                border-color: #3498db;
                color: #ffffff;
                }

                /* -------------------------------------
                    OTHER STYLES THAT MIGHT BE USEFUL
                ------------------------------------- */
                .last {
                margin-bottom: 0;
                }

                .first {
                margin-top: 0;
                }

                .align-center {
                text-align: center;
                }

                .align-right {
                text-align: right;
                }

                .align-left {
                text-align: left;
                }

                .clear {
                clear: both;
                }

                .mt0 {
                margin-top: 0;
                }

                .mb0 {
                margin-bottom: 0;
                }

                .powered-by a {
                text-decoration: none;
                }

                hr {
                border: 0;
                border-bottom: 1px solid #f6f6f6;
                margin: 20px 0;
                }

                /* -------------------------------------
                    RESPONSIVE AND MOBILE FRIENDLY STYLES
                ------------------------------------- */
            @media only screen and (max-width: 620px) {
                table[class=body] h1 {
                    font-size: 28px !important;
                    margin-bottom: 10px !important;
                }
                table[class=body] p,
                table[class=body] ul,
                table[class=body] ol,
                table[class=body] td,
                table[class=body] span,
                table[class=body] a {
                    font-size: 16px !important;
                }
                table[class=body] .wrapper,
                table[class=body] .article {
                    padding: 10px !important;
                }
                table[class=body] .content {
                    padding: 0 !important;
                }
                table[class=body] .container {
                    padding: 0 !important;
                    width: 100% !important;
                }
                table[class=body] .main {
                    border-left-width: 0 !important;
                    border-radius: 0 !important;
                    border-right-width: 0 !important;
                }
                table[class=body] .btn table {
                    width: 100% !important;
                }
                table[class=body] .btn a {
                    width: 100% !important;
                }
                table[class=body] .img-responsive {
                    height: auto !important;
                    max-width: 100% !important;
                    width: auto !important;
                }
            }

                /* -------------------------------------
                    PRESERVE THESE STYLES IN THE HEAD
                ------------------------------------- */
            @media all {
                .ExternalClass {
                    width: 100%;
                }
                .ExternalClass,
                .ExternalClass p,
                .ExternalClass span,
                .ExternalClass font,
                .ExternalClass td,
                .ExternalClass div {
                    line-height: 100%;
                }
                .apple-link a {
                    color: inherit !important;
                    font-family: inherit !important;
                    font-size: inherit !important;
                    font-weight: inherit !important;
                    line-height: inherit !important;
                    text-decoration: none !important;
                }
                #MessageViewBody a {
                    color: inherit;
                    text-decoration: none;
                    font-size: inherit;
                    font-family: inherit;
                    font-weight: inherit;
                    line-height: inherit;
                }
                .btn-primary table td:hover {
                    background-color: #34495e !important;
                }
                .btn-primary a:hover {
                    background-color: #34495e !important;
                    border-color: #34495e !important;
                }
            }';
    }

    public function emailTemplateBody()
    {
        return '<!doctype html>
                    <html>
                    <head>
                        <meta name="viewport" content="width=device-width" />
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        <title>Email</title>
                        <style>
                        {{#all_css}}
                        </style>
                    </head>
                    <body>
                        {{#template_body}}
                    </body>
                </html>';
    }


    public function defaultInvoiceTemplate(){

            return '<div class="invoice-box">
                <table cellpadding="0" cellspacing="0" border="1">
                    <tr class="top">
                        <td colspan="2">
                            <h1>Invoice</h1>
                        </td>
                    </tr>
                    <tr class="top">
                        <td colspan="2">
                            <table width="100%">
                                <tr>
                                    <td class="title">
                                        {{#company_name_and_logo}}
                                    </td>
                                    <td>
                                        Invoice : #{{#invoice_no}}<br />
                                        Created : {{#created_date}}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr class="information">
                        <td colspan="2">
                            <table>
                                <tr>
                                    <td>
                                        {{#company_address}}
                                        <br/>
                                        GST Number : {{#gst_no}}
                                    </td>
                                    <td>
                                        {{#client_name}}<br/>
                                        {{#client_email}}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr class="heading">
                        <td colspan="2">
                            <table cellpadding="0" cellspacing="0" class="details_table">
                                <tr>
                                    <th>Plan</th>
                                    <th width="15%">Subscription Date</th>
                                    <th width="5%">Amount</th>
                                    <th width="10%">Discount %</th>
                                    <th width="15%">Discount Amount</th>
                                    <th width="20%">Final Amount</th>
                                </tr>
                                <tr id="plan_list" class="plan_item">
                                    <td colspan="6"></td>
                                </tr>
                                <tfoot></tfoot>
                                <tr>
                                    <td colspan="3"></td>
                                    <th class="text_left" colspan="2">Total Amount</th>
                                    <th class="text_right">{{#total_amount}}</th>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <th class="text_left" colspan="2">SGST @ {{#sgst}}%</th>
                                    <th class="text_right">{{#sgst_amount}}</th>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <th class="text_left" colspan="2">CGST @ {{#cgst}}%</th>
                                    <th class="text_right">{{#cgst_amount}}</th>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <th class="text_left" colspan="2">IGST @ {{#igst}}%</th>
                                    <th class="text_right">{{#igst_amount}}</th>
                                </tr>
                                <tr>
                                <td colspan="3"></td>
                                    <th class="text_left" colspan="2">Net Amount</th>
                                    <th class="text_right">{{#final_amount}}</th>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <th class="text_left" colspan="2">Payment Pending</th>
                                    <th class="text_right">{{#yes_no}}</th>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <th class="text_left" colspan="2">Payment Mode</th>
                                    <th class="text_right">{{#payment_mode}}</th>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <th class="text_left" colspan="2">Payment Date</th>
                                    <th class="text_right">{{#payment_date}}</th>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <th class="text_left" colspan="2">Bank Name</th>
                                    <th class="text_right">{{#bank_name}}</th>
                                </tr>
                                <tr>
                                    <td colspan="3"></td>
                                    <th class="text_left" colspan="2">Transaction Number</th>
                                    <th class="text_right">{{#transaction_no}}</th>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>';
			/* <tr>
                                    <td colspan="3"></td>
                                    <th class="text_left" colspan="2">Round off Amount</th>
                                    <th class="text_right">{{#round_off_amt}}</th>
                                </tr> */
    }

    public function invoiceTemplateBody(){

        return '<!DOCTYPE html>
        <html>
            <head>
                <style>
                    .invoice-box {
                        max-width: 100%;
                        margin: auto;
                        border: 1px solid #eee;
                        font-size: 16px;
                        line-height: 24px;
                        color: #555;
                    }
                    .invoice-box table {
                        width: 100%;
                        line-height: inherit;
                        text-align: left;
                        border: 1px solid #b9b9b9;
                    }
                    .invoice-box table td {
                        padding: 0px;
                        vertical-align: top;
                    }
                    .invoice-box table table td {
                        padding: 10px;
                    }
                    .invoice-box table tr td:nth-child(2) {
                        text-align: right;
                    }
                    .invoice-box table tr.top table td.title {
                        font-size: 35px;
                        line-height: 60px;
                        color: #333;
                    }
                    .invoice-box table .company_logo{
                        padding-right: 10px;
                        vertical-align: middle;
                    }
                    .invoice-box table tr.top h1{
                        text-align:center;
                        color: #333;
                    }
                    .invoice-box table tr.information table {
                        padding: 0;
                        border: 0;
                    }
                    .invoice-box table tr.information table td:nth-child(1) {
                        border-right: 1px solid #ddd;
                    }
                    .invoice-box table td.title{
                        border-right: 1px solid #ddd;
                    }
                    .invoice-box table tr.top table {
                        padding: 0;
                        border: 0;
                    }
                    .invoice-box table tr.heading td,
                    .invoice-box table tr.heading th {
                        background: #eee;
                        border: 0;
                    }
                    .invoice-box table tr.details td {
                        padding-bottom: 20px;
                    }
                    .invoice-box table table tr.item td {
                        border: none !important;
                        background: #fff !important;
                    }
                    .invoice-box table tr.item.last td {
                        border-bottom: none;
                    }
                    .invoice-box table tr.total td:nth-child(2) {
                        border-top: 2px solid #eee;
                        font-weight: bold;
                    }
                    .invoice-box .details_table{
                        border: none;
                    }
                    .invoice-box .details_table td,
                    .invoice-box .details_table th {
                        border: 1px solid #b9b9b9 !important;
                        padding: 10px;
                    }
                    .invoice-box .details_table tbody th{
                        border-top: none !important;
                    }
                    .text_left{
                        text-align: left !important;
                    }
                    .text_right{
                        text-align: right !important;
                    }
                    .plan_item td{
                        background: #fff !important;
                    }
                    @media only screen and (max-width: 600px) {
                        .invoice-box table tr.top table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                        }
                        .invoice-box table tr.information table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                        }
                    }
                    {{#server_css}}
                </style>
            </head>
            <body>
                {{#template_content}}
            </body>
        </html>';
    }

    public function unitList($id = null){

        $list = array(
            1 => 'Bags',
            2 => 'Bale',
            3 => 'Bundles',
            4 => 'Buckles',
            5 => 'Billions of units',
            6 => 'Box',
            7 => 'Bottles',
            8 => 'Bunches',
            9 => 'Cans',
            10 => 'Cubic meter',
            11 => 'Cubic centimeter',
            12 => 'Centimeter',
            13 => 'Cartons',
            14 => 'Dozen',
            15 => 'Drum',
            16 => 'Great gross',
            17 => 'Grams',
            18 => 'Gross',
            19 => 'Gross yards',
            20 => 'Kilograms',
            21 => 'Kiloliter',
            22 => 'Kilometre',
            23 => 'Millilitre',
            24 => 'Meters',
            25 => 'Metric',
            26 => 'Numbers',
            27 => 'Packs',
            28 => 'Pieces',
            29 => 'Pairs',
            30 => 'Quintal',
            31 => 'Rolls',
            32 => 'Set',
            33 => 'Square feet',
            34 => 'Square meters',
            35 => 'Square yards',
            36 => 'Tablets',
            37 => 'Ten gross',
            38 => 'Thousands',
            39 => 'Tonnes',
            40 => 'Tubes',
            41 => 'Us gallons',
            42 => 'Unit',
            43 => 'Yards',
            44 => 'Others'
        );

        if($id){
            if(array_key_exists($id, $list)){
                return $list[$id];
            }else{
                return null;
            }
         }else{
             return $list;
         }
    }
}
