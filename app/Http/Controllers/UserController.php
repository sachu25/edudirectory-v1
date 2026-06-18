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
                        if($row->status == 'active'){
                            return '<span class="badge bg-success">Active</span>';
                        }
                        return '<span class="badge bg-danger">Inactive</span>';
                    })
                    ->addColumn('action', function($row){
                           $btn = '<button data-id="'.$row->id.'" class="editBtn btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>';
                           if(auth()->user()->id !== $row->id) { // Prevent deleting self
                               $btn .= ' <button data-id="'.$row->id.'" class="deleteBtn btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>';
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
        ];

        if($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        User::updateOrCreate(['id' => $request->user_id], $data);

        return response()->json(['success' => 'User saved successfully.']);
    }

    public function edit($id)
    {
        $user = User::with('role')->find($id);
        return response()->json($user);
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
