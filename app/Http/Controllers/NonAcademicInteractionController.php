<?php

namespace App\Http\Controllers;

use App\Models\NonAcademicInteraction;
use App\Models\NonAcademicClient;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NonAcademicInteractionController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('non_academic_interactions.view')) {
            abort(403, 'Unauthorized access to Corporate Interactions.');
        }

        if ($request->ajax()) {
            $data = NonAcademicInteraction::with(['client', 'employee'])->select('non_academic_interactions.*');

            if ($request->filled('non_academic_client_id')) {
                $data->where('non_academic_client_id', $request->non_academic_client_id);
            }
            if ($request->filled('user_id')) {
                $data->where('user_id', $request->user_id);
            }
            if ($request->filled('purpose')) {
                $data->where('purpose', $request->purpose);
            }
            if ($request->filled('interaction_status')) {
                $data->where('interaction_status', $request->interaction_status);
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('client_name', function ($row) {
                    return $row->client ? $row->client->name : 'N/A';
                })
                ->addColumn('employee_name', function ($row) {
                    return $row->employee ? $row->employee->name : 'N/A';
                })
                ->addColumn('formatted_date', function ($row) {
                    return $row->contact_date ? $row->contact_date->format('Y-m-d H:i') : 'N/A';
                })
                ->addColumn('formatted_next_followup_date', function ($row) {
                    return $row->next_followup_date ? $row->next_followup_date->format('Y-m-d') : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->user()->hasPermission('non_academic_interactions.edit')) {
                        $btn .= '<button data-id="' . $row->id . '" class="editBtn btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>';
                    }
                    if (auth()->user()->hasPermission('non_academic_interactions.delete')) {
                        $btn .= '<button data-id="' . $row->id . '" class="deleteBtn btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $clients = NonAcademicClient::orderBy('name')->get();
        $employees = User::orderBy('name')->get();

        return view('non_academic_interactions.index', compact('clients', 'employees'));
    }

    public function store(Request $request)
    {
        if ($request->filled('interaction_id')) {
            if (!auth()->user()->hasPermission('non_academic_interactions.edit')) {
                abort(403, 'Unauthorized to edit interaction.');
            }
        } else {
            if (!auth()->user()->hasPermission('non_academic_interactions.create')) {
                abort(403, 'Unauthorized to log interaction.');
            }
        }

        $request->validate([
            'non_academic_client_id' => 'required|exists:non_academic_clients,id',
            'user_id' => 'required|exists:users,id',
            'contact_date' => 'required|date',
            'contact_mode' => 'required|string|max:100',
            'interaction_status' => 'required|string|max:100',
            'purpose' => 'required|string|max:100',
            'client_response' => 'nullable|string',
            'remarks' => 'nullable|string',
            'next_followup_date' => 'nullable|date',
        ]);

        $data = $request->except(['_token', 'interaction_id']);

        NonAcademicInteraction::updateOrCreate(
            ['id' => $request->interaction_id],
            $data
        );

        return response()->json(['success' => 'Non-Academic Interaction saved successfully.']);
    }

    public function edit($id)
    {
        if (!auth()->user()->hasPermission('non_academic_interactions.edit')) {
            abort(403, 'Unauthorized to edit interaction.');
        }
        $interaction = NonAcademicInteraction::findOrFail($id);
        return response()->json($interaction);
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('non_academic_interactions.delete')) {
            abort(403, 'Unauthorized to delete interaction.');
        }
        NonAcademicInteraction::findOrFail($id)->delete();
        return response()->json(['success' => 'Interaction deleted successfully.']);
    }
}
