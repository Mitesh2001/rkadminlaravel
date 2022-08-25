<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Commission;
use Yajra\DataTables\DataTables;
use Auth;
use Validator;
use Exception;
use Session;
use Helper;

class CommissionController extends Controller
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
    public function index(Request $request)
    {
        if($request->ajax()){
            $commission = Commission::join('users', 'commissions.dealer_distributor', '=', 'users.id');
            $commission = $commission->select(
                'users.name',
                'commissions.id',
                'commissions.is_payment_pending',
                'commissions.dealer_distributor', 
                \DB::raw('sum(commissions.commission_amt) as `commission_amt`'),
                \DB::raw('YEAR(commissions.created_at) year, MONTH(commissions.created_at) month'))
                ->groupby('year','month','dealer_distributor','is_payment_pending')
                ->orderBy('commissions.created_at', 'desc')
                ->get();

                return Datatables::of($commission)
                ->addColumn('commission_amt', function ($commission) {
                    return Helper::decimalNumber($commission->commission_amt);
                })
                ->addColumn('created_at', function ($commission) {
                    $dateObj   = \DateTime::createFromFormat('!m', $commission->month);
                    $monthName = $dateObj->format('F');

                    return $monthName.'-'.$commission->year;
                })
                ->addColumn('action', function ($commission) {
                    
                    if($commission->is_payment_pending == 'YES'){
                        $html = '<a href="'.url('rkadmin/commissions/paid/'.encrypt($commission->dealer_distributor)).'/'.$commission->month.'/'.$commission->year.'/'.$commission->is_payment_pending.'" class="btn btn-link" data-toggle="tooltip" title="Make Paid"><i class="flaticon2-check-mark text-success"></i></a>';
                    }else{
                        $html = '<span class="text-success" title="Amount received">Amount received</span>';
                    }
                    $html .= '<a href="'.url('rkadmin/commissions/view/'.encrypt($commission->dealer_distributor)).'/'.$commission->month.'/'.$commission->year.'/'.$commission->is_payment_pending.'" class="btn btn-link" data-toggle="tooltip" title="View"><i class="flaticon-eye text-success"></i></a>';
                    return $html;
                })
                ->rawColumns(['created_at','action'])
                ->make(true);
        }
        return view('admin.commission.index');
    }

    public function makePaid($dealerDistributor, $month, $year)
    {
        $commission = Commission::where('dealer_distributor', decrypt($dealerDistributor))->whereYear('commissions.created_at', $year)->whereMonth('commissions.created_at', $month)->update(['is_payment_pending'=>'NO']);
        
        if($commission){
            $message = 'Commission paid successfully.';
        }else{
            $message = 'Something went wrong.';
        }
        return redirect()->back()->with('success', $message);
    }

    public function viewSubscription($dealerDistributor, $month, $year, $status){
        return view('admin.commission.subscriptions',compact('dealerDistributor','month','year','status'));
    }

    public function anyData(Request $request){

        $dealerDistributor = decrypt($request->dealerDistributor);
        $month  = $request->month;
        $year   = $request->year;
        $status = $request->status;

        // \DB::enableQueryLog(); 
        $commission = Commission::query();

        $commission->leftJoin('subscriptions','commissions.subscription_id', '=', 'subscriptions.id');

        $commission->select( [
            'subscriptions.subscriptions_uid',
            'subscriptions.final_amount',
            'subscriptions.payment_mode',
            'subscriptions.created_at as sub_created_at',
            'subscriptions.subscription_expiry_date',
            'commissions.is_payment_pending',
            'commissions.created_at',
		] );
        
        $commission->whereRaw('month(commissions.created_at) = ?', $month);
        $commission->whereRaw('year(commissions.created_at) = ?', $year);

        $commission->where('commissions.dealer_distributor', $dealerDistributor);
        $commission->where('commissions.is_payment_pending', $status);

        $commission = $commission->get();
        // dd(\DB::getQueryLog());
        return Datatables::of($commission)
        ->addColumn('final_amount', function ($commission) {
            return Helper::decimalNumber($commission->final_amount);
        })				
        ->addColumn('created_at', function ($commission) {
            return date("d-m-Y",strtotime($commission->sub_created_at));
        })
        ->addColumn('expiry_date', function ($commission) {
            if($commission->subscription_expiry_date!=NULL){
                return date("d-m-Y",strtotime($commission->subscription_expiry_date));
            }else{
                return '';
            }
        })
        // ->rawColumns(['action','subscriptions_uid'])
        ->make(true);    
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
        //
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
        //
    }
}
