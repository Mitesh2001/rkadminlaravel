<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
use App\Models\Subscriptions;
use Auth,DB;

class PagesController extends Controller
{ 

    public function currentUser(){
      return Auth::user();
    }

    public function dashboard()
    {
      //clients 
      $clients = Client::leftjoin('company','company.client_id','clients.id')
      ->select('company.id as companyId','company.client_id as clientsId','clients.created_by');
      $employees = User::join('master_users', 'master_users.m_user_id', '=', 'users.id');

      if($this->currentUser()->type == 3 || $this->currentUser()->type == 4){
        $clients->where('clients.created_by',$this->currentUser()->id);
        $employees->where('master_users.m_company_id',$this->currentUser()->company_id);
      }else{
        $employees->where('master_users.m_company_id',0);
      }

      $clientsCount = $clients->count();

      
      //$subscription = Subscriptions::whereDate('created_at','=', date('Y-m-d'));
      $date = date("Y-m-d");
      $runningSubscription = $closeSubscription = $employeesCount = $newSubscription = 0;
	  if($this->currentUser()->type == 3 || $this->currentUser()->type == 4){
		  $clientsData = $clients->get();
		  foreach($clientsData as $data){
		  $newSubscription = Subscriptions::whereDate('created_at','=', $date)->where('client_id',$data->clientsId)->where('company_id',$data->companyId)->count();
		  $runningSubscription = Subscriptions::whereDate('subscription_expiry_date','>=', $date)->where('client_id',$data->clientsId)->where('company_id',$data->companyId)->count();
		  $closeSubscription = Subscriptions::whereDate('subscription_expiry_date','<', $date)->where('client_id',$data->clientsId)->where('company_id',$data->companyId)->count();
		  }
	  }else{
		  $newSubscription = Subscriptions::whereDate('created_at','=', $date)->count();
		  $runningSubscription = Subscriptions::whereDate('subscription_expiry_date','>=', $date)->count();
		  $closeSubscription = Subscriptions::whereDate('subscription_expiry_date','<', $date)->count();
	  }
	  

      /* $clientsData = $clients->get();
	  

      foreach($clientsData as $data){

        $subscription = Subscriptions::whereDate('created_at','=', date('Y-m-d'))->where('client_id',$data->clientsId)->where('company_id',$data->companyId)->count();
        $newSubscription += $subscription;

        $runningSubscriptionQry = DB::select(DB::raw("SELECT COUNT(id) as runningSubscription FROM `company` WHERE `client_id` = '".$data->clientsId."' and `expiry_date` >='".date("Y-m-d")."'"));

        $closeSubscriptionQry = DB::select(DB::raw("SELECT COUNT(id) as closeSubscription FROM `company` WHERE `client_id` = '".$data->clientsId."' and `expiry_date` <='".date("Y-m-d")."'"));

        $runningSubscription += $runningSubscriptionQry[0]->runningSubscription;
        $closeSubscription += $closeSubscriptionQry[0]->closeSubscription;
      } */

      $employeesCount = $employees->count();
      
		  return view('admin.pages.dashboard',compact('clientsCount','employeesCount','newSubscription','runningSubscription','closeSubscription'));
    }
}
