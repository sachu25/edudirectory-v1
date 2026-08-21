<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('role')->select('users.*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('role_badge', function($row){
                        if($row->role){
                            $badgeClass = $row->role->name === 'admin' ? 'bg-primary' : ($row->role->name === 'staff' ? 'bg-secondary' : 'bg-info');
                            return '<span class="badge ' . $badgeClass . '">' . $row->role->label . '</span>';
                        }
                        return '<span class="badge bg-dark">No Role</span>';
                    })
                    ->addColumn('status_badge', function($row){
                        $badge = $row->status == 'active' 
                            ? '<span class="badge bg-success">Active</span>' 
                            : '<span class="badge bg-danger">Inactive</span>';
                        if ($row->force_password_change) {
                            $badge .= ' <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Password Update Required</span>';
                        }
                        return $badge;
                    })
                    ->addColumn('action', function($row){
                           $btn = '<button data-id="'.$row->id.'" class="editBtn btn btn-sm btn-outline-primary" title="Edit User"><i class="fas fa-edit"></i></button>';
                           $btn .= ' <button data-id="'.$row->id.'" class="forcePasswordBtn btn btn-sm btn-outline-warning" title="Require Forced Password Change"><i class="fas fa-key"></i></button>';
                           if(auth()->user()->id !== $row->id) { // Prevent deleting self
                               $btn .= ' <button data-id="'.$row->id.'" class="deleteBtn btn btn-sm btn-outline-danger" title="Delete User"><i class="fas fa-trash"></i></button>';
                           }
                           return $btn;
                    })
                    ->rawColumns(['role_badge', 'status_badge', 'action'])
                    ->make(true);
        }
        
        $roles = \App\Models\Role::orderBy('label')->get();
        return view('users.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->user_id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive',
        ];

        // If creating a new user, password is required
        if(!$request->user_id) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $request->validate($rules);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status,
            'force_password_change' => $request->has('force_password_change') ? true : false,
        ];

        if($request->filled('password')) {
            $data['password'] = $request->password;
        }

        User::updateOrCreate(['id' => $request->user_id], $data);

        return response()->json(['success' => 'User saved successfully.']);
    }

    public function edit($id)
    {
        $user = User::with('role')->find($id);
        return response()->json($user);
    }

    public function forcePasswordChange($id)
    {
        $user = User::findOrFail($id);
        $user->update(['force_password_change' => true]);
        return response()->json(['success' => 'Forced password change flagged for ' . $user->name . '.']);
    }

    public function destroy($id)
    {
        if(auth()->user()->id == $id) {
            return response()->json(['error' => 'You cannot delete yourself.'], 403);
        }
        
        User::find($id)->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }
}
