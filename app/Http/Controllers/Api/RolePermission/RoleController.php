<?php

namespace App\Http\Controllers\Api\RolePermission;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function createRole(Request $request){
        // dd($request);
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:255|min:1',
            'role_display_name' => 'required|string|max:255|min:1',
            'role_description' => 'string|max:255|min:1'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => 'Validation Error!', 'data' => '', 'errors' => $validator->errors()], 422);
        }
        $role = Role::create([
            'name' => $request->role_name,
            'display_name' => $request->role_display_name, // optional
            'description' => $request->role_description ?? '', // optional
        ]);
        return response()->json(['result' => true, 'message' => 'Role Created Successfully', 'data' => $role], 200);
    }
    public function allRoles(){                     
        $roles = Role::all();
        return response()->json(['result' => true, 'message' => '', 'data' => $roles], 200);
            
    }

    public function assignRole(Request $request){
        // dd($request);
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer|max:255|min:1',
            'user_id' => 'required|integer|max:255|min:1'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => 'Validation Error!', 'data' => '', 'errors' => $validator->errors()], 422);
        }
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['result' => false, 'message' => 'User Not Found', 'data' => ''], 404);
        }
        $user->addRole($request->role_id);        
        return response()->json(['result' => true, 'message' => 'Role Assigned Successfully', 'data' => ''], 200);
    }
    public function removeRole(Request $request){
        // dd($request);
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer|max:255|min:1',
            'user_id' => 'required|integer|max:255|min:1'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['result' => false, 'message' => 'Validation Error!', 'data' => '', 'errors' => $validator->errors()], 422);
        }
        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['result' => false, 'message' => 'User Not Found', 'data' => ''], 404);
        }
        $user->removeRole($request->role_id);
        return response()->json(['result' => true, 'message' => 'Role Removed Successfully', 'data' => ''], 200);
    }
}
