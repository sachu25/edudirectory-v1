<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ContactImport;
use App\Exports\ContactTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class ContactImportController extends Controller
{
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('contacts.create')) {
            return redirect()->route('imports.index')->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'import_file' => 'required|mimes:xlsx,csv,xls|max:5120',
        ]);

        try {
            $import = new ContactImport;
            Excel::import($import, $request->file('import_file'));
            $count = $import->getRowCount();
            $updated = $import->getUpdatedCount();
            $skipped = $import->skippedRows;
            $failures = $import->failures();

            $message = "Contacts processed successfully! {$count} new contacts added, {$updated} existing contacts updated.";
            if (count($skipped) > 0) {
                $message .= " " . count($skipped) . " rows were skipped.";
            }

            $response = redirect()->route('imports.index')->with('success', $message);
            if (count($skipped) > 0) {
                $response->with('skipped_contacts', $skipped);
            }
            if ($failures->isNotEmpty()) {
                $response->with('import_failures', $failures);
            }
            return $response;
        } catch (\Exception $e) {
            return redirect()->route('imports.index')->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        if (!auth()->user()->hasPermission('contacts.create')) {
            abort(403, 'Unauthorized action.');
        }

        return Excel::download(new ContactTemplateExport, 'contacts_import_template.xlsx');
    }
}
