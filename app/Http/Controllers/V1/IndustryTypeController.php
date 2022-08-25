<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IndustryType;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class IndustryTypeController extends Controller
{
    public function getAllIndustryTypes()
    {
        $itypes = IndustryType::get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('itypes')
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

        $paginated = IndustryType::where('name', 'LIKE', "%" . $query . "%")
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size);
        $itypes = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('itypes', 'totalRecord', 'current')
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
            'name' => 'required|string|unique:industry_types,name',
        ], [
            'name.unique' => 'Industry Type already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $itype = IndustryType::create([
            'name' => $request->name
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'INDUSTRYTYPE', $itype->id, 'CREATEINDUSTRYTYPE', [], $itype->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Industry Type has been created successfully.'
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
        $itype = IndustryType::find($id);

        if ($itype) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('itype')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Industry Type not found."
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
            'name' => 'required|string|unique:industry_types,name,' . $id . ',id',
        ], [
            'name.unique' => 'Industry Type already exist.'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $itype = IndustryType::find($id);
        if ($itype) {
            $oldData = $itype->toArray();
            $itype->update([
                'name' => $request->name
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'INDUSTRYTYPE', $itype->id, 'UPDATEINDUSTRYTYPE', $oldData, $itype->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Industry Type has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Industry Type not found."
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

        $itype = IndustryType::find($id);
        if ($itype) {
            $itype->delete();

            //Add Action Log
            Helper::addActionLog($user->id, 'INDUSTRYTYPE', $itype->id, 'UPDATEINDUSTRYTYPE', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Industry Type has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}
