<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query();
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $roles = $query->paginate(10);
        
        if ($request->ajax()) {
            return view('roles._table', compact('roles'))->render();
        }

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.form', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);

        return response()->json(['success' => 'Role created successfully!']);
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('roles.form', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $role->update(['name' => $request->name]);
    
        $permissions = $request->permissions ?? [];
    
        // Buat permission baru jika belum ada
        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName]);
        }
    
        // Sinkronisasi semua permission ke role
        $role->syncPermissions($permissions);
    
        return response()->json(['success' => 'Role updated successfully!']);
    }
    

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(['success' => 'Role deleted successfully!']);
    }
}
