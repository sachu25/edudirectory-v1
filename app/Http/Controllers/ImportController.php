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
        if (!auth()->user()->hasPermission('imports.view')) {
            abort(403, 'Unauthorized access to Data Imports.');
        }
        return view('imports.index');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('imports.execute')) {
            abort(403, 'Unauthorized to perform data imports.');
        }

        $request->validate([
            'import_file' => 'required|mimes:xlsx,csv,xls|max:5120',
        ]);

        try {
            $import = new CollegeImport;
            Excel::import($import, $request->file('import_file'));
            $count = $import->getRowCount();
            $updated = $import->getUpdatedCount();
            $failures = $import->failures();

            $message = "Colleges imported successfully! {$count} new records added, {$updated} existing records updated.";
            if ($failures->isNotEmpty()) {
                return redirect()->route('imports.index')->with('success', $message)->with('import_failures', $failures);
            }
            return redirect()->route('imports.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('imports.index')->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function storeUnified(Request $request)
    {
        if (!auth()->user()->hasPermission('imports.execute')) {
            abort(403, 'Unauthorized to perform data imports.');
        }

        $request->validate([
            'import_file' => 'required|mimes:xlsx,csv,xls|max:5120',
        ]);

        try {
            $import = new UnifiedImport;
            Excel::import($import, $request->file('import_file'));
            $count = $import->getRowCount();
            $updated = $import->getUpdatedCount();
            $skipped = $import->skippedRows;
            $failures = $import->failures();

            $message = "Unified import processed successfully! {$count} new contact records added, {$updated} existing contact records updated.";
            if (count($skipped) > 0) {
                $message .= " " . count($skipped) . " rows were skipped.";
            }

            if ($failures->isNotEmpty()) {
                return redirect()->route('imports.index')->with('success', $message)->with('import_failures', $failures);
            }
            return redirect()->route('imports.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('imports.index')->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function downloadUnifiedTemplate()
    {
        if (!auth()->user()->hasPermission('imports.view')) {
            abort(403, 'Unauthorized to download import template.');
        }
        return Excel::download(new UnifiedTemplateExport, 'unified_contacts_import_template.xlsx');
    }
}
