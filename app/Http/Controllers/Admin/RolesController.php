<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Client;
use App\Models\Company;
use App\Models\CompanyPermission;
use Exception;
use DB;
use Helper;
use JsValidator;
use Validator;
use Auth;

class RolesController extends Controller
{
    public function __construct(){      
        // $this->middleware(function ($request, $next) {     
        //     if(auth()->user()->type == 3 || auth()->user()->type == 4)
        //     {
        //         $pathinfo = $request->query();
        //         if(!isset($pathinfo['company_id'])){
        //             return redirect('/rkadmin')->with('success','You are not authorized to access that page.');
        //         }
        //     }
        //     return $next($request);
        // });
    }
	
    public function currentUser(){
        return Auth::user();
    }

	private $validationRules = [
        'name' => 'required|regex:/^[\pL\s\-]+$/u',
        'permission' => 'required',
    ];

    private $validationMessages = [
        'name.required' => 'Please enter role.',
        'permission.required' => 'Please select atleast one permission'
    ];

    public function index(Request $request)
    {
        try{
            $type = $request->type;
            // if (request()->ajax()) {
            //     $user = auth()->guard('admin')->user();
            //     if($user->hasRole('super-admin')){
            //         $query = Role::query()->where('deleted', '0');
            //     } else {
            //         $query = Role::where('id', '!=', '1')->where('deleted', '0');
            //     }

            //     return DataTables::of($query)
            //         ->editColumn('action', function ($role) {
            //             $companyIdUrl = null;
            //             if($request->company_id){
            //                 $companyIdUrl = 'company_id='.encrypt($companyId);
            //             }
            //             return '<a href="' . url('rkadmin/roles/'.Helper::encrypt($role->id).'/edit?'.$companyIdUrl) . '" class="btn btn-flat btn-xs bg-navy"><i class="fa fa-edit"> </i> Edit</a>';
            //         })
            //         ->editColumn('id', '{{Helper::encrypt($id)}}')
            //         ->toJson();
            // }
            $companyId = null;
            $companyData = null;
            if($request->company_id)
            {
                $companyId = decrypt($request->company_id);
                $companyData = Helper::getCompany($companyId);
                $type = 'api';
            }
            return view('admin.roles.index')->with([
                'title' => 'Roles',
                'action' => 'listing',
                'companyId' => $companyId,
                'companyData' => $companyData,
                'type' => $type,
            ]);
        }catch(Exception $e)
        {
            abort(404);
        }
    }
	
	public function anyData(Request $request)
    {
			$roles = Role::select(['name','id','client_id'])->where('deleted', 0);
            $type = $request->type;
            if($request->company_id)
            {
                $roles = $roles->where('company_id',$request->company_id);
            }elseif($type){
                if($type == 'api'){
                    $roles->whereNull('company_id');
                }
                $roles = $roles->where('guard_name',$type);
            }else{
                $roles = $roles->whereNull('company_id');
            }
            
            $roles = $roles->orderBy('id', 'desc')->get();
			return Datatables::of($roles)
				->addColumn('namelink', function ($roles) {
					return $roles->name;
				})
				// ->addColumn('clientname', function ($roles) {
                //     $client = Client::find($roles->client_id);
				// 	return (isset($client->name)) ? $client->name : NULL;
				// })
				->addColumn('action', function ($roles) use($type){
                    $companyIdUrl = null;

                    if(request()->company_id){
                        $companyIdUrl = 'company_id='.encrypt(request()->company_id);
                    }
					
                    $html = '';
                
                    $edit_btn = '<a href="'.url('rkadmin/roles/'.Helper::encrypt($roles->id).'/edit?'.$companyIdUrl) .'" class="btn btn-link" data-toggle="tooltip" title="Edit"><i class="flaticon2-pen text-success"></i></a>';
                    $html .= $edit_btn;
                    
                    if(!request()->company_id && $type != 'web')
                    {
                        $html .= '<a href="'.url('rkadmin/roles/'.Helper::encrypt($roles->id).'/edit?'.$companyIdUrl.'&is_assign=1').'" class="label label-lg label-primary label-inline" data-toggle="tooltip" title="Assign">Assign</a>';
                    }
                   
					return $html;
				})
				->rawColumns(['namelink', 'action'])
				->make(true);
    }
	

    public function create(Request $request)
    {
        try{
            $companyId = $request->company_id;
            $type = $request->type;
            if($type && !in_array($type,['web','api'])){
                abort(404);
            }
            $companyData = [];
            if($companyId)
            {
                $companyId = decrypt($companyId);
                $companyData = Helper::getCompany($companyId);
                $type = 'api';
            }
            $this->validationRules['name'] = $this->validationRules['name'].'|unique:roles,name';

            $jsValidator = JsValidator::make($this->validationRules, $this->validationMessages, [], '#frmDetail');
            $roleData = [];
            // $permission = Permission::query();
            // if($companyId)
            // {
            //     $assignedPermssion = Helper::getPermissionIds($companyId);
            //     $permission = $permission->whereIn('id',$assignedPermssion);
            // }else{
            //     $assignedPermssion = CompanyPermission::pluck('permission_id')->toArray();
            //     $permission = $permission->whereNotIn('id',$assignedPermssion);
            // }
            $permission = Permission::orderby('name');
            if($type){
                $permission = $permission->where('guard_name',$type);
            }
            $permissions = collect($permission->get())->map(function($q) use(&$roleData){
                $name = explode(': ',$q->name);
                $nameValue = $name[0];
                if(!empty($name[1]))
                {
                    $roleData[$name[0]][$q->id] = $name[1];
                }
                return $roleData;
            });
            $clients = Company::pluck('company_name', 'id');
            
            return view('admin.roles.create')->with([
                'title' => 'Roles',
                'action' => 'add',
                'clients' => $clients,
                'jsValidator' => $jsValidator,
                'permissions' => $permissions,
                'roleData' => $roleData,
                'companyId' => $companyId,
                'companyData' => $companyData,
                'type' => $type,
            ]);
        }
        catch(Exception $e)
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
        $isAssign = $request->is_assign;
        $role = Role::find($id);
        $companyId = $request->company_id;
        $type = 0;
        $companyData = [];
        if($companyId)
        {
            $companyId = decrypt($companyId);
            $companyData = Helper::getCompany($companyId);
        }
        $rolePermissions = DB::table("role_has_permissions");
        $companyIds = [];
        if($isAssign)
        {
            $roleId = Role::where('parent_id',$id)->pluck('id')->toArray();
            $companyIds = Role::where('parent_id',$id)->pluck('company_id')->toArray();
            $roleId = !empty($roleId) ? $roleId : $id;
            $rolePermissions = $rolePermissions->where("role_has_permissions.role_id", $roleId);
        }else{
            $rolePermissions = $rolePermissions->where("role_has_permissions.role_id", $id);
        }
        $roleData = [];
        $rolePermissions = $rolePermissions->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        // $permission = Permission::query();
        // if($companyId)
        // {
        //     $companyId = decrypt($companyId);
        //     $assignedPermssion = Helper::getPermissionIds($companyId);
        //     $permission = $permission->whereIn('id',$assignedPermssion);
        // }else{
        //     $assignedPermssion = CompanyPermission::pluck('permission_id')->toArray();
        //     $permission = $permission->whereNotIn('id',$assignedPermssion);
        // }
        // $permission = $permission->orderby('name')->get();
        $type = $role->guard_name;
        
        $permission = Permission::orderby('name');
        if($type){
            $permission = $permission->where('guard_name',$type);
        }
        $permissions = collect($permission->get())->map(function($q) use(&$roleData){
            $name = explode(': ',$q->name);
            $nameValue = $name[0];
            if(!empty($name[1]))
            {
                $roleData[$name[0]][$q->id] = $name[1];
            }
            return $roleData;
        });

        
        $jsValidator = JsValidator::make($this->validationRules, [], $this->validationMessages, '#frmDetail');
       
        $clients = Company::pluck('company_name', 'id');

        return view('admin.roles.edit')->with([
            'title' => 'Roles',
            'action' => 'edit',
            'role' => $role,
            'rolePermissions' => $rolePermissions,
			'clients' => $clients,
            'permissions' => $permissions,
            'jsValidator' => $jsValidator,
            'companyId' => $companyId,
            'roleData' => $roleData,
            'companyIds' => $companyIds,
            'companyData' => $companyData,
            'isAssign' => $isAssign,
            'type' => $type,
        ]);
    }

    public function update(Request $request, $id)
    {
        return $this->post_process('update', $id, $request);
    }

    public function destroy($id)
    {
        $id = Helper::decrypt($id);

        DB::table("roles")->where('id', $id)->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully');
    }

    private function post_process($action, $id, $request)
    {
        /* $validation = Validator::make($request->all(), $this->validationRules);

        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors());
        } */
		
		$validation = Validator::make($request->all(), [
            // 'company_id' => 'required|numeric',
            'name' => 'required|string|regex:/^[\pL\s\-]+$/u',
        ]);
		
        if ($validation->fails()) {
            return redirect()->back()->withErrors($validation->errors())->withInput();
        }
		
		//DB::enableQueryLog(); 
        $companyData = Company::find($request->company_id);
        if ($action == 'add') {
			//Check duplicate value
            $row = Role::where([
                ['name', '=', $request->name],
                ['client_id', '=', isset($companyData) ? $companyData->client_id : 0],
                ['company_id', '=', $request->company_id],
            ])->first();

            if (!empty($row)) {
                return redirect()->back()->with('error', 'Role already exist.');
            }
			
            $role = new Role;
            $message = 'Role has been added successfully';
        } else {
            $id = Helper::decrypt($id);
            $role = Role::find($id);
            $message = 'Role has been updated successfully';
			
			if ($role->name !== $request->name) {
                $row = Role::where([
                    ['id', '!=', $id],
                    ['client_id', '=', isset($companyData) ? $companyData->client_id : 0],
                    ['company_id', '=', $request->company_id],
                    ['name', '=', $request->name],
                ])->first();

                if (!empty($row)) {
                    return redirect()->back()->with('error', 'Role already exist.');
                }
            }
        }
        $url = null;
        if(is_array($request->company_id)){
            $data = [];
            $roleId = $request->role_id;
            if($roleId)
            {
                $role = Role::find($roleId);
                $role->name = $request->name;
                $role->save();
            }
            foreach($request->company_id as $key=>$row)
            {
                $roles = Role::where('company_id',$row)->where('parent_id',$role->id)->first();
                if(!$roles){
                    $roles = new Role();
                }
                $companyData = Company::find($row);
                $roles->name = $request->input('name');
                $roles->client_id = isset($companyData) ? $companyData->client_id : 0;
                $roles->company_id = $row;
                $roles->parent_id = $role->id;
                $roles->guard_name = (isset($request->guard_name))?$request->guard_name:'api';
                $roles->created_by = $this->currentUser()->id;
                $roles->save();
                $roles->syncPermissions($request->input('permission'));
                $url = 'is_assign=1';
            }
        }else{
            $role->name = $request->input('name');
            $role->client_id = isset($companyData) ? $companyData->client_id : 0;
            $role->company_id = $request->input('company_id') ? $request->input('company_id') : null;
            $role->guard_name = (isset($request->guard_name))?$request->guard_name:'api';
            $role->created_by = $this->currentUser()->id;
            $role->save();
            $role->syncPermissions($request->input('permission'));
        }
		
		/* $print = DB::getQueryLog();
		echo '<pre>';print_r($print);
		exit; */
        $companyId = null;
        if($request->is_company)
        {
            $companyId = encrypt($role->company_id);
            $url = '?company_id='.$companyId;
        }else{
            $url = '?type='.$role->guard_name;
        }
        return redirect('rkadmin/roles'.$url)->with('success', $message);
    }
}
