<?php

namespace App\Http\Controllers;
use Spatie\Permission\Models\Permission;


use Illuminate\Http\Request;

class PermissionController extends Controller
{
   

    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.permissions-index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.permissions-create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
        ]);
    
        Permission::create(['name' => $validatedData['name']]);
     
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('admin.permissions.permissions-edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->name = $validatedData['name'];
        $permission->save();

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
