<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DB;
use Helper;
use JsValidator;
use Validator;
use App\Models\Client;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
use App\Models\Company;
use App\Models\Cast;
use App\Models\MasterUser;
use Session;
use Exception;
use Auth;

class EmployeesController extends Controller
{   

    public function __construct(){      
        // $this->middleware(function ($request, $next) {     
        //     if(auth()->user()->type == 3 || auth()->user()->type == 4)
        //     {
        //         $pathinfo = $request->getPathInfo();
        //         $pathinfo = explode('/',$pathinfo);
        //         if(!isset($pathinfo[3])){
        //             return redirect('/rkadmin')->with('success','You are not authorized to access that page.');
        //         }
        //     }
        //     return $next($request);
        // });
    }
    public function index($companyId = 0)
    {
        try{
            $companyData = null;$total_employees = 0;
            if($companyId){
                $companyId = decrypt($companyId);
                $companyData = Helper::getCompany($companyId);
				$total_employees = User::join('master_users', 'master_users.m_user_id', '=', 'users.id')->where('master_users.m_company_id',$companyId)->count();
            }
            $usersType =Helper::usersType();
            unset($usersType[1]);
            return view('admin.employees.index',compact('companyId','companyData','usersType','total_employees'));
        }catch(Exception $e){
            abort(404);
        }
    }
	
	
    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData(Request $request)
    {
        // $employees = User::select(['id', 'name','designation', 'type','mobileno', 'email','organization_id'])->with(['organizations']);

        $employees = User::join('master_users', 'master_users.m_user_id', '=', 'users.id');

        if($request->company_id !=0){
            $employees->where('master_users.m_company_id',$request->company_id);
        }else{
            $employees->where('master_users.m_company_id',$request->company_id);
        }
        if($request->user_type){
            $employees->where('master_users.m_type',$request->user_type);
        }

        $employees = $employees->orderBy('master_users.id', 'desc')->get();
        
        return Datatables::of($employees)
            ->addColumn('namelink', function ($employees) {
                return $employees->name;
            })
            ->addColumn('type', function ($employees) {
                return Helper::usersType($employees->type);
            })
			->addColumn('commission', function ($employees) {
				return $employees->commission .' %';
            })
            ->addColumn('action', function ($employees) use($request) {
				$html = $edit_btn = $delete_btn = $permissions_btn =  '';

				$edit_btn = '<a href="'.route('admin.employees.edit', $employees->m_user_id).'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>';

                $delete_btn = '<a class="btn btn-link employee-delete" data-toggle="tooltip" title="Delete" data-id='.$employees->m_user_id.'><i class="flaticon2-trash text-danger"></i></a>';
               
                $permissions_btn = '<a href="'.url('rkadmin/employees/permission/'.$employees->m_user_id).'" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Permissions">Permissions</a>';

                $html .= $edit_btn;
                $html .= $delete_btn;
                // if($request->company_id !=0){
                //     $html .= $permissions_btn;
                // }
                    
                return $html;
            })
            ->rawColumns(['namelink','designation', 'mobileno', 'email', 'action'])
            ->make(true);
    }
	
	 /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create($companyId = null)
    {
        try{
            $companyData = null;
            if($companyId){
                $companyId = decrypt($companyId);
                $companyData = Helper::getCompany($companyId);
            }
            // $states = State::where('country_id', '101')->where('deleted', 0)->pluck('name', 'state_id');
            $countries = Country::where('deleted', 0)->pluck('name', 'country_id');
            $roleslist = Role::where('deleted', 0);
            if(!$companyId){
                $roleslist->where('guard_name','web');
            }else{
                $roleslist = $roleslist->where('company_id',$companyId);
            }
            $roleslist = collect($roleslist->get())->pluck('name', 'id');
            $clients = Company::pluck('company_name', 'id');
            $bloodGroup = $this->getBloodgGroup();
            /* $cast = Cast::pluck('name', 'id');
            $cast->prepend('Please Select cast', ''); */
			$cast = array(''=>'Please select cast');
            $clients->prepend('Please Select client', '');
            // $states->prepend('Please Select state', '');
            $roleslist->prepend('Please Select role', '');
            $countries->prepend('Please Select country', '');

            $usersType =Helper::usersType();
            unset($usersType[1]);

            //$states = DB::select(DB::raw("SELECT `state` FROM `country_pincode` GROUP BY state"));
            $states_ar = array();
            /* foreach($states as $state){
                $states_ar[$state->state] = $state->state;
            } */
			$isProfile = 0;

            return view('admin.employees.create')
                //->withUsers(User::with('department')->get()->pluck('nameAndDepartmentEagerLoading', 'id'))
                ->withClients($clients)
                ->withRoleslist($roleslist)
                ->withCountries($countries)
                ->withCast($cast)
                ->withBloodGroup($bloodGroup)
                ->withRelations($this->relationName())
                ->withStates($states_ar)
                ->withCompanyId($companyId)
                ->withCompanyData($companyData)
                ->withUsersType($usersType)
                ->withIsProfile($isProfile);
        }catch(Exception $e){
            abort(404);
        }
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
		$isProfile = $request->isProfile;
		
        if($action == 'add'){
            $email_valid = 'required|email|max:50|unique:users,email';
        }else{
            $email_valid = 'required|email|max:50|unique:users,email,'. $id;
        }

        $validation = Validator::make($request->all(), [
            'name' => 'required|string|regex:/^[A-Za-z0-9 ]+$/u|max:50',
            'email' => $email_valid,
            'mobileno' => 'required|digits:10',
            'alt_mobileno' => 'nullable|digits:10',
            'emergency_no' => 'nullable|digits:10',
            'designation' => 'string|nullable|max:15',
            'date_of_birth' => 'date|nullable|date_format:Y-m-d',
            'marriage_anniversary_date' => 'date|nullable|date_format:Y-m-d|before_or_equal:today',
            'address_line_1' => 'string|nullable',
            'address_line_2' => 'string|nullable',
            'country_id' => 'integer|nullable',
            'state_id' => 'nullable|string',
            'state_name' => 'nullable|string',
            'pincode' => 'nullable',
            'pincode_txt' => 'nullable',
            'city' => 'nullable|string',   
            'city_txt' => 'nullable|string',            
            'facebook' => 'url|string|nullable',
            'twitter' => 'url|string|nullable',
            'instagram' => 'url|string|nullable',
            'youtube' => 'url|string|nullable',
            'website' => 'url|string|nullable',
            'driving_licence_no' => 'nullable|max:16',
            'pan_no' => 'nullable|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}+$/u|max:10',
            'aadhar_no' => 'nullable|digits:12',
            'commission' => 'nullable',
        ]);
        
        if(isset($request->driving_licence_no)){
            if(!preg_match("/^(([A-Z]{2}[0-9]{2})( )|([A-Z]{2}-[0-9]{2}))((19|20)[0-9][0-9])[0-9]{7}+$/u",$request->driving_licence_no)) {
                $validation->getMessageBag()->add('driving_licence_no', 'The driving licence no format is invalid');
            }
        }
       
        if(isset($request->bank_data)){
            
            if(isset($request->bank_data[0]['bank_name'])){
                if(!preg_match("/^[A-Za-z ]+$/u",$request->bank_data[0]['bank_name'])) {
                    $validation->getMessageBag()->add('bank_name', 'Invalid bank name');
                }
            }
            if(isset($request->bank_data[0]['account_number'])){
                if(!preg_match("/^[0-9]{0,20}+$/u",$request->bank_data[0]['account_number'])) {
                    $validation->getMessageBag()->add('account_number', 'Invalid account number');
                }
            }
            if(isset($request->bank_data[0]['ifsc_code'])){
                if(!preg_match("/^[a-zA-Z0-9]{0,20}+$/u",$request->bank_data[0]['ifsc_code'])) {
                    $validation->getMessageBag()->add('ifsc_code', 'Invalid ifsc code number');
                }
            }
        }

        if(isset($request->password)){

            $password = $request->password;

            $uppercase = preg_match('@[A-Z]@', $password);
            $lowercase = preg_match('@[a-z]@', $password);
            $number    = preg_match('@[0-9]@', $password);
            $specialChars = preg_match('@[^\w]@', $password);

            if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
                $validation->getMessageBag()->add('password', 'Password should be at least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.');
            }
        }
        
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }

        $organization_id = 0;

        if($request->company_id){
            $companyData        = Company::find($request->company_id);
            $organization_id    = $companyData->client_id;
        }
               
        if ($action == 'add') {
            if($request->company_id){
                $checkLimit = Helper::checkUserLimit($request->company_id);
                if($checkLimit['status'] == 0)
                {
                    return redirect()->back()->withInput()->with('error', 'Your plan limit is reached');
                }
            }
			$row = User::where([
                ['email', '=', $request->email],
                ['organization_id', '=', $organization_id],
            ])->first();

            if (!empty($row)) {
                return redirect()->back()->with('error', 'The Email Address already associated with another user.')->withInput();
            }

            $row1 = User::where([
                ['mobileno', '=', $request->mobileno],
				['organization_id', '=', $organization_id],
            ])->first();

            if (!empty($row1)) {
                return redirect()->back()->with('error', 'The Mobile Number already associated with another user.')->withInput();
            }

            if($request->company_id){
                $msg_user = 'Employee';
            }else{
                $msg_user = 'User';
            }

			$users = new User;
            $message = $msg_user.' has been added successfully';
            $picture = '';
            $userimage = '';
        } else {
            $users = User::findOrFail($id);
            if($request->company_id && ($users->company_id != $request->company_id))
            {
                $checkLimit = Helper::checkUserLimit($request->company_id);
                if($checkLimit['status'] == 0)
                {
                    return redirect()->back()->withInput()->with('error', 'Your plan limit is reached');
                }
                if($checkLimit['status'] == 2)
                {
                    return redirect()->back()->withInput()->with('error', "Please select first company's plan");
                }
            }
            if($request->company_id){
                $msg_user = 'Employee';
            }else{
                $msg_user = 'User';
            }
			$message = $msg_user.' has been updated successfully';

			//Check duplicate value
            if ($users->email !== $request->email) {
                $row = User::where([
                    ['id', '!=', $id],
					['organization_id', '=', $organization_id],
                    ['email', '=', $request->email],
                ])->first();

                if (!empty($row)) {
                    return redirect()->back()->with('error', 'The email address already associated with another user.')->withInput();
                }
            }

            if($users->mobileno != $request->mobileno){
                $row = User::where([
                    ['id', '!=', $id],
					['organization_id', '=', $organization_id],
                    ['mobileno', '=', $request->mobileno],
                ])->first();

                if (!empty($row)) {
                    return redirect()->back()->with('error', 'The Mobile number already associated with another user.')->withInput();
                }
            }
			$userimage = $users->picture;
        }
		
		if ($request->hasFile('picture')) {
			 if ($request->file('picture')->isValid()) {
				 $validated = $request->validate([
                    'name' => 'string|max:40',
                    'picture' => 'mimes:jpeg,jpg,png|max:4098',
                ]);
				 $extension = $request->picture->extension();
				 $imagePath = $request->file('picture');
				 $imageName = $imagePath->getClientOriginalName();
				 $imageName = time().$imageName;//.'.'.$request->image->extension();  
				 $userimage = $imageName;
				 $imageName = $request->picture->move(public_path('/storage/images'), $imageName);
				 $picture = $userimage;
			 }
        }

		
		$users->organization_id = !empty($companyData->client_id) ? $companyData->client_id : 0;
		$users->company_id = !empty($companyData->id) ? $companyData->id : 0;
        if(!$isProfile){
        $users->type = !empty($companyData) ? 2 : $request->type;
        $users->parent_id = Auth::user()->id;
		}
		if(!$isProfile)
		$users->company_contact_type = $request->company_contact_type;
		$users->name = $request->name;
		$users->email = $request->email;
		$users->dob = $request->date_of_birth;
		$users->mobileno  = $request->mobileno;
		$users->alt_mobileno  = $request->alt_mobileno;
		$users->designation  = $request->designation;
		$users->gender = $request->gender;
		$users->address_line_1 = $request->address_line_1;
		$users->address_line_2 = $request->address_line_2;
		$users->landmark = $request->landmark;
		$users->country_id  = $request->country_id;
		$users->state_id = $request->state_id;
		$users->city = $request->city;

        if($request->country_id == 101){
            $users->pincode = $request->pincode;
        }else{
            /* $users->state_id = $request->state_name;
            $users->city = $request->city_txt; */
            $users->pincode = $request->pincode_txt;
        }
		
		$users->facebook = $request->facebook;
		$users->twitter = $request->twitter;
		$users->instagram = $request->instagram;
		$users->website = $request->website;
		$users->youtube = $request->youtube;
		$users->picture = $userimage;
		$users->cast_name = $request->cast_name;
		$users->marital_status = $request->marital_status;
		$users->blood_group = $request->blood_group;
		$users->critical_illness = $request->critical_illness;
		$users->legal_issue = $request->legal_issue;
		$users->other_activity = $request->other_activity;
		$users->emergency_no = $request->emergency_no;
		$users->marriage_anniversary_date = $request->marriage_anniversary_date;
		$users->driving_licence_no = $request->driving_licence_no;
		$users->aadhar_no = $request->aadhar_no;
		$users->pan_no = $request->pan_no;
        $users->commission = $request->commission ? $request->commission : 0;
        $users->education_details = $request->education_data ? json_encode($request->education_data) : null;
		$users->family_details = $request->family_detail ? json_encode($request->family_detail) : null;
		$users->previous_employer_details = $request->previous_employer ? json_encode($request->previous_employer) : null;
		$users->job_details = $request->job_data ? json_encode($request->job_data) : null;
		$users->bank_details = $request->bank_data ? json_encode($request->bank_data) : null;
		
		if(!$isProfile){
		if($request->isLocked && $users->lockedDate == null){
			$users->isLocked = $request->isLocked;
			$users->lockedDate = date("Y-m-d H:i:s");
		}elseif(!$request->isLocked && $users->lockedDate != null){
			$users->isLocked = 0;
			$users->lockedDate = null;
		}
		}
		
        $password = 'admin123';
        if ($action == 'add')
            $password = 'admin123';
		if (trim($request->password) != '') {
            $password = ($request->password);
        }
        if (trim($password) != '') {
            $users->password = (!$request->password) && $users->password ? $users->password : \Hash::make($password);
        }
        $users->save();
		if(!$isProfile){
        if($request->role_id){
            $users->syncRoles([$request->role_id]);
            // if($users->hasRole($request->role_id))
            //  $users->removeRole($users->roles->first());
            //  $users->assignRole($request->role_id);
		}
		}

        if ($action == 'add'){
            $master_user = new MasterUser;
            $master_user->m_type = $users->type;
            $master_user->m_user_id	 = $users->id;
            $master_user->m_company_id	= $users->company_id;
            $master_user->m_client_id	= $users->organization_id;
            
            if(Auth::user()->type == 3 || Auth::user()->type == 4){
                $master_user->m_dealer_distributor_id = $users->parent_id;
            }
            $master_user->save();
        }
		if(!$isProfile){
        $url = $users->company_id ? encrypt($users->company_id) : '';
        return redirect(url('rkadmin/employees/'.$url))->with('success', $message);
		}else{
		$message = 'Profile updated successfully';
		return redirect(route('admin.profile'))->with('success', $message);	
		}
    }
	
	
    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return mixed
     */
    public function show($id)
    {

		$client = User::with('industry_type','company_type','state','country')->find($id);
        //dd($client->appointments);
        return view('admin.employees.show')
            ->withUser($client)
            ->withCompanyname(Setting::first()->company)
            ->withInvoices($this->getInvoices($client))
            ->withUsers(User::with('department')->get()->pluck('nameAndDepartmentEagerLoading', 'id'))
            ->with('filesystem_integration', Integration::whereApiType('file')->first())
            ->with('documents', $client->documents()->where('integration_type', get_class(GetStorageProvider::getStorage()))->get())
            ->with('lead_statuses', Status::typeOfLead()->get())
            ->with('task_statuses', Status::typeOfTask()->get())
            ->withRecentAppointments($client->appointments()->orderBy('start_at', 'desc')->where('end_at', '>', now()->subMonths(3))->limit(7)->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return mixed
     */
    public function edit($id)
    {
        try{
            $employee = User::find($id);
            // $states = State::where('country_id', '101')->where('deleted', 0)->pluck('name', 'state_id');
            $countries = Country::where('deleted', 0)->pluck('name', 'country_id');
			if($employee->cast_name){
				$cast = Cast::where('name','=',$employee->cast_name)->pluck('name','name');
				$cast->prepend('Please Select cast', '');
			}
			else{
				$cast = array(''=>'Please select cast');
			}
			
            $bloodGroup = $this->getBloodgGroup();
            $roleslist = Role::where('deleted', 0);
            
            if(!$employee->company_id){
                $roleslist->where('guard_name','web');
            }else{
                $roleslist = $roleslist->where('client_id',$employee->organization_id)->where('company_id',$employee->company_id);
            }
            $roleslist = collect($roleslist->get())->pluck('name', 'id');
            $usersType = Helper::usersType();
            unset($usersType[1]);

            $role = $employee->roles->pluck('id');
            $clients = Company::pluck('company_name', 'id');
            $clients->prepend('Please Select client', '');
            // $states->prepend('Please Select state', '');
            $roleslist->prepend('Please Select role', '');
            $countries->prepend('Please Select country', '');
            $familyData = json_decode($employee->family_details);
            $previousEmployerData = json_decode($employee->previous_employer_details);
            $jobDetails = json_decode($employee->job_details);
            $educationDetails = json_decode($employee->education_details);
            $bankDetails = json_decode($employee->bank_details);
            //$states = DB::select(DB::raw("SELECT `state` FROM `country_pincode` GROUP BY state"));
            $states = State::where('country_id',$employee->country_id)->orderBy('name', 'ASC')->get();
            $states_ar = array();
            foreach($states as $state){
                $states_ar[$state->name] = $state->name;
            }
			$isProfile = 0;
            return view('admin.employees.edit')
                ->withEmployee($employee)
                ->withFamilyData($familyData)
                ->withJobData($jobDetails)
                ->withBankData($bankDetails)
                ->withEducationData($educationDetails)
                ->withPreviousEmployerData($previousEmployerData)
                ->withClients($clients)
                ->withRole($role)
                ->withBloodGroup($bloodGroup)
                ->withCast($cast)
                ->withRelations($this->relationName())
                ->withRoleslist($roleslist)
                ->withCountries($countries)
                ->withStates($states_ar)
                ->withCompanyId($employee->company_id)
                ->withUsersType($usersType)
				->withIsProfile($isProfile);
        }catch(Exception $e){
            abort(404);
        }
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
        $client = User::findOrFail($id);
		$client->delete();
        MasterUser::where('m_user_id', $id)->delete();
        Session::flash('success','Employee deleted successfully');
        return true;
    }

    public function relationName()
    {
        $type = collect(['F'=>'Father','M'=>'Mother','H'=>'Husband','W'=>'Wife','D'=>'Daughter','S'=>'Son']);
		$type->prepend('Please Select Relation', '');
        return $type;
    }

    public function getBloodgGroup()
    {
        return [''=>'Please select blood group','A+'=>'A+','A-'=>'A-','B+'=>'B+','B-'=>'B-','O+'=>'O+','O-'=>'O-','AB+'=>'AB+','AB-'=>'AB-'];
    }

    public function getPermission($id)
    {
        $user = User::find($id);
        if(!$user){
            abort(404);
        }
        $roleData = [];
        if($user->organization_id == 0 and $user->company_id == 0 and ($user->type == 1 || $user->type == 2 || $user->type == 3 || $user->type == 4)){
            $type = 'web';
        }else{
            $type = 'api';
        }

        $permissions = collect(Permission::where('guard_name',$type)->orderby('name')->get())->map(function($q) use(&$roleData){
            $name = explode(': ',$q->name);
            $nameValue = $name[0];
            if(!empty($name[1]))
            {
                $roleData[$name[0]][$q->id] = $name[1];
            }
            return $roleData;
        });
        $rolePermissions = \DB::table("model_has_permissions")->where("model_id",$user->id)
                        ->pluck('permission_id')
                        ->toArray();
        return view('admin.employees.permission',compact('roleData','user','rolePermissions'));
    }

    public function storePermission(Request $request)
    {
        $userId = $request->user_id;
        $user = User::find($userId);
        $permissionId = $request->permission;
        if(!empty($permissionId)){
            $user->syncPermissions($permissionId);
        }
        if($user->company_id!=0){
            return redirect('rkadmin/employees/'.encrypt($user->company_id))->with('success', 'User permission added successfully');
        }else{
            return redirect('rkadmin/employees/')->with('success', 'User permission added successfully');
        }
    }

    public function invoice()
    {
        $user = Auth::user();
        if(isset($user->invoice_template)){
            $template = $user->invoice_template;
        }else{
            $template = Helper::defaultInvoiceTemplate();    
        }
        $template_content = Helper::invoiceTemplateBody();

        $invoice_template = str_replace("{{#template_content}}", $template, $template_content);

        return view('admin.employees.invoice',compact('invoice_template'));
    }

    public function invoiceUpdate(Request $request)
    {
        $id = Auth::user()->id;
        $user =  User::find($id);
        $user->invoice_template = $request->invoice_template;
        $user->save();
        return redirect('rkadmin/invoice/')->with('success','Invoice template is successfully updated');
    }
	
	public function getProfilePage(){
		$user = Auth::user();
		$countries = Country::where('deleted', 0)->pluck('name', 'country_id');
		if($user->cast_name){
			$cast = Cast::where('name','=',$user->cast_name)->pluck('name');
			$cast->prepend('Please Select cast', '');
		}
		else{
			$cast = array(''=>'Please select cast');
		}
		
		$bloodGroup = $this->getBloodgGroup();
		$roleslist = Role::where('deleted', 0);
		
		if(!$user->company_id){
			$roleslist->where('guard_name','web');
		}else{
			$roleslist = $roleslist->where('client_id',$user->organization_id)->where('company_id',$user->company_id);
		}
		$roleslist = collect($roleslist->get())->pluck('name', 'id');
		$usersType = Helper::usersType();
		unset($usersType[1]);

		$role = $user->roles->pluck('id');
		$clients = Company::pluck('company_name', 'id');
		$clients->prepend('Please Select client', '');
		// $states->prepend('Please Select state', '');
		$roleslist->prepend('Please Select role', '');
		$countries->prepend('Please Select country', '');
		$familyData = json_decode($user->family_details);
		$previousEmployerData = json_decode($user->previous_employer_details);
		$jobDetails = json_decode($user->job_details);
		$educationDetails = json_decode($user->education_details);
		$bankDetails = json_decode($user->bank_details);
		//$states = DB::select(DB::raw("SELECT `state` FROM `country_pincode` GROUP BY state"));
		$states = State::where('country_id',$user->country_id)->get();
		$states_ar = array();
		foreach($states as $state){
			$states_ar[$state->state] = $state->state;
		}
		$isProfile = 1;
		return view('admin.employees.profile')
			->withEmployee($user)
			->withFamilyData($familyData)
			->withJobData($jobDetails)
			->withBankData($bankDetails)
			->withEducationData($educationDetails)
			->withPreviousEmployerData($previousEmployerData)
			->withClients($clients)
			->withRole($role)
			->withBloodGroup($bloodGroup)
			->withCast($cast)
			->withRelations($this->relationName())
			->withRoleslist($roleslist)
			->withCountries($countries)
			->withStates($states_ar)
			->withCompanyId($user->company_id)
			->withUsersType($usersType)
			->withIsProfile($isProfile);
	}
	
	public function profileupdate($id, Request $request)
    {
		return $this->post_process('profile', $id, $request);
    }
}
