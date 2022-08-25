<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class DepartmentsController extends Controller
{
    public function getAllDepartments()
    {
        $departments = Department::get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('departments')
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

        $paginated = Department::where('name', 'LIKE', "%" . $query . "%")
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size);
        $departments = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('departments', 'totalRecord', 'current')
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
            'name' => 'required|string|unique:departments,name',
        ], [
            'name.unique' => 'Department already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $department = Department::create([
            'name' => $request->name
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'DEPARTMENT', $department->id, 'CREATEDEPARTMENT', [], $department->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Department has been created successfully.'
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
        $department = Department::find($id);

        if ($department) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('department')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Department not found."
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
            'name' => 'required|string|unique:departments,name,' . $id . ',id',
        ], [
            'name.unique' => 'Department already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $department = Department::find($id);
        if ($department) {
            $oldData = $department->toArray();
            $department->update([
                'name' => $request->name
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'DEPARTMENT', $department->id, 'UPDATEDEPARTMENT', $oldData, $department->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Department has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Department not found."
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

        $department = Department::find($id);
        if ($department) {
            $department->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'DEPARTMENT', $department->id, 'UPDATEDEPARTMENT', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Department has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}
