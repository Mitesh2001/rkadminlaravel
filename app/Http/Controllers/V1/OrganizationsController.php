<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class OrganizationsController extends Controller
{
    public function getAllOrganizations()
    {
        $organizations = Organization::get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('organizations')
        ]);
    }
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->searchTxt;
        $paginationData = Helper::paginationData($request);

        $paginated = Organization::where('name', 'LIKE', "%" . $query . "%")
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size);
        $organizations = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('organizations', 'totalRecord', 'current')
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
            'name' => 'required|string',            
            'email' => 'required|email',
            'phone' => 'required|digits:10',
            'website' => 'string',
			'industry_id' => 'required|integer',
			'ctype_id' => 'required|integer',
			//'assigned_to' => 'required|integer',
            'connected_since' => 'date|nullable',
            'address' => 'string',
            'city' => 'string',
            'description' => 'string',
        ],
		[
            'name' => 'Organization Name Required.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $organization = Organization::create([
            'name' => $request->name,
			'email' => $request->email,
			'phone' => $request->phone,
			'website' => $request->website,
            'industry_id' => $request->industry_id,
            'ctype_id' => $request->ctype_id,
            'assigned_to' => $request->assigned_to,
            'connected_since' => $request->connected_since,
            'address' => $request->address,
            'city' => $request->city,            
            'description' => $request->description,
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'ORGANIZATION', $organization->id, 'CREATEORGANIZATION', [], $organization->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Organization has been created successfully.'
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
        $organization = Organization::find($id);

        if ($organization) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('organization')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Organization not found."
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
            'name' => 'required|string',            
            'email' => 'required|email',
            'phone' => 'required|digits:10',
            'website' => 'string',
			'industry_id' => 'required|integer',
			'ctype_id' => 'required|integer',
            'connected_since' => 'date|nullable',
            'address' => 'string',
            'city' => 'string',
            'description' => 'string',
        ], [
           // 'name.unique' => 'Organization already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $organization = Organization::find($id);
        if ($organization) {
            $oldData = $organization->toArray();
            $organization->update([
            'name' => $request->name,
			'email' => $request->email,
			'phone' => $request->phone,
			'website' => $request->website,
            'industry_id' => $request->industry_id,
            'ctype_id' => $request->ctype_id,
			'assigned_to' => $request->assigned_to,
            'connected_since' => $request->connected_since,
            'address' => $request->address,
            'city' => $request->city,            
            'description' => $request->description,
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'ORGANIZATION', $organization->id, 'UPDATEORGANIZATION', $oldData, $organization->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Organization has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Organization not found."
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

        $organization = Organization::find($id);
        if ($organization) {
            $organization->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'ORGANIZATION', $organization->id, 'UPDATEORGANIZATION', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Organization has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}
