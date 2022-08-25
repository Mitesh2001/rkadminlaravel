<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\Client;
use App\Models\Company;
use App\Models\Country;
use App\Models\Plan;
use App\Models\ClientPlan;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Auth;
use Validator;
use Exception;
use Helper;
use App\Mail\SendEmail;
use App\Mail\SendEmailViaQueue;
use Storage, Mail;
use App\Models\ActionLog;
use App\Models\EmailSmsLog;

class EmailsController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            if(auth()->user()->type == 3 || auth()->user()->type == 4)
            {
                return redirect('/rkadmin')->with('success','You are not authorized to access that page.');

            }
            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $organization_id = $company_id = '';

        if(isset($user)){
            $organization_id = $user->organization_id;
            $company_id = $user->company_id;
        }

        //$getInvoiceTemplateSend = EmailTemplate::select('email_template_id')->where('client_id',$organization_id)->where('company_id',$company_id)->where('default_template','0')->count();//->where('name','Invoice email send')

        if($request->ajax()){

            //if($getInvoiceTemplateSend==0){
                $emailTemplate = EmailTemplate::select(['email_template_id', 'name', 'subject','template_type'])->where('client_id','0')->where('company_id','0')->where('default_template','1');
				if($request->template_type){
					$emailTemplate->where('template_type','=',$request->template_type);
				}
            /* }else{
                $emailTemplate = EmailTemplate::select(['email_template_id', 'name', 'subject'])->where('client_id',$organization_id)->where('company_id',$company_id)->where('default_template','0');
            } */
            $emailTemplate->orderBy('email_template_id', 'desc')->get();
            return Datatables::of($emailTemplate)
				->addColumn('type', function ($emailTemplate) {
					return ($emailTemplate->template_type == 1)?'Default Events':(($emailTemplate->template_type == 2)?'Marketing':'');
				})
                ->addColumn('action', function ($emailTemplate) {
                    $html = '<a href="'.route('admin.emails.edit',encrypt($emailTemplate->email_template_id)).'" class="btn btn-link" ><i class="flaticon2-pen text-success" data-toggle="tooltip" title="Edit"></i></a>';
					if($emailTemplate->template_type == 2)
					$html .= '<a href="'.route('admin.emails.send',encrypt($emailTemplate->email_template_id)).'" class="btn btn-link" ><i class="flaticon2-email text-success" data-toggle="tooltip" title="Send"></i></a>';
                    return $html;
                })
                ->rawColumns(['action','content'])
                ->make(true);
        }
        return view('admin.email.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.email.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		return $this->post_process('add', 0, $request);
    }

	private function post_process($action, $id, $request)
    {
		$validation = Validator::make($request->all(), [
            'subject' => 'required|max:200',
            'content' => 'required',
        ],
        ['content.required' => 'The email template field is required.']
        );

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }

        $id = $request->id;
        $user_id = Auth::user()->id;
        $user = User::find($user_id);

		if($action == 'add'){
			$emailTemplate = new EmailTemplate;
			$emailTemplate->default_template = 1;
			$emailTemplate->external_key = substr(strtolower(str_replace(array(' ','-'),'',$request->name)),0,25);
        }else{
            /* $getTemplate = EmailTemplate::find($id);
			if($getTemplate->default_template == 1){
				$emailTemplate = new EmailTemplate();
				$emailTemplate->default_template = 0;
				$emailTemplate->createdBy = $user_id;
				$emailTemplate->client_id = $user->organization_id;
				$emailTemplate->company_id = $user->company_id;
				$emailTemplate->name = $getTemplate->name;
			}else */{
				$emailTemplate = EmailTemplate::find($id);
			}
        }
		if($request->template_type)
        $emailTemplate->template_type = $request->template_type;
		$emailTemplate->name = $request->name;
        //$emailTemplate->template_variables = $request->template_variables;
        $emailTemplate->subject = $request->subject;
        $emailTemplate->content = $request->content;
        $emailTemplate->updatedBy = $user_id;
        $emailTemplate->save();

        return redirect('rkadmin/emails')->with('success', 'Your email template is successfully saved');
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $id = decrypt($id);
            $emailTemplate = EmailTemplate::find($id);

            /* $emailTemplateBody =  Helper::emailTemplateBody();

            $emailTemplateBody =  str_replace("{{#template_body}}", $emailTemplate->content, $emailTemplateBody);

            $emailTemplate->content = str_replace("{{#all_css}}", Helper::emailTemplateCss(), $emailTemplateBody); */

            return view('admin.email.edit',compact('emailTemplate'));
        }catch(Exception $e){
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
        return $this->post_process('update', $id, $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	public function send($id)
    {
        try{
            $id = decrypt($id);
            $emailTemplate = EmailTemplate::find($id);

			$companyName = Company::pluck('company_name','id');
            $clientName = Client::pluck('name','id');
			$plan = Plan::pluck('name','id');
			
			$countries = Country::where('deleted', 0)->pluck('name', 'country_id');
			//$states = \DB::select(\DB::raw("SELECT `state` FROM `country_pincode` GROUP BY state"));
			$states_ar = array();
			/* foreach($states as $state){
				$states_ar[$state->state] = $state->state;
			} */

            return view('admin.email.send',compact('emailTemplate','companyName','clientName','plan','countries'))->withStates($states_ar);
        }catch(Exception $e){
            abort(404);
        }
    }
	public function clientsdata(Request $request){
		
		$clients = Client::leftjoin('company','company.client_id','clients.id')
                 ->leftjoin('clients_plan','clients_plan.company_id','company.id')
                //  ->leftjoin('plans','plans.id','clients_plan.plan_id')
                 ->select('company.company_name','company.id as company_id','company.expiry_date','clients.id as clientId','clients.name','clients.city','clients.client_uid','clients.created_by');
        if ($request->client_id && $request->client_id!='') {
            $clients->where('clients.id',$request->client_id);
        }
        if ($request->company_id && $request->company_id!='') {
            $clients->where('company.id',$request->company_id);
        }
		if ($request->country_id && $request->country_id!='') {
            $clients->where(function($query) use ($request) {
                $query->where('company.country_id','=', $request->country_id)
                      ->orWhere('clients.country_id', '=', $request->country_id);
            });
        }
		if ($request->state_id && $request->state_id!='') {
            $clients->where(function($query) use ($request) {
                $query->where('company.state_id','=', $request->state_id)
                      ->orWhere('clients.state_id','=', $request->state_id);
            });
        }
		if ($request->state_name && $request->state_name!='') {
            $clients->where(function($query) use ($request) {
                $query->where('company.state_name', $request->state_name)
                      ->orWhere('clients.state_name', $request->state_name);
            });
        }
		if ($request->city_id && $request->city_id!='') {
            $clients->where(function($query) use ($request) {
                $query->where('company.city','=', $request->city_id)
                      ->orWhere('clients.city', '=', $request->city_id);
            });
        }
		if ($request->city_txt && $request->city_txt!='') {
            $clients->where(function($query) use ($request) {
                $query->where('company.city', $request->city_txt)
                      ->orWhere('clients.city', $request->city_txt);
            });
        }
        if ($request->plan && $request->plan !='') {
            $clients->where('clients_plan.plan_id',$request->plan);
        }
		if($request->date && $request->date!=''){
        $date = $request->date;
        if($date){
            $date = explode('/',$date);
            $startDate = trim($date[0],' ');
            $endDate = trim($date[1],' ');
            $clients = $clients->whereBetween('company.expiry_date', [$startDate, $endDate]);
        }
		}

        if($this->currentUser()->type == 3 || $this->currentUser()->type == 4){
            $clients->where('clients.created_by',$this->currentUser()->id);
        }

        $clients = $clients->distinct('clients_plan.company_id')->orderBy('clientId', 'desc')->get();
		
		return Datatables::of($clients)
			->addColumn('client_company_id', function ($clients) {
                $html = '<input type="checkbox" name="client_company_id[]" class="companies" value="'.$clients->company_id.'" class="">';
                return $html;
            })
            ->addColumn('namelink', function ($clients) {
                return $clients->name;
            })
            ->addColumn('company_name', function ($clients) {
                return $clients->company_name;
            })
            ->addColumn('plan_name', function ($clients) {
                return Helper::getPlanSubscriptionsData($clients->clientId,$clients->company_id,'1');
            })
            ->addColumn('final_amount', function ($clients) {
                $planPrice = Helper::getPlanSubscriptionsData($clients->clientId,$clients->company_id,'2');
                $planPrice = !empty($planPrice) ? explode(',',$planPrice) : [] ;

                return Helper::decimalNumber(array_sum($planPrice));
            })
            // ->addColumn('client_uid', function ($clients) {
            //     return $clients->client_uid;
            // })
            ->addColumn('expiry_date', function ($clients) {
                $date = Carbon::parse($clients->expiry_date);
                $diffHtml = '';
                if($clients->expiry_date < date('Y-m-d')){
                    $diffHtml = 'Already Expired';
                }else{
                    $diff  = Carbon::parse(Carbon::now())->diff($clients->expiry_date);
                    $diffYears = $diff->y;
                    $diffMonths = $diff->m;
                    $diffDays = $diff->d;
                    if($diffYears){
                        $diffHtml .= $diffYears.' Years ';
                    }
                    if($diffMonths){
                        $diffHtml .= $diffMonths.' Months ';
                    }
                    if($diffDays){
                        $diffHtml .= $diffDays.' Days ';
                    }
                }

                $planPrice = Helper::getPlanSubscriptionsData($clients->clientId,$clients->company_id,'2');
                $planPrice = !empty($planPrice) ? explode(',',$planPrice) : [] ;

                $planPrice = Helper::decimalNumber(array_sum($planPrice));

                if($planPrice>0){
                    return '<span class="tooltipped" data-toggle="tooltip" data-theme="dark" title="'.  $diffHtml.'">'.$clients->expiry_date.'</span>';
                }else{
                    return '';
                }
            })

            ->rawColumns(['client_company_id','namelink', 'company_name', 'city','expiry_date','client_uid'])
            ->make(true);
	}

	public function sendbulkemails(Request $request){
		$companies = $request->companies;
		$template_id = $request->template_id;
		$template = EmailTemplate::find($template_id);
		if($companies){
			$senderId = $this->currentUser()->id;
			$companies = explode(',',$companies);
			foreach($companies as $company){
				$clients = Company::with(['client_data'])->find($company);
				if($clients){
					$contact_person = User::where('organization_id','=',$clients->client_data->id)->where('company_id','=',$company)->where('company_contact_type','=','1')->first();
				$emailcotent = $template->content;
				$shortcodes = Helper::shortcodes($clients);
				if($clients->company_logo)
					$shortcodes['{{#company_logo}}'] = '<img src="'.asset('storage/images/'.$clients->company_logo).'">';
				else
					$shortcodes['{{#company_logo}}'] = '';

				$shortcodes['{{#company_address}}'] = $clients->address_line_1.','.$clients->address_line_2.'-'.$clients->city;
				$shortcodes['{{#client_address}}'] = $clients->client_data->address_line_1.','.$clients->client_data->address_line_2.'-'.$clients->client_data->city;
				//echo '<pre>';print_r($clients);exit;
				if($contact_person){
					$shortcodes['{{#company_contact_person_name}}'] = $contact_person->name;
					$shortcodes['{{#company_contact_person_mobile}}'] = $contact_person->mobileno;
					$shortcodes['{{#company_contact_person_email}}'] = $contact_person->email;
				}else{
					$shortcodes['{{#company_contact_person_name}}'] = '';
					$shortcodes['{{#company_contact_person_mobile}}'] = '';
					$shortcodes['{{#company_contact_person_email}}'] = '';
				}
				$to = [
					[
						'email' => $clients->client_data->email,
						'name' => $clients->company_name,
					]
				];
				//print_r($shortcodes);exit;
				$when = now()->addMinutes(1);
				Mail::to($to)->later($when, new SendEmailViaQueue($template_id, $shortcodes));
				$log = new EmailSmsLog;
				$log->user_id = $senderId;
				$log->client_id = $clients->client_id;
				$log->client_number = $clients->client_data->mobile_no;
				$log->client_email = $clients->client_data->email;
				$log->company_id = $company;
				$log->template_id = $template_id;
				$log->type = 'Email';
				$log->response = '1';
				$log->save();
				}
			}
		}
		return response()->json([
            'success'=>true
        ]);
	}

	public function currentUser(){
        return Auth::user();
    }
}
