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
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Cast;
use App\Models\InterestedProduct;
use App\Models\TeleCallerContact;
use App\Models\ContactCategory;
use App\Models\ContactSection;
use App\Models\ContactField;
use App\Models\ContactValue;
use App\Models\TeleCallerContactNote;
use JWTAuth;
use Helper;
use DB;

class ContactsController extends Controller
{
    public function getAllContacts()
    {
		$user = JWTAuth::parseToken()->authenticate();
		$userId = $user->id;
		$role = $user->roles->first();
		
		$contacts = Contacts::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)->get();
		
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
		$company_type_id = $request->company_type_id;
        $industry_type_id = $request->industry_type_id;
        $country_id = $request->country_id;
        $state_id = $request->state_id;
        $city = $request->city;
        $postcode = $request->postcode;

		$userId = $user->id;
		$role = $user->roles->first();
        $paginationData = Helper::paginationData($request);
		if($user->hasAllPermissions(['Contact: Create', 'Contact: Edit', 'Telecaller: Assign'])){
			$paginated = Contacts::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)->where(function($query1) use ($query) {
				if($query!=''){
				$query1->where('company_name', 'LIKE', "%" . $query . "%")
					->orWhere('name', 'LIKE', "%" . $query . "%")
					->orWhere('email', 'LIKE', "%" . $query . "%")
					->orWhere('secondary_email', 'LIKE', "%" . $query . "%")
					->orWhere('mobile_no', 'LIKE', "%" . $query . "%")
					->orWhere('secondary_mobile_no', 'LIKE', "%" . $query . "%");
				}
			})->where(function($query1) use ($query,$company_type_id,$industry_type_id,$country_id,$state_id,$city,$postcode) {
				if($company_type_id)
						$query1->where('company_type_id', '=', $company_type_id);
					if($industry_type_id)
						$query1->where('industry_type_id', '=', $industry_type_id);
					if($country_id>0)
						$query1->where('country_id', '=', $country_id);
					if($state_id)
						$query1->where('state_id', 'LIKE', "%" .$state_id. "%");
					if($city)
						$query1->where('city', 'LIKE', "%" .$city. "%");
					if($postcode)
						$query1->where('postcode', 'LIKE', "%" .$postcode. "%");
			})->orderBy($paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
		}else{
		$paginated = TeleCallerContact::join('contacts', 'contacts.id', '=', 'tele_caller_contacts.contact_id')->where('tele_caller_contacts.user_id',$userId)->whereNull('contacts.deleted_at')->where(function($query1) use ($query) {//->where('is_working',1)
				$query1->where('contacts.company_name', 'LIKE', "%" . $query . "%")
					->orWhere('contacts.name', 'LIKE', "%" . $query . "%")
					->orWhere('contacts.email', 'LIKE', "%" . $query . "%")
					->orWhere('contacts.secondary_email', 'LIKE', "%" . $query . "%")
					->orWhere('contacts.mobile_no', 'LIKE', "%" . $query . "%")
					->orWhere('contacts.secondary_mobile_no', 'LIKE', "%" . $query . "%");
			})->where(function($query1) use ($query,$company_type_id,$industry_type_id,$country_id,$state_id,$city,$postcode) {
				if($company_type_id)
						$query1->where('contacts.company_type_id', '=', $company_type_id);
					if($industry_type_id)
						$query1->where('contacts.industry_type_id', '=', $industry_type_id);
					if($country_id>0)
						$query1->where('contacts.country_id', '=', $country_id);
					if($state_id)
						$query1->where('contacts.state_id', 'LIKE', "%" .$state_id. "%");
					if($city)
						$query1->where('contacts.city', 'LIKE', "%" .$city. "%");
					if($postcode)
						$query1->where('contacts.postcode', 'LIKE', "%" .$postcode. "%");
			})->groupBy('contacts.id')->orderBy('contacts.'.$paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
		}
		// $print = DB::getQueryLog();

		// dd($print);

		$contacts = $paginated->getCollection()->toArray();
		$totalRecord = $paginated->total();
		$current = $paginated->currentPage();

		return response()->json([
			'status' => 'SUCCESS',
			'data' => compact('contacts', 'totalRecord', 'current')
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
		}
		

        $validator = Validator::make($request->all(), [
            'description' => 'string|nullable',
            'company_name' => 'required|string',
            'established_in' => 'date_format:Y|nullable',
            'turnover' => 'integer|nullable',
            'gst_no' => 'string|nullable',
            'pan_no' => 'string|nullable',
            'no_of_employees' => 'integer|nullable',
            'name' => 'required|string|regex:/^[A-Za-z0-9 ]+$/u|max:50',
            'website' => 'string|url',
            'email' => 'email',
            'secondary_email' => 'email|nullable',
            'mobile_no' => 'required|digits:10',
            'secondary_mobile_no' => 'digits:10|nullable',
            'address_line_1' => 'string|nullable',
            'address_line_2' => 'string|nullable',
            'country_id' => 'integer|nullable',
            'state_id' => 'string|nullable',
            'city' => 'required|string',
            'postcode' => 'string|nullable',
            'notes' => 'string|nullable',
            'industry_id' => 'integer|nullable',
            'company_type_id' => 'integer|nullable',
			'special_instructions' => 'string|nullable',
            'sticky_note' => 'string|nullable',
            'category_id' => 'integer|nullable',
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
			['company_id', '=', $user->company_id],
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
				['company_id', '=', $user->company_id],
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
			'company_id' => $user->company_id,
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
            'state_id' => $request->state_id,
            'city' => $request->city,
            'postcode' => $request->postcode,
            'notes' => $request->notes,
            'special_instructions' => $request->special_instructions,
            'sticky_note' => $request->sticky_note,
            'category_id' => $request->category_id,
			'sub_category' => $request->sub_category,
            'created_by' => $user->id,
        ]);

		$tele_caller_contact = new TeleCallerContact;
		$tele_caller_contact->contact_id = $contact->id;
		$tele_caller_contact->user_id =  $user->id;
		$tele_caller_contact->created_by = $user->id;
		$tele_caller_contact->save();	

		$sectionIds = ContactSection::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->pluck('id');
        $fieldData = ContactField::whereIn('section_id',$sectionIds)->get();
		if(count($fieldData) != 0)
        {
			foreach($fieldData as $key=>$row)
			{
				$valueData[$key]['name'] = $row->label_name;
				$valueData[$key]['value'] = json_encode($request->get($row->label_name));
				$valueData[$key]['field_id'] = $row->id;
				$valueData[$key]['client_id'] = $user->organization_id;
				$valueData[$key]['company_id'] = $user->company_id;
				$valueData[$key]['contact_id'] = $contact->id;
				$valueData[$key]['created_by'] = $user->id;
				$valueData[$key]['created_at'] = date('Y-m-d H:i:s');
				$valueData[$key]['updated_at'] = date('Y-m-d H:i:s');
			}
			if(!empty($valueData)){
				/* $contactId = $contact->id;
				$valueData = array_map(function($q) use($contactId){
					$q['contact_id'] = $contactId;
					return $q;
				}, $valueData); */
				$fieldModelData = ContactValue::insert($valueData);
			}
		}
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
			$formValueData = [];
			$contact = Contacts::with('industry_type','company_type','country')->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);//,'state'
			if ($contact) {
			$contactNote = TeleCallerContactNote::where('contact_id',$id)->orderBy('id','DESC')->first();
			$isNote = 0;
            if($contactNote && $contactNote->is_sticky_note == 1){
                $contact->sticky_note = $contactNote->note;
				$isNote = $contactNote->user_id == $user->id ? 1 : 0;
            }
			$products = Helper::interestedProductAssign($id,null);
	        $fieldData = collect(ContactValue::where('contact_id',$contact->id)->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->get())->map(function($q) use($contact){
							$contact[$q->name] = json_decode($q->value);
							return $contact;
						});
			// $contact = $fieldData;
			$checkContact = TeleCallerContact::where('contact_id',$id)->where('user_id',$user->id)->where('is_working',1)->first();
			$contact->is_lock = $checkContact ? 1 : 0;
			$contact->is_note = $isNote;
			
			$contact->intrested_product = array_filter($products);
			
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
			// }
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

			$validator = Validator::make($request->all(), [
				'description' => 'string|nullable',
				'company_name' => 'required|string',
				'established_in' => 'date_format:Y|nullable',
				'turnover' => 'string|nullable',
				'gst_no' => 'string|nullable',
				'pan_no' => 'string|nullable',
				'no_of_employees' => 'integer|nullable',
				'name' => 'required|string|regex:/^[A-Za-z0-9 ]+$/u|max:50',
				'website' => 'string|nullable',
				'email' => 'email',
				'secondary_email' => 'email|nullable',
				'mobile_no' => 'required|digits:10',
				'secondary_mobile_no' => 'digits:10|nullable',
				'address_line_1' => 'string|nullable',
				'address_line_2' => 'string|nullable',
				'country_id' => 'integer|nullable',
				'state_id' => 'string|nullable',
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
						['company_id',$user->company_id],
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
						['company_id', '=', $user->company_id],
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
					'state_id' => $request->state_id,
					'city' => $request->city,
					'postcode' => $request->postcode,
					'notes' => $request->notes,
					'special_instructions' => $request->special_instructions,
					'category_id' => $request->category_id,
					'sub_category' => $request->sub_category,
					'sticky_note' => $request->sticky_note,
				]);
				$sectionIds = ContactSection::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->pluck('id');
				$fieldData = ContactField::whereIn('section_id',$sectionIds)->get();
				if(count($fieldData) != 0)
				{
					ContactValue::where('contact_id',$contact->id)->delete();
					foreach($fieldData as $key=>$row)
					{
						$valueData[$key]['name'] = $row->label_name;
						$valueData[$key]['value'] = json_encode($request->get($row->label_name));
						$valueData[$key]['field_id'] = $row->id;
						$valueData[$key]['client_id'] = $user->organization_id;
						$valueData[$key]['company_id'] = $user->company_id;
						$valueData[$key]['contact_id'] = $contact->id;
						$valueData[$key]['created_by'] = $user->id;
						$valueData[$key]['created_at'] = date('Y-m-d H:i:s');
						$valueData[$key]['updated_by'] = $user->id;
						$valueData[$key]['updated_at'] = date('Y-m-d H:i:s');
					}
					if(!empty($valueData)){
						$contactId = $contact->id;
						/* $valueData = array_map(function($q) use($contactId){
							$q['contact_id'] = $contactId;
							return $q;
						}, $valueData); */
						$fieldModelData = ContactValue::insert($valueData);
					}
				}
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
		}

        $contact = Contacts::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);
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
		$uclient = Helper::get_client_info($user->organization_id);
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
					
					if($name!='' && $mobile_no!=''){
						$company_type = CompanyType::where('name', 'LIKE', "%" . $company_type_id . "%")->first();
						if ($company_type) 
						$company_type_id = $company_type->id;
						
						$industry = IndustryType::where('name', 'LIKE', "%" . $industry_id . "%")->first();
						if ($industry) 
						$industry_id = $industry->id;
						
						$country = Country::where('name', 'LIKE', "%" . $country_id . "%")->first();
						if ($country) 
						$country_id = $country->country_id;
							
						$contact = Contacts::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->where(function($query1) use ($email,$mobile_no) {
							$query1->where('email','=', $email)
							->orWhere('mobile_no','=', $mobile_no);
						})->first();
						
						if (empty($contact)) {
						
							$contact = Contacts::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->where('email','=', $email)->where('mobile_no','=', $mobile_no)->first();
							
							$isNewEntry = false;
							if (!$contact) {
								$contact = Contacts::create([
									'client_id' => $user->organization_id,
									'company_id' => $user->company_id,
									'name' => $name,
									'email' => $email,
									'secondary_email' => $secondary_email,
									'mobile_no' => $mobile_no,
									'secondary_mobile_no' => $secondary_mobile_no,
									'address_line_1' => $address_line_1,
									'address_line_2' => $address_line_2,
									'country_id' => (int)$country_id,
									'state_id' => $state_id,
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
									'created_by' => $user->id,
								]);

								$tele_caller_contact = new TeleCallerContact;
								$tele_caller_contact->contact_id = $contact->id;
								$tele_caller_contact->user_id =  $user->id;
								$tele_caller_contact->created_by = $user->id;
								$tele_caller_contact->save();

								//Add Action Log
								Helper::addActionLog($user->id, 'CONTACT', $contact->id, 'CREATECONTACT', [], $contact->toArray());
								$newRecords++;
								$isNewEntry = true;
							}else{
								$oldData = $contact->toArray();
								$contact->update([
									'name' => ($name!='')?$name:$contact->name,
									'secondary_email' => ($secondary_email!='')?$secondary_email:$contact->secondary_email,
									'secondary_mobile_no' => ($secondary_mobile_no!='')?$secondary_mobile_no:$contact->secondary_mobile_no,
									'address_line_1' => ($address_line_1!='')?$address_line_1:$contact->address_line_1,
									'address_line_2' => ($address_line_2!='')?$address_line_2:$contact->address_line_2,
									'country_id' => ($country_id!='')?(int)$country_id:$contact->country_id,
									'state_id' =>($state_id!='')?$state_id:$contact->state_id,
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
				$contact = Contacts::with('industry_type','company_type','country')->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);//,'state'
				if ($contact) {
					$lead = Lead::create([
						'client_id' => $contact->client_id,
						'company_id' => $contact->company_id,
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
			// }
		}
    }

	private function checkCast($cast)
	{
		if(!empty($cast))
		{
			$cast = Cast::whereRaw('LOWER(`cast`) LIKE ? ',[trim(strtolower($cast)).'%'])->first();
			if(!$cast)
			{
				$cast = new Cast();
				$cast->name = $cast;
				$cast->save();
			}
		}
		return $cast;
	}

	public function addInterestedProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' =>  'required',
            'contact_id' =>  'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
		$user = JWTAuth::parseToken()->authenticate();
		$productId = explode(',',$request->product_id);
		if(!empty($productId)){
			$interestedProduct = [];
			foreach($productId as $key=>$row){
				$interestedProduct[$key]['product_id'] = $row;
				$interestedProduct[$key]['contact_id'] = $request->contact_id;
				$interestedProduct[$key]['created_by'] = $user->id;
			}
			$interestedProduct = InterestedProduct::insert($interestedProduct);
		}

		return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Successfully your interested product is added.'
        ]);
	}

	public function sendEmail(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'template_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
		$user = JWTAuth::parseToken()->authenticate();
		$templateId = $request->template_id;
		$contactId = explode(',',$request->contact_id);
		$searchText = $request->searchTxt;
		$companyTypeId = $request->company_type_id;
		$industryTypeId = $request->industry_type_id;
		$countryId = $request->country_id;
		$stateId = $request->state_id;
		$city = $request->city;
		$postcode = $request->postcode;
		$contactData = Contacts::query();
		if(!empty($contactId) && array_filter($contactId)){
			$contactData = $contactData->whereIn('id',$contactId);
		}
		if($searchText){
			$contactData = $contactData->where(function($query) use ($searchText) {
				$query->where('company_name', 'LIKE', "%" . $searchText . "%")
				->orWhere('name', 'LIKE', "%" . $searchText . "%");
			});
		}
		if($companyTypeId){
			$contactData = $contactData->where('company_type_id',$companyTypeId);
		}
		if($industryTypeId){
			$contactData = $contactData->where('industry_id',$industryTypeId);
		}
		if($countryId){
			$contactData = $contactData->where('country_id',$countryId);
		}
		if($stateId){
			$contactData = $contactData->where('state_id', 'LIKE', "%" . $stateId . "%");
		}
		if($city){
			$contactData = $contactData->where('city', 'LIKE', "%" . $city . "%");
		}
		if($postcode){
			$contactData = $contactData->where('postcode',$postcode);
		}
		$contactData = $contactData->select('id','client_id','email','cc_email','bcc_email')->get();
		foreach($contactData as $key=>$row)
		{
			//Helper::storeEmailHistory($templateId,2,$row->id,$user->id,0,$row->email,$row->cc_email,$row->bcc_email);
			Helper::storeEmailHistory($templateId,$user->id,$row->client_id,$row->email,$row->cc_email,$row->bcc_email);
		}
		return response()->json([
			'status' => 'SUCCESS',
			'message' => 'Your mail is succesfully sent.'
		]);
	}

	public function checkPermissionRule($roleId,$permissionName)
	{
		$permissionData = app('App\Http\Controllers\V1\Auth')->getUserPermission($roleId)['permission'];
		$permissionValue = !empty($permissionData[$permissionName]) ? \Arr::flatten($permissionData[$permissionName]) : [];
		if(in_array('Create',$permissionValue) && in_array('Edit',$permissionValue)){
			return 0;
		}
		return 1;
	}

	public function getContactCategory()
	{
		$user = JWTAuth::parseToken()->authenticate();
        
        $contactCategory = ContactCategory::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->get(['id','name']);

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('contactCategory')
        ]);
	}

	public function removeIntrestedProduct($id)
	{
		$user = JWTAuth::parseToken()->authenticate();

		$interestedProduct = InterestedProduct::find($id);
		
		if(!$interestedProduct){
			return response()->json([
				'status' => 'FAIL',
				'message' => 'Your interested product is not found.'
			]);
		}
		$interestedProduct->deleted_by = $user->id;
		$interestedProduct = $interestedProduct->delete();
		return response()->json([
			'status' => 'SUCCESS',
			'message' => 'Your interested product is successfully removed.'
		]);
	}
}
