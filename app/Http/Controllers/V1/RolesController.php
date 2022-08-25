<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Helper;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function getAllRoles(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $roles = Role::where('client_id', '=', $user->organization_id)->where('guard_name','api')->where('deleted', '0')->get();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('roles')
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
        $paginated = Role::where('client_id', '=', $user->organization_id)->where('company_id',$user->company_id)->where('guard_name','api')->where('deleted', '0')->where('name', 'LIKE', "%" . $query . "%")
            ->orderBy($paginationData->sortField, $paginationData->sortOrder)
            ->paginate($paginationData->size);
        $roles = $paginated->getCollection();
        $totalRecord = $paginated->total();
        $current = $paginated->currentPage();

        return response()->json([
            'status' => 'SUCCESS',
            'data' => compact('roles', 'totalRecord', 'current')
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

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
            'parent_id' => 'integer|nullable'
        ]);

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $role = Role::create([
            'name'       => $request->name,
            'client_id'  => (int)$user->organization_id,
            'company_id'  => (int)$user->company_id,
            'parent_id'  => (int)$request->parent_id,
            'guard_name'  => 'api',
        ]);
        $role->givePermissionTo($request->input('permissions'));

        //Add Action Log
        Helper::addActionLog($user->id, 'ROLE', $role->id, 'CREATEROLE', [], $role->toArray());

        return response()->json([
            'status' => 'SUCCESS',
            'message' => 'Role has been created successfully.'
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
        $role = Role::where('id', $id)->where('deleted', '0')->where('guard_name','api')->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->first();

        if ($role) {
			 $rolePermissions = \DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        $permissions = $rolePermissions;
        $permissions = array_values($rolePermissions);

        $options = Permission::where('deleted', 0)->where('client_id',$user->organization_id)->where('company_id',$user->company_id)->get();
		
            return response()->json([
                'status' => 'SUCCESS',
                'data' => compact('role', 'permissions', 'options'),
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Role not found."
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
            ['name' => 'required|string|unique:roles,name,'.$id,
            'parent_id' => 'integer|nullable']
        );

        if ($validator->fails()) {
            $errorString = implode(",", $validator->messages()->all());
            return response()->json([
                'status' => 'FAIL',
                'message' => $errorString
            ]);
        }

        $role = Role::where('client_id',$user->organization_id)->where('guard_name','api')->where('company_id',$user->company_id)->where('deleted', '0')->find($id);
        if ($role) {
            $old = $role->toArray();
            $role->update([
                'name'       => $request->name,
                'guard_name'  => 'api',
				'parent_id'  => (int)$request->parent_id,
            ]);

            $role->syncPermissions($request->input('permissions'));

            //Add Action Log
            Helper::addActionLog($user->id, 'ROLE', $role->id, 'UPDATEROLE', $old, $role->toArray());

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Role has been updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => 'FAIL',
                'message' => "Role not found."
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
        $role = Role::where('client_id',$user->organization_id)->where('guard_name','api')->where('company_id',$user->company_id)->where('deleted', '0')->find($id);
        if ($role) {
            $role->update(['deleted' => 1]);

            //Add Action Log
            Helper::addActionLog($user->id, 'ROLE', $role->id, 'DELETEROLE', [], []);

            return response()->json([
                'status' => 'SUCCESS',
                'message' => 'Role has been deleted successfully.'
            ]);
        }

        return response()->json([
            'status' => 'FAIL',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    /*
    public function getChildRoles($parent_id)
    {
        $ids = [];
        $roles = Role::whereRaw("find_in_set('" . $parent_id . "',parent_id)")->get();
        if ($roles->count()) {
            foreach ($roles as $role) {
                $ids[] = $role->id;
                $childs = $this->getChildRoles($role->id);
                $ids = array_merge($ids, $childs);
            }
        }
        return array_unique($ids);
    }

    public function treeRoles($parent_id, $isSuperAdmin)
    {
        $r = [];
        

        $roles = Role::whereRaw("find_in_set('" . $parent_id . "',parent_id)")->get();
        if ($roles->count()) {
            foreach ($roles as $role) {
                $children = $this->treeRoles($role->id, $isSuperAdmin);
                if ($children) {
                    $role->children = $children;
                }
                $role->title = $role->name;
                $role->label = $role->name;
                $role->value = $role->id;
                $role->key = microtime();
                array_push($r, $role);
            }
        }

        return $r;
    }
    */
}
