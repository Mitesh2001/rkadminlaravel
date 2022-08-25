<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadsComments;
use App\Models\TeleCallerContactNote;
use App\Models\Lead;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class LeadsCommentsController extends Controller
{
    public function getAllLeadsComments($lid)
    {
        $comments = LeadsComments::where('lead_id','=',$lid)->with('user')->get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('comments')
        ]);
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->lead_id;
        $paginationData = Helper::paginationData($request);

        $data = LeadsComments::where('lead_id', '=',$query)->with('lead','user')->get();
            // ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            // ->paginate($paginationData->size);
        $lead = Lead::find($query);
        if($lead->contact_id){
            $contactNotes = TeleCallerContactNote::where('contact_id',$lead->contact_id)->get();
            $data = $data->merge($contactNotes);
        }
        $data = $data->paginate($paginationData->size);
        $comments = $data->getCollection();
        $totalRecord = $data->total();
        $current = $data->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('comments', 'totalRecord', 'current')
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
            'lead_id' => 'required|integer',
            'remark' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $comment = LeadsComments::create([
            'lead_id' => $request->lead_id,
            'remark' => $request->remark,
            'is_sticky_note' => $request->is_sticky_note ? $request->is_sticky_note : 0,
            'user_id' => (isset($request->user_id))?$request->user_id:$user->id,
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'LEADSCOMMENTS', $comment->id, 'CREATELEADSCOMMENTS', [], $comment->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Leads Comment has been created successfully.'
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
        $comment = LeadsComments::with('lead','user')->find($id);

        if ($comment) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('comment')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Leads Comment not found."
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
			'lead_id' => 'required|integer',
			'remark' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $comment = LeadsComments::find($id);
        if ($comment) {
            $oldData = $comment->toArray();
            $comment->update([
				'lead_id' => $request->lead_id,
				'remark' => $request->remark,
				'is_sticky_note' => $request->is_sticky_note ? $request->is_sticky_note : 0,
				'user_id' => (isset($request->user_id))?$request->user_id:$user->id,
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'LEADSCOMMENTS', $comment->id, 'UPDATELEADSCOMMENTS', $oldData, $comment->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Leads Comment has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Leads Comment not found."
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

        $comment = LeadsComments::find($id);
        if ($comment) {
            $comment->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'LEADSCOMMENTS', $comment->id, 'UPDATELEADSCOMMENTS', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Leads Comment has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}
