<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Country;
use Helper;
use JWTAuth;

class CountriesController extends Controller
{
    public function  getAllCountries()
    {
        $countries = Country::where('deleted', 0)->get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('countries')
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
        $paginationData = Helper::paginationData($request, 'name');

        $paginated = Country::where('deleted', 0)->where('name', 'LIKE', "%" . $query . "%")->orderBy($paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
        $countries = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('countries', 'totalRecord', 'current')
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

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'sortname' => 'required'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $country = Country::create([
            'name' => $request->name,
            'sortname' => $request->sortname,
            'phonecode' => $request->phonecode
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'COUNTRY', $country->country_id, 'CREATECOUNTRY', [], $country->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Country has been created successfully.'
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
        $country = Country::find($id);

        if ($country) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('country')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Country not found."
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

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'sortname' => 'required'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $country = Country::find($id);
        if ($country) {
            $oldData = $country->toArray();

            $country->update([
                'name' => $request->name,
                'sortname' => $request->sortname,
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'COUNTRY', $country->country_id, 'UPDATECOUNTRY', $oldData, $country->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Country has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Country not found."
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

        $country = Country::find($id);
        if ($country) {
            $country->update(['deleted' => 1]);
            //Add Action Log
            Helper::addActionLog($user->id, 'COUNTRY', $id, 'DELETECOUNTRY', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Country has been deleted successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }
}
