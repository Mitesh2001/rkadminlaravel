<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Auth;
use Validator;
use Exception;
use Session;

class AnnouncementController extends Controller
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
            $announcement = Announcement::select(['id', 'announcement', 'start_date_time','end_date_time', 'created_by']);
            $announcement = $announcement->orderBy('id', 'desc')->get();
            
            return Datatables::of($announcement)
                ->addColumn('start_date_time', function ($announcement) {
                    return Carbon::parse($announcement->start_date_time)->format('d M Y H:i');
                })
                ->addColumn('end_date_time', function ($announcement) {
                    return Carbon::parse($announcement->end_date_time)->format('d M Y H:i');
                })
                ->addColumn('craetedBy', function ($announcement) {
                    return !empty($announcement->getCreatedBy->name) ? $announcement->getCreatedBy->name : null;
                })
                ->addColumn('action', function ($announcement) {
                    $html = '<a href="'.url('rkadmin/announcement/edit/'.encrypt($announcement->id)).'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>';

                    $html .= '<a class="btn btn-link announcement-delete" data-id='.$announcement->id.'><i class="flaticon2-trash text-danger" data-toggle="tooltip" title="Delete"></i></a>';

                    return $html;
                })
                ->rawColumns(['end_date', 'craetedBy','action'])
                ->make(true);
        }
        return view('admin.announcement.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.announcement.form');
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
            'announcement' => 'required',
            'announcement_date_time' => 'required',
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
        $announcement = new Announcement();
        $id = $request->id;$message = "Your announcement is successfully added";
        if($id){
            $announcement = $announcement->find($id);
			$message = "Your announcement is successfully updated";
        }
        
        $announcement_date_time = explode('-',$request->announcement_date_time);

        $announcement->announcement = $request->announcement;
        $announcement->start_date_time = date('Y-m-d H:i:s',strtotime($announcement_date_time[0]));
        $announcement->end_date_time = date('Y-m-d H:i:s',strtotime($announcement_date_time[1]));
		if(!$id){
        $announcement->created_by = $this->currentUser()->id;
		}
        $announcement->save();
        return redirect('rkadmin/announcement')->with('success', $message);
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
        try{
            $id = decrypt($id);
            $announcement = Announcement::find($id);
            $announcement->announcement_date_time = Carbon::parse($announcement->start_date_time)->format('Y/m/d H:i:s') .' - '.Carbon::parse($announcement->end_date_time)->format('Y/m/d H:i:s');
            return view('admin.announcement.form',compact('announcement'));
        }catch(Exception $e){
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
        $announcement = Announcement::find($id);
		$announcement->delete();
        Session::flash('success','Announcement deleted successfully');
        return true;
    }
}
