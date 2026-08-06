<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\College;
use App\Models\University;
use App\Models\User;
use App\Models\Interaction;
use App\Models\NonAcademicInteraction;
use App\Models\AuditLog;
use App\Exports\CollegeExport;
use App\Exports\StaffActivityExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('reports.view')) {
            abort(403, 'Unauthorized access to Reports Center.');
        }

        $activeTab = $request->get('report_tab', 'institution');

        if ($activeTab === 'staff' && !auth()->user()->hasPermission('reports.staff.view')) {
            abort(403, 'Unauthorized access to Staff Activity Reports.');
        }

        $universities = University::where('status', 'active')->orderBy('name')->get();
        $states = College::$states;
        $users = User::orderBy('name')->get();

        // 1. Institution Report Data
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
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }
        if ($request->filled('fdp_client')) {
            $query->where('fdp_client', $request->fdp_client);
        }

        $colleges = $query->paginate(20, ['*'], 'colleges_page')->withQueryString();

        // 2. Staff Activity Report Data
        $staffData = collect();
        $detailedLogs = collect();

        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;

        $targetUsersQuery = User::query();
        if ($request->filled('staff_id')) {
            $targetUsersQuery->where('id', $request->staff_id);
        }
        $targetUsers = $targetUsersQuery->orderBy('name')->get();

        foreach ($targetUsers as $user) {
            $collegesAddedQuery = AuditLog::where('user_id', $user->id)
                ->where('module', 'College')
                ->where('action', 'create');

            $contactsAddedQuery = AuditLog::where('user_id', $user->id)
                ->where('module', 'ContactPerson')
                ->where('action', 'create');

            $clientsAddedQuery = AuditLog::where('user_id', $user->id)
                ->where('module', 'NonAcademicClient')
                ->where('action', 'create');

            $academicInteractionsQuery = Interaction::where('user_id', $user->id);
            $nonAcademicInteractionsQuery = NonAcademicInteraction::where('user_id', $user->id);

            if ($startDate && $endDate) {
                $collegesAddedQuery->whereBetween('created_at', [$startDate, $endDate]);
                $contactsAddedQuery->whereBetween('created_at', [$startDate, $endDate]);
                $clientsAddedQuery->whereBetween('created_at', [$startDate, $endDate]);
                $academicInteractionsQuery->whereBetween('contact_date', [$startDate, $endDate]);
                $nonAcademicInteractionsQuery->whereBetween('contact_date', [$startDate, $endDate]);
            }

            $academicLogsCount = $academicInteractionsQuery->count();
            $nonAcademicLogsCount = $nonAcademicInteractionsQuery->count();
            $collegesAddedCount = $collegesAddedQuery->count();
            $contactsAddedCount = $contactsAddedQuery->count();
            $clientsAddedCount = $clientsAddedQuery->count();

            $pendingFollowupsCount = Interaction::where('user_id', $user->id)
                ->whereNotNull('next_followup_date')
                ->where('next_followup_date', '>=', Carbon::today())
                ->count() + NonAcademicInteraction::where('user_id', $user->id)
                ->whereNotNull('next_followup_date')
                ->where('next_followup_date', '>=', Carbon::today())
                ->count();

            $staffData->push((object)[
                'user_id' => $user->id,
                'staff_name' => $user->name,
                'staff_email' => $user->email,
                'colleges_added' => $collegesAddedCount,
                'contacts_added' => $contactsAddedCount,
                'clients_added' => $clientsAddedCount,
                'academic_interactions' => $academicLogsCount,
                'non_academic_interactions' => $nonAcademicLogsCount,
                'total_interactions' => $academicLogsCount + $nonAcademicLogsCount,
                'pending_followups' => $pendingFollowupsCount,
            ]);
        }

        // Detailed Activity Log Feed for Staff Tab
        $academicLogsQuery = Interaction::with(['college', 'contactPerson', 'status', 'user', 'contactMode']);
        $nonAcademicLogsQuery = NonAcademicInteraction::with(['client', 'employee']);

        if ($request->filled('staff_id')) {
            $academicLogsQuery->where('user_id', $request->staff_id);
            $nonAcademicLogsQuery->where('user_id', $request->staff_id);
        }

        if ($startDate && $endDate) {
            $academicLogsQuery->whereBetween('contact_date', [$startDate, $endDate]);
            $nonAcademicLogsQuery->whereBetween('contact_date', [$startDate, $endDate]);
        }

        $academicMapped = $academicLogsQuery->latest('contact_date')->take(20)->get()->map(function ($item) {
            return (object)[
                'contact_date' => $item->contact_date,
                'staff_name' => $item->user->name ?? 'N/A',
                'type' => 'Academic',
                'target_name' => $item->college->name ?? 'N/A',
                'contact_mode' => $item->contactMode->name ?? 'Call',
                'status' => $item->status->name ?? 'Logged',
                'remarks' => $item->remarks ?? $item->college_response,
            ];
        });

        $nonAcademicMapped = $nonAcademicLogsQuery->latest('contact_date')->take(20)->get()->map(function ($item) {
            return (object)[
                'contact_date' => $item->contact_date,
                'staff_name' => $item->employee->name ?? 'N/A',
                'type' => 'Non-Academic',
                'target_name' => $item->client->name ?? 'N/A',
                'contact_mode' => $item->contact_mode ?? 'Meeting',
                'status' => $item->interaction_status ?? 'Logged',
                'remarks' => $item->remarks ?? $item->client_response,
            ];
        });

        $detailedLogs = $academicMapped->concat($nonAcademicMapped)
            ->sortByDesc(function ($item) {
                return Carbon::parse($item->contact_date)->timestamp;
            })
            ->take(30)
            ->values();

        return view('reports.index', compact(
            'activeTab', 'colleges', 'universities', 'states', 'users',
            'staffData', 'detailedLogs'
        ));
    }

    public function exportExcel(Request $request)
    {
        if (!auth()->user()->hasPermission('reports.export')) {
            abort(403, 'Unauthorized report export.');
        }
        return Excel::download(new CollegeExport($request), 'colleges_report.xlsx');
    }

    public function exportCsv(Request $request)
    {
        if (!auth()->user()->hasPermission('reports.export')) {
            abort(403, 'Unauthorized report export.');
        }
        return Excel::download(new CollegeExport($request), 'colleges_report.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdf(Request $request)
    {
        if (!auth()->user()->hasPermission('reports.export')) {
            abort(403, 'Unauthorized report export.');
        }
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
        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }
        if ($request->filled('fdp_client')) {
            $query->where('fdp_client', $request->fdp_client);
        }

        $colleges = $query->get();

        $pdf = Pdf::loadView('reports.pdf', compact('colleges'));
        return $pdf->download('colleges_report.pdf');
    }

    public function exportStaffExcel(Request $request)
    {
        if (!auth()->user()->hasPermission('reports.export')) {
            abort(403, 'Unauthorized report export.');
        }
        return Excel::download(new StaffActivityExport($request), 'staff_activity_report.xlsx');
    }

    public function exportStaffCsv(Request $request)
    {
        if (!auth()->user()->hasPermission('reports.export')) {
            abort(403, 'Unauthorized report export.');
        }
        return Excel::download(new StaffActivityExport($request), 'staff_activity_report.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportStaffPdf(Request $request)
    {
        if (!auth()->user()->hasPermission('reports.export')) {
            abort(403, 'Unauthorized report export.');
        }
        $export = new StaffActivityExport($request);
        $staffData = $export->collection();

        $selectedStaffName = null;
        if ($request->filled('staff_id')) {
            $selectedStaffName = User::find($request->staff_id)?->name;
        }

        $dateRangeText = 'All Time';
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $dateRangeText = Carbon::parse($request->start_date)->format('d M Y') . ' to ' . Carbon::parse($request->end_date)->format('d M Y');
        }

        // Generate detailed logs for PDF
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->endOfDay() : null;

        $academicLogsQuery = Interaction::with(['college', 'user', 'contactMode']);
        $nonAcademicLogsQuery = NonAcademicInteraction::with(['client', 'employee']);

        if ($request->filled('staff_id')) {
            $academicLogsQuery->where('user_id', $request->staff_id);
            $nonAcademicLogsQuery->where('user_id', $request->staff_id);
        }

        if ($startDate && $endDate) {
            $academicLogsQuery->whereBetween('contact_date', [$startDate, $endDate]);
            $nonAcademicLogsQuery->whereBetween('contact_date', [$startDate, $endDate]);
        }

        $academicMapped = $academicLogsQuery->latest('contact_date')->take(20)->get()->map(function ($item) {
            return (object)[
                'contact_date' => $item->contact_date,
                'staff_name' => $item->user->name ?? 'N/A',
                'type' => 'Academic',
                'target_name' => $item->college->name ?? 'N/A',
                'contact_mode' => $item->contactMode->name ?? 'Call',
                'status' => $item->status->name ?? 'Logged',
                'remarks' => $item->remarks ?? $item->college_response,
            ];
        });

        $nonAcademicMapped = $nonAcademicLogsQuery->latest('contact_date')->take(20)->get()->map(function ($item) {
            return (object)[
                'contact_date' => $item->contact_date,
                'staff_name' => $item->employee->name ?? 'N/A',
                'type' => 'Non-Academic',
                'target_name' => $item->client->name ?? 'N/A',
                'contact_mode' => $item->contact_mode ?? 'Meeting',
                'status' => $item->interaction_status ?? 'Logged',
                'remarks' => $item->remarks ?? $item->client_response,
            ];
        });

        $detailedLogs = $academicMapped->concat($nonAcademicMapped)
            ->sortByDesc(function ($item) {
                return Carbon::parse($item->contact_date)->timestamp;
            })
            ->take(30)
            ->values();

        $pdf = Pdf::loadView('reports.staff_pdf', compact('staffData', 'selectedStaffName', 'dateRangeText', 'detailedLogs'));
        return $pdf->download('staff_activity_report.pdf');
    }
}
