<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DB;
use Helper;
use JsValidator;
use Validator;
use Auth;
use App\Models\Client;
use App\Models\IndustryType;
use App\Models\CompanyType;
use App\Models\State;
use App\Models\City;
use App\Models\Country;
use App\Models\Company;
use App\Models\Plan;
use App\Models\ClientPlan;
use App\Models\CountryPincode;
use App\Models\Event;
use App\Models\User;
use App\Models\Cast;
use App\Exports\ClientExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ClientsController extends Controller
{

    public function currentUser(){
        return Auth::user();
    }

    public function index()
    {
        if($this->currentUser()->type == 3 || $this->currentUser()->type == 4){
            $companyName = Company::where('created_by',$this->currentUser()->id)->pluck('company_name','id');
            $clientName = Client::where('created_by',$this->currentUser()->id)->pluck('name','id');
        }else{
            $companyName = Company::pluck('company_name','id');
            $clientName = Client::pluck('name','id');
        }
        $plan = Plan::pluck('name','id');
		$countries = Country::where('deleted', 0)->pluck('name', 'country_id');
		//$states = \DB::select(\DB::raw("SELECT `state` FROM `country_pincode` GROUP BY state"));
		$states_ar = array();
		/* foreach($states as $state){
			$states_ar[$state->state] = $state->state;
		} */
		
        return view('admin.clients.index',compact('companyName','clientName','plan','countries'))->withStates($states_ar);
    }


    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData(Request $request)
    {
        // DB::enableQueryLog();
        $clients = Client::leftjoin('company','company.client_id','clients.id')
                 ->leftjoin('clients_plan','clients_plan.company_id','company.id')
                //  ->leftjoin('plans','plans.id','clients_plan.plan_id')
                 ->select('company.company_name','company.id as company_id','company.expiry_date','clients.id as clientId','clients.name','clients.city','clients.client_uid','clients.created_by')->whereNull('company.deleted_at');
        if ($request->client_id && $request->client_id!='') {
            $clients->where('clients.id',$request->client_id);
        }
        if ($request->company_id && $request->company_id!='') {
            $clients->where('company.id',$request->company_id);
        }
        if ($request->plan && $request->plan !='') {
            $clients->where('clients_plan.plan_id',$request->plan);
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
        // dd(DB::getQueryLog());
        // dd($clients);
        if(!$request->ajax()){
            $name = 'client_'.date('Y_m_d_H_i_s').'.csv';
            collect($clients)->map(function($q,$k)use(&$data){
                $k++;
                $data[$k]['sr'] = $k;
                $data[$k]['client_name'] = $q->name;
                $data[$k]['company_name'] = $q->company_name;
                $data[$k]['client_uid'] = $q->client_uid;
                $data[$k]['plan_name'] = Helper::getPlanSubscriptionsData($q->clientId,$q->company_id,'1');
                $planPrice = Helper::getPlanSubscriptionsData($q->clientId,$q->company_id,'2');
                $planPrice = !empty($planPrice) ? explode(',',$planPrice) : [] ;
                $data[$k]['plan_price'] = array_sum($planPrice);
                $data[$k]['expiry_date'] = $q->expiry_date;
                $data[$k]['city'] = $q->city;
                unset($q->company_id,$q->clientId);
                return $q;
            });
            $data = collect($data);
            return Excel::download(new ClientExport($data), $name);
        }

        // $clients = Client::select(['id', 'name', 'company_name', 'city']);
        return Datatables::of($clients)
            ->addColumn('namelink', function ($clients) {
                return $clients->name;
            })
            ->addColumn('company_name', function ($clients) {
                return '<a href="'.url('rkadmin/company/'.encrypt($clients->company_id)).'">'.$clients->company_name.'</a>';
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
            ->addColumn('action', function ($clients) {

                $edit_company = $product_category = $product_service = $employee = $custom_form = $subscriptions = $role ='';

                $html = '<div class="dropdown"><button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                $html.= 'Action</button><div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                $edit_company = '<a href="'.route('admin.clients.edit', [$clients->clientId]).'"  class="dropdown-item">Edit Company</a>';

                $html.= $edit_company;

               // $product_category = '<a href="'.url('rkadmin/product-category?company_id='.encrypt($clients->company_id)).'" class="dropdown-item">Product Category</a>';
                $product_category = '<a href="'.route('admin.category.index',['companyId'=>encrypt($clients->company_id)]).'" class="dropdown-item">Product Category</a>';

                //$product_service = '<a href="'.route('admin.products.index', '/'.encrypt($clients->company_id)).'" class="dropdown-item">Products / Service</a>';
                $product_service = '<a href="'.route('admin.products.index', ['companyId'=>encrypt($clients->company_id)]).'" class="dropdown-item">Products / Service</a>';

                $employee = '<a href="'.url('rkadmin/employees/'.encrypt($clients->company_id)).'" class="dropdown-item">Employee</a>';

                $custom_form = '<a href="'.url('rkadmin/custom-form/'.encrypt($clients->company_id)).'" class="dropdown-item">Custom Field</a>';

                $subscriptions = '<a href="'.url('rkadmin/subscriptions?company_id='.encrypt($clients->company_id)).'" class="dropdown-item">Subscriptions</a>';

                $role = '<a href="'.url('rkadmin/roles?company_id='.encrypt($clients->company_id)).'" class="dropdown-item">Roles</a>';

                if($clients->company_id){
                    $html.= $product_category;
                    $html.= $product_service;
                    $html.= $employee;
                    $html.= $custom_form;
                    $html.= $subscriptions;
                    $html.= $role;
                }
                $html.= '</div>';
                return $html;
            })
            ->rawColumns(['namelink', 'company_name', 'city','expiry_date','client_uid','action'])
            ->make(true);
    }

	 /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     *
     */
    public function create()
    {
		// $states = State::where('country_id', '101')->where('deleted', 0)->pluck('name', 'state_id');
		$countries = Country::where('deleted', 0)->pluck('name', 'country_id');
        $plans = Plan::select("*", DB::raw("CONCAT('Name : ', name,', ',' Number Of Users : ',no_of_users,', ',' Price : ',price) AS plan"))->pluck('plan','id');
		$plans->prepend('Please Select plan', '');

        //$states = DB::select(DB::raw("SELECT `state` FROM `country_pincode` GROUP BY state"));
        $states_ar = array();
        /* foreach($states as $state){
            $states_ar[$state->state] = $state->state;
        } */

        return view('admin.clients.create')
            //->withUsers(User::with('department')->get()->pluck('nameAndDepartmentEagerLoading', 'id'))
            //->withCountry(Country::fromCode(Setting::first()->country))
            ->withIndustries($this->listAllIndustries())
            ->withCompanytypes($this->listAllCompanyTypes())
            ->withLicensetypes($this->listAllLicenseType())
            ->withEstablishYears($this->establishYears())
            ->withCountries($countries)
            ->withPlans($plans)
            ->withStates($states_ar);
    }
	/**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
		return $this->post_process('add', 0, $request);
    }

	private function post_process($action, $id, $request)
    {
        $validation = Validator::make($request->all(), [
            'company_name.*' => 'required|string',
            'turnover' => 'string|nullable',
            // 'state_id' => 'required',
            'gst_no' => 'string|nullable',
            'pan_no' => 'string|nullable',
            'no_of_employees' => 'integer|nullable',
            'company_license_no.*' => 'nullable|max:20',
            'name' => 'required|string',
            'website' => 'string|url',
            'email' => 'email',
            'secondary_email' => 'email|nullable',
            'mobile_no' => 'required|digits:10',
            'secondary_mobile_no' => 'digits:10|nullable',
            'address_line_1' => 'string|nullable',
            'address_line_2' => 'string|nullable',
            'country_id' => 'integer|nullable',
            'city' => 'nullable|string',
            'city_txt' => 'nullable|string',
            'postcode' => 'nullable',
            'postcode_txt' => 'nullable',
            'industry_id' => 'integer',
            'company_type_id' => 'integer',
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }

        if ($action == 'add') {
            $client = new Client;
            $message = 'Client has been added successfully';
            $picture = '';
            $company_logo = '';
        } else {
            $client = Client::findOrFail($id);
			$message = 'Client has been updated successfully';
			$picture = $client->picture;
			$company_logo = $client->company_logo;
        }

		if ($action == 'add'){
            $client_uid_count = Client::withTrashed()->count();
            $id_length = strlen($client_uid_count);

            $client_uid = '';

            /* if($id_length==0){
                $client_uid = '000001';
            }elseif($id_length==1){
                $client_uid = '00000'.$client_uid_count;
            }elseif($id_length==2){
                $client_uid = '0000'.$client_uid_count;
            }elseif($id_length==3){
                $client_uid = '000'.$client_uid_count;
            }elseif($id_length==4){
                $client_uid = '00'.$client_uid_count;
            }elseif($id_length==5){
                $client_uid = '0'.$client_uid_count;
            }else{
                $client_uid = $client_uid_count;
            } */
			//if($client_uid_count>0){
				$client_uid_count+=1;
			$client_uid = str_pad($client_uid_count,6,"0",STR_PAD_LEFT);
			/* }
			else{
				$client_uid = str_pad(1,6,"0",STR_PAD_LEFT);
			} */

           $client->client_uid = $client_uid;
        }

		if ($request->hasFile('picture')) {
			 if ($request->file('picture')->isValid()) {
				 $validated = $request->validate([
                    'name' => 'string|max:40',
                    'picture' => 'mimes:jpeg,jpg,png|max:4098',
                ]);
				 //$extension = $request->picture->extension();
				 $imagePath = $request->file('picture');
				 $imageName = $imagePath->getClientOriginalName();
				 $imageName = time().$imageName;//.'.'.$request->image->extension();
				 $productimage= $imageName;
				 $imageName = $request->picture->move(public_path('/storage/images'), $imageName);
				 $picture = $productimage;
			 }
		}

		$client->name = $request->name;
		// $client->company_name = $request->company_name;
		$client->email = $request->email;
		$client->secondary_email  = $request->secondary_email;
		$client->mobile_no  = $request->mobile_no;
		$client->secondary_mobile_no  = $request->secondary_mobile_no;
		// $client->established_in = $request->established_in;
		// $client->turnover = $request->turnover;
		// $client->gst_no = $request->gst_no;
		// $client->pan_no = $request->pan_no;
		// $client->no_of_employees = $request->no_of_employees;
		// $client->website = $request->website;
		// $client->company_logo = $company_logo;
		$client->picture = $picture;
		$client->address_line_1 = $request->address_line_1;
		$client->address_line_2 = $request->address_line_2;
		$client->state_id = $request->cli_state_id;
        $client->city = $request->cli_city;
        if($request->cli_country_id == '101'){
            $client->postcode = $request->cli_postcode;
        }else{
           // $client->state_name = ($request->cli_state_name ? $request->cli_state_name : '');
           /*  $client->state_id = ($request->cli_state_name ? $request->cli_state_name : '');
            $client->city = ($request->cli_city_txt ? $request->cli_city_txt : ''); */
            $client->postcode = ($request->cli_postcode_txt ? $request->cli_postcode_txt : '');
        }

		$client->country_id  = $request->cli_country_id;

		// $client->company_type_id  = $request->company_type_id;
		// $client->industry_id  = $request->industry_id;
        $companyData = $request->company_data;

        if ($action == 'add')
		$client->created_by = $this->currentUser()->id;
        $client->save();

        if(!empty($companyData))
        {
            //$company = Company::where('client_id',$client->id)->delete();

            foreach($companyData as $key=>$row)
            {
                $company = new Company();
                if(!empty($row['company_id']) && count($row) > 1)
                {
                    //$company->id = $row['company_id'];
					$company = $company->find($row['company_id']);
                }
                $company->company_name = $row['company_name'];
                $company->company_type_id = $row['company_type_id'];
                $company->industry_id = $row['industry_id'];
                $company->gst_no = $row['gst_no'];
                $company->pan_no = $row['pan_no'];
                $company->no_of_employees = $row['no_of_employees'];
                $company->website = $row['website'];
                $company->established_in = $row['established_in'];
                $company->turnover = $row['turnover'];
                $company->vat_no = $row['vat_no'];
                $company->excise_no = $row['excise_no'];
                $company->company_license_type = $row['company_license_type'];
                $company->company_license_no = $row['company_license_no'];
                $company->address_line_1 = $row['address_line_1'];
                $company->address_line_2 = $row['address_line_2'];

                $company->from_email = $row['from_email'];
                $company->from_name = $row['from_name'];
                $company->sms_sender_id = $row['sms_sender_id'];
                $company->send_sms = (isset($row['send_sms'])) ? '1' : '0';

                $company->email_service = (isset($row['email_service'])) ? '1' : '0';
                $company->sms_service = (isset($row['sms_service'])) ? '1' : '0';

                /* $company->total_sms = $row['total_sms'];
                $company->total_email = $row['total_email'];
                $company->used_sms = $row['used_sms'];
                $company->used_email = $row['used_email'];
                $company->expiry_date = $row['expiry_date']; */
				$company->state_id = $row['state_id'];
                $company->city = $row['city'];
                if($row['country_id'] == '101'){
                    $company->postcode = $row['postcode'];
                }else{
                    //$company->state_name = (isset($row['state_name']) ? $row['state_name'] : '');
                    /* $company->state_id = (isset($row['state_name']) ? $row['state_name'] : '');
                    $company->city =  (isset($row['city_txt']) ? $row['city_txt'] : ''); */
                    $company->postcode = (isset($row['postcode_txt']) ? $row['postcode_txt'] : '');
                }

                $company->country_id = $row['country_id'];

                // $company->product_service = $row['product_service'];
                if (!empty($row['company_logo']))
                {
                    $imagePath = $row['company_logo'];
                    $imageName = $imagePath->getClientOriginalName();
                    $imageName = Carbon::now()->format('YmdHisu').$imageName;//.'.'.$row->image->extension();
                    $company->company_logo = $imageName;
                    $imageName = $row['company_logo']->move(public_path('/storage/images'), $imageName);
                }else{
                    $company->company_logo = !empty($row['company_old_logo']) ? $row['company_old_logo'] : null;
                }
                $company->client_id = $client->id;
                $company->created_by = $this->currentUser()->id;
                // if(!empty($row['plan_id']))
                // {
                //     $planData = Plan::find($row['plan_id']);
                //     $company->plan_id = $row['plan_id'];
                //     $company->plan_price = $planData->price;
                //     $company->no_of_users = $planData->no_of_users;
                // }
                // if($action == 'add')
                // {
                //     $addMonths = !empty($planData->duration_months) ? $planData->duration_months : 0;
                //     $company->purchase_date = date('Y-m-d');
                //     $company->expiry_date = Carbon::now()->addMonth($addMonths);
                // }
				if(!empty($row['company_id']) && count($row) > 1)
					$company->update();
				else
                $company->save();
			
                // $oldPlanId = !empty($row['old_plan_id']) ? $row['old_plan_id'] : null;
                // if(!empty($row['plan_id']) && ($company->plan_id != $oldPlanId))
                // {
                //     $clientPlan = new ClientPlan();
                //     $clientPlan->client_id = $client->id;
                //     $clientPlan->plan_id = $row['plan_id'];
                //     $clientPlan->company_id = $company->id;
                //     $clientPlan->save();
                // }
            }
        }
		return redirect(url('rkadmin/clients'))->with('success', $message);
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return mixed
     */
    public function show($id)
    {

		$client = Client::with('industry_type','company_type','state','country')->find($id);
        //dd($client->appointments);
        return view('admin.clients.show')
            ->withClient($client);
            //->withCompanyname(Setting::first()->company)
            //->withInvoices($this->getInvoices($client))
            //->withUsers(User::with('department')->get()->pluck('nameAndDepartmentEagerLoading', 'id'))
            //->with('filesystem_integration', Integration::whereApiType('file')->first())
            //->with('documents', $client->documents()->where('integration_type', get_class(GetStorageProvider::getStorage()))->get())
            //->with('lead_statuses', Status::typeOfLead()->get())
           // ->with('task_statuses', Status::typeOfTask()->get())
            //->withRecentAppointments($client->appointments()->orderBy('start_at', 'desc')->where('end_at', '>', now()->subMonths(3))->limit(7)->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return mixed
     */
    public function edit($id)
    {
        $client = Client::with('industry_type','company','state','country')->find($id);
		// $states = State::where('country_id', '101')->where('deleted', 0)->pluck('name', 'state_id');
		$countries = Country::where('deleted', 0)->pluck('name', 'country_id');
        $plans = Plan::select("*", DB::raw("CONCAT('Name : ', name,', ',' Number Of Users : ',no_of_users,', ',' Price : ',price) AS plan"))->pluck('plan','id');
        //$states = DB::select(DB::raw("SELECT `state` FROM `country_pincode` GROUP BY state"));
		$states = State::where('country_id',$client->country_id)->orderBy('name', 'ASC')->get();
		
        $states_ar = array(''=>'Please select state');
        foreach($states as $state){
            $states_ar[$state->name] = $state->name;
        }
		if($client->state_id!=''){
			$cities = City::WhereHas('state', function ($q2) use ($client) {
						return $q2->where('name', '=', $client->state_id);
				})->orderBy('name', 'ASC')->get();
		}elseif($client->state_name!=''){
			$cities = City::WhereHas('state', function ($q2) use ($client) {
						return $q2->where('name', '=', $client->state_name);
				})->orderBy('name', 'ASC')->get();
		}
        $cities_ar = array(''=>'Please select city');
        foreach($cities as $city){
            $cities_ar[$city->name] = $city->name;
        }
		// $plans->prepend('Please Select plan', '');
		// $states->prepend('Please Select state', '');
		// $countries->prepend('Please Select country', '');
        // dd($client);
        $city = array($client->city => ucwords(strtolower($client->city)));
        $postcode = array($client->postcode => $client->postcode);

        return view('admin.clients.edit')
            ->withClient($client)
            ->withIndustries($this->listAllIndustries())
            ->withCompanytypes($this->listAllCompanyTypes())
            ->withLicensetypes($this->listAllLicenseType())
            ->withEstablishYears($this->establishYears())
            ->withCountries($countries)
            ->withPlans($plans)
            ->withCity($city)
            ->withPostcode($postcode)
            ->withStates($states_ar)
            ->withCities($cities_ar);
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
		return $this->post_process('update', $id, $request);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $company = Company::where('client_id',$client->id)->delete();
		$client->delete();

        return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully');
    }

	public function listAllIndustries()
    {
		$types = IndustryType::pluck('name', 'id');
		$types->prepend('Please Select industry', '');

        return $types;
    }
	public function listAllCompanyTypes()
    {
		$type = CompanyType::pluck('name', 'id');
		$type->prepend('Please Select company type', '');
        return $type;
    }

    public function listAllLicenseType()
    {
        $types = collect(['1'=>'General Business Licenses','2'=>'Special Business Licenses','3'=>'Doing Business As (DBA) Licenses','4'=>'Direct Sales Licenses','5'=>'Municipal And Provincial Licenses','6'=>'Professional / Occupational Licenses','7'=>'Federal Business Licenses','8'=>'Sales Tax Licenses','9'=>'Industry Licenses']);
        $types->prepend('Please Select Licenses Type', '');
        return $types;
    }

    public function establishYears()
    {
        $years = range(1949,date('Y'));
        $years = array_values($years);
        $years = array_slice($years, 1, null, true);
        return $years;
    }

    // show company details
    public function companyShow($companyId)
    {
        try{
            $companyId = decrypt($companyId);
            $companyData = Helper::getCompany($companyId);
            $companyType = $this->listAllCompanyTypes();
            $industries = $this->listAllIndustries();
            $licenseType = $this->listAllLicenseType();
            return view('admin.clients.company_show',compact('companyData','companyType','industries','licenseType'));
        }catch(Exception $e){
            abort(404);
        }
    }
	
	public function getState(Request $request)
    {
        //$states = CountryPincode::groupBy('state')->get();//DB::select(DB::raw("SELECT `state` FROM `country_pincode` GROUP BY state"));
		$states = State::where('country_id',$request->country_id)->orderBy('name', 'ASC')->get();
        $states_ar = array();
		$states_ar[] = array('id'=>'','text'=>'Please select state');
        foreach($states as $state){
            $states_ar[] = array('id'=>ucwords(strtolower($state->name)),'text'=>ucwords(strtolower($state->name)));
        }
		$states_ar = mb_convert_encoding($states_ar, "UTF-8", "auto");
        return response()->json(['success'=>true, 'states'=>$states_ar]);
    }
	
    public function getCity(Request $request)
    {
        //$cities = DB::select(DB::raw("SELECT `state`,`district` FROM `country_pincode` where `state` = '".$request->state_name."' GROUP BY district"));
		$cities = array();
		if($request->state_name!=''){
		$cities = City::WhereHas('state', function ($q2) use ($request) {
					if($request->state_name!=''){
						return $q2->where('name', '=', $request->state_name);
					}
				})->orderBy('name', 'ASC')->get();
		}

        $cities_ar = array();
		$cities_ar[] = array('id'=>'','text'=>'Please select city');
        foreach($cities as $city){
            $cities_ar[] = array('id'=>ucwords(strtolower($city->name)),'text'=>ucwords(strtolower($city->name)));
        }
		$cities_ar = mb_convert_encoding($cities_ar, "UTF-8", "auto");
        return response()->json(['success'=>true, 'cities'=>$cities_ar]);
    }

    public function getPostcode(Request $request)
    {
        //$postcodes = DB::select(DB::raw("SELECT `district`,`pincode` FROM `country_pincode` where `district` = '".$request->city_name."' GROUP BY pincode"));
		 $postcodes = CountryPincode::select('district','pincode')->where('district',$request->city_name)->groupBy('state')->groupBy('district')->groupBy('pincode')->orderBy('pincode', 'ASC')->get();//DB::select(DB::raw("SELECT `district`,`pincode` FROM `country_pincode` where `district` = '".$request->city_name."' GROUP BY pincode"));

        $postcodes_ar = array();
		$postcodes_ar[] = array('id'=>'','text'=>'Please select post code');
        foreach($postcodes as $postcode){
            $postcodes_ar[] = array('id'=>$postcode->pincode,'text'=>$postcode->pincode);
        }
        return response()->json(['success'=>true, 'postcodes'=>$postcodes_ar]);
    }
	
	public function getCast(Request $request)
    {
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data =Cast::where('name','LIKE',"%$search%")
            		->where('status','=',"1")
					->orderBy('name', 'asc')
            		->pluck("name","name");
        }
		$data->prepend('New Cast','Add New Cast');
        return response()->json($data);
    }
	
	public function addCast(Request $request)
    {
        $NewCast = $request->NewCast;
		$cast = Cast::where('name','=',$NewCast)->first();
		if(!$cast){
		$cast = new Cast;
		$cast->name = $NewCast;
		$cast->status = 1;
		$cast->save();
		}else{
			if($cast->status != 1){
				$cast->status = 1;
				$cast->save();
			}
		}
        return response()->json(['success'=>true, 'id'=>$cast->id, 'name'=>$cast->name]);
    }
	
	public function getCompanies(Request $request)
    {
        $data = [];

        if($request->has('q')){
            $search = $request->q;
            $data =Company::with(['client_data'])->where('company_name','LIKE',"%$search%")
            		->whereNull('deleted_at')
					->orderBy('company_name', 'asc')
            		->get();
        }
        return response()->json($data);
    }
}
