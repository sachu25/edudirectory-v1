<?php

namespace App\Http\Controllers;

use App\Models\InteractionStatus;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class InteractionStatusController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = InteractionStatus::select('*');
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
        
        return view('interaction_statuses.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 
                'string', 
                'max:255', 
                Rule::unique('interaction_statuses', 'name')
                    ->ignore($request->interaction_status_id)
                    ->whereNull('deleted_at')
            ],
            'status' => 'required|in:active,inactive'
        ]);

        InteractionStatus::updateOrCreate(
            ['id' => $request->interaction_status_id],
            [
                'name' => $request->name,
                'status' => $request->status
            ]
        );        

        return response()->json(['success' => 'Interaction Status saved successfully.']);
    }

    public function edit($id)
    {
        $interactionStatus = InteractionStatus::find($id);
        return response()->json($interactionStatus);
    }

    public function destroy($id)
    {
        if(!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        InteractionStatus::find($id)->delete();
        return response()->json(['success' => 'Interaction Status deleted successfully.']);
    }
}
