<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Models\Permissions;
use App\Models\User;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;

class PermissionsController extends Controller
{
    public function getAllPermissions()
    {
		$user = JWTAuth::parseToken()->authenticate();
        // $permissions = Permissions::where('deleted', 0)
                        // ->where('client_id', '=', $user->organization_id)
                        // ->where('company_id', '=', $user->company_id)
                        // ->get();
        $permissions = [];
        $j = 0;
        $roleData = collect(Permission::orderby('name')->where('guard_name','api')->where('deleted',0)->get())->map(function($q,$i) use(&$permissions,&$j){
            $name = explode(': ',$q->name);
            $nameValue = $name[0];
            if(!empty($name[1]))
            {
                if(!isset($permissions[$name[0]])){
                    $j = 0;
                } else {
                    $j++;
                }
                $permissions[$name[0]][$j]['id'] = $q->id;
                $permissions[$name[0]][$j]['name'] = $name[1];
               
            }
            return $permissions;
        });
        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('permissions')
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $query = $request->searchTxt;
        $paginationData = Helper::paginationData($request);

        $paginated = Permissions::where('deleted', 0)
                        ->where('guard_name','api')
                        // ->where('client_id', '=', $user->organization_id)
                        // ->where('company_id', '=', $user->company_id)
                        ->where('name', 'LIKE', "%" . $query . "%")
                        ->orderBy($paginationData->sortField, $paginationData->sortOrder)
                        ->paginate($paginationData->size);
        $permissions = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('permissions', 'totalRecord', 'current'),
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
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make(
            $request->all(),
            ['name' => 'required|string'],//|unique:permissions,name
            ['name.unique' => 'Permission already exist.']
        );

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $permission = Permission::create([
            'name' => $request->name,
			'client_id'  => (int)$user->organization_id,
			'company_id'  => (int)$user->company_id,
			'guard_name' => (isset($request->guard_name))?$request->guard_name:'api',
        ]);

        //Add Action Log
        Helper::addActionLog($user->id, 'PERMISSION', $permission->id, 'CREATEPERMISSION', [], $permission->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Permission has been created successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		$user = JWTAuth::parseToken()->authenticate();
        $permission = Permissions::where('deleted', '0')
                            ->where('guard_name','api')
                            // ->where('client_id',$user->organization_id)
                            // ->where('company_id',$user->company_id)
                            ->find($id);

        if ($permission) {
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('permission'),
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Permission not found."
            ]);
        }
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
        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make(
            $request->all(),
            ['name' => 'required|string'],//|unique:permissions,name,' . $id
            ['name.unique' => 'Permission already exist.']
        );

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]); //['error'=>$validator->errors()]
        }

        $permission = Permission::where('deleted', '0')
                        // ->where('client_id',$user->organization_id)
                        // ->where('company_id',$user->company_id)
                        ->find($id);
        if ($permission) {
            $old = $permission->toArray();
            $permission->update([
                'name' => $request->name,
				'guard_name' => (isset($request->guard_name))?$request->guard_name:'api',
            ]);

            //Add Action Log
            Helper::addActionLog($user->id, 'PERMISSION', $permission->id, 'UPDATEPERMISSION', $old, $permission->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Permission has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Permission not found."
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $permission = Permissions::where('deleted', '0')
                        // ->where('client_id',$user->organization_id)
                        // ->where('company_id',$user->company_id)
                        ->find($id);
        if ($permission) {
            $permission->update(['deleted' => 1]);

            //Add Action Log
            Helper::addActionLog($user->id, 'PERMISSION', $permission->id, 'DELETEPERMISSION', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Permission has been deleted successfully.'
            ]);
        }

        return response()->json([
            'status' => 'FAIL',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    // add user wise permission
    public function userPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permission_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        $user = User::find($request->user_id);
        if($user){
            $permissionId = explode(',',$request->permission_id);
            if(!empty($permissionId)){
                $user->syncPermissions($permissionId);
            }
            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Permission has been added successfully.'
            ]);
        }else{
            return response()->json([
                'status' => 'FAIL',
                'message' => 'User not found.'
            ]);
        }
    }

    public function getUserPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }
        
        $userId = $request->user_id;
        
        $permission = \DB::table("model_has_permissions")->where("model_id",$userId)
        ->pluck('permission_id')
        ->toArray();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('permission'),
        ]);
    }
}
