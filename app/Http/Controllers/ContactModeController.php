<?php

namespace App\Http\Controllers;

use App\Models\ContactMode;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class ContactModeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ContactMode::select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status_badge', function($row){
                        if($row->status == 'active'){
                            return '<span class="badge bg-success">Active</span>';
                        }
                        return '<span class="badge bg-danger">Inactive</span>';
                    })
                    ->addColumn('action', function($row){
                           $btn = '<button data-id="'.$row->id.'" class="editBtn btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>';
                           if(auth()->user()->isAdmin()) {
                               $btn .= ' <button data-id="'.$row->id.'" class="deleteBtn btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>';
                           }
                           return $btn;
                    })
                    ->rawColumns(['status_badge', 'action'])
                    ->make(true);
        }
        
        return view('contact_modes.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('contact_modes', 'name')
                    ->ignore($request->contact_mode_id)
                    ->whereNull('deleted_at')
            ],
            'status' => 'required|in:active,inactive'
        ]);

        ContactMode::updateOrCreate(
            ['id' => $request->contact_mode_id],
            [
                'name' => $request->name,
                'status' => $request->status
            ]
        );        

        return response()->json(['success' => 'Contact Mode saved successfully.']);
    }

    public function edit($id)
    {
        $contactMode = ContactMode::find($id);
        return response()->json($contactMode);
    }

    public function destroy($id)
    {
        if(!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        ContactMode::find($id)->delete();
        return response()->json(['success' => 'Contact Mode deleted successfully.']);
    }
}
