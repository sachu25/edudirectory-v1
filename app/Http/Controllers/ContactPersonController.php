<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use App\Models\College;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ContactPersonController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ContactPerson::with(['college', 'designation'])->select('contact_persons.*');
            
            if ($request->filled('college_id')) {
                $data->where('college_id', $request->college_id);
            }

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('college', function($row){
                        return $row->college ? $row->college->name : 'N/A';
                    })
                    ->addColumn('designation', function($row){
                        return $row->designation ? $row->designation->name : 'N/A';
                    })
                    ->addColumn('status_badge', function($row){
                        if($row->status == 'active'){
                            return '<span class="badge bg-success">Active</span>';
                        }
                        return '<span class="badge bg-danger">Inactive</span>';
                    })
                    ->addColumn('action', function($row){
                           $btn = '';
                           if(auth()->user()->hasPermission('contacts.edit')) {
                               $btn .= '<button data-id="'.$row->id.'" class="editBtn btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>';
                           }
                           if(auth()->user()->hasPermission('contacts.delete')) {
                               $btn .= ' <button data-id="'.$row->id.'" class="deleteBtn btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>';
                           }
                           return $btn;
                    })
                    ->rawColumns(['status_badge', 'action'])
                    ->make(true);
        }
        
        $colleges = College::select('id', 'name')->orderBy('name')->get();
        $designations = \App\Models\Designation::where('status', 'active')->orderBy('name')->get();
        return view('contacts.index', compact('colleges', 'designations'));
    }

    public function store(Request $request)
    {
        $permission = $request->contact_id ? 'contacts.edit' : 'contacts.create';
        if (!auth()->user()->hasPermission($permission)) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'name' => 'required|string|max:255',
            'designation_id' => 'required|exists:designations,id',
            'department' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive'
        ]);

        ContactPerson::updateOrCreate(
            ['id' => $request->contact_id],
            [
                'college_id' => $request->college_id,
                'name' => $request->name,
                'designation_id' => $request->designation_id,
                'department' => $request->department,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'whatsapp' => $request->whatsapp_number,
                'status' => $request->status,
                'is_priority' => $request->has('is_primary') ? 1 : 0
            ]
        );        

        return response()->json(['success' => 'Contact Person saved successfully.']);
    }

    public function edit($id)
    {
        if (!auth()->user()->hasPermission('contacts.edit')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $contact = ContactPerson::find($id);
        return response()->json($contact);
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('contacts.delete')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
        
        ContactPerson::find($id)->delete();
        return response()->json(['success' => 'Contact Person deleted successfully.']);
    }
}
