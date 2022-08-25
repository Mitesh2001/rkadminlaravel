<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use Spatie\Permission\Models\Permission;

use Helper;
use JsValidator;
use Validator;
use Exeception;
use App\Models\Client;
use App\Models\Company;
use App\Models\CompanyPermission;

class PermissionsController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['role:super-admin']);
    }

    private $validationRules = [
        'name' => 'required|unique:permissions,name',
    ];

    private $validationMessages = [
        'name.required' => 'Please enter permission name.',
    ];

    public function index(Request $request)
    {
        try{
            $roleData = [];
            if (request()->ajax()) {
                return DataTables::of(Permission::query())
                    ->editColumn('action', function ($permission) {
                        return '<a href="' . route('admin.permissions.edit', Helper::encrypt($permission->id)) . '" class="btn btn-flat btn-xs bg-navy"><i class="fa fa-edit"> </i> Edit</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="btn btn-flat btn-xs btn-danger" onclick="_do_delete(\'' . route('admin.permissions.destroy', Helper::encrypt($permission->id)) . '\')"><i class="fa fa-trash"> </i> Delete</a>';
                    })
                    ->editColumn('id', '{{Helper::encrypt($id)}}')
                    ->toJson();
            }
            $assignedPermssion = [];
            $companyId = $request->company_id;
            $companyData = null;
            if($companyId){
                $companyId = decrypt($companyId);
                $companyData = Helper::getCompany($companyId);
                $assignedPermssion = Helper::getPermissionIds($companyId);
                $permissions = collect(Permission::orderby('name')->get())->map(function($q) use(&$roleData){
                    $name = explode(': ',$q->name);
                    $nameValue = $name[0];
                    if(!empty($name[1]))
                    {
                        $roleData[$name[0]][$q->id] = $name[1];
                    }
                    return $roleData;
                });
            }
            return view('admin.permissions.index')->with([
                'title' => 'Permissions',
                'action' => 'listing',
                'companyId' => $companyId,
                'companyData' => $companyData,
                'roleData' => $roleData,
                'assignedPermssion' => $assignedPermssion,
            ]);
        }catch(Exception $e)
        {
            abort(404);
        }
    }
	
	public function anyData(Request $request)
    {
			$permissions = Permission::select(['name','id','client_id'])->where('deleted', 0);
            $companyId = $request->company_id;
            if($companyId){
                $permissions = $permissions->where('company_id',$companyId);
            }
			return Datatables::of($permissions)
				->addColumn('namelink', function ($permissions) {
					return '<a href="/permissions/' . Helper::encrypt($permissions->id) . '" ">' . $permissions->name . '</a>';
				})
				->addColumn('clientname', function ($permissions) {
                    $client = Client::find($permissions->client_id);
					return (isset($client->name)) ? $client->name : NULL;
				})
				->addColumn('action', function ($permissions) {
					$html = '<form action="'.route('admin.permissions.destroy', Helper::encrypt($permissions->id)).'" method="POST">';
					//if(\Entrust::can('permission-update'))
					$html .= '<a href="'.route('admin.permissions.edit', Helper::encrypt($permissions->id)).'" class="btn btn-link" ><i class="flaticon2-pen text-success"></i></a>';
					$html .= '<input type="hidden" name="_method" value="DELETE">';
					//if(\Entrust::can('permission-delete'))
					$html .= '<button type="submit" name="submit" value="' . __('Delete') . '" class="btn btn-link" onClick="return confirm(\'Are you sure? \')""><i class="flaticon2-trash text-danger"></i></button>';
					$html .= csrf_field();
					$html .= '</form>';
					return $html;
				})
				->rawColumns(['namelink', 'clientname', 'action'])
				->make(true);
    }

    public function create(Request $request)
    {
        try{
            $companyId = $request->company_id;
            if($companyId){
                $companyId = decrypt($companyId);
            }
            $jsValidator = JsValidator::make($this->validationRules, $this->validationMessages, [], '#frmDetail');
            
            $clients = Company::pluck('company_name', 'id');
            $clients->prepend('Please Select client', '');
    
            return view('admin.permissions.create')->with([
                'title' => 'Permissions',
                'action' => 'add',
                'clients' => $clients,
                'jsValidator' => $jsValidator,
                'companyId' => $companyId,
            ]);
        }catch(Exception $e)
        {
            abort(404);
        }
    }

    public function store(Request $request)
    {
        return $this->post_process('add', 0, $request);
    }

    public function show($id)
    {

    }

    public function edit($id,Request $request)
    {
        $id = Helper::decrypt($id);
        $permission = Permission::findOrFail($id);
        $companyId = $request->company_id;
        if($companyId)
        {
            $companyId = decrypt($companyId);
        }
        $jsValidator = JsValidator::make($this->validationRules, [], $this->validationMessages, '#frmDetail');
		
		$clients = Company::pluck('company_name', 'id');
		$clients->prepend('Please Select client', '');

        return view('admin.permissions.edit')->with([
            'title' => 'Permissions',
            'action' => 'edit',
			'clients' => $clients,
            'permission' => $permission,
            'jsValidator' => $jsValidator,
            'companyId' => $companyId,
        ]);
    }

    public function update(Request $request, $id)
    {
        return $this->post_process('update', $id, $request);
    }

    public function destroy($id)
    {
        $id = Helper::decrypt($id);
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted successfully');
    }

    private function post_process($action, $id, $request)
    {
        $validation = Validator::make($request->all(), $this->validationRules);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors());
        }
        $company = Company::find($request->company_id);
        if ($action == 'add') {
			
			//Check duplicate value
            $row = Permission::where([
                ['name', '=', $request->name],
                ['guard_name', '=', $request->guard_name],
                ['client_id', '=', 0],
                ['company_id', '=', null],
            ])->first();

            if (!empty($row)) {
                return redirect()->back()->with('error', 'Permission already exist.');
            }
			
            $permission = new Permission;
            $message = 'Permission has been added successfully';
        } else {
            $id = Helper::decrypt($id);
            $permission = Permission::findOrFail($id);
            $message = 'Permission has been updated successfully';

            //Check duplicate value
            if ($permission->name !== $request->name) {
                $row = Permission::where([
                    ['id', '!=', $id],
					['client_id', '=', 0],
                    ['guard_name', '=', $request->guard_name],
                    ['company_id', '=', null],
                    ['name', '=', $request->name],
                ])->first();

                if (!empty($row)) {
                    return redirect()->back()->with('error', 'Permission already exist.');
                }
            }
        }

        $permission->name = $request->name;
        $permission->client_id = 0;
        $permission->company_id = null;
        $permission->guard_name = (isset($request->guard_name))?$request->guard_name:'api';
        $permission->save();
        $companyId = null;
        if($request->is_company)
        {
            $companyId = encrypt($permission->company_id);
        }
        return redirect('rkadmin/permissions/'.Helper::encrypt($permission->id).'/edit?company_id='.$companyId)->with('success', $message);
    }

    public function permissionAssign(Request $request)
    {
        $companyPermission = CompanyPermission::where('company_id',$request->company_id)->delete();
        $permissionIds = $request->permission;
        $company = Company::find($request->company_id);
        $userId = auth()->user()->id;
        if(!empty($permissionIds))
        {
            $data = [];
            foreach($permissionIds as $key=>$row)
            {
                $data[$key]['client_id'] = $company->client_id;
                $data[$key]['company_id'] = $company->id;
                $data[$key]['permission_id'] = $row;
                $data[$key]['assigned_by'] = $userId;
                $data[$key]['created_at'] = date('Y-m-d H:i:s');
                $data[$key]['updated_at'] = date('Y-m-d H:i:s');
            }
            CompanyPermission::insert($data);
        }
        return redirect()->back()->with('success', 'Successfully your Permission is assigned in company.');
    }
}
