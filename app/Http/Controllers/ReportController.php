<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\University;
use App\Exports\CollegeExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $universities = University::where('status', 'active')->orderBy('name')->get();

        $query = College::with('university');

        if ($request->filled('university_id')) {
            $query->where('university_id', $request->university_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $colleges = $query->paginate(20)->withQueryString();

        return view('reports.index', compact('colleges', 'universities'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new CollegeExport($request), 'colleges_report.xlsx');
    }

    public function exportCsv(Request $request)
    {
        return Excel::download(new CollegeExport($request), 'colleges_report.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdf(Request $request)
    {
        $query = College::with('university');

        if ($request->filled('university_id')) {
            $query->where('university_id', $request->university_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $colleges = $query->get();

        $pdf = Pdf::loadView('reports.pdf', compact('colleges'));
        return $pdf->download('colleges_report.pdf');
    }
}
