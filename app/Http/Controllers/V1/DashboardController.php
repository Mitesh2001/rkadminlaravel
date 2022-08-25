<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FollowUp;
use App\Models\FollowUpAssign;
use App\Models\Lead;
use App\Models\Contacts;
use App\Models\LeadAssign;
use App\Models\Announcement;
use App\Models\NoticeBoard;
use App\Models\Subscriptions;
use App\Models\Company;
use App\Models\AnnouncementHistory;
use App\Models\User;
use App\Models\ClientPlan;
use Validator;
use JWTAuth;
use DB;
use Helper;
use PDF;

class DashboardController extends Controller
{
    
    // get dashboard details
    public function index(Request $request)
    {
		$user = JWTAuth::parseToken()->authenticate();
        $roleName = strtolower($user->roles()->value('name'));
		
       /*  $followUpData = FollowUp::leftJoin('leads', function($join) use ($user){
			$join->on('leads.id', '=', 'follow_up.follow_up_id');
			$join->where('follow_up.type', '=' ,'1');
			$join->where('leads.company_id', '=' ,$user->company_id);
			$join->where('leads.client_id', '=' ,$user->organization_id);
		})->leftJoin('contacts', function($join) use ($user){
			$join->on('contacts.id', '=', 'follow_up.follow_up_id');
			$join->where('follow_up.type', '=' ,'2');
			$join->where('contacts.company_id', '=' ,$user->company_id);
			$join->where('contacts.client_id', '=' ,$user->organization_id);
		})->where(function($q){
			$q->orWhereNotNull('leads.id');
			$q->orWhereNotNull('contacts.id');
		})->with('getAssignToData','getCreatedBy','getRole','getFollowUpAssign.getAssignToData','getFollowUpAssign.getCreatedBy');
        
        if($followUpData === NULL){
            $followUpData = array();
        } */
		/* $followUpData = FollowUpAssign::join('follow_up', function($join) use ($user){
			$join->on('follow_up.id', '=', 'follow_up_assigns.follow_up_id');
		})->leftJoin('leads', function($join) use ($user){
			$join->on('leads.id', '=', 'follow_up.follow_up_id');
			$join->where('follow_up.type', '=' ,'1');
			$join->where('leads.company_id', '=' ,$user->company_id);
			$join->where('leads.client_id', '=' ,$user->organization_id);
		})->leftJoin('contacts', function($join) use ($user){
			$join->on('contacts.id', '=', 'follow_up.follow_up_id');
			$join->where('follow_up.type', '=' ,'2');
			$join->where('contacts.company_id', '=' ,$user->company_id);
			$join->where('contacts.client_id', '=' ,$user->organization_id);
		})->where(function($q){
			$q->orWhereNotNull('leads.id');
			$q->orWhereNotNull('contacts.id');
		})->where('follow_up_assigns.user_id', '=' ,$user->id); */
		/* $followUpData = FollowUpAssign::with('getFollowUp')->whereHas('getFollowUp', function($q){
				$q->orWhereNotNull('getFollowUp.getLead');
				$q->orWhereNotNull('getFollowUp.getContact');
			})->where('user_id', '=' ,$user->id); */
		
		
        $now_date = \Carbon::now();
        $now_date->timezone = 'Asia/Kolkata';
        $filter_date = $now_date->format("Y-m-d H:i:s");

        $leadData = Lead::with('user','industry_type','company_type','lead_status','country')->where('company_id', '=' ,$user->company_id)->where('client_id', '=' ,$user->organization_id)->get();//,'state'
        if($leadData === NULL){
            $leadData = array();
        }
		
        /* if($request->type == 1)
        {
            if($followUpData != NULL){    
                foreach($followUpData as $row){
                    $lead = Lead::find($row->follow_up_id);
                    if($lead){
                        $contactFollowUp = FollowUp::where('type',2)->where('follow_up_id',$lead->contact_id)->get();
                        if($contactFollowUp != NULL){
                            $followUpData = $followUpData->merge($contactFollowUp);
                        }                        
                    }
                }
            }
        } */
        /* if(!strpos($roleName,'admin') && !strpos($roleName,'manager')){
            if($followUpData != NULL){
                $followUpData = $followUpData->where('user_id',$user->id);
                $leadData = $leadData->map(function($q) use ($user){
                    $leadAssignRecord = LeadAssign::with(['getModel'=>function($q) {
						$q->whereNotNull('id');
					}])->where('lead_id',$q->id)->where('user_id',$user->id)->first();
                    if($leadAssignRecord)
                    {
                        $checkLeadStatus = LeadAssign::where('lead_id',$q->id)->where('lock_status',1)->exists();
                        if($checkLeadStatus)
                        {
                            if($leadAssignRecord->lock_status == 1)
                            {
                                return $q;
                            }
                        }else{
                            return $q;
                        }
                    }
                })->filter();
            }
        } */
        
        // DB::enableQueryLog();
        $announcements = Announcement::select('id','start_date_time','end_date_time','announcement')->where('start_date_time','<=',$filter_date)->Where('end_date_time','>=',$filter_date)->orderBy('id','DESC')->get();
        // dd(DB::getQueryLog());
        if($announcements === NULL){
            $announcement = array();
        }else{
            $announcement = array();
            foreach($announcements as $value){
                
                $announcementHistory = AnnouncementHistory::where('user_id',$user->id)->where('announcement_id',$value->id)->first();
    
                if($announcementHistory === NULL){
                    $is_seen = 0;
                }else{
                    $is_seen = $announcementHistory ? 1 : 0;   
                }
    
                $announcement[] = array(
                        'id' => $value->id,
                        'announcement' => $value->announcement,
                        'start_date_time' => $value->start_date_time,
                        'end_date_time' => $value->end_date_time, 
                        'is_seen' => $is_seen
                    );  
            }
        }
         
        $notice = DB::select(DB::raw("SELECT `id`, `notice`, `description` FROM `notice_boards` WHERE `user_id` = '".$user->id."' and `company_id` = '".$user->company_id."' and (`start_date_time` <= '".$filter_date."' and `end_date_time` >='".$filter_date."')"));
        
        if($notice === NULL){
            $notice = array();
        }
        // dd($notice);
        // DB::enableQueryLog();
        $companyNotice = DB::select(DB::raw("SELECT `id`, `notice`, `description` FROM `notice_boards` WHERE `company_id` = '".$user->company_id."' and `user_id` = '' and (`start_date_time` <= '".$filter_date."' and `end_date_time` >='".$filter_date."')"));
        // dd(DB::getQueryLog());
        if($companyNotice === NULL){
            $companyNotice = array();
        }
		$followUpData = FollowUpAssign::with('getFollowUp')->where('user_id', '=' ,$user->id);
        $followUpData = $followUpData->get();
        if($followUpData === NULL){
            $followUpCount = 0;
            $followUpData = array();
        }else{
            $followUpCount = count($followUpData);
        }
        
        $newSubscription = $newLead = $contactCount = $newContact = $runningSubscription = $closeSubscription = $monthlySubscriptionGrowth = $activeAdminUser = 0;

        $runningSubscriptionList = $monthWiseLead = $leadConversionRatio = $telecallingAssignRatio = $sms = $email = []; 
        $today_date = $now_date->format("Y-m-d");
        $next_day_date = $now_date->addDay()->format("Y-m-d");

        $expiredNotificationQry = DB::select(DB::raw("SELECT COUNT(id) as expired_count, `expiry_date` FROM `company` WHERE `client_id` = '".$user->organization_id."' and (`expiry_date` ='".$today_date."' or `expiry_date` ='".$next_day_date."')"));
       
        if($expiredNotificationQry[0]->expired_count > 0){
            $expired_notice = array('id' => '0', 'notice' => 'Your subscription expired on '.$expiredNotificationQry[0]->expiry_date);

            $notice = array_merge($notice,$expired_notice);
            $companyNotice = array_merge($companyNotice,$expired_notice);
        }
        
        if (strpos($roleName, 'admin') !== false) {       
            //new subscription 
            $newSubscription = Subscriptions::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->whereDate('created_at','=', date('Y-m-d'))->count();

            //running subscription 
            $runningSubscriptionQry = Subscriptions::select(['id','subscriptions_uid','payment_mode','company_id','final_amount','is_payment_pending','subscription_expiry_date'])->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->whereDate('subscription_expiry_date','>=', date('Y-m-d'))->where('is_payment_pending','NO')->get();
            
            $runningSubscriptionList = $runningSubscriptionQry;
            $runningSubscription = $runningSubscriptionQry->count();
            
            //close subscription 
            $closeSubscriptionQry = DB::select(DB::raw("SELECT COUNT(id) as closeSubscription FROM `subscriptions` WHERE `client_id` = '".$user->organization_id."' and company_id = '".$user->company_id."' and `is_payment_pending` = 'YES' and `subscription_expiry_date` <='".date("Y-m-d")."'"));
            $closeSubscription = $closeSubscriptionQry[0]->closeSubscription;

            //monthly subscription growth
            $monthlySubscriptionGrowth = Subscriptions::selectRaw('sum(final_amount) as sum')->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->whereMonth('created_at','=', date('m'))->get()[0]['sum'];

            $monthlySubscriptionGrowth = Helper::decimalNumber($monthlySubscriptionGrowth);

            $activeAdminUser = User::where('organization_id',$user->organization_id)->where('company_id',$user->company_id)->where('type',1)->count();

             //new lead 
             $newLead = Lead::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->whereDate('created_at','=', date('Y-m-d'))->count();

             //contact count
             $contactCount = Contacts::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->count();
 
             //new contact 
             $newContact = Contacts::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->whereDate('created_at','=', date('Y-m-d'))->count();
        }  

        if (strpos($roleName, 'manager') !== false) {       
            //new lead 
            $newLead = Lead::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->whereDate('created_at','=', date('Y-m-d'))->count();

            //contact count
            $contactCount = Contacts::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->count();

            //new contact 
            $newContact = Contacts::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->whereDate('created_at','=', date('Y-m-d'))->count();

            //month wise leads count
            $leads = Lead::select('created_at')
                ->where('client_id',$user->organization_id)
                ->where('company_id',$user->company_id)
                ->whereYear('created_at',date('Y'))->get()
                ->groupBy(function($date) 
                {
                    return \Carbon::parse($date->created_at)->format('m');
                });
    
            $leadsCount = [];
            foreach ($leads as $key => $value) {
                $leadsCount[(int)$key] = count($value);
            }
            
            for($i = 1; $i <= 12; $i++){
                if(!empty($leadsCount[$i])){
                    $monthWiseLead[$i] = $leadsCount[$i];    
                }else{
                    $monthWiseLead[$i] = 0;    
                }
            }

            //lead conversion ratio
            $totalLead = Lead::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->count();

            $totalConvertedLead = Lead::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->where('stage_id',5)->count();

            if($totalLead){
                // $leadConversionRatio = Helper::decimalNumber(($totalConvertedLead / $totalLead) * 100);
                $leadConversionRatio['totalLead'] = $totalLead;
                $leadConversionRatio['totalConvertedLead'] = $totalConvertedLead;
            }

            //telecalling assign ratio
            $leadIds = Lead::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->pluck('id')->toArray();

            $leadAssignTotal = LeadAssign::whereIn('lead_id',$leadIds)->count();

            if($leadAssignTotal){
                // $telecallingAssignRatio = Helper::decimalNumber(($leadAssignTotal / $totalLead) * 100);
                $telecallingAssignRatio['totalLead'] = $totalLead;
                $telecallingAssignRatio['leadAssignTotal'] = $leadAssignTotal;
            }

        }

        if (strpos($roleName, 'manager') !== true || strpos($roleName, 'admin') !== true) { 

            //new lead 
            $newLead = Lead::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->where('created_by',$user->id)->whereDate('created_at','=', date('Y-m-d'))->count();

            //contact count
            $contactCount = Contacts::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->where('created_by',$user->id)->count();

            //new contact 
            $newContact = Contacts::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->whereDate('created_at','=', date('Y-m-d'))->where('created_by',$user->id)->count();

        }

        $followUpType = config('module_name.follow_type');
        $followUpData = $followUpData->map(function($q) use($followUpType){
            $q->follow_up_type = !empty($followUpType[$q->follow_up_type]) ? $followUpType[$q->follow_up_type] : null;
            $q->name = $this->getLeadContactName($q->follow_up_id,$q->follow_up_type);
            return $q;
        });

        $companyData = Company::find($user->company_id);

        $sms['total'] = $companyData->total_sms;
        $sms['balance'] = $companyData->used_sms;
        $email['total'] = $companyData->total_email;
        $email['balance'] = $companyData->used_email;
        
        if (strpos($roleName, 'admin') !== false){
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('followUpData','followUpCount','leadData','announcement','notice','companyNotice','newSubscription','newLead', 'newContact', 'contactCount', 'runningSubscription','runningSubscriptionList','closeSubscription','monthWiseLead','monthlySubscriptionGrowth','activeAdminUser','sms','email')
            ]); 
        }else if(strpos($roleName, 'manager') !== false){
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('followUpData','followUpCount','leadData','announcement','notice','companyNotice','newLead', 'newContact', 'contactCount','monthWiseLead','leadConversionRatio','telecallingAssignRatio','sms','email')
            ]);
        }else{
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('followUpData','followUpCount','leadData','announcement','notice','companyNotice','newLead', 'newContact', 'contactCount','sms','email')
            ]);
        }

        /*return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('followUpData','followUpCount','leadData','announcement','notice','companyNotice','newSubscription','newLead', 'contactCount', 'runningSubscription','closeSubscription','monthWiseLead','monthlySubscriptionGrowth','leadConversionRatio','telecallingAssignRatio','activeAdminUser','sms','email')
        ]);*/ 
    }

    public function getLeadContactName($id,$type)
    {
        if($type == 1){
            $data = Lead::find($id);
            $name = $data ? $data->lead_name : null;
        }else{
            $data = Contacts::find($id);
            $name = $data ? $data->name : null;
        }
        return $name;
    }
    
    public function announcementHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'announcement_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $announcementHistory = new AnnouncementHistory();
        $announcementHistory->announcement_id = $request->announcement_id;
        $announcementHistory->user_id = $request->user_id;
        $announcementHistory->save();
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Announcement has been seen successfully.'
        ]);
    }

    public function invoiceDownload($id)
    {
        $subscription = Subscriptions::with(['company','client'])->find($id);
        $clientplans = ClientPlan::where('subscription_id','=',$id)->with(['plan'])->get();

        $user = JWTAuth::parseToken()->authenticate();

        if(isset($user)){
            if(isset($user->invoice_template)){
                $template = $user->invoice_template;
            }else{
                $template = Helper::defaultInvoiceTemplate();  
            }
        }else{
            $template = Helper::defaultInvoiceTemplate();    
        }
        $template = trim(preg_replace('/\s\s+/', '', $template));
        $template_content = Helper::invoiceTemplateBody();

        $template = str_replace("<p></p>","",$template);
        $template = str_replace("<p>&nbsp;</p>","",$template);    
        $invoice_template = str_replace("{{#template_content}}", $template, $template_content);

        $dom = new \DOMDocument();
        $dom->loadHTML($invoice_template); 
        $xpath = new \DOMXPath($dom); 

        if($subscription->state_name == "Gujarat") {
            $text_arr = ['{{#igst}}', '{{#igst_amount}}'];
        } else {
            $text_arr = ['{{#sgst}}', '{{#sgst_amount}}', '{{#cgst}}', '{{#cgst_amount}}'];
        } 
 
        if($subscription->payment_mode == "CASH") { 
            array_push($text_arr, '{{#bank_name}}', '{{#transaction_no}}');
        } 

        foreach ($text_arr as $text) {
            foreach ($xpath->query("(//*[text()[contains(., '$text')]])[1]/parent::tr") as $row) {  
                $row->parentNode->removeChild($row);
            } 
        } 
        $invoice_template = $dom->saveHTML();

        $plan_list = '';
        foreach ($clientplans as $cplan){
            $plan_list .='<tr class="plan_item">';
            $plan_list .='<td class="text_left">'.$cplan->plan->name. '<br/>Email :' .$cplan->no_of_email .' SMS : '.$cplan->no_of_sms.'</td>';
            $plan_list .='<td class="text_right" width="15%">'.$cplan->subscription_date.'</td>';
            $plan_list .='<td class="text_right" width="5%">'.Helper::decimalNumber($cplan->plan_price).'</td>';
            $plan_list .='<td class="text_right" width="10%">'.Helper::decimalNumber($cplan->discount).'</td>';
            $plan_list .='<td class="text_right" width="15%">'.Helper::decimalNumber($cplan->discount_amount).'</td>';
            $plan_list .='<td class="text_right" width="20%">'.Helper::decimalNumber($cplan->final_amount).'</td>';
            $plan_list .='</tr>';
        }

        $server_css = "#plan_list{display:none}";

        $image_logo = '';
        if($subscription->company->company_logo!=''){
            
            $logo = asset('storage/images/'.$subscription->company->company_logo);

            $image_logo = '<img class="company_logo" height="50" src="'.$logo.'" title="'.$subscription->company->company_name.'">';
        }
        $variables = array(
            "{{#company_name_and_logo}}" => $image_logo . $subscription->company->company_name,
            "{{#company_address}}" => $subscription->client->address_line_1.','.$subscription->client->address_line_2.'-'.$subscription->client->city,
            "{{#invoice_no}}" => $subscription->subscriptions_uid,
            "{{#client_name}}" => $subscription->client->name,
            "{{#client_email}}" => $subscription->client->email,
            "{{#created_date}}" => $subscription->created_at->format('Y-m-d'),
            "{{#gst_no}}" => $subscription->company->gst_no,
            "{{#total_amount}}" => Helper::decimalNumber($subscription->total_amount),
            "{{#sgst_amount}}" => Helper::decimalNumber($subscription->sgst_amount),
            "{{#sgst}}" => Helper::decimalNumber($subscription->sgst),
            "{{#cgst_amount}}" => Helper::decimalNumber($subscription->cgst_amount),
            "{{#cgst}}" => Helper::decimalNumber($subscription->cgst),
            "{{#igst_amount}}" => Helper::decimalNumber($subscription->igst_amount),
            "{{#igst}}" => Helper::decimalNumber($subscription->igst),
            "{{#final_amount}}" => Helper::decimalNumber($subscription->final_amount),
            "{{#round_off_amt}}" => Helper::decimalNumber($subscription->round_off_amount),
            "{{#yes_no}}" => $subscription->is_payment_pending,
            "{{#payment_mode}}" => $subscription->payment_mode,
            "{{#payment_date}}" => $subscription->payment_date,
            "{{#bank_name}}" => $subscription->payment_bank_name,
            "{{#transaction_no}}" => $subscription->payment_number,
            "{{#server_css}}" => $server_css,
            '<tfoot></tfoot>' => $plan_list
        );
    
        foreach ($variables as $key => $value)
            $invoice_template = str_replace($key, $value, $invoice_template);
        
        $pdfName = date('Y_m_d_H_i_s_'.$id).'.pdf';

        $pdf = PDF::loadHTML($invoice_template);

        $file = $pdf->setPaper('landscape')->setWarnings(false)->download($pdfName);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => base64_encode($file)
        ]);
    }
}
