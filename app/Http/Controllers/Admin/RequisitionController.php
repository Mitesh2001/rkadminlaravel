<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Models\Requisition;
use App\Models\Company;
use App\Models\Client;
use Carbon\Carbon;
use Exception;
use Helper;

class RequisitionController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            $clientId = Requisition::pluck('client_id','client_id');
            $companyId = Requisition::pluck('company_id','company_id');
            $clientName = Client::whereIn('id',$clientId)->pluck('name','id');
            $companyName = Company::whereIn('id',$companyId)->pluck('company_name','id');
            return view('admin.requisition.index',compact('clientName','companyName'));
        }catch(Exception $e){
            abort(404);
        }
    }


    public function anyData(Request $request)
    {
        $requisition = Requisition::select(['id','client_id','company_id','type', 'quantity','status','created_at','note']);
        if ($request->client_id) {
            $requisition->where('client_id',$request->client_id);
        }
        if ($request->company_id) {
            $requisition->where('company_id',$request->company_id);
        }
        if ($request->status) {
            $status = $request->status == 1 ? 1 : ($request->status == 3 ? 0 : 2);
            $requisition->where('status',$status);
        }
        $requisition = $requisition->orderBy('id','desc')->get();
        return Datatables::of($requisition)
			->addColumn('client', function ($requisition) {
				return !empty($requisition->client->name) ? $requisition->client->name : null;
            })
			->addColumn('company', function ($requisition) {
				return !empty($requisition->company->company_name) ? $requisition->company->company_name : null;
            })
			->addColumn('type', function ($requisition) {
				return $requisition->type == 1 ? 'Email' : 'SMS';
            })
			->addColumn('created', function ($requisition) {
				return Carbon::parse($requisition->created_at)->format('d-m-Y');
            })
			->addColumn('status', function ($requisition) {
                $status = '';
                switch ($requisition->status) {
                    case '0':
                        $status = '<span class="label label-warning label-inline">Pending</span>';
                        break;
                    case '1':
                        $status = '<span class="label label-success label-inline">Accepted</span>';
                        break;
                    case '2':
                        $status = '<span class="label label-danger label-inline">Rejected</span>';
                        break;

                }
                return $status;
            })
            ->addColumn('action', function ($requisition) {
                $html = '-';
                if($requisition->status == 0)
                {
                    $html = '<div class="dropdown">';
                    $html.= '<button class="btn btn-sm btn-success dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>';

                    $html.= '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                  
                    $html.= '<a href="javascript:;" data-toggle="modal" data-target="#requisitions_modal" data-status="1" data-id="'.$requisition->id.'"  class="dropdown-item requisition_status">Accept</a>';

                    $html.= '<a href="javascript:;" data-toggle="modal" data-target="#requisitions_modal" data-status="2" data-id="'.$requisition->id.'"  class="dropdown-item requisition_status">Reject</a>';

                    $html.= '</div>';
                    
                    $html.= '</div>';
                }
                return $html;
            })
            ->addColumn('note', function ($requisition) {
                $note = Str::limit($requisition->note, 30, $end='...');
				return '<span title="'.$requisition->note.'">'.$note.'</span>';
            })
            ->rawColumns(['client','company','type','created','status','note','action'])
            ->make(true);
    }


    public function changeStatus(Request $request)
    {
        try{
            $id = $request->id;
            $type = $request->status;
            $requisition = Requisition::find($id);
            if($requisition){
                $requisition->status = $type;
                $requisition->note = $request->note;
                $requisition->save();

                // $company = Company::find($requisition->company_id);
                // if($company && $type == 1)
                // {
                //     if($requisition->type == 1)
                //     {
                //         $company->total_sms =  $company->total_sms + $requisition->quantity;
                //     }else{
                //         $company->total_email = $company->total_email + $requisition->quantity;
                //     }
                //     $company->save();
                // }
            }
            $msg = $type == 1 ? 'accepted' : 'rejected';
            return back()->with('success','Your requisition is successfully '.$msg);
        }catch(Exception $e){
            abrt(404);
        }
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
