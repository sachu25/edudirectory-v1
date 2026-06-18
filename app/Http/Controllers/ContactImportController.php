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
            return back()->with('error', 'Unauthorized action.');
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

            $message = "Contacts processed successfully! {$count} new contacts added, {$updated} existing contacts updated.";
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

    public function downloadTemplate()
    {
        if (!auth()->user()->hasPermission('contacts.create')) {
            abort(403, 'Unauthorized action.');
        }

        return Excel::download(new ContactTemplateExport, 'contacts_import_template.xlsx');
    }
}
