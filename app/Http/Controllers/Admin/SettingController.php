<?php

namespace App\Http\Controllers\Admin;

use DB;
use Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Valuestore\Valuestore;
use App\Models\EmailTemplate;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$subs = array('notificationbeforeexpire1','notificationbeforeexpire2','notificationbeforeexpire3','notificationbeforeexpire4','notificationbeforeexpire5','notificationbeforeexpire6');
		$templates = EmailTemplate::whereIn('external_key',$subs)->where('template_type','=',1)->get();
		$alltemplates = array();
		if($templates){
		foreach($templates as $temp){
			$alltemplates[$temp->external_key] = $temp->name;
		}
		}

        return view('admin.settings.index',compact('alltemplates'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data['app_name'] = $request->app_name ?? "";
        $data['mail_from'] = $request->mail_from ?? "";
        $data['mail_from_name'] = $request->mail_from_name ?? "";
        $data['app_link'] = $request->app_link ?? "";

        $data['sb_mail_1'] = $request->sb_mail_1;
        $data['sb_mail_2'] = $request->sb_mail_2;
        $data['sb_mail_3'] = $request->sb_mail_3;
        $data['sb_mail_4'] = $request->sb_mail_4;
        $data['sb_mail_5'] = $request->sb_mail_5;
        $data['sb_mail_6'] = $request->sb_mail_6;

		$data['sb_mail_day_1'] = $request->sb_mail_day_1;
        $data['sb_mail_day_2'] = $request->sb_mail_day_2;
        $data['sb_mail_day_3'] = $request->sb_mail_day_3;
        $data['sb_mail_day_4'] = $request->sb_mail_day_4;
        $data['sb_mail_day_5'] = $request->sb_mail_day_5;
        $data['sb_mail_day_6'] = $request->sb_mail_day_6;

        $data['sb_mail_1_status'] = $request->sb_mail_1_status ? 1 : 0;
        $data['sb_mail_2_status'] = $request->sb_mail_2_status ? 1 : 0;
        $data['sb_mail_3_status'] = $request->sb_mail_3_status ? 1 : 0;
        $data['sb_mail_4_status'] = $request->sb_mail_4_status ? 1 : 0;
        $data['sb_mail_5_status'] = $request->sb_mail_5_status ? 1 : 0;
        $data['sb_mail_6_status'] = $request->sb_mail_6_status ? 1 : 0;

        $valuestore = Valuestore::make(storage_path('app/settings.json'));

        foreach ($data as $key => $value) {
            $valuestore->put($key, $value);
        }


        return redirect()->back()->with('success', 'Settings successfully updated!');
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
