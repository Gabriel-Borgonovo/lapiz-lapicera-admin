<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('admin.roles.roles-index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.roles-create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $validatedData['name']]);

        if (!empty($validatedData['permissions'])) {
            $role->syncPermissions($validatedData['permissions']);
        }

        return redirect()->route('roles')->with('success', 'Role created successfully.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        return view('admin.roles.roles-edit', compact('role', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'string|max:255',
            'permissions' => 'array',
        ]);

        $role = Role::findOrFail($id);
        if (isset($validatedData['name'])) {
            $role->name = $validatedData['name'];
        }
        if (isset($validatedData['permissions'])) {
            $role->syncPermissions($validatedData['permissions']);
        }
        $role->save();

        return redirect()->route('roles')->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return redirect()->route('roles')->with('success', 'Role deleted successfully.');
    }
}
