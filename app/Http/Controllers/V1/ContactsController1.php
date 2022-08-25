<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contacts;
use App\Models\IndustryType;
use App\Models\CompanyType;
use App\Models\Country;
use App\Models\State;
use App\Models\ConstructionContacts;
use App\Models\Lead;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;
use DB;

class ContactsController1 extends Controller
{
    public function getAllContacts()
    {
		$user = JWTAuth::parseToken()->authenticate();
		//DB::enableQueryLog(); 
		$uclient = Helper::get_client_info($user->organization_id);
		if($uclient->industry_id == 7){
			$contacts = ConstructionContacts::select("*","date(created_at) as date","concat(flat_selection,' BHK') as selection")->where('client_id', '=', $user->organization_id)->get();
		}else{
			$contacts = Contacts::where('client_id', '=', $user->organization_id)->get();
		}
		//$print = DB::getQueryLog();
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('contacts')
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
        $query = $request->searchTxt;
        $paginationData = Helper::paginationData($request);
		
		$uclient = Helper::get_client_info($user->organization_id);
		DB::enableQueryLog(); 
		if($uclient->industry_id == 7){
			$paginated = ConstructionContacts::select(DB::raw("*, DATE_FORMAT(created_at,'%d-%m-%Y') as date, concat(flat_selection,' BHK') as selection"))->where('client_id', '=', $user->organization_id)
				->where('name', 'LIKE', "%" . $query . "%")
				->orderBy($paginationData->sortField, $paginationData->sortOrder)
				->paginate($paginationData->size);
			$contacts = $paginated->getCollection();
			$totalRecord = $paginated->total();
			$current = $paginated->currentPage();
		}else{
        $paginated = Contacts::where('client_id', '=', $user->organization_id)
			->where(function($query1) use ($query) {
				$query1->where('company_name', 'LIKE', "%" . $query . "%")
				->orWhere('name', 'LIKE', "%" . $query . "%");
			})
			->with('industry_type','company_type','state','country')
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size);
        $contacts = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();
		}
		$print = DB::getQueryLog();
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('contacts', 'totalRecord', 'current','print')
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
		if(isset($user->organization_id)){
		$uclient = Helper::get_client_info($user->organization_id);
		if($uclient->industry_id == 7){
			$validator = Validator::make($request->all(), [
				'name' => 'required|string',
				'mobile_no' => 'required|digits:10',
			]);

			if ($validator->fails()) {
				$errorString = implode(",", $validator->messages()->all());
				return response()->json([
					'status' => 'FAIL',
					'message' => $errorString
				]); //['error'=>$validator->errors()]
			}
			
			$row1 = ConstructionContacts::where([
				['mobile_no', '=', $request->mobile_no],
				['client_id', '=', $user->organization_id],
			])->first();

			if (!empty($row1)) {
				return response()->json([
					'status' => 'FAIL',
					'message' => 'The Mobile Number already associated with another contact.'
				]);
			}
			$birthdates = array();
			if(isset($request->birthdates_h))
			$birthdates['h'] = $request->birthdates_h;
			if(isset($request->birthdates_h))
			$birthdates['w'] = $request->birthdates_w;
			if(isset($request->birthdates_h))
			$birthdates['c1'] = $request->birthdates_c1;
			if(isset($request->birthdates_h))
			$birthdates['c2'] = $request->birthdates_c2;
			
			$followup = array();
			if(isset($request->followup1))
			$followup['one'] = $request->followup1;
			if(isset($request->followup2))
			$followup['two'] = $request->followup2;
			if(isset($request->followup3))
			$followup['three'] = $request->followup3;
			if(isset($request->followup4))
			$followup['four'] = $request->followup4;
			if(isset($request->followup5))
			$followup['five'] = $request->followup5;
			if(isset($request->followup6))
			$followup['six'] = $request->followup6;
			
			
			$birthdates = json_encode($birthdates);
			$followup = json_encode($followup);
			
			$contact = ConstructionContacts::create([
				'client_id' => $user->organization_id,
				'name' => $request->name,
				'mobile_no' => $request->mobile_no,
				'business' => $request->business,
				'cast' => $request->cast,
				'budget' => $request->budget,
				'flat_selection' => $request->flat_selection,
				'fav_location' => $request->fav_location,
				'fav_floor' => $request->fav_floor,
				'broker_name' => $request->broker_name,
				'broker_mobile_no' => $request->broker_mobile_no,
				'reference' => $request->reference,
				'birthdates' => $birthdates,
				'anniversary' => $request->anniversary,
				'tokan_time' => $request->tokan_time,
				'followup' => $followup,
				'remarks' => $request->remarks,
				'il' => $request->il,
				'contact_date' => date("Y-m-d"),//$request->contact_date,
				'created_by' => $user->id,
			]);
			//Add Action Log
			Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'CREATECONTACT', [], $contact->toArray());

			return response()->json([
				'status' => 'SUCCESS',
				'message' => 'Contact has been created successfully.'
			]);
		}
		}
		

        $validator = Validator::make($request->all(), [
            'description' => 'string|nullable',
            'company_name' => 'required|string',
            'established_in' => 'date_format:Y|nullable',
            'turnover' => 'string|nullable',
            'gst_no' => 'string|nullable',
            'pan_no' => 'string|nullable',
            'no_of_employees' => 'integer|nullable',
            'name' => 'required|string',
            'website' => 'string|nullable',
            'email' => 'email',
            'secondary_email' => 'email|nullable',
            'mobile_no' => 'required|digits:10',
            'secondary_mobile_no' => 'digits:10|nullable',
            'address_line_1' => 'string|nullable',
            'address_line_2' => 'string|nullable',
            'country_id' => 'integer|nullable',
            'state_id' => 'integer|nullable',
            'city' => 'required|string',
            'postcode' => 'string|nullable',
            'notes' => 'string|nullable',
            'industry_id' => 'integer|nullable',
            'company_type_id' => 'integer|nullable',
			'special_instructions' => 'string|nullable',
            'sticky_note' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }
		
		$row1 = Contacts::where([
			['mobile_no', '=', $request->mobile_no],
			['client_id', '=', $user->organization_id],
		])->first();

		if (!empty($row1)) {
			return response()->json([
                'status' => 'FAIL',
                'message' => 'The Mobile Number already associated with another contact.'
            ]);
		}
		
		if($request->email!=''){
			$row1 = Contacts::where([
				['email', '=', $request->email],
				['client_id', '=', $user->organization_id],
			])->first();

			if (!empty($row1)) {
				return response()->json([
                'status' => 'FAIL',
                'message' => 'The email already associated with another contact.'
            ]);
			}
		}
		
		$company_logo = '';
		 if (isset($request->company_logo) && isset($request->company_logo['base64'])) {
            $company_logo = Helper::createImageFromBase64($request->company_logo['base64']);
        }
		
		$picture = '';
		 if (isset($request->picture) && isset($request->picture['base64'])) {
            $picture = Helper::createImageFromBase64($request->picture['base64']);
        }
		
		

        $contact = Contacts::create([
			'client_id' => $user->organization_id,
            'company_type_id' => (int)$request->company_type_id,
            'industry_id' => (int)$request->industry_id,
            'description' => $request->description,
            'company_name' => $request->company_name,
			'established_in' => $request->established_in,
            'turnover' => $request->turnover,
            'gst_no' => $request->gst_no,
            'pan_no' => $request->pan_no,
            'no_of_employees' => $request->no_of_employees,
            'company_logo' => $company_logo,
            'picture' => $picture,
            'website' => $request->website,
            'name' => $request->name,
            'email' => $request->email,
            'secondary_email' => $request->secondary_email,
            'mobile_no' => $request->mobile_no,
            'secondary_mobile_no' => $request->secondary_mobile_no,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'country_id' => (int)$request->country_id,
            'state_id' =>(int)$request->state_id,
            'city' => $request->city,
            'postcode' => $request->postcode,
            'notes' => $request->notes,
            'special_instructions' => $request->special_instructions,
            'sticky_note' => $request->sticky_note,
            'created_by' => $user->id,
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'CREATECONTACT', [], $contact->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Contact has been created successfully.'
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
		if(isset($user->organization_id)){
		$uclient = Helper::get_client_info($user->organization_id);
		if($uclient->industry_id == 7){
			$contact = ConstructionContacts::where('client_id',$user->organization_id)->find($id);
			if ($contact) {
				$contact->date = date("d-m-Y",strtotime($contact->created_at));
				$contact->selection = $contact->flat_selection .'BHK';
				$contact->birthdates = $birthdates = json_decode($contact->birthdates);
				$contact->followup = $followup = json_decode($contact->followup);
				
				return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('contact')
            ]);
			}else{
				return response()->json([
					'status' => 'FAIL',
					'message' => "Contact not found."
				]);
			}
		}else{
        $contact = Contacts::with('industry_type','company_type','country')->where('client_id',$user->organization_id)->find($id);//,'state'

        if ($contact) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('contact')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Contact not found."
            ]);
        }
		}
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
		
		if(isset($user->organization_id)){
		$uclient = Helper::get_client_info($user->organization_id);
		if($uclient->industry_id == 7){
			$validator = Validator::make($request->all(), [
				'name' => 'required|string',
				'mobile_no' => 'required|digits:10',
			]);

			if ($validator->fails()) {
				$errorString = implode(",", $validator->messages()->all());
				return response()->json([
					'status' => 'FAIL',
					'message' => $errorString
				]); //['error'=>$validator->errors()]
			}
			
			$contact = ConstructionContacts::where('client_id',$user->organization_id)->find($id);
			if ($contact) {
			if($contact->mobile_no != $request->mobile_no){
				$row = ConstructionContacts::where([
					['id', '!=', $id],
					['client_id', '=', $user->organization_id],
					['mobile_no', '=', $request->mobile_no],
				])->first();

				if (!empty($row)) {
					return response()->json([
						'status' => 'FAIL',
						'message' => 'The Mobile number already associated with another contact.'
					]);
				}
			}
			$oldData = $contact->toArray();
			
			$birthdates = array();
			if(isset($request->birthdates_h))
			$birthdates['h'] = $request->birthdates_h;
			if(isset($request->birthdates_h))
			$birthdates['w'] = $request->birthdates_w;
			if(isset($request->birthdates_h))
			$birthdates['c1'] = $request->birthdates_c1;
			if(isset($request->birthdates_h))
			$birthdates['c2'] = $request->birthdates_c2;
			
			$followup = array();
			if(isset($request->followup1))
			$followup['one'] = $request->followup1;
			if(isset($request->followup2))
			$followup['two'] = $request->followup2;
			if(isset($request->followup3))
			$followup['three'] = $request->followup3;
			if(isset($request->followup4))
			$followup['four'] = $request->followup4;
			if(isset($request->followup5))
			$followup['five'] = $request->followup5;
			if(isset($request->followup6))
			$followup['six'] = $request->followup6;
			
			
			$birthdates = json_encode($birthdates);
			$followup = json_encode($followup);
			
			//print_r($request->input('followup'));exit;
			//print_r($request->input('birthdates'));exit;
			//DB::enableQueryLog(); 
            $contact->update([			
				'name' => $request->name,
				'mobile_no' => $request->mobile_no,
				'business' => $request->business,
				'cast' => $request->cast,
				'budget' => $request->budget,
				'flat_selection' => $request->flat_selection,
				'fav_location' => $request->fav_location,
				'fav_floor' => $request->fav_floor,
				'broker_name' => $request->broker_name,
				'broker_mobile_no' => $request->broker_mobile_no,
				'reference' => $request->reference,
				'birthdates' => $birthdates,
				'anniversary' => $request->anniversary,
				'tokan_time' => $request->tokan_time,
				'followup' => $followup,
				'remarks' => $request->remarks,
				'il' => $request->il,
				//'contact_date' => $request->contact_date,
			]);
			
			//$print = DB::getQueryLog();
		//echo '<pre>';print_r($print);
		//exit;
			//Add Action Log
            Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'UPDATECONTACT', $oldData, $contact->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Contact has been updated successfully.'
            ]);
			} else {
				return response()->json([
					'status' => 'FAIL',
					'message' => "Contact not found."
				]);
			}
		}
		}

        $validator = Validator::make($request->all(), [
            'description' => 'string|nullable',
            'company_name' => 'required|string',
            'established_in' => 'date_format:Y|nullable',
            'turnover' => 'string|nullable',
            'gst_no' => 'string|nullable',
            'pan_no' => 'string|nullable',
            'no_of_employees' => 'integer|nullable',
            'name' => 'required|string',
            'website' => 'string|nullable',
            'email' => 'email',
            'secondary_email' => 'email|nullable',
            'mobile_no' => 'required|digits:10',
            'secondary_mobile_no' => 'digits:10|nullable',
            'address_line_1' => 'string|nullable',
            'address_line_2' => 'string|nullable',
            'country_id' => 'integer|nullable',
            'state_id' => 'integer|nullable',
            'city' => 'required|string',
            'postcode' => 'string|nullable',
            'notes' => 'string|nullable',
            'industry_id' => 'integer|nullable',
            'company_type_id' => 'integer|nullable',
			'special_instructions' => 'string|nullable',
            'sticky_note' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $contact = Contacts::where('client_id',$user->organization_id)->find($id);
		
		if ($contact) {
			
			if ($contact->email !== $request->email) {
				$row = Contacts::where([
					['id', '!=', $id],
					['client_id', '=', $user->organization_id],
					['email', '=', $request->email],
				])->first();

				if (!empty($row)) {
					return response()->json([
						'status' => 'FAIL',
						'message' => 'The email address already associated with another contact.'
					]);
				}
			}

			if($contact->mobile_no != $request->mobile_no){
				$row = Contacts::where([
					['id', '!=', $id],
					['client_id', '=', $user->organization_id],
					['mobile_no', '=', $request->mobile_no],
				])->first();

				if (!empty($row)) {
					return response()->json([
						'status' => 'FAIL',
						'message' => 'The Mobile number already associated with another contact.'
					]);
				}
			}
		
			$company_logo = $contact->company_logo;
			 if (isset($request->company_logo) && isset($request->company_logo['base64'])) {
				$company_logo = Helper::createImageFromBase64($request->company_logo['base64']);
			}
			
			$picture = $contact->picture;
			 if (isset($request->picture) && isset($request->picture['base64'])) {
				$picture = Helper::createImageFromBase64($request->picture['base64']);
			}
        
            $oldData = $contact->toArray();
            $contact->update([
				'company_type_id' => (int)$request->company_type_id,
				'industry_id' => (int)$request->industry_id,
				'description' => $request->description,
				'company_name' => $request->company_name,
				'established_in' => $request->established_in,
				'turnover' => $request->turnover,
				'gst_no' => $request->gst_no,
				'pan_no' => $request->pan_no,
				'no_of_employees' => $request->no_of_employees,
				'company_logo' => $company_logo,
				'picture' => $picture,
				'website' => $request->website,
				'name' => $request->name,
				'email' => $request->email,
				'secondary_email' => $request->secondary_email,
				'mobile_no' => $request->mobile_no,
				'secondary_mobile_no' => $request->secondary_mobile_no,
				'address_line_1' => $request->address_line_1,
				'address_line_2' => $request->address_line_2,
				'country_id' => (int)$request->country_id,
				'state_id' => (int)$request->state_id,
				'city' => $request->city,
				'postcode' => $request->postcode,
				'notes' => $request->notes,
				'special_instructions' => $request->special_instructions,
				'sticky_note' => $request->sticky_note,
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'UPDATECONTACT', $oldData, $contact->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Contact has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Contact not found."
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
		
		if(isset($user->organization_id)){
		$uclient = Helper::get_client_info($user->organization_id);
		if($uclient->industry_id == 7){
			$contact = ConstructionContacts::where('client_id',$user->organization_id)->find($id);
			if ($contact) {
				$contact->delete();

				//Add Action Log
				Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'UPDATECONTACT', [], []);

				return response()->json([
					'status' => 'SUCCESS',
					'message' => 'Contact has been deleted successfully.'
				]);
			} else {
				return response()->json([
					'status' => 'FAIL',
					'message' => 'Something went wrong. Please try again.'
				]);
			}
		}
		}

        $contact = Contacts::where('client_id',$user->organization_id)->find($id);
        if ($contact) {
            $contact->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'UPDATECONTACT', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Contact has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }
	
	public function importContacts(Request $request){
		$user = JWTAuth::parseToken()->authenticate();
		$file = isset($request->importFile['base64']) ? $request->importFile['base64'] : false;
        if (!$file) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Please select CSV file.'
            ]);
        }
		
		$rows = Helper::getRows($file);
		$newRecords = 0;
        $missings = [];
        $updated = 0;
        $total = 0;
		if (count($rows)) {
            foreach ($rows as $k => $row) {
				if ($k > 0) {
					$name = ((isset($row[0]) && $row[0]!='') ? $row[0] : '');
					$email = ((isset($row[1]) && $row[1]!='') ? $row[1] : '');
					$secondary_email = ((isset($row[2]) && $row[3]!='') ? $row[2] : '');
					$mobile_no = ((isset($row[3]) && $row[3]!='') ? $row[3] : '');
					$secondary_mobile_no = ((isset($row[4]) && $row[4]!='') ? $row[4] : 'NULL');
					$address_line_1 = ((isset($row[5]) && $row[5]!='') ? $row[5] : 'NULL');
					$address_line_2 = ((isset($row[6]) && $row[6]!='') ? $row[6] : 'NULL');
					$country_id = ((isset($row[7]) && $row[7]!='') ? $row[7] : 0);
					$state_id = ((isset($row[8]) && $row[8]!='') ? $row[8] : 0);
					$city = ((isset($row[9]) && $row[9]!='') ? $row[9] : 'NULL');
					$postcode = ((isset($row[10]) && $row[10]!='') ? $row[10] : 'NULL');
					$company_name = ((isset($row[11]) && $row[11]!='') ? $row[11] : 'NULL');
					$company_type_id = ((isset($row[12]) && $row[12]!='') ? $row[12] : 0);
					$industry_id = ((isset($row[13]) && $row[13]!='') ? $row[13] : 0);
					$established_in = ((isset($row[14]) && $row[14]!='') ? $row[14] : NULL);
					$turnover = ((isset($row[15]) && $row[15]!='') ? $row[15] : 'NULL');
					$gst_no = ((isset($row[16]) && $row[16]!='') ? $row[16] : 'NULL');
					$pan_no = ((isset($row[17]) && $row[17]!='') ? $row[17] : 'NULL');
					$no_of_employees = ((isset($row[18]) && $row[18]!='') ? $row[18] : 'NULL');
					$website = ((isset($row[19]) && $row[19]!='') ? $row[19] : 'NULL');
					if($name!='' && $email!='' && $mobile_no!=''){
						
						$company_type = CompanyType::where('name', 'LIKE', "%" . $company_type_id . "%")->first();
						if ($company_type) 
						$company_type_id = $company_type->id;
						
						$industry = IndustryType::where('name', 'LIKE', "%" . $industry_id . "%")->first();
						if ($industry) 
						$industry_id = $industry->id;
						
						$country = Country::where('name', 'LIKE', "%" . $country_id . "%")->first();
						if ($country) 
						$country_id = $country->country_id;
						
						$state = State::where('name', 'LIKE', "%" . $state_id . "%")->first();
						if ($state) 
						$state_id = $state->state_id;
						
						$uclient = Helper::get_client_info($user->organization_id);
						if($uclient->industry_id == 7){
							$contact = ConstructionContacts::where('client_id',$user->organization_id)->where('mobile_no','=', $mobile_no)->first();
							$isNewEntry = false;
							if (!$contact) {
							$contact = ConstructionContacts::create([
								'name' => $name,
								'mobile_no' => $mobile_no,
								'client_id' => $user->organization_id,
								'created_by' => $user->id,
							]);

							//Add Action Log
							Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'CREATECONTACT', [], $contact->toArray());
							$newRecords++;
							$isNewEntry = true;
							}else{
								$oldData = $contact->toArray();
								$contact->update([
									'name' => ($name!='')?$name:$contact->name,
									'client_id' => $user->organization_id,
								]);

								//Add Action Log
								Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'UPDATECONTACT', $oldData, $contact->toArray());
								$updated++;
							}
							
						}else{
							
						$contact = Contacts::where('client_id',$user->organization_id)->where(function($query1) use ($email,$mobile_no) {
							$query1->where('email','=', $email)
							->orWhere('mobile_no','=', $mobile_no);
						})->first();
						
						if (empty($contact)) {
						
						$contact = Contacts::where('client_id',$user->organization_id)->where('email','=', $email)->where('mobile_no','=', $mobile_no)->first();
						
						$isNewEntry = false;
						if (!$contact) {
						$contact = Contacts::create([
							'name' => $name,
							'email' => $email,
							'secondary_email' => $secondary_email,
							'mobile_no' => $mobile_no,
							'secondary_mobile_no' => $secondary_mobile_no,
							'address_line_1' => $address_line_1,
							'address_line_2' => $address_line_2,
							'country_id' => (int)$country_id,
							'state_id' =>(int)$state_id,
							'city' => $city,
							'postcode' => $postcode,
							'company_name' => $company_name,
							'company_type_id' => (int)$company_type_id,
							'industry_id' => (int)$industry_id,
							'established_in' => $established_in,
							'turnover' => $turnover,
							'gst_no' => $gst_no,
							'pan_no' => $pan_no,
							'no_of_employees' => $no_of_employees,
							'website' => $website,
							'client_id' => $user->organization_id,
							'created_by' => $user->id,
						]);

						//Add Action Log
						Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'CREATECONTACT', [], $contact->toArray());
						$newRecords++;
                        $isNewEntry = true;
						}else{
							$oldData = $contact->toArray();
							$contact->update([
								'name' => ($name!='')?$name:$contact->name,
								//'email' => $email,
								'secondary_email' => ($secondary_email!='')?$secondary_email:$contact->secondary_email,
								//'mobile_no' => $mobile_no,
								'secondary_mobile_no' => ($secondary_mobile_no!='')?$secondary_mobile_no:$contact->secondary_mobile_no,
								'address_line_1' => ($address_line_1!='')?$address_line_1:$contact->address_line_1,
								'address_line_2' => ($address_line_2!='')?$address_line_2:$contact->address_line_2,
								'country_id' => ($country_id!='')?(int)$country_id:$contact->country_id,
								'state_id' =>($state_id!='')?(int)$state_id:$contact->state_id,
								'city' => ($city!='')?$city:$contact->city,
								'postcode' => ($postcode!='')?$postcode:$contact->postcode,
								'company_name' => ($company_name!='')?$company_name:$contact->company_name,
								'company_type_id' => ($company_type_id!='')?(int)$company_type_id:$contact->company_type_id,
								'industry_id' => ($industry_id!='')?(int)$industry_id:$contact->industry_id,
								'established_in' => ($established_in!='')?$established_in:$contact->established_in,
								'turnover' => ($turnover!='')?$turnover:$contact->turnover,
								'gst_no' => ($gst_no!='')?$gst_no:$contact->gst_no,
								'pan_no' => ($pan_no!='')?$pan_no:$contact->pan_no,
								'no_of_employees' => ($no_of_employees!='')?$no_of_employees:$contact->no_of_employees,
								'website' => ($website!='')?$website:$contact->website,
								'client_id' => $user->organization_id,
							]);

							//Add Action Log
							Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'UPDATECONTACT', $oldData, $contact->toArray());
							$updated++;
						}
						}
						}
					$total++;
					}
					
					
				}
			}
		}
		$message = "Done: Total:" . $total . " |
                    New Records: " . $newRecords . " |
                    Updated: " . $updated;
		return response()->json([
            'status' => 'SUCCESS',
            'message' => $message,
            //'rows' => $rows
        ]);
	}
	
	/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function makeLeadFromContact($id)
    {
		$user = JWTAuth::parseToken()->authenticate();
		if(isset($user->organization_id)){
		$uclient = Helper::get_client_info($user->organization_id);
		if($uclient->industry_id == 7){
			$contact = ConstructionContacts::where('client_id',$user->organization_id)->find($id);
			if ($contact) {
				$lead = Lead::create([
					'client_id' => $contact->client_id,
					'contact_id' => $contact->id,
					'user_id' => $user->id,
					'customer_name' => $contact->name,
					'lead_name' => $contact->name,
					'mobile_no' => $contact->mobile_no,
					'created_by' => $user->id,
				]);
				//Add Action Log
				Helper::addActionLog($user->id, 'LEAD', $lead->id, 'CREATELEAD', [], $lead->toArray());
				return response()->json([
					'status' => 'SUCCESS',
					'message' => 'Lead has been created successfully.',
					'data' => compact('lead')
				]);
			}else{
				return response()->json([
					'status' => 'FAIL',
					'message' => "Contact not found."
				]);
			}
		}else{
        $contact = Contacts::with('industry_type','company_type','country')->where('client_id',$user->organization_id)->find($id);//,'state'
        if ($contact) {
            $lead = Lead::create([
				'client_id' => $contact->client_id,
				'contact_id' => $contact->id,
				'user_id' => $user->id,
				'customer_name' => $contact->name,
				'mobile_no' => $contact->mobile_no,
				'secondary_mobile_no' => $contact->secondary_mobile_no,
				'email' => $contact->email,
				'secondary_email' => $contact->secondary_email,
				'company_name'=>$contact->company_name,
				'established_in'=>$contact->established_in,
				'turnover'=>$contact->turnover,
				'gst_no'=>$contact->gst_no,
				'pan_no'=>$contact->pan_no,
				'no_of_employees'=>$contact->no_of_employees,
				'website'=>$contact->website,
				'company_logo'=>$contact->company_logo,
				'address_line_1'=>$contact->address_line_1,
				'address_line_2'=>$contact->address_line_2,
				'city'=>$contact->city,
				'state_id'=>$contact->state_id,
				'country_id'=>$contact->country_id,
				'postcode'=>$contact->postcode,
				'notes'=>$contact->notes,
				'special_instructions'=>$contact->special_instructions,
				'sticky_note'=>$contact->sticky_note,
				'company_type_id'=>$contact->company_type_id,
				'industry_id'=>$contact->industry_id,
				'created_by' => $user->id,
			]);
			//Add Action Log
			Helper::addActionLog($user->id, 'LEAD', $lead->id, 'CREATELEAD', [], $lead->toArray());
			//$contact->update();
			return response()->json([
				'status' => 'SUCCESS',
				'message' => 'Contact has been converted to lead successfully.',
				'data' => compact('lead')
			]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Contact not found."
            ]);
        }
		}
		}
    }
}
