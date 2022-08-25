<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class EmailTemplates extends Controller
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

        $paginated = EmailTemplate::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)
		->where(function($query1) use ($query) {
			$query1->where('name', 'LIKE', "%" . $query . "%")
			->orWhere('subject', 'LIKE', "%" . $query . "%");
		})
		->orderBy($paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
        $email = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('email', 'totalRecord', 'current')
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
            'name' => 'required|string|unique:email_templates,name',
            'subject' => 'nullable|string',
            'content' => 'nullable|string',
        ], [
            'name.unique' => 'Email template already exists.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $template = EmailTemplate::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'createdBy' => $user->id,
			'client_id' => $user->organization_id,
			'company_id' => $user->company_id,
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'EMAILEMPLATE', $template->email_template_id, 'CREATEEMAILEMPLATE', [], $template->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Email template has been created successfully.'
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
        $email = EmailTemplate::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);

        if ($email) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('email')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Email template not found."
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
        $email = EmailTemplate::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);

        if ($email) {
			$newemail = $email->replicate();
			$newemail->created_at = \Carbon::now();
			$newemail->save();
            return response()->json([
                'status' => 'SUCCESS',
				'message' => "Successfully made the copy of email template.",
                'data' => array('email'=>$newemail)
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Email template not found."
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
            'name' => 'required|string|unique:email_templates,name,' . $id . ',email_template_id',
            'subject' => 'nullable|string',
            'content' => 'nullable|string',
        ], [
            'name.unique' => 'Email template already exists.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $template = EmailTemplate::where('client_id',$user->organization_id)->find($id);
        if ($template) {
            $oldData = $template->toArray();
            $template->update([
                'name' => $request->name,
                'subject' => $request->subject,
                'content' => $request->content,
				'client_id' => $user->organization_id,
				'company_id' => $user->company_id,
                'updatedBy' => $user->id
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'EMAILEMPLATE', $template->email_template_id, 'UPDATEEMAILEMPLATE', $oldData, $template->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Email template has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Email template not found."
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
        $template = EmailTemplate::where('client_id',$user->organization_id)->where('company_id',$user->company_id)->find($id);

        if ($template) {
            $oldData = $template->toArray();
            $template->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'EMAILEMPLATE', $id, 'DELETEEMAILEMPLATE', $oldData, []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Email template has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}
