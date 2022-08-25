<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\MasterUser;
use App\Models\EmployeeSection;
use App\Models\EmployeeField;
use App\Models\EmployeeValue;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class EmployeesController extends Controller
{
    public function getAllEmployees(Request $request)
    {
		$user = JWTAuth::parseToken()->authenticate();
        $employees = User::where('organization_id', '=', $user->organization_id)->where('company_id',$user->company_id)->where('type',2);
        if($request->is_calendar){
            $employees = $employees->select('id','name as label','name as value');
        }
        $roleId = $request->role_id;
        if($roleId){
            $employees = User::where('type',2)->whereHas("roles", function($q) use($roleId){ $q->where("id", $roleId); });
        }
        $employees = $employees->get();
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('employees')
        ]);
    }

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		$user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $query = $request->searchTxt;
        $paginationData = Helper::paginationData($request);
		//\DB::enableQueryLog();

        $paginated = User::where('organization_id', '=', $user->organization_id)
            ->where('company_id',$user->company_id)
            ->where('type',2)
			->where('name', 'LIKE', "%" . $query . "%")
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size);
            foreach($paginated as $row)
            {
                $row->bank_details = $row->bank_details ? json_decode($row->bank_details) : null;
                $row->education_details = $row->education_details ? json_decode($row->education_details) : null;
                $row->job_details = $row->job_details ? json_decode($row->job_details) : null;
                $row->family_details = $row->family_details ? array_values(json_decode($row->family_details,true)) : [];
                $row->previous_employer_details = $row->previous_employer_details ? array_values(json_decode($row->previous_employer_details,true)) : [];
            }
        $employees = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();
//$qry = \DB::getQueryLog();
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('employees', 'totalRecord', 'current')
        ]);
    }

	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|regex:/^[A-Za-z0-9 ]+$/u|max:50',
            'email' => 'required|email',
            'mobileno' => 'required|digits:10',
            'alt_mobileno' => 'nullable',
            'designation' => 'string|nullable',
            'gender' => 'string|nullable',
            'dob' => 'nullable|date_format:Y-m-d',
            'address_line_1' => 'string|nullable',
            'address_line_2' => 'string|nullable',
            'country_id' => 'integer|nullable',
            'state_id' => 'nullable|string',
            'pincode' => 'nullable|digits:6',
            'city' => 'nullable|string',
            'facebook' => 'string|nullable',
            'twitter' => 'string|nullable',
            'instagram' => 'string|nullable',
            'website' => 'string|nullable',
        ],
		[
            'name' => 'Employee Name Required.'
        ]);

        if(isset($request->password)){
            $password = $request->password;
            $uppercase = preg_match('@[A-Z]@', $password);
            $lowercase = preg_match('@[a-z]@', $password);
            $number    = preg_match('@[0-9]@', $password);
            $specialChars = preg_match('@[^\w]@', $password);
            if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
                $validator->getMessageBag()->add('password', 'Password should be at least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.');
            }
        }

		if (isset($request->picture) && isset($request->picture['base64'])) {
            $pathinfo = pathinfo($request->picture['name']);
            if(!in_array($pathinfo['extension'], array('jpeg','jpg','png'))){
                $validator->getMessageBag()->add('picture', 'The picture must be a file of type: jpeg, jpg, png.');
            }
        }

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }


		$row = User::where([
			['email', '=', $request->email],
			['organization_id', '=', $user->organization_id],
			['company_id', '=', $user->company_id],
		])->first();

		if (!empty($row)) {
			return response()->json([
                'status' => 'FAIL',
                'message' => 'The Email Address already associated with another user.'
            ]);

		}

		$row1 = User::where([
			['mobileno', '=', $request->mobileno],
			['organization_id', '=', $user->organization_id],
			['company_id', '=', $user->company_id],
		])->first();

		if (!empty($row1)) {
			return response()->json([
                'status' => 'FAIL',
                'message' => 'The Mobile Number already associated with another user.'
            ]);
		}
        $checkLimit = Helper::checkUserLimit($user->company_id);
        if($checkLimit['status'] == 0)
        {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Your plan limit is reached.'
            ]);
        }
		$employee = new User;
		$employee->organization_id = (int)$user->organization_id;
        $employee->company_id = (int)$user->company_id;
		$employee->parent_id = $user->id;
        $employee->name = $request->name;
		$employee->email = $request->email;
		$employee->mobileno  = $request->mobileno;
		$employee->alt_mobileno  = $request->alt_mobileno;
		$employee->designation  = $request->designation;
		$employee->gender = $request->gender;
        $employee->dob = $request->dob;
		$employee->address_line_1 = $request->address_line_1;
		$employee->address_line_2 = $request->address_line_2;
		$employee->landmark = $request->landmark;

		$employee->country_id  = $request->country_id;
        $employee->state_id = $request->state_id;
        $employee->city     = $request->city;
        $employee->pincode  = $request->pincode;

		$employee->facebook = $request->facebook;
		$employee->twitter = $request->twitter;
		$employee->instagram = $request->instagram;
		$employee->website = $request->website;
        $employee->cast_name = $request->cast_id;
		$employee->marital_status = $request->marital_status;
		$employee->blood_group = $request->blood_group;
		$employee->critical_illness = $request->critical_illness;
		$employee->legal_issue = $request->legal_issue;
		$employee->other_activity = $request->other_activity;
		$employee->emergency_no = $request->emergency_no;
		$employee->marriage_anniversary_date = $request->marriage_anniversary_date;
		$employee->driving_licence_no = $request->driving_licence_no;
		$employee->aadhar_no = $request->aadhar_no;
		$employee->pan_no = $request->pan_no;
        $educationData = $request->education_data;
        $jobData = $request->job_data;
        $bankData = $request->bank_data;
        $employee->education_details = !empty($educationData) ? json_encode($educationData) : null;
        $employee->job_details = !empty($jobData) ? json_encode($jobData) : null;
        $employee->bank_details = !empty($bankData) ? json_encode($bankData) : null;
		$employee->family_details = $request->family_detail ? json_encode($request->family_detail) : null;
		$employee->previous_employer_details = $request->previous_employer ? json_encode($request->previous_employer) : null;
        /* if ($request->hasFile('picture')) {
            if ($request->file('picture')->isValid()) {
                $validated = $request->validate([
                    'namane' => 'string|max:40',
                    'picture' => 'mimes:jpeg,jpg,png|max:4098',
                ]);
                $imagePath = $request->file('picture');
                $imageName = $imagePath->getClientOriginalName();
                $imageName = time().$imageName;
                $employee->picture = $imageName;
                $imageName = $request->picture->move(public_path('/storage/images'), $imageName);
            }
       } */
	   if (isset($request->picture) && isset($request->picture['base64'])) {
			$imageName = Helper::createImageFromBase64($request->picture['base64']);
			$employee->picture = $imageName;
		}
		if (trim($request->password) != '') {
			$employee->password = \Hash::make($request->password);
		}
		$employee->save();
		if($request->roles){
		//if(!$employee->hasRole($request->roles))
		// $employee->assignRole($request->roles);
        $employee->syncRoles([$request->roles]);
		}

        $master_user = new MasterUser;
        $master_user->m_type = 2;
        $master_user->m_user_id	 = $employee->id;
        $master_user->m_company_id	= $employee->company_id;
        $master_user->m_client_id	= $employee->organization_id;

        $master_user->m_dealer_distributor_id = $employee->parent_id;
        $master_user->save();

		$sectionIds = EmployeeSection::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->pluck('id');
        $fieldData = EmployeeField::whereIn('section_id',$sectionIds)->get();
		if(count($fieldData) != 0)
        {
			foreach($fieldData as $key=>$row)
			{
				$valueData[$key]['name'] = $row->label_name;
				$valueData[$key]['value'] = json_encode($request->get($row->label_name));
				$valueData[$key]['field_id'] = $row->id;
				$valueData[$key]['client_id'] = $user->organization_id;
				$valueData[$key]['company_id'] = $user->company_id;
				$valueData[$key]['employee_id'] = $employee->id;
				$valueData[$key]['created_by'] = $user->id;
				$valueData[$key]['created_at'] = date('Y-m-d H:i:s');
				$valueData[$key]['updated_at'] = date('Y-m-d H:i:s');
			}
			if(!empty($valueData)){
				/* $employeeId = $employee->id;
				$valueData = array_map(function($q) use($employeeId){
					$q['employee_id'] = $employeeId;
					return $q;
				}, $valueData); */
				$fieldModelData = EmployeeValue::insert($valueData);
			}
		}

        //Add Action Log
        Helper::addActionLog($user->id, 'EMPLOYEE', $employee->id, 'CREATEEMPLOYEE', [], $employee->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Employee has been created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		$user = JWTAuth::parseToken()->authenticate();
        $employee = User::where('organization_id',$user->organization_id)->where('company_id',$user->company_id)->where('type',2)->find($id);
        if ($employee) {
            $employee->bank_details = $employee->bank_details ? json_decode($employee->bank_details) : null;
            $employee->education_details = $employee->education_details ? json_decode($employee->education_details) : null;
            $employee->job_details = $employee->job_details ? json_decode($employee->job_details) : null;
            $employee->family_details = $employee->family_details ? array_values(json_decode($employee->family_details,true)) : [];
            $employee->previous_employer_details = $employee->previous_employer_details ? array_values(json_decode($employee->previous_employer_details,true)) : [];
			/* if(($employee->hasAnyRole())) */
			$role = $employee->roles->pluck('id')->toArray();
			if($employee->picture)
				$employee->picture = 'images/'.$employee->picture;
			$employee->cast_id = $employee->cast_name;
			if(!empty($role))
			{
				$employee->role_id = $role[0];
			}else{
				$employee->role_id = 0;
			}
			$fieldData = collect(EmployeeValue::where('employee_id',$employee->id)->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->get())->map(function($q) use($employee){
							$employee[$q->name] = json_decode($q->value);
							return $employee;
						});
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('employee')//
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Employee not found."
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please refresh the page.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|string|regex:/^[A-Za-z0-9 ]+$/u|max:50',
            'email' => 'required|email',
            'mobileno' => 'required|digits:10',
            'alt_mobileno' => 'nullable|digits:10',
            'emergency_no' => 'nullable|digits:10',
            'designation' => 'string|nullable',
            'gender' => 'string|nullable',
            'dob' => 'nullable|date_format:Y-m-d',
            'marriage_anniversary_date' => 'date|nullable|date_format:Y-m-d|before_or_equal:today',
            'address_line_1' => 'string|nullable',
            'address_line_2' => 'string|nullable',
            'country_id' => 'integer|nullable',
            'state_id' => 'nullable|string',
            'city' => 'nullable|string',
            'pincode' => 'nullable|digits:6',
            'facebook' => 'url|string|nullable',
            'twitter' => 'url|string|nullable',
            'instagram' => 'url|string|nullable',
            'website' => 'url|string|nullable',
            'pan_no' => 'nullable',
            'driving_licence_no' => 'nullable',
            'aadhar_no' => 'nullable',
        ], [
            'name' => 'Employee Name Required.'
        ]);

        if(isset($request->password)){
            $password = $request->password;
            $uppercase = preg_match('@[A-Z]@', $password);
            $lowercase = preg_match('@[a-z]@', $password);
            $number    = preg_match('@[0-9]@', $password);
            $specialChars = preg_match('@[^\w]@', $password);
            if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
                $validator->getMessageBag()->add('password', 'Password should be at least 8 characters in length and should include at least 1 upper case letter, 1 number, and 1 special character.');
            }
        }

		if (isset($request->picture) && isset($request->picture['base64'])) {
            $picturePathinfo = pathinfo($request->picture['name']);
            if(!in_array($picturePathinfo['extension'], array('jpeg','jpg','png'))){
                $validator->getMessageBag()->add('picture', 'The picture must be a file of type: jpeg, jpg, png.');
            }
        }

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }
		$employee = User::where('organization_id',$user->organization_id)->where('company_id',$user->company_id)->findOrFail($id);
		//Check duplicate value
		if ($employee->email !== $request->email) {
			$row = User::where([
				['id', '!=', $id],
				['organization_id', '=', (int)$user->organization_id],
				['company_id', '=', (int)$user->company_id],
				['email', '=', $request->email],
			])->first();

			if (!empty($row)) {
				return response()->json([
					'status' => 'FAIL',
					'message' => 'The email address already associated with another employee.'
				]);
			}
		}

		if($employee->mobileno != $request->mobileno){
			$row = User::where([
				['id', '!=', $id],
				['organization_id', '=', (int)$user->organization_id],
				['company_id', '=', (int)$user->company_id],
				['mobileno', '=', $request->mobileno],
			])->first();

			if (!empty($row)) {
				return response()->json([
					'status' => 'FAIL',
					'message' => 'The Mobile number already associated with another employee.'
				]);

			}
		}

        if ($employee) {
            $oldData = $employee->toArray();
			$employee->organization_id = (int)$user->organization_id;
			$employee->company_id = (int)$user->company_id;
			$employee->name = $request->name;
			$employee->email = $request->email;
			$employee->mobileno  = $request->mobileno;
			$employee->alt_mobileno  = $request->alt_mobileno;
			$employee->designation  = $request->designation;
			$employee->gender = $request->gender;
            $employee->dob = $request->dob;
			$employee->address_line_1 = $request->address_line_1;
			$employee->address_line_2 = $request->address_line_2;
			$employee->landmark = $request->landmark;

            $employee->country_id  = $request->country_id;
            $employee->state_id = $request->state_id;
            $employee->city     = $request->city;
            $employee->pincode  = $request->pincode;

			$employee->facebook = $request->facebook;
			$employee->twitter = $request->twitter;
			$employee->instagram = $request->instagram;
			$employee->website = $request->website;
			$employee->youtube = $request->youtube;
            $employee->cast_name = $request->cast_id;
            $employee->marital_status = $request->marital_status;
            $employee->blood_group = $request->blood_group;
            $employee->critical_illness = $request->critical_illness;
            $employee->legal_issue = $request->legal_issue;
            $employee->other_activity = $request->other_activity;
            $employee->emergency_no = $request->emergency_no;
            $employee->marriage_anniversary_date = $request->marriage_anniversary_date;
            $employee->driving_licence_no = $request->driving_licence_no;
            $employee->aadhar_no = $request->aadhar_no;
            $employee->pan_no = $request->pan_no;
            $educationData = $request->education_data;
            $jobData = $request->job_data;
            $bankData = $request->bank_data;
            $employee->education_details = !empty($educationData) ? json_encode($educationData) : null;
            $employee->job_details = !empty($jobData) ? json_encode($jobData) : null;
            $employee->bank_details = !empty($bankData) ? json_encode($bankData) : null;
            $employee->family_details = $request->family_detail ? json_encode($request->family_detail) : null;
            $employee->previous_employer_details = $request->previous_employer ? json_encode($request->previous_employer) : null;
            /* if ($request->hasFile('picture')) {
                if ($request->file('picture')->isValid()) {
                    $validated = $request->validate([
                        'name' => 'string|max:40',
                        'picture' => 'mimes:jpeg,jpg,png|max:4098',
                    ]);
                    $imagePath = $request->file('picture');
                    $imageName = $imagePath->getClientOriginalName();
                    $imageName = time().$imageName;
                    $employee->picture = $imageName;
                    $imageName = $request->picture->move(public_path('/storage/images'), $imageName);
                }
           } */
		   if (isset($request->picture) && isset($request->picture['base64'])) {
				$imageName = Helper::createImageFromBase64($request->picture['base64']);
				$employee->picture = $imageName;
			}
			if (trim($request->password) != '') {
				$employee->password = \Hash::make($request->password);
			}

			$employee->save();
			if($request->roles){
                // $user->roles()->detach();
                // $employee->assignRole($request->roles);
                $employee->syncRoles([$request->roles]);
			}

			$sectionIds = EmployeeSection::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->pluck('id');
        $fieldData = EmployeeField::whereIn('section_id',$sectionIds)->get();
		if(count($fieldData) != 0)
        {
			EmployeeValue::where('employee_id',$employee->id)->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->delete();
			foreach($fieldData as $key=>$row)
			{
				$valueData[$key]['name'] = $row->label_name;
				$valueData[$key]['value'] = json_encode($request->get($row->label_name));
				$valueData[$key]['field_id'] = $row->id;
				$valueData[$key]['client_id'] = $user->organization_id;
				$valueData[$key]['company_id'] = $user->company_id;
				$valueData[$key]['employee_id'] = $employee->id;
				$valueData[$key]['created_by'] = $user->id;
				$valueData[$key]['created_at'] = date('Y-m-d H:i:s');
				$valueData[$key]['updated_by'] = $user->id;
				$valueData[$key]['updated_at'] = date('Y-m-d H:i:s');
			}
			if(!empty($valueData)){
				/* $employeeId = $employee->id;
				$valueData = array_map(function($q) use($employeeId){
					$q['employee_id'] = $employeeId;
					return $q;
				}, $valueData); */
				$fieldModelData = EmployeeValue::insert($valueData);
			}
		}

            //Add Action Log
            Helper::addActionLog($user->id, 'EMPLOYEE', $employee->id, 'UPDATEEMPLOYEE', $oldData, $employee->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Employee has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Employee not found."
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $employee = User::where('organization_id','=',$user->organization_id)->where('type',2)->find($id);
        if ($employee) {
            $employee->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'EMPLOYEE', $employee->id, 'UPDATEEMPLOYEE', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Employee has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}
