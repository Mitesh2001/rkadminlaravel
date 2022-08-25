<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Models\Subscriptions;
use App\Models\Client;
use App\Models\Company;
use App\Models\ClientPlan;
use App\Models\Plan;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Models\Commission;
use App\Models\EmailSmsLog;
use Validator;
use Exception;
use Helper;
use Mail;
use PDF;
use DB;
use URL;
use Auth;

class SubscriptionsController extends Controller
{
    public function __construct(){      
        $this->middleware(function ($request, $next) { 
            if(auth()->user()->type == 3 || auth()->user()->type == 4)
            {   
                $getRequestUri = $request->getRequestUri();
                $getRequestUri = explode('/',$getRequestUri);
                if(isset($getRequestUri[3]) and $getRequestUri[3] == 'all'){
                    return redirect('/rkadmin')->with('success','You are not authorized to access that page.');
                }
            }
            return $next($request);
        });
    }
    public function currentUser(){
        return Auth::user();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $cId = decrypt($request->company_id);
            $companyData = Helper::getCompany($cId);
            $payment_modes = config('global.payment_modes');
            $selected = array('Please select payment mode');
            $payment_modes = array_merge($selected,$payment_modes);
            return view('admin.subscriptions.index',compact('cId','companyData','payment_modes'));
        }catch(Exception $e)
        {
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        try{
            $payment_modes = config('global.payment_modes');
            $selected = array(''=>'Please Select');
            $payment_modes = array_merge($selected,$payment_modes);
			$new = ($request->new)?$request->new:0;
			$running = ($request->running)?$request->running:0;
			$closed = ($request->closed)?$request->closed:0;
            return view('admin.subscriptions.all',compact('payment_modes','new','running','closed'));
        }catch(Exception $e)
        {
            abort(404);
        }
    }
	
	/**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData(Request $request)
    {
        $subscriptions = Subscriptions::select(['id','subscriptions_uid','payment_mode','company_id','final_amount','is_payment_pending','subscription_expiry_date','created_at'])->where('company_id',$request->company_id);

        if(isset($request->payment_pending) and $request->payment_pending!=0){
            if($request->payment_pending == '1'){
                $subscriptions->where('is_payment_pending','YES');
            }else if($request->payment_pending == '2'){
                $subscriptions->where('is_payment_pending','No');
            }
        }
		
		if(isset($request->expired_subscription) and $request->expired_subscription!=0){
			$date = date("Y-m-d");
            if($request->expired_subscription == '1'){
				 $subscriptions->where(function($q) use ($date){
					$q->where('subscription_expiry_date','<',$date)->orWhereNull('subscription_expiry_date');
				});                
            }else if($request->expired_subscription == '2'){
                $subscriptions->where('subscription_expiry_date','<',$date);
            }
        }

        if(isset($request->payment_mode) and $request->payment_mode!='0'){
            $subscriptions->where('payment_mode',$request->payment_mode);
        }

        $subscriptions = $subscriptions->with(['company','client'])->orderBy('id', 'desc')->get();

        return Datatables::of($subscriptions)
            ->addColumn('final_amount', function ($subscriptions) {
                return Helper::decimalNumber($subscriptions->final_amount);
            })				
			->addColumn('created', function ($subscriptions) {
                return date("d-m-Y",strtotime($subscriptions->created_at));
            })
            ->addColumn('expiry_date', function ($subscriptions) {
                if($subscriptions->subscription_expiry_date!=NULL){
                    return date("d-m-Y",strtotime($subscriptions->subscription_expiry_date));
                }else{
                    return '';
                }
            })
            ->addColumn('action', function ($subscriptions) {
                $html = $edit_btn = $cancel_btn = '';

                $html .= '<a href="'.route('admin.subscriptions.show', encrypt($subscriptions->id)).'" class="btn btn-link" data-toggle="tooltip" title="View"><i class="flaticon-eye text-success"></i></a>';
                
                if($subscriptions->is_payment_pending == "YES"){

                    $edit_btn = '<a href="'.route('admin.subscriptions.edit', encrypt($subscriptions->id)).'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>';

                    $html .= $edit_btn;
                
                    $cancel_btn = '<a href="javascript:;" data-id="'.encrypt($subscriptions->id).'"  class="btn btn-link cancel_subscription" data-toggle="tooltip" title="Cancel Subscription"><i class="flaticon2-cancel text-danger"></i></a>';
                    
                    $html .= $cancel_btn;
                }

                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function allData(Request $request)
    {
        //  \DB::enableQueryLog(); 
        $subscriptions = Subscriptions::select(['id','subscriptions_uid','payment_mode','client_id', 'company_id','final_amount','is_payment_pending','subscription_expiry_date','created_at']);
        
        if(isset($request->payment_mode) && $request->payment_mode!='0'){
            $subscriptions->where('payment_mode',$request->payment_mode);
        }
		
		$date = date("Y-m-d");
		if(isset($request->expired_subscription) && $request->expired_subscription!=0){
            if($request->expired_subscription == '1'){
				$subscriptions->where('subscription_expiry_date','<',$date);
				/* $subscriptions->where(function($q) use ($date){
					$q->where('subscription_expiry_date','<',$date)->orWhereNull('subscription_expiry_date');
				}); */                
            }else if($request->expired_subscription == '2'){
                $subscriptions->where('subscription_expiry_date','>=',$date);
            }
        }
		if(isset($request->newsubscription) && $request->newsubscription == 1){
			$subscriptions->where('payment_date','=',$date);
		}

        if(isset($request->payment_pending) && $request->payment_pending!=0){
            if($request->payment_pending == '1'){
                $subscriptions->where('is_payment_pending','YES');
            }else if($request->payment_pending == '2'){
                $subscriptions->where('is_payment_pending','No');
            }
        }

        if($this->currentUser()->type == 3 ||$this->currentUser()->type == 4){
			$subscriptions->where('created_by',$this->currentUser()->id);
			//$subscriptions->where('client_id',$this->currentUser()->organization_id);
			//$subscriptions->where('company_id',$this->currentUser()->company_id);
        }
        
        $subscriptions = $subscriptions->with(['company','client'])->orderBy('id', 'desc')->get();
        //dd(\DB::getQueryLog());
        return Datatables::of($subscriptions)
            ->addColumn('subscriptions_uid', function ($subscriptions) {
                return $subscriptions->subscriptions_uid;
            })
            ->addColumn('company_name', function ($subscriptions) {
                return $subscriptions->company ? $subscriptions->company->company_name : '';
            })
            ->addColumn('client_name', function ($subscriptions) {
                return ($subscriptions->client ? $subscriptions->client->name : '');
            })	    
            ->addColumn('final_amount', function ($subscriptions) {
                return Helper::decimalNumber($subscriptions->final_amount);
            })				
			->addColumn('created', function ($subscriptions) {
                return date("d-m-Y",strtotime($subscriptions->created_at));
            })
            ->addColumn('expiry_date', function ($subscriptions) {
                if($subscriptions->subscription_expiry_date!=NULL){
                    return date("d-m-Y",strtotime($subscriptions->subscription_expiry_date));
                }else{
                    return '';
                }
            })
            ->rawColumns(['action','subscriptions_uid'])
            ->make(true);
    }
	
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try{
            $companyId = decrypt($request->company_id);

            $plan = Plan::pluck('name', 'id');
            $company = Company::pluck('company_name', 'id');
            $companyData = Helper::getCompany($companyId);
            $company->prepend('Please Select Company', '');
            
            $plan->prepend('Please Select Plan', '');
            $payment_modes = config('global.payment_modes');
            $payment_modes = array_merge(array(''=>'Select Payment Mode'),$payment_modes);
            return view('admin.subscriptions.subscriptions')->withCompany($company)->with(['payment_modes'=>$payment_modes])->withPlan($plan)->withCompanyId($companyId)->withCompanyData($companyData);
        }catch(Exception $e){
            abort(404);
        }
    }
	
	 /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $validation = Validator::make($request->all(), [
            'company_id' => 'required|numeric',
            'plans' => 'required|array|min:1',
            'payment_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:today',
            'payment_bank_name' => 'nullable|max:40|regex:/^[A-Za-z ]+$/u',
            'payment_number' => 'nullable|max:16|regex:/^[0-9]+$/u'
        ],
        [
            'plans.required'=>'The plan field is required.'
        ]);
		
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
       
        $subscription = new Subscriptions();

        $subscriptions_uid_count = Subscriptions::withTrashed()->count();
        $id_length = strlen($subscriptions_uid_count);
        $subscriptions_uid = '';

        /* if($id_length==0){
            $subscriptions_uid = '000001';
        }elseif($id_length==1){
            $subscriptions_uid = '00000'.$subscriptions_uid_count;
        }elseif($id_length==2){
            $subscriptions_uid = '0000'.$subscriptions_uid_count;
        }elseif($id_length==3){
            $subscriptions_uid = '000'.$subscriptions_uid_count;
        }elseif($id_length==4){
            $subscriptions_uid = '00'.$subscriptions_uid_count;
        }elseif($id_length==5){
            $subscriptions_uid = '0'.$subscriptions_uid_count;
        }else{
            $subscriptions_uid = $subscriptions_uid_count;
        } */
		$subscriptions_uid_count+=1;
		$subscriptions_uid = str_pad($subscriptions_uid_count,6,"0",STR_PAD_LEFT);
        
        $message = 'Plan has been added successfully';
        if($request->subscription_id)
        {
            $subscriptionId = decrypt($request->subscription_id);
            $subscription = Subscriptions::find($subscriptionId);
            $message = 'Plan has been updated successfully';
        }
        
        $plans = $request->plans;
        
        $plans_discount = $request->plans_discount;
        $subscription_date = $request->subscription_date;
		$allPlans = Plan::whereIn('id',$plans)->get();
		$total_amount = $totaluser = $totalmonth = $no_of_sms = $no_of_email = 0;
		foreach($allPlans as $plan){
			$amount = $plan->price;
			$discount = $plans_discount[$plan->id];
			$discount = Helper::decimalNumber($discount);
			$discount_amount = $amount * $discount / 100;
			$discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount;
			$netamount = Helper::decimalNumber($netamount);
			$total_amount += $netamount;
			$totaluser += $plan->no_of_users;
			$totalmonth += $plan->duration_months;
            $no_of_sms += $plan->no_of_sms;
            $no_of_email += $plan->no_of_email;
        }
       
        $companyData = Company::find($request->company_id);
        
		$sgst = Helper::decimalNumber($request->sgst);
		$sgst_amount = Helper::decimalNumber($request->sgst_amount);
		$cgst = Helper::decimalNumber($request->cgst);
		$cgst_amount = Helper::decimalNumber($request->cgst_amount);
		$igst = Helper::decimalNumber($request->igst);
		$igst_amount = Helper::decimalNumber($request->igst_amount);
        
        if($companyData->state_id == 'Gujarat'){
            $final_amount = $total_amount + $sgst_amount + $cgst_amount;
        }else{
            $final_amount = $total_amount + $igst_amount;
        }
		
        if($companyData){
            $subscription->company_id = $companyData->id;
            $subscription->client_id = $companyData->client_id;
			
			if($companyData->expiry_date!= NULL && date("Y-m-d",strtotime($companyData->expiry_date)) > date("Y-m-d"))
			{
				$startdate = date("Y-m-d",strtotime($companyData->expiry_date));
			}else{
                $startdate = date("Y-m-d");
			}
            if($totalmonth>0){
			    $expiry_date = date('Y-m-d', strtotime("+".$totalmonth." months", strtotime($startdate)));

                $subscription_expiry_date = date('Y-m-d', strtotime("+".$totalmonth." months", strtotime(date("Y-m-d"))));
                
			    $companyData->expiry_date = $expiry_date;
			}
            
			$companyData->no_of_users   = $companyData->no_of_users + $totaluser;
            $companyData->total_sms     = $companyData->total_sms + $no_of_sms;
            $companyData->total_email   = $companyData->total_email + $no_of_email;
            $companyData->used_sms      = $companyData->used_sms + $no_of_sms;
            $companyData->used_email    = $companyData->used_email + $no_of_email;
			$companyData->save();
        }
        
        $subscription->total_amount = $total_amount;
		$subscription->sgst = $sgst;
		$subscription->sgst_amount = $sgst_amount;
		$subscription->cgst = $cgst;
		$subscription->cgst_amount = $cgst_amount;
		$subscription->igst = $igst;
		$subscription->igst_amount = $igst_amount;
        $subscription->final_amount = $final_amount;
        $subscription->payment_mode = $request->payment_mode;
        $subscription->payment_bank_name = $request->payment_bank_name;
        $subscription->payment_number = $request->payment_number;
        $subscription->payment_amount = $final_amount;
        $subscription->state_name = $companyData->state_id; // State id was assigned as string so attaching the same
        $subscription->payment_date = $request->payment_date;
		if(isset($subscription_expiry_date))
        $subscription->subscription_expiry_date = $subscription_expiry_date;
        $subscription->is_payment_pending = $request->payment_status;
        $subscription->round_off_amount = round($final_amount);
        $subscription->subscriptions_uid = $subscriptions_uid;
        $subscription->created_by = auth()->guard('admin')->user()->id;
        $subscription->save();

		foreach($allPlans as $plan){
			$clientPlan = new ClientPlan();
            if($companyData){
				$clientPlan->company_id = $companyData->id;
				$clientPlan->client_id = $companyData->client_id;
			}
			$clientPlan->subscription_id = $subscription->id;
			$clientPlan->plan_id = $plan->id;
			$amount = $plan->price;
			$amount = Helper::decimalNumber($amount);
			$discount = $plans_discount[$plan->id];
			$discount = Helper::decimalNumber($discount);
			$discount_amount = $amount * $discount / 100;
			$discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount;
			$netamount = Helper::decimalNumber($netamount);
			
			$clientPlan->plan_price = $amount;
			$clientPlan->discount = $discount;
			$clientPlan->discount_amount = $discount_amount;
            $clientPlan->final_amount = $netamount;
            $clientPlan->no_of_sms = $plan->no_of_sms;
            $clientPlan->no_of_email = $plan->no_of_email;
            $clientPlan->subscription_date = $subscription_date[$plan->id];
			$clientPlan->save();
		}

        $commission_per = $this->currentUser()->commission;

        if($commission_per > 0 && $request->payment_status == 'NO'){

            $commission_amt     = ($total_amount * $commission_per) / 100;
            $subscription_id    = $subscription->id;
            $company_id         = $companyData->id;
            $client_id          = $companyData->client_id;
            $dealer_distributor = auth()->guard('admin')->user()->id;

            $commission = new Commission();
            $commission->commission_amt = $commission_amt;
            $commission->subscription_id = $subscription_id;
            $commission->company_id = $company_id;
            $commission->client_id = $client_id;
            $commission->dealer_distributor = $dealer_distributor;
            $commission->is_payment_pending = 'YES';
            $commission->save();
        }

        return redirect('rkadmin/subscriptions?company_id='.encrypt($request->company_id))->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        $sId = decrypt($id);
        
        $subscription = Subscriptions::with(['company','client'])->find($sId);
        $clientplans = ClientPlan::where('subscription_id','=',$sId)->with(['plan'])->get();
        
        $clientEmail = !empty($request->email) ? $request->email : null;
        $company = Company::pluck('company_name', 'id');
        $company->prepend('Please Select Company', '');
        $plan = Plan::pluck('name', 'id');
        $plan->prepend('Please Select Plan', '');
        $payment_modes = config('global.payment_modes');
        $companyId = $subscription->company_id;
       
        $user = Auth::user();
        
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

        // dd($invoice_template);

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

        // dd($invoice_template);
               
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
            // "{{#round_off_amt}}" => Helper::decimalNumber($subscription->round_off_amount),
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
        

            // $dom = new \DOMDocument();
            // @$dom->loadHTML($invoice_template);
            // $xpath = new \DOMXPath($dom);

            // // loop all <tr> element.
            // foreach ($xpath->query('//tr') as $tr) {
            //     $tds = $tr->getElementsByTagName('td');
                
            //     // get total <td> in this <tr>
            //     $total_item = $tds->length;
            
            //     // loop to all <td> and check.
            //     for ($i = 0; $i <= ($total_item-1); $i++) {
            //         // get table cell value.
            //         $table_cell_value = $tds->item($i)->nodeValue;
            //         if($tds->item($i)->getElementsByTagName('td')->length > 0){
            //         //    print_r($tds->item($i)->getElementsByTagName('td'));
            //             $j = 0;
            //             foreach($tds->item($j)->getElementsByTagName('td') as $innerTd){
            //                 // print_r($innerTd->getElementsByTagName('td')->nodeValue);
            //                 $j++;
            //             }
            //         }
            //         // preg_match('/\[\[charge_(\d+)\]\]/', $table_cell_value, $charge_id);
            
            //         // check if condition is met, the charge_id is 13.
            //         // if (is_array($charge_id) && array_key_exists(1, $charge_id) && $charge_id[1] == 13) {
            //         // echo "----".$table_cell_value;
            //         echo "<br/>";
                    
            //         // if (strpos($table_cell_value, '{{#round_off_amt}}') !== false) {
            //         //     $tr->parentNode->removeChild($tr);
            //         // }

            //         // if ($table_cell_value == '{{#round_off_amt}}') { 
            //         //     // condition met. charge_id is 13. remove this table row.
            //         //     $tr->parentNode->removeChild($tr);
            //         // }
            //         unset($charge_id, $table_cell_value);
            //     }
            
            //     unset($tds, $total_item);
            // }
            
            // // get the result
            // $saved_html = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $dom->saveHTML());
            // echo $saved_html;
        // die;    
        $pdfName = date('Y_m_d_H_i_s_'.$sId).'.pdf';
        
        if($request->is_pdf || $request->is_email){
            $data['subscription'] = $subscription;
            $data['company'] = $company;
            $data['plan'] = $plan;
            $data['clientplans'] = $clientplans;
            $data['companyId'] = $companyId;
            $data['payment_modes'] = $payment_modes;

            $pdf = PDF::loadHTML($invoice_template);
        
            // return $pdf->setPaper('landscape')->setWarnings(false)->stream();

            if($request->is_pdf){
                $file_name = 'storage/invoice_file/'.$subscription->created_by.'/'.date('Y').'/'.date('m').'/'.$pdfName;
                \Storage::disk('public')->put($file_name, $pdf->output());
                return $pdf->setPaper('landscape')->setWarnings(false)->download($pdfName);
            }
            if($request->is_email){
                if($clientEmail){
                    if($user){
                        $message = 'Invoice sent successfully.';
                        $emails = explode(',', $clientEmail);
                        $emails =  array_slice($emails, 0, 3);
						$emails = array_map('trim', $emails);

                        $getInvoiceTemplateSend = EmailTemplate::select(['email_template_id', 'subject', 'content'])->where('name','Invoice email send')->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->where('default_template','0')->get();
                        
                        if(count($getInvoiceTemplateSend)){
                            $message_content = $getInvoiceTemplateSend[0]['content'];
                            $subject = $getInvoiceTemplateSend[0]['subject'];
                        }else{
                            $getInvoiceTemplateSend = EmailTemplate::select(['email_template_id', 'subject', 'content'])->where('name','Invoice email send')->where('client_id',0)->where('company_id',0)->where('default_template','1')->get();
                            $message_content = $getInvoiceTemplateSend[0]['content'];
                            $subject = $getInvoiceTemplateSend[0]['subject'];  
                        }

                        $company_details = Company::find($user->company_id);

                        $details = ($company_details != null) ? $company_details->company_name : '';
                        
                        if(isset($company_details)){
                            
                            if($company_details->address_line_1){
                                $details .= ', '.$company_details->address_line_1;
                            }
                            if($company_details->address_line_2){
                                $details .= ', '.$company_details->address_line_2;
                            } 
                        }

                        $email_variable = array(
                            '{{#client_name}}' => $subscription->client->name,
                            '{{#user_name}}' =>  $user->name,
                            '{{#company_details}}' =>  $details,
                            '{{#copy_right}}' =>  ($company_details != null) ? $company_details->company_name : '' .' @ ' .date('Y-m-d'),
                        );
                        $companyData = Company::find($companyId);
                        
                        foreach ($email_variable as $key => $value)
                            $message_content = str_replace($key, $value, $message_content);

                            $emailTemplateBody =  Helper::emailTemplateBody();

                            $emailTemplateBody =  str_replace("{{#template_body}}", $message_content, $emailTemplateBody);
            
                            $message_content = str_replace("{{#all_css}}", Helper::emailTemplateCss(), $emailTemplateBody); 

                            $companyData = Company::find($companyId);
                          
                            $data['subject'] = $subject;
                            $data['messagecontent'] = $message_content;
                            $data['from_email'] = $companyData->from_email;
                            $data['from_name'] =  $companyData->from_name;

                            if($companyData->used_email >=1){

                                if($companyData->email_service == 0){

                                    $used_email = ($companyData->used_email > 1) ? $companyData->used_email - 1 : 0;

                                    if(is_array($emails)){
										foreach($emails as $semail){
									$log = new EmailSmsLog;
                                    $log->user_id = $user->id;
                                    $log->client_id = $subscription->client_id;
                                    $log->company_id = $subscription->company_id;
                                    $log->client_number = $subscription->client->mobile_no;
                                    $log->client_email = $semail;//$subscription->client->email;
                                    $log->template_id = $getInvoiceTemplateSend[0]['email_template_id'];
                                    $log->type = 'EMAIL';
                                    $log->response = '1';
                                    $log->msg = $message_content;
                                    $log->save();
										}
									}

                                    Mail::send('emails.email', $data, function($message)use($data, $pdf,$pdfName,$emails) {
                                        $message->subject($data['subject']);
                                        
                                        if(isset($data['from_email']) && isset($data['from_name'])){
                                            $message->from($data['from_email'], $data['from_name']);
                                        }
                                        $message->to($emails)->attachData($pdf->output(), $pdfName);
                                    });
                                    $companyData->used_email = $used_email;
                                    $companyData->save(); 

                                }else{
                                    $message = 'Please enable your email service.';
                                }
                               
                            }else{
                                $message = 'Your email balance is 0. Please recharge and send again.';
                            }
                    }
                }
                return back()->with('success', $message);
            }
        }
        return view('admin.subscriptions.show',compact('subscription','company','plan','clientplans','companyId'))->with(['payment_modes'=>$payment_modes]);
       
    }

    /**
     * Show the form for editing the specified plan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $sId = decrypt($id);
            $subscription = Subscriptions::with(['company','client'])->find($sId);
            $clientplans = ClientPlan::where('subscription_id','=',$sId)->with(['plan'])->get();
            $company = Company::pluck('company_name', 'id');
			$company->prepend('Please Select Company', '');
			$plan = Plan::pluck('name', 'id');
            $plan->prepend('Please Select Plan', '');
            $companyData = Helper::getCompany($subscription->company_id);
            $payment_modes = config('global.payment_modes');
            $payment_modes = array_merge(array(''=>'Select Payment Mode'),$payment_modes);
            return view('admin.subscriptions.subscriptions',compact('company','plan','clientplans','subscription'))->with(['payment_modes'=>$payment_modes])->withCompanyId($subscription->company_id)->withCompanyData($companyData);
        }catch(Exception $e)
        {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'company_id' => 'required|numeric',
            'payment_date' => 'nullable|date',
            'payment_bank_name' => 'nullable|max:40|regex:/^[A-Za-z ]+$/u',
            'payment_number' => 'nullable|max:16|regex:/^[0-9]+$/u'
        ]);
		
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }

        $subscription = new Subscriptions();
        $message = 'Plan has been added successfully';
        if($request->subscription_id)
        {
            $subscriptionId = decrypt($request->subscription_id);
            $subscription = Subscriptions::find($subscriptionId);
            $message = 'Plan has been updated successfully';
        }
        
        if(isset($request->plans_add_id) && isset($request->plans_update))
        {
            $plans = array_merge($request->plans_add_id, $request->plans_update);
            $plans_discount = $request->plans_discount + $request->plans_add_discount;
        }else if(isset($request->plans_add_id)){
            $plans = $request->plans_add_id;
            $plans_discount = $request->plans_add_discount;
        }else if(isset($request->plans_update)){
            $plans = $request->plans_update;
            $plans_discount = $request->plans_discount;
        }
       
        $plans_update_data = $request->plans_update_data;
        $new_plans_add = $request->plans_add_id;
        $plans_add_price = $request->plans_add_price;

        $subscription_add_date = $request->subscription_add_date;
        $plans_add_discount = $request->plans_add_discount;
        $subscription_date = $request->subscription_date;
        $plans_price = $request->plans_price;

        $allPlans = Plan::whereIn('id',$plans)->get();
        $total_amount = $totaluser = $totalmonth = $no_of_sms = $no_of_email = $used_sms = $used_email = 0;
        
        $companyData = Company::find($request->company_id);
        
        //for update company data
		foreach($plans as $plan_id){
            $plan = Plan::find($plan_id);
            $amount = $plan->price;
            $discount = $plans_discount[$plan_id];
            $discount = Helper::decimalNumber($discount);
			$discount_amount = $amount * $discount / 100;
            $discount_amount = Helper::decimalNumber($discount_amount);
			$netamount = $amount - $discount_amount;
			$netamount = Helper::decimalNumber($netamount);
			$total_amount += $netamount;
			$totaluser += $plan->no_of_users;
			// $totalmonth += $plan->duration_months;

            $no_of_sms += $plan->no_of_sms;
            $no_of_email += $plan->no_of_email;
        }

        //for add new plan update date
        if(isset($request->plans_add_id)){
            foreach($new_plans_add as $plan){
                $plan_get = Plan::find($plan);;
                $totalmonth += $plan_get->duration_months;
            }
        }
        
        $sgst = Helper::decimalNumber($request->sgst);
		$sgst_amount = Helper::decimalNumber($request->sgst_amount);
		$cgst = Helper::decimalNumber($request->cgst);
		$cgst_amount = Helper::decimalNumber($request->cgst_amount);
		$igst = Helper::decimalNumber($request->igst);
		$igst_amount = Helper::decimalNumber($request->igst_amount);

        if($companyData->state_id == 'Gujarat'){
            $final_amount = $total_amount + $sgst_amount + $cgst_amount;
        }else{
            $final_amount = $total_amount + $igst_amount;
        }
        if($companyData){
            $subscription->company_id = $companyData->id;
            $subscription->client_id = $companyData->client_id;
			
			if($companyData->expiry_date!= NULL && date("Y-m-d",strtotime($companyData->expiry_date)) > date("Y-m-d"))
			{
				$startdate = date("Y-m-d",strtotime($companyData->expiry_date));
			}else{
				$startdate = date("Y-m-d");
			}

			if($totalmonth>0){

                $expiry_date_check = date('Y-m-d', strtotime("+".$totalmonth." months", strtotime(date("Y-m-d"))));

                $subscription_expiry_date = date('Y-m-d', strtotime("+".$totalmonth." months", strtotime($subscription->subscription_expiry_date)));

                if($startdate > $expiry_date_check){
                    $expiry_date = date('Y-m-d', strtotime("+".$totalmonth." months", strtotime($startdate)));
                }else{
                    $expiry_date = $expiry_date_check;
                }
			    $companyData->expiry_date = $expiry_date;
			}
            $companyData->no_of_users = $totaluser;
            $companyData->total_sms = $no_of_sms;
            $companyData->total_email = $no_of_email;
			$companyData->save();
        }

        $subscription->total_amount = $total_amount;
		$subscription->sgst = $sgst;
		$subscription->sgst_amount = $sgst_amount;
		$subscription->cgst = $cgst;
		$subscription->cgst_amount = $cgst_amount;
		$subscription->igst = $igst;
		$subscription->igst_amount = $igst_amount;
        $subscription->final_amount = $final_amount;
        $subscription->payment_mode = $request->payment_mode;
        $subscription->payment_bank_name = $request->payment_bank_name;
        $subscription->payment_number = $request->payment_number;
        $subscription->payment_amount = $final_amount;
        $subscription->payment_date = $request->payment_date;
        
        if(isset($request->plans_add_id)){
            $subscription->subscription_expiry_date = $subscription_expiry_date;
        }
        
        $subscription->is_payment_pending = $request->payment_status;
        $subscription->round_off_amount = round($final_amount);
        $subscription->created_by = auth()->guard('admin')->user()->id;
        $subscription->save();
        
        //update plan data
        if(isset($request->plans_update_data)){
            foreach($plans_update_data as $plan){
            
                $clientPlan = ClientPlan::find($plan);
                $plan = Plan::find($clientPlan->plan_id);
               
                if($companyData){
                    $clientPlan->company_id = $companyData->id;
                    $clientPlan->client_id = $companyData->client_id;
                }
               
                $clientPlan->subscription_id = $subscription->id;
                $amount = $plans_price[$plan->id];
                $amount = Helper::decimalNumber($amount);
                $discount = $plans_discount[$plan->id];
                $discount = Helper::decimalNumber($discount);
                $discount_amount = $amount * $discount / 100;
                $discount_amount = Helper::decimalNumber($discount_amount);
                $netamount = $amount - $discount_amount;
                $netamount = Helper::decimalNumber($netamount);
                
                $clientPlan->plan_price = $amount;
                $clientPlan->discount = $discount;
                $clientPlan->discount_amount = $discount_amount;
                $clientPlan->final_amount = $netamount;
                $clientPlan->no_of_sms = $plan->no_of_sms;
                $clientPlan->no_of_email = $plan->no_of_email;
                $clientPlan->subscription_date = $subscription_date[$plan->id];
                $clientPlan->save();
            }
        }
       
        //for add new plan
        if(isset($request->plans_add_id)){
            
            foreach($new_plans_add as $plan){
                $clientPlan = new ClientPlan();
                $plan_data = Plan::find($plan);
                if($companyData){
                    $clientPlan->company_id = $companyData->id;
                    $clientPlan->client_id = $companyData->client_id;
                }
                $clientPlan->subscription_id = $subscription->id;
                $clientPlan->plan_id = $plan;
                $amount =  $plans_add_price[$plan];
                
                $amount = Helper::decimalNumber($amount);
                $discount = $plans_add_discount[$plan];
                $discount = Helper::decimalNumber($discount);
                $discount_amount = $amount * $discount / 100;
                $discount_amount = Helper::decimalNumber($discount_amount);
                $netamount = $amount - $discount_amount;
                $netamount = Helper::decimalNumber($netamount);
                
                $clientPlan->plan_price = $amount;
                $clientPlan->discount = $discount;
                $clientPlan->discount_amount = $discount_amount;
                $clientPlan->final_amount = $netamount;
                $clientPlan->no_of_sms = $plan_data->no_of_sms;
                $clientPlan->no_of_email = $plan_data->no_of_email;
                $clientPlan->subscription_date = $subscription_add_date[$plan];
                $clientPlan->save();

                $companyDataUpdate = Company::find($request->company_id);
                $companyDataUpdate->used_sms = $companyDataUpdate->used_sms + $plan_data->no_of_sms;
                $companyDataUpdate->used_email = $companyDataUpdate->used_email + $plan_data->no_of_email;
                $companyDataUpdate->save();
            }
        }

        $commission_per = $this->currentUser()->commission;

        if($commission_per > 0 && $request->payment_status == 'NO') {

            $commission_amt     = ($total_amount * $commission_per) / 100;
            $subscription_id    = $subscription->id;
            $company_id         = $companyData->id;
            $client_id          = $companyData->client_id;
            $dealer_distributor = auth()->guard('admin')->user()->id;

            $commission = new Commission();
            $commission->commission_amt = $commission_amt;
            $commission->subscription_id = $subscription_id;
            $commission->company_id = $company_id;
            $commission->client_id = $client_id;
            $commission->dealer_distributor = $dealer_distributor;
            $commission->is_payment_pending = 'YES';
            $commission->save();
        }
        

        return redirect('rkadmin/subscriptions?company_id='.encrypt($companyData->id))->with('success', $message);
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try{
            $planId = decrypt($id);
            $plan = Subscriptions::find($planId)->delete();
            return redirect()->route('admin.subscriptions')->with('success', 'Subscription deleted successfully');
        }catch(Exception $e)
        {
            abort(500);
        }
    }

    public function cancel($id)
    {
        $message = 'Plan has been cancelled successfully';
        $subscriptionsId = decrypt($id);

        $subscription = Subscriptions::find($subscriptionsId);
        $company_id = $subscription->company_id;

        $companyDataUpdate = Company::find($company_id);
       
        $clientPlanGet = ClientPlan::where('subscription_id',$subscriptionsId)->get()->pluck('plan_id');
       
        $planGet = Plan::select(
            DB::raw("SUM(no_of_users) as userSum"),
            DB::raw("SUM(no_of_sms) as smsSum"),
            DB::raw("SUM(no_of_email) as emailSum"),
            DB::raw("SUM(duration_months) as monthSum")
        )->whereIn('id',$clientPlanGet)->get();
        
        $userSum    = $planGet[0]['userSum'];
        $smsSum     = $planGet[0]['smsSum'];
        $emailSum   = $planGet[0]['emailSum'];
        $monthSum   = $planGet[0]['monthSum'];
        
        $expiry_date = date('Y-m-d', strtotime("-".$monthSum." months", strtotime($companyDataUpdate->expiry_date)));

        $companyDataUpdate->no_of_users = $companyDataUpdate->no_of_users - $userSum;
        $companyDataUpdate->total_sms = $companyDataUpdate->total_sms - $smsSum;
        $companyDataUpdate->total_email = $companyDataUpdate->total_email - $emailSum;

        $companyDataUpdate->used_sms = $companyDataUpdate->used_email - $smsSum;
        $companyDataUpdate->used_email = $companyDataUpdate->used_email - $emailSum;
        $companyDataUpdate->expiry_date = $expiry_date;
        
        $clientPlan = ClientPlan::where('subscription_id',$subscriptionsId);

        $companyDataUpdate->save();
        $clientPlan->delete();
        $subscription->delete();

        return redirect('rkadmin/subscriptions?company_id='.encrypt($company_id))->with('success', $message);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePlan($id)
    {
        $message = 'Plan has been deleted successfully';
        $plan = ClientPlan::find($id);
        $plan_final_amount = $plan->final_amount;
        $subscription_id = $plan->subscription_id;
        $planData = Plan::find($plan->plan_id);

        $subscriptions = Subscriptions::find($subscription_id);    
        $companyData = Company::find($subscriptions->company_id);
        
        $plan_month = $planData->duration_months;
        $plan_expiry_date = $companyData->expiry_date;
        $expiry_date = date('Y-m-d', strtotime("-".$plan_month." months", strtotime($plan_expiry_date)));

        $subscription_expiry_date = date('Y-m-d', strtotime("-".$plan_month." months", strtotime($subscriptions->subscription_expiry_date)));

        $final_amount = $subscriptions->final_amount;
        $round_off_amount = $subscriptions->round_off_amount;

        $sgst_amount = $cgst_amount = $igst = $total_gst = $total_sgst = $total_cgst = $total_igst = $gst_amount = 0;

        if($companyData->state_id == 'Gujarat'){
            $sgst = $subscriptions->sgst;
            $cgst = $subscriptions->cgst;
            $sgst_amount = $plan_final_amount * $sgst / 100;
            $cgst_amount = $plan_final_amount * $cgst / 100;
            $gst_amount = $sgst_amount + $cgst_amount;

            $total_sgst = $subscriptions->sgst_amount - $sgst_amount;
            $total_cgst = $subscriptions->cgst_amount - $cgst_amount;
            $total_gst = $total_sgst + $total_cgst;
        }else{
            $igst = $subscriptions->igst;
            $igst_amount = $plan_final_amount * $igst / 100;
            $gst_amount = $igst_amount;
            $total_igst = $subscriptions->igst_amount - $igst_amount;
            $total_gst = $total_igst;
        }

        $plan_final_amount += $gst_amount;
        
        $final_amount = $final_amount - $plan_final_amount;
        $round_off_amount = $round_off_amount - $plan_final_amount;
        
        $subscriptions->sgst_amount = $total_sgst;
        $subscriptions->cgst_amount = $total_cgst;
        $subscriptions->igst_amount = $total_igst;
        
        $subscriptions->total_amount = $final_amount - $total_gst;
        $subscriptions->final_amount = $final_amount;
        $subscriptions->round_off_amount = round($round_off_amount);
        $subscriptions->subscription_expiry_date = $subscription_expiry_date;
        $subscriptions->save();

        $companyData->expiry_date = $expiry_date;
        $companyData->no_of_users = $companyData->no_of_users - $planData->no_of_users;
        $companyData->total_sms = $companyData->total_sms - $planData->no_of_sms;
        $companyData->total_email = $companyData->total_email - $planData->no_of_email;
        $companyData->used_sms = $companyData->used_sms - $planData->no_of_sms;
        $companyData->used_email = $companyData->used_email - $planData->no_of_email;
        $companyData->save();
        
        $plan->delete();
        
        return redirect('rkadmin/subscriptions/'.encrypt($subscription_id).'/edit')->with('success', $message);

    }
    
    
	public function getPlanDetail(Request $request)
    {
		 $id = $request->plan_id;
		 $company = Company::find($request->company_id);
         $planId = ($id);
         $plan = Plan::find($planId);
		 return response()->json(['success'=>true,'plan'=>$plan, 'company'=>$company]);
    }
	public function getCompanyDetail(Request $request)
    {
		 $company = Company::find($request->company_id);
		 return response()->json(['success'=>true, 'company'=>$company]);
    }

}
