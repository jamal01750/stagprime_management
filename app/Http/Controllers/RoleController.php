<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    public function store(Request $request)
    {
        $request->validate(['role_name' => 'required|unique:roles']);
        Role::create(['role_name' => $request->role_name]);
        return back()->with('success', 'Role created.');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return back()->with('success', 'Role deleted.');
    }
}
