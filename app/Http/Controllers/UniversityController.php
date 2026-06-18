<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UniversityController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = University::select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status_badge', function($row){
                        if($row->status == 'active'){
                            return '<span class="badge bg-success">Active</span>';
                        }
                        return '<span class="badge bg-danger">Inactive</span>';
                    })
                    ->addColumn('action', function($row){
                           $btn = '';
                           if(auth()->user()->hasPermission('universities.edit')) {
                               $btn .= '<button data-id="'.$row->id.'" class="editBtn btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>';
                           }
                           if(auth()->user()->hasPermission('universities.delete')) {
                               $btn .= ' <button data-id="'.$row->id.'" class="deleteBtn btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>';
                           }
                           return $btn;
                    })
                    ->rawColumns(['status_badge', 'action'])
                    ->make(true);
        }
        
        return view('universities.index');
    }

    public function store(Request $request)
    {
        $permission = $request->university_id ? 'universities.edit' : 'universities.create';
        if (!auth()->user()->hasPermission($permission)) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                \Illuminate\Validation\Rule::unique('universities', 'name')
                    ->ignore($request->university_id)
                    ->whereNull('deleted_at')
            ],
            'short_name' => 'nullable|string|max:50',
            'state' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive'
        ]);

        University::updateOrCreate(
            ['id' => $request->university_id],
            [
                'name' => $request->name,
                'short_name' => $request->short_name,
                'state' => $request->state,
                'status' => $request->status
            ]
        );        

        return response()->json(['success' => 'University saved successfully.']);
    }

    public function edit($id)
    {
        if (!auth()->user()->hasPermission('universities.edit')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $university = University::find($id);
        return response()->json($university);
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('universities.delete')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
        
        University::find($id)->delete();
        return response()->json(['success' => 'University deleted successfully.']);
    }
}
