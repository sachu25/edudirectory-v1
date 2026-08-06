<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Interaction;
use App\Models\NonAcademicInteraction;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StaffActivityExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request = null)
    {
        $this->request = $request ?? request();
    }

    public function collection()
    {
        $usersQuery = User::query();
        if ($this->request->filled('user_id')) {
            $usersQuery->where('id', $this->request->user_id);
        }
        $users = $usersQuery->get();

        $startDate = $this->request->filled('start_date') ? Carbon::parse($this->request->start_date)->startOfDay() : null;
        $endDate = $this->request->filled('end_date') ? Carbon::parse($this->request->end_date)->endOfDay() : null;

        $reportData = collect();

        foreach ($users as $user) {
            // Audit Log Created Counts
            $collegesAddedQuery = AuditLog::where('user_id', $user->id)
                ->where('module', 'College')
                ->where('action', 'create');

            $contactsAddedQuery = AuditLog::where('user_id', $user->id)
                ->where('module', 'ContactPerson')
                ->where('action', 'create');

            $clientsAddedQuery = AuditLog::where('user_id', $user->id)
                ->where('module', 'NonAcademicClient')
                ->where('action', 'create');

            // Interactions Queries
            $academicInteractionsQuery = Interaction::where('user_id', $user->id);
            $nonAcademicInteractionsQuery = NonAcademicInteraction::where('user_id', $user->id);

            // Follow-ups Query
            $pendingFollowupsQuery1 = Interaction::where('user_id', $user->id)
                ->whereNotNull('next_followup_date')
                ->where('next_followup_date', '>=', Carbon::today());

            $pendingFollowupsQuery2 = NonAcademicInteraction::where('user_id', $user->id)
                ->whereNotNull('next_followup_date')
                ->where('next_followup_date', '>=', Carbon::today());

            if ($startDate && $endDate) {
                $collegesAddedQuery->whereBetween('created_at', [$startDate, $endDate]);
                $contactsAddedQuery->whereBetween('created_at', [$startDate, $endDate]);
                $clientsAddedQuery->whereBetween('created_at', [$startDate, $endDate]);
                $academicInteractionsQuery->whereBetween('contact_date', [$startDate, $endDate]);
                $nonAcademicInteractionsQuery->whereBetween('contact_date', [$startDate, $endDate]);
            }

            $collegesAdded = $collegesAddedQuery->count();
            $contactsAdded = $contactsAddedQuery->count();
            $clientsAdded = $clientsAddedQuery->count();
            $academicLogs = $academicInteractionsQuery->count();
            $nonAcademicLogs = $nonAcademicInteractionsQuery->count();
            $pendingFollowups = $pendingFollowupsQuery1->count() + $pendingFollowupsQuery2->count();

            $reportData->push((object)[
                'staff_name' => $user->name,
                'staff_email' => $user->email,
                'colleges_added' => $collegesAdded,
                'contacts_added' => $contactsAdded,
                'clients_added' => $clientsAdded,
                'academic_interactions' => $academicLogs,
                'non_academic_interactions' => $nonAcademicLogs,
                'total_interactions' => $academicLogs + $nonAcademicLogs,
                'pending_followups' => $pendingFollowups,
            ]);
        }

        return $reportData;
    }

    public function headings(): array
    {
        return [
            'Staff Member',
            'Email Address',
            'Institutions Created',
            'Contacts Created',
            'Corporate Clients Created',
            'Academic Interactions Logged',
            'Non-Academic Interactions Logged',
            'Total Interactions Logged',
            'Pending Follow-ups',
        ];
    }

    public function map($row): array
    {
        return [
            $row->staff_name,
            $row->staff_email,
            $row->colleges_added,
            $row->contacts_added,
            $row->clients_added,
            $row->academic_interactions,
            $row->non_academic_interactions,
            $row->total_interactions,
            $row->pending_followups,
        ];
    }
}
