<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Role::withCount('users')->select('roles.*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('users_count', function($row){
                        return '<span class="badge bg-secondary">'.$row->users_count.' Users</span>';
                    })
                    ->addColumn('action', function($row){
                        $btn = '';
                        // Do not allow deleting system roles (admin, staff, user)
                        $isSystemRole = in_array($row->name, ['admin', 'staff', 'user']);
                        
                        $btn .= '<a href="'.route('roles.edit', $row->id).'" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-user-shield"></i> Permissions</a>';
                        
                        if (!$isSystemRole) {
                            $btn .= '<button data-id="'.$row->id.'" class="deleteBtn btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['users_count', 'action'])
                    ->make(true);
        }
        
        return view('roles.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $request->role_id,
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        // Clean name to be lowercase and alphanumeric/snake_case
        $name = strtolower(str_replace(' ', '_', preg_replace('/[^A-Za-z0-9 ]/', '', $request->name)));

        Role::updateOrCreate(
            ['id' => $request->role_id],
            [
                'name' => $name,
                'label' => $request->label,
                'description' => $request->description,
            ]
        );

        return response()->json(['success' => 'Role saved successfully.']);
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        // Fetch all permissions grouped by their module
        $permissionsByModule = Permission::all()->groupBy('module');
        
        return view('roles.edit', compact('role', 'permissionsByModule'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'label' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'label' => $request->label,
            'description' => $request->description,
        ]);

        // Sync permissions
        $permissions = $request->input('permissions', []);
        $role->permissions()->sync($permissions);

        return redirect()->route('roles.index')->with('success', 'Role and permissions updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent deleting system roles
        if (in_array($role->name, ['admin', 'staff', 'user'])) {
            return response()->json(['error' => 'System roles cannot be deleted.'], 403);
        }

        // Reassign users of this role to default 'user' role
        $defaultUserRole = Role::where('name', 'user')->first();
        if ($defaultUserRole) {
            $role->users()->update(['role_id' => $defaultUserRole->id]);
        }

        $role->delete();
        return response()->json(['success' => 'Role deleted successfully.']);
    }
}
