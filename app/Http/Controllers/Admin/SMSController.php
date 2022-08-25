<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use DB;
use Helper;
use JsValidator;
use Validator;
use App\Models\Client;
use App\Models\Products;
use App\Models\ProductCategory;
use App\Models\Company;
use App\Models\SmsSettings;
use Carbon\Carbon;
use Exception;
use Session;
use URL;
use Auth;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class SMSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try{
            
            $settings = SmsSettings::find(1);

            return view('admin.sms.index', compact('settings'));

        }catch(Exception $e){
            abort(404);
        }
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
            'api_url' => 'required|string', 
        ]);
                
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }

        $user_id = Auth::user()->id;
        $api_url = $request->api_url;
        $parameters = $request->parameters;
        $values = $request->values;

        $arr = array();     
        $x = 1;
        foreach($parameters as $parameter) {
            array_push($arr, ['key' => $parameters[$x], 'value' => $values[$x]]);
            $x++;
        } 

        $settings = SmsSettings::find(1);
        
        if(!empty($settings) > 0) {
            
            $settings->update([
                'api_url' => $api_url,
                'parameters' => json_encode($arr),
                'final_url' => $request->url_preview,
                'updated_by' => $user_id,
                'mobile_param' => $request->mobile_param,
                'msg_param' => $request->msg_param,
                'is_tested' => 0,
                'is_working' => 0,
            ]);

        } else { 
            SmsSettings::create([
                'api_url' => $api_url,
                'parameters' => json_encode($arr),
                'final_url' => $request->url_preview,
                'updated_by' => $user_id,
                'mobile_param' => $request->mobile_param,
                'msg_param' => $request->msg_param,
                'is_tested' => 0,
                'is_working' => 0,
            ]);
        } 
 
        return redirect()->back()->with('success', 'SMS API Settings is successfully updated');
    }

    public function updateParameters(Request $request)
    {
        $form_data = $request->form_data;
        
        $data = [];
        parse_str($form_data, $data); 

        $user_id = Auth::user()->id;
        $parameters = $data['parameters'] ?? [];
        $values = $data['values'] ?? [];
  
        $arr = array();     
        $x = 1;
        foreach($parameters as $key => $parameter) {
            array_push($arr, ['key' => $parameter, 'value' => $values[$key]]);
            $x++;
        } 
 
        try {

            SmsSettings::find(1)->update([ 
                'parameters' => json_encode($arr),
                'final_url' => $data['url_preview'],
                'updated_by' => $user_id,
                'is_tested' => 0,
                'is_working' => 0,
            ]);
            
     
            return response()->json([
                'status' => true,
                'message' => "Parameter successfully removed",
            ]);

        } catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Something went to wrong!",
            ]);
        } 
    }
 
 
    public function testAPI(Request $request)
    {
        $number = $request->number;
        $message = $request->message;
        
        $user = Auth()->user();
        $shortcodes = Helper::shortcodes($user);

        $test = Helper::sendSMS(1, [$number], $shortcodes, $user, $message); 

        if($test == true) {
            SmsSettings::find(1)->update([  
                'is_tested' => 1,
                'is_working' => 1,
            ]);

            $alert = "success";
            $message = "SMS configuration is working. Please check sms on ". $number;

        } else {
            SmsSettings::find(1)->update([  
                'is_tested' => 1,
                'is_working' => 0,
            ]);

            $alert = "error";
            $message = "SMS configuration is not working! please check configuration and test again.";
        }
         
        return redirect()->back()->with($alert, $message); 
    }
}
