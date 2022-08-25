<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailSmsLog;
use JWTAuth;
use Helper;

class EmailAndSmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $paginationData = Helper::paginationData($request);
        $query = $request->searchTxt;
        $from_date = $request->from_date;
        $to_date = $request->to_date;
        $type = $request->type;
        $paginated = EmailSmsLog::join('users', 'email_sms_logs.user_id', '=', 'users.id')
        ->select(
            'email_sms_logs.log_id',
            'email_sms_logs.response',
            'email_sms_logs.type',
            'users.name',
            'email_sms_logs.created_at'
        )
        ->where('email_sms_logs.company_id',$user->company_id)
        ->where(function($query1) use ($query) {
            $query1->where('users.name', 'LIKE', "%" . $query . "%");
        })
        ->where(function($query1) use ($query, $from_date, $to_date, $type) {
            if(isset($from_date) and isset($to_date)){
                $query1->whereRaw('date(email_sms_logs.created_at) >= ? and date(email_sms_logs.created_at) <= ? ',[$from_date, $to_date]);
            }
            if(isset($type)){
                $query1->where('email_sms_logs.type', $type);
            }
        })
        ->orderBy('email_sms_logs.'.$paginationData->sortField, $paginationData->sortOrder)->paginate($paginationData->size);
        $email_sms_log = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('email_sms_log', 'totalRecord', 'current')
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
