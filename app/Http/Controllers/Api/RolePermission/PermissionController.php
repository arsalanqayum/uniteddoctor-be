<?php

namespace App\Http\Controllers\Api\RolePermission;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function createPermission(Request $request){
        // dd($request);
        $validator = Validator::make($request->all(), [
            'permission_name' => 'required|string|max:255|min:1|unique:permissions,name',
            'permission_display_name' => 'required|string|max:255|min:1',
            'permission_description' => 'string|max:255|min:1'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => 'Validation Error!', 'data' => '', 'errors' => $validator->errors()], 422);
        }
        $createPermission = Permission::create([
            'name' => $request->permission_name,
            'display_name' => $request->role_dipermission_display_namesplay_name, // optional
            'description' => $request->permission_description ?? '', // optional
        ]);
        return response()->json(['result' => true, 'message' => 'Role Created Successfully', 'data' => $createPermission], 200);
    }
    public function assignRolePermission(Request $request){
        // dd($request);
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer|max:255|min:1',
            'permissions' => 'required|array',
        ]);
        // dd($request->permissions);
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => 'Validation Error!', 'data' => '', 'errors' => $validator->errors()], 422);
        }
        $role = Role::find($request->role_id);
        if (!$role) {
            return response()->json(['result' => false, 'message' => 'Role Not Found', 'data' => ''], 404);
        }
        $role->givePermissions($request->permissions);
        return response()->json(['result' => true, 'message' => 'Permissions Assigned Successfully', 'data' => ''], 200);
    }
    public function assignUserPermission(Request $request){
        // dd($request);
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|max:255|min:1',
            'permissions' => 'required|array',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => 'Validation Error!', 'data' => '', 'errors' => $validator->errors()], 422);
        }
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['result' => false, 'message' => 'User Not Found', 'data' => ''], 404);
        }
        $user->givePermissions($request->permissions);
        return response()->json(['result' => true, 'message' => 'Permissions Assigned Successfully', 'data' => ''], 200);
    }
    public function removeRolePermission(Request $request){
        // dd($request);
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer|max:255|min:1',
            'permission_id' => 'required|integer|max:255|min:1',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => 'Validation Error!', 'data' => '', 'errors' => $validator->errors()], 422);
        }
        $role = Role::find($request->role_id);
        if (!$role) {
            return response()->json(['result' => false, 'message' => 'Role Not Found', 'data' => ''], 404);
        }
        $role->removePermission($request->permission_id);
        return response()->json(['result' => true, 'message' => 'Permission removed Successfully', 'data' => ''], 200);
    }
    public function allUserPermissions(){
        // dd($request);                
        $data = auth('api')->user()->allPermissions();
        return response()->json(['result' => true, 'message' => '', 'data' => $data], 200);
    }
    public function allPermissions(){
        // dd($request);                
        $data = Permission::all();
        return response()->json(['result' => true, 'message' => '', 'data' => $data], 200);
    }
    public function checkUserPermission(Request $request){
        // dd(auth('api')->user()->hasPermission($request->permission_name)); 
        $validator = Validator::make($request->all(), [
            'permission_name' => 'required|string|max:255|min:1',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => 'Validation Error!', 'data' => '', 'errors' => $validator->errors()], 422);
        }             
        if(auth('api')->user()->hasPermission($request->permission_name)){
            return response()->json(['result' => true, 'message' => 'User are Allowed For '. $request->permission_name. ' permission', 'data' => ''], 200);
        }else{
            return response()->json(['result' => false, 'message' => 'User are not Allowed For'. $request->permission_name. ' permission', 'data' => ''], 200);
        }
    }
}
