<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\State;
use App\Models\City;
use App\Models\CountryPincode;
use Helper;
use JWTAuth;
use DB;

class StatesController extends Controller
{
    public function getAllStates($id)
    {
        $states = State::where('country_id', $id)->where('deleted', 0)->orderBy('name', 'ASC')->get();
        //$states = CountryPincode::where('country_id', $id)->groupBy('state')->get();//->where('deleted', 0)
		//$states = DB::select(DB::raw("SELECT `state` FROM `country_pincode` where country = '$id' GROUP BY state"));

        if ($states) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('states')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "State not found."
            ]);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $states = State::with('country')->get();
        $query = $request->searchTxt;
		if (!$request->sortOrder) {
            $request->sortOrder = "ascend";
        }
        $paginationData = Helper::paginationData($request, 'name');//'state'

        $paginated = State::where('deleted', 0)->with('country')->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size);
        /* $paginated = CountryPincode::selectRaw('country_pincode.country_id, country_pincode.state, country_pincode.state as name, country_pincode.state as state_id')->with('country')->where(function($q) use ($query) {
				if($query!=''){
				$q->where('state', 'LIKE', "%" . $query . "%")//where('deleted', 0)->
					->orWhereHas("country", function ($q2) use ($query) {
						return $q2->where('name', 'LIKE', "%" . $query . "%");
				});
				}
			})->groupBy('state')
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size); */
			/* if($query!=''){
				$paginated->where('state', 'LIKE', "%" . $query . "%")//where('deleted', 0)->
					->orWhereHas("country", function ($q2) use ($query) {
						return $q2->where('name', 'LIKE', "%" . $query . "%");
				});
			}
			$paginated->groupBy('state')
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size); */

        $states = $paginated->getCollection();
		
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('states', 'totalRecord', 'current')
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
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $state = State::create([
            'name' => $request->name,
            //'state' => $request->name,
            'country_id' => ($request->country_id) ? $request->country_id : 0,
        ]);

        //Add Action Log
        //Helper::addActionLog($user->id, 'STATE', $state->state_id, 'CREATESTATE', [], $state->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'State has been created successfully.'
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
        $state = State::where('state_id', $id)->with('country')->first();
        /* $state = CountryPincode::where('state', $id)
            ->with('country')
			->groupBy('state')
            ->first(); */

        if ($state) {
			//$state->name = $state->state;
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('state')
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "State not found."
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
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $state = State::find($id);
        //$state = CountryPincode::where('state','=',$id)->get();
        if ($state) {
            $old = $state->toArray();
			State::where('state_id',$id)->update([
                'name' => $request->name,
                'country_id' => ($request->country_id) ? $request->country_id : 0,
            ]);
            /* CountryPincode::where('state',$id)->update([
                //'name' => $request->name,
                'state' => $request->name,
                'country_id' => ($request->country_id) ? $request->country_id : 0,
            ]); */

            //Add Action Log
            Helper::addActionLog($user->id, 'STATE', $state->state_id, 'UPDATESTATE', $old, $state->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'State has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "State not found."
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
        $state = State::where('state_id',$id)->find($id);//->get();//
        if ($state) {
            $state->update(['deleted' => 1]);

            //Add Action Log
            Helper::addActionLog($user->id, 'STATE', $state->state_id, 'DELETESTATE', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'State has been deleted successfully.'
            ]);
        }

        return response()->json([
            'status' => 'FAIL',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }
    public function getState(Request $request)
    {
        //$states = CountryPincode::groupBy('state')->get();//DB::select(DB::raw("SELECT `state` FROM `country_pincode` GROUP BY state"));
		$states = State::where('country_id',$request->country_id)->orderBy('name', 'ASC')->get();
        $states_ar = array();
        foreach($states as $state){
			
            $states_ar[] = array('id'=>ucwords(strtolower($state->name)),'text'=>ucwords(strtolower($state->name)));
        }
		$states_ar = mb_convert_encoding($states_ar, "UTF-8", "auto");
        return response()->json(['status'=>'SUCCESS', 'states'=>$states_ar]);
    }
    public function getCity(Request $request)
    {
        //$cities = CountryPincode::select('state','district')->where('state',$request->state_name)->groupBy('state')->groupBy('district')->get();//DB::select(DB::raw("SELECT `state`,`district` FROM `country_pincode` where `state` = '".$request->state_name."' GROUP BY district"));
		$cities = City::WhereHas('state', function ($q2) use ($request) {
					if($request->state_name!=''){
						return $q2->where('name', '=', $request->state_name);
					}
				})->orderBy('name', 'ASC')->get();
        $cities_ar = array();
        foreach($cities as $city){
            $cities_ar[] = array('id'=>ucwords(strtolower($city->name)),'text'=>ucwords(strtolower($city->name)));
        }
		$cities_ar = mb_convert_encoding($cities_ar, "UTF-8", "auto");
        return response()->json(['status'=>'SUCCESS', 'cities'=>$cities_ar]);
    }

    public function getPostcode(Request $request)
    {
        $postcodes = CountryPincode::select('district','pincode')->where('district',$request->city_name)->groupBy('state')->groupBy('district')->groupBy('pincode')->orderBy('pincode', 'ASC')->get();//DB::select(DB::raw("SELECT `district`,`pincode` FROM `country_pincode` where `district` = '".$request->city_name."' GROUP BY pincode"));

        $postcodes_ar = array();
        foreach($postcodes as $postcode){
            $postcodes_ar[] = array('id'=>$postcode->pincode,'text'=>$postcode->pincode);
        }
        return response()->json(['status'=>'SUCCESS', 'postcodes'=>$postcodes_ar]);
    }
}
