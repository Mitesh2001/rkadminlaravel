<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\NoticeBoard;
use App\Models\User;
use App\Models\Company;
use Carbon\Carbon;
use Validator;
use Exception;
use Auth;
use Session;

class NoticeBoardController extends Controller
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
    public function index(Request $request)
    {
        if($request->ajax()){
            $notice = NoticeBoard::select(['id', 'notice','description','start_date_time', 'end_date_time','user_id','created_by']);
            
            $notice = $notice->orderBy('id', 'desc')->get();
            
            return Datatables::of($notice)
                ->addColumn('start_date_time', function ($notice) {
                    return Carbon::parse($notice->start_date_time)->format('d M Y H:i');
                })
                ->addColumn('end_date_time', function ($notice) {
                    return Carbon::parse($notice->end_date_time)->format('d M Y H:i');
                })
                ->addColumn('user_id', function ($notice) {
                    return !empty($notice->getUser->name) ? $notice->getUser->name : null;
                })
                ->addColumn('description', function ($notice) {
                    return !empty($notice->description) ? Str::limit($notice->description, 70, $end='...') : null;
                })
                ->addColumn('created_by', function ($notice) {
                    return !empty($notice->getCreatedBy->name) ? $notice->getCreatedBy->name : null;
                })
                ->addColumn('action', function ($notice) {
                    $html = '<a href="'.url('rkadmin/notice-board/edit/'.encrypt($notice->id)).'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>';

                    $html .= '<a class="btn btn-link notice-delete" data-id='.$notice->id.'><i class="flaticon2-trash text-danger" data-toggle="tooltip" title="Delete"></i></a>';

                    return $html;
                })
                ->rawColumns(['end_date_time','user_id','created_by','action'])
                ->make(true);
        }
        return view('admin.notice.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $company = Company::pluck('company_name', 'id')->toArray();
        $users = User::pluck('name','id')->toArray();
        return view('admin.notice.form',compact('users','company'));
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
            'notice' => 'required',
            'user_id' => 'required_without:company_id',
            'notice_date_time' => 'required',
            'description' => 'required'
        ]);
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
        $notice = new NoticeBoard();
        $id = $request->id;$message = 'Your notice is successfully added';
        if($id){
            $notice = $notice->find($id);$message = 'Your notice is successfully updated';
        }

        $notice_date_time = explode('-',$request->notice_date_time);

        $notice->notice = $request->notice;
        $notice->company_id = $request->company_id;
        $notice->user_id = $request->user_id;
        $notice->description = $request->description;
        $notice->start_date_time = date('Y-m-d H:i:s',strtotime($notice_date_time[0]));
        $notice->end_date_time = date('Y-m-d H:i:s',strtotime($notice_date_time[1]));
        $notice->created_by = Auth::user()->id;
        $notice->save();
        return redirect('rkadmin/notice-board')->with('success', $message);
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
            $notice = NoticeBoard::find($id);
            $users = User::pluck('name','id')->toArray();
            $company = Company::pluck('company_name','id')->toArray();

            $notice->notice_date_time = Carbon::parse($notice->start_date_time)->format('Y/m/d H:i:s') .' - '.Carbon::parse($notice->end_date_time)->format('Y/m/d H:i:s');

            return view('admin.notice.form',compact('notice','users','company'));
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
        $notice = NoticeBoard::find($id);
		$notice->delete();
        Session::flash('success','Notice deleted successfully');
        return true;
    }

    public function getCompanyWiseUsers($id)
    {
        $users = User::select(['id','name as text'])->where('company_id',$id)->get();
        $i = 0;
        foreach ($users as $user){
            if($i==0){
                $list[] = array('id' => '', 'text' => 'Please select user');
            } 
            $list[] = array('id' => $user->id, 'text' => $user->text);
            $i++;
        }
        return response()->json(['success'=>true, 'users'=> $list]);
    }
}
