<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Auth;
use Validator;
use Exception;
use Helper;

class EmailsControllerBackup extends Controller
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
        $user = Auth::user();
        $organization_id = $company_id = '';

        if(isset($user)){
            $organization_id = $user->organization_id;
            $company_id = $user->company_id;
        }

        $getInvoiceTemplateSend = EmailTemplate::select('email_template_id')->where('name','Invoice email send')->where('client_id',$organization_id)->where('company_id',$company_id)->where('default_template','0')->count();

        if($request->ajax()){

            if($getInvoiceTemplateSend==0){
                $emailTemplate = EmailTemplate::select(['email_template_id', 'name', 'subject'])->where('client_id','0')->where('company_id','0')->where('default_template','1');
            }else{
                $emailTemplate = EmailTemplate::select(['email_template_id', 'name', 'subject'])->where('client_id',$organization_id)->where('company_id',$company_id)->where('default_template','0');
            }
            $emailTemplate->orderBy('email_template_id', 'desc')->get();
            return Datatables::of($emailTemplate)
                ->addColumn('action', function ($emailTemplate) {
                    $html = '<a href="'.url('rkadmin/emails/edit/'.encrypt($emailTemplate->email_template_id)).'" class="btn btn-link" ><i class="flaticon2-pen text-success" data-toggle="tooltip" title="Edit"></i></a>';
                    return $html;
                })
                ->rawColumns(['action','content'])
                ->make(true);
        }
        return view('admin.email.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.email.form');
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
            'subject' => 'required|max:200',
            'content' => 'required',
        ],
        ['content.required' => 'The email template field is required.']
        );
        
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }

        $id = $request->id;
        $user_id = Auth::user()->id;
        $user = User::find($user_id);

        $getTemplate = EmailTemplate::find($id);

        if($getTemplate->default_template == 1){
            $emailTemplate = new EmailTemplate();
            $emailTemplate->default_template = 0;
            $emailTemplate->createdBy = $user_id;
            $emailTemplate->client_id = $user->organization_id;
            $emailTemplate->company_id = $user->company_id;
            $emailTemplate->name = $getTemplate->name;
        }else{
            $emailTemplate = EmailTemplate::find($id);
        }

        $emailTemplate->subject = $request->subject;
        $emailTemplate->content = $request->content;
        $emailTemplate->updatedBy = $user_id;
        $emailTemplate->save();

        return redirect('rkadmin/emails')->with('success', 'Your email template is successfully updated');
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
            $emailTemplate = EmailTemplate::find($id);

            $emailTemplateBody =  Helper::emailTemplateBody();

            $emailTemplateBody =  str_replace("{{#template_body}}", $emailTemplate->content, $emailTemplateBody);

            $emailTemplate->content = str_replace("{{#all_css}}", Helper::emailTemplateCss(), $emailTemplateBody);

            return view('admin.email.form',compact('emailTemplate'));
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
        //
    }
}
