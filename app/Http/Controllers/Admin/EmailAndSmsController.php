<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\EmailSmsLog;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class EmailAndSmsController extends Controller
{
    public function currentUser(){
        return Auth::user();
    }

    public function index()
    {
        $type = $this->currentUser()->type;
        return view('admin.email_sms_log.index',compact('type'));
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData(Request $request)
    {
        $email_sms_log = EmailSmsLog::join('users', 'email_sms_logs.user_id', '=', 'users.id');
        $email_sms_log->leftJoin('company', 'email_sms_logs.company_id', '=', 'company.id');
        //$email_sms_log->leftJoin('users as dd', 'company.created_by', '=', 'dd.id');
        //$email_sms_log->leftJoin('contacts', 'email_sms_logs.client_id', '=', 'contacts.id');
        $email_sms_log->leftJoin('clients', 'email_sms_logs.client_id', '=', 'clients.id');
        $email_sms_log->select(
            'email_sms_logs.response',
            'email_sms_logs.type',
            'users.name',
            'company.company_name',
            //'dd.name as dealer_distributor_name',
            'email_sms_logs.created_at as log_date',
            'clients.name as client_name',
            'email_sms_logs.client_number as mobile_no',
            'email_sms_logs.client_email as client_email'
        );
        
        if(isset($request->log_date)){

            $date       = explode('/',$request->log_date);
            $startDate  = trim($date[0],' ');
            $endDate    = trim($date[1],' ');
            
            $email_sms_log->whereRaw('date(email_sms_logs.created_at) >= ? and date(email_sms_logs.created_at) <= ? ',[$startDate, $endDate]);
        } 

        if(isset($request->log_type)){
            $email_sms_log->where('email_sms_logs.type', $request->log_type);
        }
        if($this->currentUser()->type == 3 || $this->currentUser()->type == 4){
			$userid = $this->currentUser()->id;
            //$email_sms_log->where('company.created_by', $this->currentUser()->id);
            $email_sms_log->where(function($q) use ($userid){
				$q->orWhere('company.created_by', $userid);
				$q->orWhere('clients.created_by', $userid);
			});
        }    
        $email_sms_log =  $email_sms_log->orderBy('email_sms_logs.created_at', 'desc')->get();

        return Datatables::of($email_sms_log)
        ->addColumn('status', function ($email_sms_log) {
          return ($email_sms_log->response == '1' ? 'Success' : 'Fail');
        })
        ->addColumn('created_at', function ($email_sms_log) {
            return  Carbon::parse($email_sms_log->log_date)->format('Y-m-d');;
        })
        ->make(true);
    }
}
