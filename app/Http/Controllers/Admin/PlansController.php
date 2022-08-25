<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\Plan;
use App\Models\ClientPlan;
use Validator;
use Exception;
use DB;
use Session;
use Auth;
use Helper;

class PlansController extends Controller
{   
    public function __construct(){      
        $this->middleware(function ($request, $next) {     
            if(auth()->user()->type == 3 || auth()->user()->type == 4)
            {
                return redirect('/rkadmin')->with('success','You are not authorized to access that page.');
            
            }
            return $next($request);
        });
    }
    public function currentUser(){
        return Auth::user();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.plan.index');
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData()
    {
        $plans = Plan::select(['id', 'name', 'price', 'no_of_users','duration_months','no_of_sms','no_of_email']);

        $plans = $plans->orderBy('id', 'desc')->get();

        return Datatables::of($plans)
            ->addColumn('price', function ($plans) {
                return Helper::decimalNumber($plans->price);
            })
            ->addColumn('action', function ($plans) {

                $html = '';
				$html .= '<a href="'.route('admin.plan.edit', encrypt($plans->id)).'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>';
                
                $count = ClientPlan::where('plan_id',$plans->id)->count();

                if($count == 0){
                    $delete_btn = '<a class="btn btn-link plan-delete" data-toggle="tooltip" data-id='.$plans->id.' title="Delete"><i class="flaticon2-trash text-danger"></i></a>';
                    $html .= $delete_btn;  
                }
                return $html;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.plan.plan');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|regex:/^[0-9.]+$/u',
            'no_of_users' => 'required|numeric',
            'duration_months' => 'required|numeric',
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
        $plan = new Plan();
        $message = 'Plan has been added successfully';
        if($request->plan_id)
        {
            $planId = decrypt($request->plan_id);
            $plan = Plan::find($planId);
            $message = 'Plan has been updated successfully';
        }
        $plan->name = $request->name;
        $plan->price = $request->price;
        $plan->no_of_users = $request->no_of_users;
        $plan->no_of_sms = $request->no_of_sms ? $request->no_of_sms : 0;
        $plan->no_of_email = $request->no_of_email ? $request->no_of_email : 0;
        $plan->duration_months = $request->duration_months;
        $plan->description = $request->description;
        $plan->created_by = $this->currentUser()->id;
        $plan->save();
        return redirect(route('admin.plan'))->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified plan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $planId = decrypt($id);
            $plan = Plan::find($planId);
            return view('admin.plan.plan',compact('plan'));
        }catch(Exception $e)
        {
            abort(404);
        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plan = Plan::find($id);
		$plan->delete();
        Session::flash('success','Plan deleted successfully');
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deletePlan($id)
    {
        $plan = Plan::find($id);
		$plan->delete();
        Session::flash('success','Plan deleted successfully');
        return true;
    }
}
