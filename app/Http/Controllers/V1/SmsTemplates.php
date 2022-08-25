<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmsTemplate;
use App\Models\EmailSmsLog;
use App\Models\Company;
use App\Models\Contacts;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class SmsTemplates extends Controller
{
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

        $paginated = SmsTemplate::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)->where('name', 'LIKE', "%" . $query . "%")->orderBy($paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
        $sms = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('sms', 'totalRecord', 'current')
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
            'name' => 'required|string|unique:sms_templates,name',
            'content' => 'required|string',
        ], [
            'name.unique' => 'Sms Template already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $template = SmsTemplate::create([
            'name' => $request->name,
            'content' => $request->content,
			'client_id' => $user->organization_id,
			'company_id' => $user->company_id,
            'createdBy' => $user->id
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'SMSTEMPLATE', $template->sms_template_id, 'CREATESMSTEMPLATE', [], $template->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'SMS template has been created successfully.'
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
        $sms = SmsTemplate::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);

        if ($sms) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('sms')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Sms Template not found."
            ]);
        }
    }
	
	/**
     * duplicate the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate($id)
    {
		$user = JWTAuth::parseToken()->authenticate();
        $sms = SmsTemplate::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);

        if ($sms) {
			$newsms = $sms->replicate();
			$newsms->created_at = \Carbon::now();
			$newsms->save();
            return response()->json([
                'status' => 'SUCCESS',
				'message' => "Successfully made the copy of sms template.",
                'data' => array('sms'=>$newsms)
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Sms Template not found."
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
            'name' => 'required|string|unique:sms_templates,name,' . $id . ',sms_template_id',
            'content' => 'required|string',
        ], [
            'name.unique' => 'SMS template already exists.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $template = SmsTemplate::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);
        if ($template) {

            $old = $template->toArray();

            $template->update([
                'name' => $request->name,
                'content' => $request->content,
				'client_id' => $user->organization_id,
				'company_id' => $user->company_id,
                'updatedBy' => $user->id
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'SMSTEMPLATE', $template->sms_template_id, 'UPDATESMSTEMPLATE', $old, $template->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'SMS template has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "SMS template not found."
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
        $template = SmsTemplate::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);
        if ($template) {
            //Add Action Log
            Helper::addActionLog($user->id, 'SMSTEMPLATE', $template->sms_template_id, 'DELETESMSTEMPLATE', [], []);

            $template->delete();

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'SMS template has been deleted successfully.'
            ]);
        }
        return response()->json([
            'status' => 'FAIL',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function send(Request $request){
	
        $contact_id = $request->contact_id;
        $template_id = $request->template_id;
    
        $template  = SmsTemplate::find($template_id);
     
        $contacts_arr = explode(',', $contact_id);
    
        $user = JWTAuth::parseToken()->authenticate();
    
        $companyData = Company::find($user->company_id);  
    
        if($companyData->sms_service == 0){

            if($companyData->used_sms >= 1 ){

                $used_sms = ($companyData->used_sms > 1) ? $companyData->used_sms - 1 : 0;

                $companyData->used_sms = $used_sms;
                $companyData->save();

                $contact_numbers = Contacts::whereIn('id', $contacts_arr)->pluck('mobile_no')->toArray();
                    
                $shortcodes = Helper::shortcodes($user);
                $test = Helper::sendSMS($template_id, $contact_numbers, $shortcodes, $user);

                foreach($contacts_arr as $contact) { 
        
                    $contact_detail = Contacts::find($contact);

                    $log = new EmailSmsLog;
                    $log->user_id = $user->id;
                    $log->client_id = $user->organization_id;//$contact;
                    $log->client_number = $contact_detail->mobile_no ?? "";
                    $log->client_email = "";
                    $log->company_id = $user->company_id;
                    $log->template_id = $request->template_id;
                    $log->type = 'SMS';
                    $log->response = '1';
                    $log->msg = $template->content;
                    $log->save();
                } 
                $message = 'Your sms is succesfully sent.'; 
            }else{
                $message = 'Your sms balance is 0. Please recharge and try again.';
            } 
        }else{
            $message = 'Please enable your sms service.';
        } 

        return response()->json([
            'status' => 'SUCCESS',
            'message' => $message
        ]);
    }  
}
