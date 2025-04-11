<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Permission;

class UserPermissionController extends Controller
{
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $permissions = Permission::all();
        $userPermissions = $user->permissions->pluck('id')->toArray();
        

        return view('admin.users.edit', compact('user', 'permissions', 'userPermissions'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->permissions()->sync($request->permissions ?? []);
        return back()->with('success', 'Cập nhật quyền thành công!');
    }
}
