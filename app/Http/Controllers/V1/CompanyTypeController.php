<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyType;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class CompanyTypeController extends Controller
{
    public function getAllCompanyTypes()
    {
        $ctypes = CompanyType::get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('ctypes')
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

        $paginated = CompanyType::where('name', 'LIKE', "%" . $query . "%")
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size);
        $ctypes = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('ctypes', 'totalRecord', 'current')
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
            'name' => 'required|string|unique:company_type,name',
        ], [
            'name.unique' => 'Company Type already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $ctype = CompanyType::create([
            'name' => $request->name
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'COMPANYTYPE', $ctype->id, 'CREATECOMPANYTYPE', [], $ctype->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Company Type has been created successfully.'
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
        $ctype = CompanyType::find($id);

        if ($ctype) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('ctype')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Company Type not found."
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
            'name' => 'required|string|unique:company_type,name,' . $id . ',id',
        ], [
            'name.unique' => 'Company Type already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $ctype = CompanyType::find($id);
        if ($ctype) {
            $oldData = $ctype->toArray();
            $ctype->update([
                'name' => $request->name
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'COMPANYTYPE', $ctype->id, 'UPDATECOMPANYTYPE', $oldData, $ctype->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Company Type has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Company Type not found."
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

        $ctype = CompanyType::find($id);
        if ($ctype) {
            $ctype->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'COMPANYTYPE', $ctype->id, 'UPDATECOMPANYTYPE', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Company Type has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}
