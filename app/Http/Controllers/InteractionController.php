<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use App\Models\College;
use App\Models\ContactPerson;
use App\Models\InteractionStatus;
use App\Models\Purpose;
use App\Models\ContactMode;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InteractionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Interaction::with(['college', 'contactPerson', 'status', 'user', 'purposes', 'contactMode'])->select('interactions.*');
            
            if ($request->filled('college_id')) {
                $data->where('interactions.college_id', $request->college_id);
            }
            
            if ($request->filled('user_id')) {
                $data->where('interactions.user_id', $request->user_id);
            }
            
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('college', function($row){
                        return $row->college ? $row->college->name : 'N/A';
                    })
                    ->addColumn('contact_person', function($row){
                        return $row->contactPerson ? $row->contactPerson->name : 'N/A';
                    })
                    ->addColumn('user', function($row){
                        return $row->user ? $row->user->name : 'N/A';
                    })
                    ->addColumn('status', function($row){
                        if($row->status){
                            return '<span class="badge bg-info">'.$row->status->name.'</span>';
                        }
                        return 'N/A';
                    })
                    ->addColumn('contact_mode', function($row){
                        return $row->contactMode ? $row->contactMode->name : 'N/A';
                    })
                    ->addColumn('purposes', function($row){
                        $purposes = $row->purposes->pluck('name')->toArray();
                        return implode(', ', $purposes);
                    })
                    ->addColumn('contact_date_formatted', function($row){
                        return $row->contact_date ? $row->contact_date->format('Y-m-d H:i') : 'N/A';
                    })
                    ->addColumn('action', function($row){
                           $btn = '';
                           if(auth()->user()->hasPermission('interactions.edit')) {
                               $btn .= '<button data-id="'.$row->id.'" class="editBtn btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>';
                           }
                           if(auth()->user()->hasPermission('interactions.delete')) {
                               $btn .= ' <button data-id="'.$row->id.'" class="deleteBtn btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>';
                           }
                           return $btn;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
        }
        
        $colleges = College::orderBy('name')->get();
        $statuses = InteractionStatus::where('status', 'active')->orderBy('name')->get();
        $purposes = Purpose::where('status', 'active')->orderBy('name')->get();
        $contactModes = ContactMode::where('status', 'active')->orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();
        
        return view('interactions.index', compact('colleges', 'statuses', 'purposes', 'contactModes', 'users'));
    }

    public function create()
    {
        return redirect()->route('interactions.index', ['action' => 'create']);
    }

    // Endpoint to get contact persons for a college via AJAX
    public function getContactPersons($college_id)
    {
        $contacts = ContactPerson::with('designation')->where('college_id', $college_id)->orderBy('name')->get();
        return response()->json($contacts);
    }

    public function store(Request $request)
    {
        $permission = $request->interaction_id ? 'interactions.edit' : 'interactions.create';
        if (!auth()->user()->hasPermission($permission)) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'college_id' => 'required|exists:colleges,id',
            'contact_person_id' => 'nullable|exists:contact_persons,id',
            'user_id' => 'required|exists:users,id',
            'interaction_status_id' => 'nullable|exists:interaction_statuses,id',
            'contact_date' => 'required|date',
            'contact_mode_id' => 'required|exists:contact_modes,id',
            'purposes' => 'required|array',
            'purposes.*' => 'exists:purposes,id',
            'college_response' => 'nullable|string',
            'remarks' => 'nullable|string',
            'next_followup_date' => 'nullable|date'
        ]);

        $interaction = Interaction::updateOrCreate(
            ['id' => $request->interaction_id],
            [
                'college_id' => $request->college_id,
                'contact_person_id' => $request->contact_person_id,
                'user_id' => $request->user_id,
                'interaction_status_id' => $request->interaction_status_id,
                'contact_date' => $request->contact_date,
                'contact_mode_id' => $request->contact_mode_id,
                'college_response' => $request->college_response,
                'remarks' => $request->remarks,
                'next_followup_date' => $request->next_followup_date
            ]
        );
        
        $interaction->purposes()->sync($request->purposes);

        return response()->json(['success' => 'Interaction saved successfully.']);
    }

    public function edit($id)
    {
        if (!auth()->user()->hasPermission('interactions.edit')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $interaction = Interaction::with('purposes')->find($id);
        $interaction->purpose_ids = $interaction->purposes->pluck('id');
        return response()->json($interaction);
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('interactions.delete')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
        
        Interaction::find($id)->delete();
        return response()->json(['success' => 'Interaction deleted successfully.']);
    }
}
