<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\University;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CollegeTemplateExport;

class CollegeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = College::with('university')->select('colleges.*');
            
            if ($request->filled('type')) {
                $data->where('type', $request->type);
            }
            if ($request->filled('is_university')) {
                $data->where('is_university', $request->is_university === 'yes' ? 1 : 0);
            }
            if ($request->filled('university_id')) {
                $data->where('university_id', $request->university_id);
            }
            if ($request->filled('state')) {
                $data->where('state', $request->state);
            }

            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('affiliated_university', function($row){
                        return $row->university ? $row->university->name : 'N/A';
                    })
                    ->addColumn('action', function($row){
                           $btn = '';
                           if(auth()->user()->hasPermission('colleges.edit')) {
                               $btn .= '<button data-id="'.$row->id.'" class="editBtn btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></button>';
                           }
                           if(auth()->user()->hasPermission('colleges.view')) {
                               $btn .= '<a href="'.route('colleges.show', $row->id).'" class="btn btn-sm btn-outline-info me-1"><i class="fas fa-eye"></i></a>';
                           }
                           if(auth()->user()->hasPermission('colleges.delete')) {
                               $btn .= '<button data-id="'.$row->id.'" class="deleteBtn btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>';
                           }
                           return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        
        $universities = University::where('status', 'active')->orderBy('name')->get();
        $states = College::$states;
        return view('colleges.index', compact('universities', 'states'));
    }

    public function create()
    {
        return redirect()->route('colleges.index', ['action' => 'create']);
    }

    public function store(Request $request)
    {
        $permission = $request->college_id ? 'colleges.edit' : 'colleges.create';
        if (!auth()->user()->hasPermission($permission)) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'code' => [
                'nullable', 
                'string', 
                'max:50', 
                \Illuminate\Validation\Rule::unique('colleges', 'code')
                    ->ignore($request->college_id)
                    ->whereNull('deleted_at')
            ],
            'university_id' => 'nullable|exists:universities,id',
            'type' => 'nullable|string',
            'official_email' => 'nullable|email|max:255',
            'established_year' => 'nullable|integer',
            'status' => 'nullable|string|in:active,inactive',
            'fdp_client' => 'nullable|string|in:Yes,No',
        ]);

        $data = $request->except(['_token', 'college_id']);
        if (isset($data['state'])) {
            $data['state'] = College::sanitizeState($data['state']);
        }
        $data['is_university'] = $request->has('is_university') ? 1 : 0;
        $data['hostel_facility'] = $request->has('hostel_facility') ? 1 : 0;
        $data['placement_cell'] = $request->has('placement_cell') ? 1 : 0;

        College::updateOrCreate(
            ['id' => $request->college_id],
            $data
        );        

        return response()->json(['success' => 'Institution saved successfully.']);
    }

    public function edit($id)
    {
        if (!auth()->user()->hasPermission('colleges.edit')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $college = College::find($id);
        return response()->json($college);
    }

    public function show($id)
    {
        if (!auth()->user()->hasPermission('colleges.view')) {
            abort(403, 'Unauthorized action.');
        }

        $college = College::with(['contactPersons', 'university'])->findOrFail($id);
        return view('colleges.show', compact('college'));
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('colleges.delete')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }
        
        College::find($id)->delete();
        return response()->json(['success' => 'College deleted successfully.']);
    }

    public function downloadTemplate()
    {
        if (!auth()->user()->hasPermission('colleges.create')) {
            abort(403, 'Unauthorized action.');
        }

        return Excel::download(new CollegeTemplateExport, 'colleges_import_template.xlsx');
    }
}
