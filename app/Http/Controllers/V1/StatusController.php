<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Status;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class StatusController extends Controller
{
    public function getAllStatuses($type)
    {
        $statuses = Status::where('source_type', '=', $type)->get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('statuses')
        ]);
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       $query = $request->source_type;
        $paginationData = Helper::paginationData($request);
        $paginated = Status::where('source_type', '=', $query)
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size);
        $statuses = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('statuses', 'totalRecord', 'current')
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
            'title' => 'required|string|unique:status,title',
            'source_type' => 'string',
        ], [
            'title.unique' => 'Status already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $status = Status::create([
            'title' => $request->title,
            'source_type' => $request->source_type
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'STATUS', $status->id, 'CREATESTATUS', [], $status->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Status has been created successfully.'
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
        $status = Status::find($id);

        if ($status) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('status')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Status not found."
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
            'title' => 'required|string|unique:status,title,' . $id . ',id',
			'source_type' => 'string',
        ], [
            'title.unique' => 'Status already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $status = Status::find($id);
        if ($status) {
            $oldData = $status->toArray();
            $status->update([
                'title' => $request->title,
                'source_type' => $request->source_type
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'STATUS', $status->id, 'UPDATESTATUS', $oldData, $status->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Status has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Status not found."
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

        $status = Status::find($id);
        if ($status) {
            $status->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'STATUS', $status->id, 'UPDATESTATUS', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Status has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}
