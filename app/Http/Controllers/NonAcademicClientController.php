<?php

namespace App\Http\Controllers;

use App\Models\NonAcademicClient;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NonAcademicClientController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('non_academic_clients.view')) {
            abort(403, 'Unauthorized access to Corporate Clients.');
        }

        if ($request->ajax()) {
            $data = NonAcademicClient::with('contactedEmployee')->select('non_academic_clients.*');

            if ($request->filled('industry')) {
                $data->where('industry', $request->industry);
            }
            if ($request->filled('contacted_user_id')) {
                $data->where('contacted_user_id', $request->contacted_user_id);
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('contacted_employee_name', function ($row) {
                    return $row->contactedEmployee ? $row->contactedEmployee->name : 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (auth()->user()->hasPermission('non_academic_clients.edit')) {
                        $btn .= '<button data-id="' . $row->id . '" class="editBtn btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>';
                    }
                    $btn .= '<a href="' . route('non-academic-clients.show', $row->id) . '" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></a>';
                    if (auth()->user()->hasPermission('non_academic_clients.delete')) {
                        $btn .= '<button data-id="' . $row->id . '" class="deleteBtn btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $employees = User::orderBy('name')->get();
        // Extract unique industries for filtering
        $industries = NonAcademicClient::whereNotNull('industry')->distinct()->pluck('industry');

        return view('non_academic_clients.index', compact('employees', 'industries'));
    }

    public function store(Request $request)
    {
        if ($request->filled('client_id')) {
            if (!auth()->user()->hasPermission('non_academic_clients.edit')) {
                abort(403, 'Unauthorized to edit clients.');
            }
        } else {
            if (!auth()->user()->hasPermission('non_academic_clients.create')) {
                abort(403, 'Unauthorized to create clients.');
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'industry' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_designation' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
        ]);

        $data = $request->except(['_token', 'client_id']);

        NonAcademicClient::updateOrCreate(
            ['id' => $request->client_id],
            $data
        );

        return response()->json(['success' => 'Non-Academic Client saved successfully.']);
    }

    public function edit($id)
    {
        if (!auth()->user()->hasPermission('non_academic_clients.edit')) {
            abort(403, 'Unauthorized to edit clients.');
        }
        $client = NonAcademicClient::findOrFail($id);
        return response()->json($client);
    }

    public function show($id)
    {
        if (!auth()->user()->hasPermission('non_academic_clients.view')) {
            abort(403, 'Unauthorized access to client profile.');
        }
        $client = NonAcademicClient::with(['interactions.employee'])->findOrFail($id);
        return view('non_academic_clients.show', compact('client'));
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('non_academic_clients.delete')) {
            abort(403, 'Unauthorized to delete clients.');
        }
        NonAcademicClient::findOrFail($id)->delete();
        return response()->json(['success' => 'Client deleted successfully.']);
    }
}
