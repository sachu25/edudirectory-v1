<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\CollegeImport;
use Maatwebsite\Excel\Facades\Excel;

use App\Imports\UnifiedImport;
use App\Exports\UnifiedTemplateExport;

class ImportController extends Controller
{
    public function index()
    {
        return view('imports.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,csv,xls|max:5120',
        ]);

        try {
            $import = new CollegeImport;
            Excel::import($import, $request->file('import_file'));
            $count = $import->getRowCount();
            $updated = $import->getUpdatedCount();
            return back()->with('success', "Colleges imported successfully! {$count} new records added, {$updated} existing records updated.");
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->with('error', 'Validation Error. Row: ' . $failures[0]->row() . ' - ' . $failures[0]->errors()[0]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function storeUnified(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,csv,xls|max:5120',
        ]);

        try {
            $import = new UnifiedImport;
            Excel::import($import, $request->file('import_file'));
            $count = $import->getRowCount();
            $updated = $import->getUpdatedCount();
            $skipped = $import->skippedRows;

            $message = "Unified import processed successfully! {$count} new contact records added, {$updated} existing contact records updated.";
            if (count($skipped) > 0) {
                $message .= " " . count($skipped) . " rows were skipped.";
                return back()->with('success', $message)->with('skipped_contacts', $skipped);
            }
            return back()->with('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->with('error', 'Validation Error. Row: ' . $failures[0]->row() . ' - ' . $failures[0]->errors()[0]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function downloadUnifiedTemplate()
    {
        return Excel::download(new UnifiedTemplateExport, 'unified_import_template.xlsx');
    }
}
