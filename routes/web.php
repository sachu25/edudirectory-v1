<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\NonAcademicClientController;
use App\Http\Controllers\NonAcademicInteractionController;
use App\Http\Controllers\ContactPersonController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('universities', UniversityController::class);
    Route::get('colleges/download-template', [CollegeController::class, 'downloadTemplate'])->name('colleges.download-template');
    Route::resource('colleges', CollegeController::class);
    Route::resource('non-academic-clients', NonAcademicClientController::class);
    Route::resource('non-academic-interactions', NonAcademicInteractionController::class);
    Route::resource('designations', \App\Http\Controllers\DesignationController::class);
    Route::get('contacts/download-template', [\App\Http\Controllers\ContactImportController::class, 'downloadTemplate'])->name('contacts.download-template');
    Route::post('contacts/import', [\App\Http\Controllers\ContactImportController::class, 'store'])->name('contacts.import');
    Route::get('contacts/export', [ContactPersonController::class, 'export'])->name('contacts.export');
    Route::resource('contacts', ContactPersonController::class);
    
    // Interactions Module
    Route::resource('interaction-statuses', \App\Http\Controllers\InteractionStatusController::class);
    Route::resource('contact-modes', \App\Http\Controllers\ContactModeController::class);
    Route::resource('purposes', \App\Http\Controllers\PurposeController::class);
    Route::resource('interactions', \App\Http\Controllers\InteractionController::class);
    Route::get('interactions/get-contacts/{college_id}', [\App\Http\Controllers\InteractionController::class, 'getContactPersons']);
    
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('reports/export/staff-excel', [ReportController::class, 'exportStaffExcel'])->name('reports.export.staff-excel');
    Route::get('reports/export/staff-csv', [ReportController::class, 'exportStaffCsv'])->name('reports.export.staff-csv');
    Route::get('reports/export/staff-pdf', [ReportController::class, 'exportStaffPdf'])->name('reports.export.staff-pdf');
    Route::get('imports', [ImportController::class, 'index'])->name('imports.index');
    Route::post('imports', [ImportController::class, 'store'])->name('imports.store');
    Route::get('imports/download-unified-template', [ImportController::class, 'downloadUnifiedTemplate'])->name('imports.download-unified-template');
    Route::post('imports/unified', [ImportController::class, 'storeUnified'])->name('imports.unified');
    
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', \App\Http\Controllers\RoleController::class);
    });
});

require __DIR__.'/auth.php';
