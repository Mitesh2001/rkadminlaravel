<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use App\Models\FollowUp;
use App\Models\InterestedProduct;
use App\Models\LeadAssign;
use App\Models\LeadCustomer;
use App\Models\LeadStageHistory;
use App\Models\LeadsComments;
use App\Models\TeleCallerContact;
use App\Models\User;
use App\Models\Contacts;
use App\Models\FollowUpAssign;
use App\Models\Company;
use App\Models\EmailTemplate;

use Helper;


class LeadsController extends Controller
{
    public function getAllLeads()
    {
		$user = JWTAuth::parseToken()->authenticate();
        $leads = Lead::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)->get(); 

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('leads')
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
        $company_type_id = $request->company_type_id;
        $industry_type_id = $request->industry_type_id;
        $country_id = $request->country_id;
        $state_id = $request->state_id;
        $city = $request->city;
        $postcode = $request->postcode;
        $lead_status = $request->lead_status;
        $paginationData = Helper::paginationData($request);
        $userId = $user->id;
		
		if($user->hasAllPermissions(['Lead: Create', 'Lead: Edit', 'Leadassign: Assign'])){
			$paginated = Lead::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)
		->where(function($query1) use ($query) {
			$query1->where('lead_name', 'LIKE', "%" . $query . "%")
				->orWhere('description', 'LIKE', "%" . $query . "%")
				->orWhere('company_name', 'LIKE', "%" . $query . "%")
				->orWhere('customer_name', 'LIKE', "%" . $query . "%")
				->orWhere('email', 'LIKE', "%" . $query . "%")
				->orWhere('secondary_email', 'LIKE', "%" . $query . "%")
				->orWhere('mobile_no', 'LIKE', "%" . $query . "%")
				->orWhere('secondary_mobile_no', 'LIKE', "%" . $query . "%")
				->orWhere('gst_no', 'LIKE', "%" . $query . "%")
				->orWhere('pan_no', 'LIKE', "%" . $query . "%")
				->orWhere('website', 'LIKE', "%" . $query . "%")
				->orWhere('notes', 'LIKE', "%" . $query . "%")
				->orWhere('special_instructions', 'LIKE', "%" . $query . "%")
				->orWhere('sticky_note', 'LIKE', "%" . $query . "%");
		})
		->where(function($query1) use ($query,$company_type_id,$industry_type_id,$country_id,$state_id,$city,$postcode,$lead_status) {
			if($company_type_id)
					$query1->where('company_type_id', '=', $company_type_id);
				if($industry_type_id)
					$query1->where('industry_type_id', '=', $industry_type_id);
				if($country_id>0)
					$query1->where('country_id', '=', $country_id);
				if($state_id>0)
					$query1->where('state_id', '=', $state_id);
				if($city)
					$query1->where('city', 'LIKE', "%" .$city. "%");
				if($postcode)
					$query1->where('postcode', 'LIKE', "%" .$postcode. "%");
				if($lead_status>0)
					$query1->where('lead_status', '=', $lead_status);
		})
		->with('user','industry_type','company_type','lead_status','country')//,'state'
        ->orderBy($paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
		}else{       
        $paginated = LeadAssign::join('leads', 'leads.id', '=', 'lead_assigns.lead_id')->where('lead_assigns.user_id',$userId)->whereNull('leads.deleted_at')->where(function($query1) use ($query) {
            	$query1->where('leads.lead_name', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.description', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.company_name', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.customer_name', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.email', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.secondary_email', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.mobile_no', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.secondary_mobile_no', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.gst_no', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.pan_no', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.website', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.notes', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.special_instructions', 'LIKE', "%" . $query . "%")
            		->orWhere('leads.sticky_note', 'LIKE', "%" . $query . "%");
            })->where(function($query1) use ($query,$company_type_id,$industry_type_id,$country_id,$state_id,$city,$postcode,$lead_status) {
                	if($company_type_id)
                			$query1->where('leads.company_type_id', '=', $company_type_id);
                		if($industry_type_id)
                			$query1->where('leads.industry_type_id', '=', $industry_type_id);
                		if($country_id>0)
                			$query1->where('leads.country_id', '=', $country_id);
                		if($state_id)
                            $query1->where('leads.state_id', 'LIKE', "%" .$state_id. "%");
                		if($city)
                			$query1->where('leads.city', 'LIKE', "%" .$city. "%");
                		if($postcode)
                			$query1->where('leads.postcode', 'LIKE', "%" .$postcode. "%");
                		if($lead_status>0)
                			$query1->where('leads.lead_status', '=', $lead_status);
                })->groupBy('leads.id')->orderBy('leads.'.$paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
		}
            $leads = $paginated->getCollection()->toArray();
            $totalRecord = $paginated->total();
            $current = $paginated->currentPage();

            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('leads', 'totalRecord', 'current')
            ]);

        // $paginated = Lead::where('client_id', '=', $user->organization_id)->where('company_id',
        
        // $user->company_id)
		// ->where(function($query1) use ($query) {
		// 	$query1->where('lead_name', 'LIKE', "%" . $query . "%")
		// 		->orWhere('description', 'LIKE', "%" . $query . "%")
		// 		->orWhere('company_name', 'LIKE', "%" . $query . "%")
		// 		->orWhere('customer_name', 'LIKE', "%" . $query . "%")
		// 		->orWhere('email', 'LIKE', "%" . $query . "%")
		// 		->orWhere('secondary_email', 'LIKE', "%" . $query . "%")
		// 		->orWhere('mobile_no', 'LIKE', "%" . $query . "%")
		// 		->orWhere('secondary_mobile_no', 'LIKE', "%" . $query . "%")
		// 		->orWhere('gst_no', 'LIKE', "%" . $query . "%")
		// 		->orWhere('pan_no', 'LIKE', "%" . $query . "%")
		// 		->orWhere('website', 'LIKE', "%" . $query . "%")
		// 		->orWhere('notes', 'LIKE', "%" . $query . "%")
		// 		->orWhere('special_instructions', 'LIKE', "%" . $query . "%")
		// 		->orWhere('sticky_note', 'LIKE', "%" . $query . "%");
		// })
		// ->where(function($query1) use ($query,$company_type_id,$industry_type_id,$country_id,$state_id,$city,$postcode,$lead_status) {
		// 	if($company_type_id)
		// 			$query1->where('company_type_id', '=', $company_type_id);
		// 		if($industry_type_id)
		// 			$query1->where('industry_type_id', '=', $industry_type_id);
		// 		if($country_id>0)
		// 			$query1->where('country_id', '=', $country_id);
		// 		if($state_id>0)
		// 			$query1->where('state_id', '=', $state_id);
		// 		if($city)
		// 			$query1->where('city', 'LIKE', "%" .$city. "%");
		// 		if($postcode)
		// 			$query1->where('postcode', 'LIKE', "%" .$postcode. "%");
		// 		if($lead_status>0)
		// 			$query1->where('lead_status', '=', $lead_status);
		// })
		// ->with('user','industry_type','company_type','lead_status','country')//,'state'
        // ->orderBy($paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
        // $role = $user->roles->first();
        
        // $leads = $paginated->getCollection()->transform(function ($q) use($role,$userId){
        //     $checkTelePermission = app('App\Http\Controllers\V1\ContactsController')->checkPermissionRule($role->id,'Lead');
		// 	if($role && $checkTelePermission)
        //     {

        //         $leadAssignRecord = LeadAssign::where('lead_id',$q->id)->where('user_id',$userId)->first();
        //         if($leadAssignRecord)
        //         {
        //             $checkLeadStatus = LeadAssign::where('lead_id',$q->id)->where('lock_status',1)->exists();
        //             if($checkLeadStatus)
        //             {
        //                 if($leadAssignRecord->lock_status == 1)
        //                 {
        //                     return $q;
        //                 }
        //             }else{
        //                 return $q;
        //             }
        //         }
        //     }else{
        //         return $q;
        //     }
        // })->toArray();
    //     $leads = array_values(array_filter($leads));
    //     $totalRecord = $paginated->total();
    //     $current = $paginated->currentPage();
    // //$qry = \DB::getQueryLog();
    //     return response()->json([
    //         'status' => 'SUCCESS',
    //         'data' => compact('leads', 'totalRecord', 'current')//,'qry'
    //     ]);
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
            'lead_name' => 'string',
            'description' => 'string|nullable',
            'lead_source' => 'string|nullable',
            'lead_status' => 'integer|nullable',
            'company_name' => 'string|nullable',
            'established_in' => 'date_format:Y|nullable',
            'turnover' => 'string|nullable',
            'gst_no' => 'string|nullable',
            'pan_no' => 'string|nullable',
            'no_of_employees' => 'integer|nullable',
            'customer_name' => 'required|string',
            'website' => 'string|nullable',
            'email' => 'email|nullable',
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
            'enquiry_for' => 'string|nullable',
            'reference' => 'string|nullable',
            'deadline' => 'date_format:Y-m-d H:i:s|nullable',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }
		
		$row1 = Lead::where([
			['mobile_no', '=', $request->mobile_no],
			['client_id', '=', $user->organization_id],
			['company_id', '=', $user->company_id],
		])->first();

		if (!empty($row1)) {
			return response()->json([
                'status' => 'FAIL',
                'message' => 'The Mobile Number already associated with another lead.'
            ]);
		}
		
		if($request->email!=''){
			$row1 = Lead::where([
				['email', '=', $request->email],
				['client_id', '=', $user->organization_id],
				['company_id', '=', $user->company_id],
			])->first();

			if (!empty($row1)) {
				return response()->json([
                'status' => 'FAIL',
                'message' => 'The email already associated with another lead.'
            ]);
			}
		}
		
		$company_logo = '';
		 if (isset($request->company_logo) && isset($request->company_logo['base64'])) {
            $company_logo = Helper::createImageFromBase64($request->company_logo['base64']);
        }
        $lead = Lead::create([
			'client_id' => $user->organization_id,
			'company_id' => $user->company_id,
            // 'user_id' => (isset($request->user_id)?$request->user_id:$user->id),
            'user_id' => null,
            'company_type_id' => (int)$request->company_type_id,
            'industry_id' => (int)$request->industry_id,
            'lead_name' => $request->lead_name,
            'description' => $request->description,
            'lead_source' => $request->lead_source,
            'lead_status' => (int)$request->lead_status,
            'company_name' => $request->company_name,
			'established_in' => $request->established_in,
            'turnover' => $request->turnover,
            'gst_no' => $request->gst_no,
            'pan_no' => $request->pan_no,
            'no_of_employees' => $request->no_of_employees,
            'company_logo' => $company_logo,
            'website' => $request->website,
            'customer_name' => $request->customer_name,
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
            'enquiry_for' => $request->enquiry_for,
            'reference' => $request->reference,
            'created_by' => $user->id,
        ]);
        $interestedProducts = !empty($request->interested_products) && is_array($request->interested_products) ? $request->interested_products : [];
        $interestedProductsData = [];
        foreach($interestedProducts as $key=>$row)
        {
            $interestedProductsData[$key]['product_id'] = $row;
            $interestedProductsData[$key]['lead_id'] = $lead->id;
            $interestedProductsData[$key]['created_by'] = $user->id;
        }
        if(!empty($interestedProductsData)){
            InterestedProduct::insert($interestedProductsData);
        }
        // dd($request->user_id);
        $leadUserId = !empty($request->user_id) && is_array($request->user_id) ? $request->user_id : [];
        // dd($leadUserId);
        if(!empty($leadUserId))
        {
            $this->storeLeadAssign($leadUserId,$lead->id,$user->id);
        }
        //Add Action Log
        Helper::addActionLog($user->id, 'LEAD', $lead->id, 'CREATELEAD', [], $lead->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Lead has been created successfully.'
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
        $lead = Lead::with('user','industry_type','company_type','lead_status','country')->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);//,'state'
        if ($lead) {
            $products = Helper::interestedProductAssign($lead->contact_id,$id);
            $leadAssign = LeadAssign::where('lead_id',$id)->get(['user_id']);
            $lead->assign_user = $leadAssign;
            $leadCommnets = LeadsComments::where('lead_id',$id)->orderBy('id','DESC')->first();
            if($leadCommnets && $leadCommnets->is_sticky_note == 1){
                $lead->sticky_note = $leadCommnets->remark;
            }
            $checkLead = LeadAssign::where('lead_id',$id)->where('user_id',$user->id)->where('lock_status',1)->first();
            $lead->is_lock = $checkLead ? 1 : 0;
            $lead->intrested_product = array_filter($products);
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('lead')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Lead not found."
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
            'lead_name' => 'required|string',
            'description' => 'string|nullable',
            'lead_source' => 'string|nullable',
            'lead_status' => 'integer|nullable',
            'company_name' => 'string|nullable',
            'established_in' => 'date_format:Y|nullable',
            'turnover' => 'string|nullable',
            'gst_no' => 'string|nullable',
            'pan_no' => 'string|nullable',
            'no_of_employees' => 'integer|nullable',
            'customer_name' => 'required|string',
            'website' => 'string|nullable',
            'email' => 'required|email',
            'secondary_email' => 'email|nullable',
            'mobile_no' => 'required|digits:10',
            'secondary_mobile_no' => 'digits:10|nullable',
            'address_line_1' => 'string|nullable',
            'address_line_2' => 'string|nullable',
            'country_id' => 'integer|nullable',
            'state_id' => 'string|nullable',
            'city' => 'string|nullable',
            'postcode' => 'string|nullable',
            'notes' => 'string|nullable',
            'industry_id' => 'integer|nullable',
            'company_type_id' => 'integer|nullable',
			'special_instructions' => 'string|nullable',
            'sticky_note' => 'string|nullable',
			'enquiry_for' => 'string|nullable',
            'reference' => 'string|nullable',
            'deadline' => 'date_format:Y-m-d H:i:s|nullable',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $lead = Lead::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);
		
        if ($lead) {
			
			if ($lead->email !== $request->email) {
				$row = Lead::where([
					['id', '!=', $id],
					['client_id', '=', $user->organization_id],
					['company_id', '=', $user->company_id],
					['email', '=', $request->email],
				])->first();

				if (!empty($row)) {
					return response()->json([
						'status' => 'FAIL',
						'message' => 'The email address already associated with another lead.'
					]);
				}
			}

			if($lead->mobile_no != $request->mobile_no){
				$row = Lead::where([
					['id', '!=', $id],
					['client_id', '=', $user->organization_id],
					['company_id', '=', $user->company_id],
					['mobile_no', '=', $request->mobile_no],
				])->first();

				if (!empty($row)) {
					return response()->json([
						'status' => 'FAIL',
						'message' => 'The Mobile number already associated with another lead.'
					]);
				}
			}
			
			$company_logo = $lead->company_logo;
			 if (isset($request->company_logo) && isset($request->company_logo['base64'])) {
				$company_logo = Helper::createImageFromBase64($request->company_logo['base64']);
			}
			
            $oldData = $lead->toArray();
            $lead->update([
                // 'user_id' => (isset($request->user_id)?$request->user_id:$user->id),
				'company_type_id' => (int)$request->company_type_id,
				'industry_id' => (int)$request->industry_id,
				'lead_name' => $request->lead_name,
				'description' => $request->description,
				'lead_source' => $request->lead_source,
				'lead_status' => (int)$request->lead_status,
				'company_name' => $request->company_name,
				'established_in' => $request->established_in,
				'turnover' => $request->turnover,
				'gst_no' => $request->gst_no,
				'pan_no' => $request->pan_no,
				'no_of_employees' => $request->no_of_employees,
				'company_logo' => $company_logo,
				'website' => $request->website,
				'customer_name' => $request->customer_name,
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
				'enquiry_for' => $request->enquiry_for,
				'reference' => $request->reference,
            ]);
            $interestedProducts = !empty($request->interested_products) && is_array($request->interested_products) ? $request->interested_products : [];
            $interestedProductsData = [];
            foreach($interestedProducts as $key=>$row)
            {
                $interestedProductsData[$key]['product_id'] = $row;
                $interestedProductsData[$key]['lead_id'] = $lead->id;
                $interestedProductsData[$key]['created_by'] = $user->id;
            }
            if(!empty($interestedProductsData)){
                InterestedProduct::where('lead_id',$lead->id)->delete();
                InterestedProduct::insert($interestedProductsData);
            }
            $leadUserId = !empty($request->user_id) && is_array($request->user_id) ? $request->user_id : [];
            if(!empty($leadUserId))
            {
                LeadAssign::where('lead_id',$lead->id)->delete();
                $this->storeLeadAssign($leadUserId,$lead->id,$user->id);
            }
            //Add Action Log
            Helper::addActionLog($user->id, 'LEAD', $lead->id, 'UPDATELEAD', $oldData, $lead->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Lead has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Lead not found."
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

        $lead = Lead::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);
        if ($lead) {
            $lead->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'LEAD', $lead->id, 'UPDATELEAD', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Lead has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }

    public function addFollowUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'note' =>  'required',
            'type' =>  'required',
            'date' => 'required|date_format:Y-m-d',
            'time' =>  'date_format:H:i:s',
            'user_id' => 'required',
            'follow_up_id' =>  'required',
            'assign_type' =>  'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $user = JWTAuth::parseToken()->authenticate();
        $followUpType = config('module_name.follow_type');
        $followUpType = array_keys($followUpType,strtolower($request->follow_up_type));
        $followUp = new FollowUp();
        $assignType = $request->assign_type;
        $followUp->type = $request->type;
        $followUp->created_by = $user->id;
        if($assignType == 1)
        {
            $followUp->user_id = 0;
            $followUp->role_id = $request->user_id;
        }else{
            $followUp->user_id = $request->user_id;
            $followUp->role_id = 0;
        }
        $followUp->date = $request->date;
        $followUp->assign_type = $assignType;
        $followUp->time = $request->time;
        $followUp->follow_up_id = $request->follow_up_id;
        $followUp->follow_up_type = !empty($followUpType) ? $followUpType[0] : 0;
        $followUp->note = $request->note;
        $followUp->save();
		
		$followUpData = array();
		$followUpData['follow_up_id'] = $followUp->id;
		$followUpData['user_id'] = $followUp->user_id;
		$followUpData['role_id'] = $followUp->role_id;
		$followUpData['created_by'] = $user->id;
		FollowUpAssign::insert($followUpData);

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Successfully your follow up is added.'
        ]);
    }

    public function getFollowUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' =>  'required',
            // 'follow_up_id' => 'required_without:role_id',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
                
            ]);
        }
		
		$user = JWTAuth::parseToken()->authenticate();
		
        $paginationData = Helper::paginationData($request);
        $followUpType = config('module_name.follow_type');
        $data = FollowUp::where('type',$request->type)->with(['getAssignToData','getCreatedBy','getRole','getFollowUpAssign.getAssignToData','getFollowUpAssign.getCreatedBy']);
		/* if($request->type == 1)
		{
			$data->join('leads', function($join) use ($user)
			{
				$join->on('follow_up.follow_up_id','=','leads.id')
					 ->where('leads.client_id', '=', $user->organization_id)
					 ->where('leads.company_id', '=', $user->company_id);
			});
		}else{
			$data->join('contacts', function($join) use ($user)
			{
				$join->on('follow_up.follow_up_id','=','contacts.id')
					 ->where('contacts.client_id', '=', $user->organization_id)
					 ->where('contacts.company_id', '=', $user->company_id);
			});
		} */
        $roleId = $request->role_id;
        $userId = $request->user_id;
        if($roleId){
            //$data = $data->where('role_id',$roleId);
        }
        if($userId){
            //$data = $data->where('user_id',$userId);
        }
        if($request->follow_up_id){
            $data = $data->where('follow_up_id',$request->follow_up_id);
        }
        $data = $data->orderBy('id', 'desc')->get();
        if($request->type == 1)
        {
            foreach($data as $row){
                $lead = Lead::find($row->follow_up_id);
                if($lead){
                    $contactFollowUp = FollowUp::where('type',2)->where('follow_up_id',$lead->contact_id)->get();
                    $data = $data->merge($contactFollowUp);
                }
            }
        }
        $data = $data->paginate($paginationData->size);
        $followUp = $data->getCollection()->transform(function ($q) use($followUpType,$userId){
            $q->follow_up_type = !empty($followUpType[$q->follow_up_type]) ? $followUpType[$q->follow_up_type] : null;
            // $q->is_assign = ($q->user_id && $q->user_id != 0) && $q->user_id == $userId ? 1 : 0;
            $userIds = !empty($q->getFollowUpAssign) ? $q->getFollowUpAssign->pluck('user_id')->toArray() : [];
            $q->is_assign = (!empty($userIds) && in_array($userId,$userIds)) ? 1 : 0;
            //$q->name = $this->getLeadContactName($q->follow_up_id,$q->follow_up_type);
            $q->name = $this->getLeadContactName($q->follow_up_id,$q->type);//for lead or contact
            return $q;
        })->toArray();
        $followUp = array_values(array_filter($followUp));
        $totalRecord = $data->total();
        $current = $data->currentPage();
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('followUp', 'totalRecord', 'current')
        ]); 
    }

    // assign lead to user
    public function leadAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $user = JWTAuth::parseToken()->authenticate();
        $loginUserId = $user->id;
        $userId = $request->user_id ? $request->user_id : 0;
        $roleId = $request->role_id ? $request->role_id : 0;
        // if($userId){
        //     LeadAssign::where('user_id',$userId)->delete();
        // }
        // if($roleId){
        //     LeadAssign::where('user_id',$roleId)->delete();
        // }

        $hiddenids = $request->hiddenIds;
        if($hiddenids){
            $hiddenids = explode(',',$hiddenids);
            if(is_array($hiddenids))
            {
                LeadAssign::whereIn('lead_id',$hiddenids)->where('user_id',$userId)->delete();
            }
        }

        $leadId = explode(',',$request->lead_id);
        $leadData = [];
        $leadData = array_map(function ($q) use ($leadId,$leadData,$userId,$loginUserId,$roleId) {
            $leadData[$q]['lead_id'] = $q;
            $leadData[$q]['user_id'] = $userId;
            $leadData[$q]['role_id'] = $roleId;
            $leadData[$q]['created_by'] = $loginUserId;
            $leadData[$q]['created_at'] = date('Y-m-d H:i:s');
            $leadData[$q]['updated_at'] = date('Y-m-d H:i:s');
            return $leadData;
        }, $leadId);
        $leadData = \Arr::collapse($leadData);
        LeadAssign::insert($leadData);
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Successfully lead has been assigned to user.'
        ]);
    }

    private function storeLeadAssign($userId,$leadId,$loginUserId)
    {
        $leadData = [];
        $leadData = array_map(function ($q) use ($leadId,$leadData,$userId,$loginUserId) {
            $leadData[$q]['lead_id'] = $leadId;
            $leadData[$q]['user_id'] = $q;
            $leadData[$q]['created_by'] = $loginUserId;
            $leadData[$q]['created_at'] = date('Y-m-d H:i:s');
            $leadData[$q]['updated_at'] = date('Y-m-d H:i:s');
            return $leadData;
        }, $userId);
        $leadData = \Arr::collapse($leadData);
        LeadAssign::insert($leadData);
    }

    public function getLeadAssgin(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $paginationData = Helper::paginationData($request);
        $userId = $request->user_id;
		$query = $request->searchTxt;
		$state_id = $request->state_id;
        $city = $request->city;
		$company_type_id = $request->company_type_id;
        $industry_type_id = $request->industry_type_id;
        $country_id = $request->country_id;
        $postcode = $request->postcode;
        $lead_status = $request->lead_status;
        $leads = Lead::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)
		->where(function($query1) use ($query) {
			$query1->where('lead_name', 'LIKE', "%" . $query . "%")
				->orWhere('description', 'LIKE', "%" . $query . "%")
				->orWhere('company_name', 'LIKE', "%" . $query . "%")
				->orWhere('customer_name', 'LIKE', "%" . $query . "%")
				->orWhere('email', 'LIKE', "%" . $query . "%")
				->orWhere('secondary_email', 'LIKE', "%" . $query . "%")
				->orWhere('mobile_no', 'LIKE', "%" . $query . "%")
				->orWhere('secondary_mobile_no', 'LIKE', "%" . $query . "%")
				->orWhere('gst_no', 'LIKE', "%" . $query . "%")
				->orWhere('pan_no', 'LIKE', "%" . $query . "%")
				->orWhere('website', 'LIKE', "%" . $query . "%")
				->orWhere('notes', 'LIKE', "%" . $query . "%")
				->orWhere('special_instructions', 'LIKE', "%" . $query . "%")
				->orWhere('sticky_note', 'LIKE', "%" . $query . "%");
		})
		->where(function($query1) use ($query,$company_type_id,$industry_type_id,$country_id,$state_id,$city,$postcode,$lead_status) {
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
				if($lead_status>0)
					$query1->where('lead_status', '=', $lead_status);
		})
		->orderBy($paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
        if($userId > 0){
            $userId = $request->user_id;   
        }else{
            $userId = $user->id;   
        }
        $leadData = $leads->getCollection()->transform(function ($q) use($userId){
            $userIds = !empty($q->getAssignLead) ? $q->getAssignLead->pluck('user_id')->toArray() : [];
            
            $q->is_assign = (!empty($userIds) && in_array($userId,$userIds)) ? 1 : 0;
            
            unset($q->getAssignLead);
            return $q;
        });
        $totalRecord = $leads->total();
        $current = $leads->currentPage();
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('leadData', 'totalRecord', 'current')
        ]); 

    }

    public function convertLeadToCustomer($leadId)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $lead = Lead::find($leadId);
        if (!$lead) {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Lead not found.'
            ]);
        }
        $leadCustomer = new LeadCustomer();
        $leadCustomer->lead_id = $leadId;
        $leadCustomer->client_id = $lead->client_id;
        $leadCustomer->company_id = $lead->company_id;
        $leadCustomer->lead_name = $lead->lead_name;
        $leadCustomer->company_name = $lead->company_name;
        $leadCustomer->customer_name = $lead->customer_name;
        $leadCustomer->email = $lead->email;
        $leadCustomer->secondary_email = $lead->secondary_email;
        $leadCustomer->mobile_no = $lead->mobile_no;
        $leadCustomer->secondary_mobile_no = $lead->secondary_mobile_no;
        $leadCustomer->established_in = $lead->established_in;
        $leadCustomer->turnover = $lead->turnover;
        $leadCustomer->gst_no = $lead->gst_no;
        $leadCustomer->pan_no = $lead->pan_no;
        $leadCustomer->no_of_employees = $lead->no_of_employees;
        $leadCustomer->website = $lead->website;
        $leadCustomer->created_by = $user->id;
        $leadCustomer->save();

        // change the lead status
        $lead->is_completed = 1;
        $lead->save();
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Lead has been converted to customer.'
        ]);
    }

    public function leadLockUnlock(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'is_lock' => 'required'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $lead = LeadAssign::where('lead_id',$request->lead_id)->where('user_id',$user->id)->first();
        if(!$lead){
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Lead not found.'
            ]);
        }
        
        $msg = 'unlock';
        $status = 0;
        if($request->is_lock == 'true')
        {
            $checkLead = LeadAssign::where('lead_id',$request->lead_id)->where('lock_status',1)->first();
            if($checkLead){
                return response()->json([
                    'status' => 'FAIL',
                    'message' => 'This lead is already locked.'
                ]);
            }
            $msg = 'lock';
            $status = 1;
            $lead->locked_date = date('Y-m-d H:i:s');
            //$lead->created_by = $user->id;
        }else{
            $lead->unlocked_date = date('Y-m-d H:i:s');
            $lead->unlocked_by = $user->id;
            $lead->note = $request->note;
        }
        $lead->lock_status = $status;
        $lead->save();

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Your lead is successfully '.$msg
        ]);
    }

    public function leadStage(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required',
            'lead_stage' => 'required'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $leadStage = config('module_name.lead_stage');
        $leadStage = array_keys($leadStage,strtolower($request->lead_stage));
        $leadStage = !empty($leadStage) ? $leadStage[0] : 0;
        $lead = Lead::where('id',$request->lead_id)->first();
        if(!$lead){
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Lead not found.'
            ]);
        }
        if($leadStage != $lead->stage_id)
        {
            $leadStageHistory = new LeadStageHistory();
            $leadStageHistory->stage_id = $leadStage;
            $leadStageHistory->lead_id = $lead->id;
            $leadStageHistory->user_id = $user->id;
            $leadStageHistory->save();
        }
        $lead->stage_id = $leadStage;
        $lead->save();
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Lead stage has been added successfully.'
        ]);
    }

    public function getLeadStage($id)
    {
        $leadStageData = config('module_name.lead_stage');
        $leadStage = collect(LeadStageHistory::where('lead_id',$id)->with('getUserData')->get(['id','lead_id','stage_id','user_id','created_at']))->map(function($q) use($leadStageData){
                        $q->lead_stage = !empty($q->stage_id) ? ucfirst($leadStageData[$q->stage_id]) : null;
                        return $q;
                    });
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('leadStage')
        ]);
    }

    public function sendBulkEmils(Request $request)
    { 
        $contact_id = $request->contact_id;
        $template_id = $request->template_id;
    
        $template  = EmailTemplate::find($template_id);
     
        $contacts_arr = explode(',', $contact_id);
    
        $user = JWTAuth::parseToken()->authenticate();
    
        $companyData = Company::find($user->company_id);  

        $from_data['from_email'] = $companyData->from_email;
        $from_data['from_name'] =  $companyData->from_name;
    
        if($companyData && $companyData->email_service == 0){

            $used_email_total = $companyData->used_email;

            foreach($contacts_arr as $contact) { 

                if($used_email_total >= 1 ){

                    $used_email = $used_email_total - 1;

                    $companyData->used_email = $used_email;
                    $companyData->save();
    
                    $contact_detail = Contacts::find($contact);

                    //Helper::storeEmailHistory($template_id, $user->id, $contact_detail->id, $contact_detail->email, null, null, null, null, $from_data);
                    Helper::storeEmailHistory($template_id, $user->id, $contact_detail->client_id, $contact_detail->email, null, null, null, null, $from_data);
  
                    $used_email_total--;

                } else {
                    $message = 'Insufficient balance email. Please recharge and try again.';
                    break;
                }
                $message = 'Your email is succesfully sent.';
            } 
 
        }else{
            $message = 'Please enable your email service.';
        } 

        return response()->json([
            'status' => 'SUCCESS',
            'message' => $message
        ]); 
    }

    public function sendEmail(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'type_id' => 'required',
            'email' => 'required',
            'template_id' => 'required',
            'subject' => 'required',
            'content' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $templateId = $request->template_id;
        $typeId = $request->type_id;
        $type = $request->type;
        $email = $request->email;
        $ccEmail = $request->cc_email;
        $bccEmail = $request->bcc_email;
        $senderId = $user->id;
        $subject = $request->subject;
        $content = $request->content;
        $model = Contacts::query();
        if($type == 1)
        {
            $model = Lead::query();
        }
        $model = $model->find($request->type_id);
        if($model){
            $model->email = $email;
            $model->cc_email = $ccEmail;
            $model->bcc_email = $bccEmail;
        }
        
        $receiverId = 0;
        $receiver = User::where('organization_id',$user->organization_id)->where('company_id',$user->company_id)->where('email',$request->email)->first();
        if($receiver)
        {
            //$receiverId = $receiver->id;
            $receiverId = $receiver->organization_id;
        }

        $companyData = Company::find($user->company_id);                

        $from_data['from_email'] = $companyData->from_email;
        $from_data['from_name'] =  $companyData->from_name;

        $cc_count = $ccEmail != null ? count(explode(',',$ccEmail)) : 0;
        $bcc_count = $bccEmail != null ? count(explode(',',$bccEmail)) : 0;
    
        $total_email_deduce = $cc_count + $bcc_count + 1;    

        if($companyData->used_email >= $total_email_deduce){

            $used_email = $companyData->used_email - $total_email_deduce;

            Helper::storeEmailHistory($templateId,$user->id,$receiverId,$email,$ccEmail,$bccEmail,$subject,$content, $from_data);
            $companyData->used_email = $used_email;
            $companyData->save();
            
            $message = 'Your mail is succesfully sent.';
        }else{
            // $message = 'Your email balance is 0. Please recharge and try again.';
            $message = 'Insufficient balance. Please recharge and try again.';
        }
       
        return response()->json([
            'status' => 'SUCCESS',
            'message' => $message
        ]);
    }

    public function followupAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'follow_up_id' => 'required',
            'role_id' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $user = JWTAuth::parseToken()->authenticate();
        $userId = $request->user_id;
        $roleId = $request->role_id;
        $hiddenids = $request->hiddenids;
        if($hiddenids){
            $hiddenids = explode(',',$hiddenids);
            if(is_array($hiddenids))
            {
                FollowUpAssign::whereIn('follow_up_id',$hiddenids)->where('role_id',$roleId)->where('user_id',$userId)->delete();
            }
        }
        $followUpId = explode(',',$request->follow_up_id);
        $createdById = $user->id;
        
        $followUpData = [];
        $followUpData = array_map(function ($q) use ($followUpId,$followUpData,$userId,$createdById,$roleId) {
            $followUpData[$q]['follow_up_id'] = $q;
            $followUpData[$q]['user_id'] = $userId;
            $followUpData[$q]['created_by'] = $createdById;
            $followUpData[$q]['role_id'] = $roleId;
            $followUpData[$q]['created_at'] = date('Y-m-d H:i:s');
            $followUpData[$q]['updated_at'] = date('Y-m-d H:i:s');
            return $followUpData;
        }, $followUpId);
        $followUpData = \Arr::collapse($followUpData);
        FollowUpAssign::insert($followUpData);
        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Successfully followup has been assigned to user.'
        ]);

    }

    public function getAssignLog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            // 'assign_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $type = $request->type;
        $assignId = $request->assign_id;
        $roleId = $request->role;
        $userId = $request->user_id;
        if($type == 1){
            $assignLog = LeadAssign::select('id','user_id','lock_status','created_by','lead_id','locked_date','unlocked_by','unlocked_date','note','created_at');
            if($assignId){
                $assignLog = $assignLog->where('lead_id',$assignId);
            }
            if($roleId){
                $assignLog = $assignLog->where('role_id',$roleId);
            }
            if($userId){
                $assignLog = $assignLog->where('user_id',$userId);
            }
        }else{
            $assignLog = TeleCallerContact::select('id','user_id','is_working as lock_status','contact_id','created_by','locked_date','unlocked_by','unlocked_date','note','created_at');
            if($assignId){
               $assignLog = $assignLog->where('contact_id',$assignId);
            }
            if($roleId){
                $assignLog = $assignLog->where('role_id',$roleId);
            }
            if($userId){
                $assignLog = $assignLog->where('user_id',$userId);
            }
        }
        $assignLog = $assignLog->with('getCreatedBy','getUserData','getUnlockUserData','getModel.createdBy')->orderBy('created_at','DESC')->get()->map(function($q) use($type){
            if($type == 1){
                $q->lead_created_by = !empty($q->getModel->createdBy) ? $q->getModel->createdBy : null;
            }else{
                $q->contact_created_by = !empty($q->getModel->createdBy) ? $q->getModel->createdBy : null;
            }
            unset($q->getModel);
            return $q;
        });
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('assignLog')
        ]);
    }

    public function showFollowup($id)
    {
        $followUp = FollowUp::where('id',$id)->with('getAssignToData','getCreatedBy','getRole','getFollowUpAssign.getAssignToData','getFollowUpAssign.getCreatedBy')->first();
        $followUpType = config('module_name.follow_type');
        $followUp->follow_up_type = !empty($followUpType[$followUp->follow_up_type]) ? $followUpType[$followUp->follow_up_type] : null;
        //$followUp->name = $this->getLeadContactName($followUp->follow_up_id,$followUp->follow_up_type);
        $followUp->name = $this->getLeadContactName($followUp->follow_up_id,$followUp->type);
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('followUp')
        ]);
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
}
