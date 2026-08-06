<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\College;
use App\Models\ContactPerson;
use App\Models\Interaction;
use App\Models\NonAcademicClient;
use App\Models\NonAcademicInteraction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalColleges = College::count();
        $totalUniversities = College::where('is_university', true)->count();
        $totalContacts = ContactPerson::count();
        $autonomousColleges = College::where('type', 'Autonomous')->count();
        $affiliatedColleges = College::where('type', 'Affiliated')->count();
        $addedThisMonth = College::whereMonth('created_at', Carbon::now()->month)
                                 ->whereYear('created_at', Carbon::now()->year)
                                 ->count();
        
        $totalNonAcademicClients = NonAcademicClient::count();
        $totalNonAcademicInteractions = NonAcademicInteraction::count();

        // Chart Data (Widgets)
        $interactedCount = College::has('interactions')->count();
        $nonInteractedCount = College::doesntHave('interactions')->count();
        $collegeInteractionStats = [
            'Interacted' => $interactedCount,
            'Not Interacted' => $nonInteractedCount
        ];
        
        $universityWiseCount = College::with('university')
            ->selectRaw('university_id, count(*) as count')
            ->whereNotNull('university_id')
            ->groupBy('university_id')
            ->get()
            ->mapWithKeys(function ($item) {
                $name = $item->university ? $item->university->name : 'N/A';
                return [$name => $item->count];
            });
            
        $districtWiseCount = College::selectRaw('district, count(*) as count')
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->groupBy('district')
            ->pluck('count', 'district');
            
        $recentColleges = College::latest()->take(5)->get();

        // User-specific ("My Activity") metrics
        $userId = auth()->id();
        $myAcademicInteractionsCount = Interaction::where('user_id', $userId)->count();
        $myNonAcademicInteractionsCount = NonAcademicInteraction::where('user_id', $userId)->count();
        $myTotalInteractionsCount = $myAcademicInteractionsCount + $myNonAcademicInteractionsCount;

        $myRecentInteractions = Interaction::with(['college', 'contactPerson', 'status', 'contactMode'])
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $myRecentNonAcademicInteractions = NonAcademicInteraction::with(['client'])
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $academicFollowups = Interaction::with(['college', 'contactPerson', 'status'])
            ->where('user_id', $userId)
            ->whereNotNull('next_followup_date')
            ->where('next_followup_date', '>=', Carbon::today())
            ->get()
            ->map(function ($item) {
                return (object)[
                    'id' => $item->id,
                    'type' => 'Academic',
                    'type_badge' => 'bg-primary-subtle text-primary',
                    'icon' => 'fas fa-university',
                    'title' => $item->college->name ?? 'N/A',
                    'subtitle' => 'Contact: ' . ($item->contactPerson->name ?? 'N/A'),
                    'next_followup_date' => $item->next_followup_date,
                    'status' => $item->status->name ?? 'Pending',
                    'remarks' => $item->remarks,
                    'view_url' => $item->college_id ? route('colleges.show', $item->college_id) : route('interactions.index'),
                ];
            });

        $nonAcademicFollowups = NonAcademicInteraction::with(['client'])
            ->where('user_id', $userId)
            ->whereNotNull('next_followup_date')
            ->where('next_followup_date', '>=', Carbon::today())
            ->get()
            ->map(function ($item) {
                return (object)[
                    'id' => $item->id,
                    'type' => 'Non-Academic',
                    'type_badge' => 'bg-secondary-subtle text-dark',
                    'icon' => 'fas fa-building',
                    'title' => $item->client->name ?? 'N/A',
                    'subtitle' => ($item->client && $item->client->contact_person_name ? ('Contact: ' . $item->client->contact_person_name) : ($item->purpose ?? 'Corporate Touchpoint')),
                    'next_followup_date' => $item->next_followup_date,
                    'status' => $item->interaction_status ?? 'Pending',
                    'remarks' => $item->remarks,
                    'view_url' => $item->non_academic_client_id ? route('non-academic-clients.show', $item->non_academic_client_id) : route('non-academic-interactions.index'),
                ];
            });

        $myUpcomingFollowups = $academicFollowups->concat($nonAcademicFollowups)
            ->sortBy(function ($item) {
                return Carbon::parse($item->next_followup_date)->timestamp;
            })
            ->take(10)
            ->values();

        $myPendingFollowupsCount = $academicFollowups->count() + $nonAcademicFollowups->count();

        $myMonthlyAuditCount = \App\Models\AuditLog::where('user_id', $userId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $myAddedCollegesCount = \App\Models\AuditLog::where('user_id', $userId)
            ->where('module', 'College')
            ->where('action', 'create')
            ->count();

        $myAddedContactsCount = \App\Models\AuditLog::where('user_id', $userId)
            ->where('module', 'ContactPerson')
            ->where('action', 'create')
            ->count();

        $myAddedClientsCount = \App\Models\AuditLog::where('user_id', $userId)
            ->where('module', 'NonAcademicClient')
            ->where('action', 'create')
            ->count();

        return view('dashboard', compact(
            'totalColleges', 'totalUniversities', 'totalContacts', 
            'autonomousColleges', 'affiliatedColleges', 'addedThisMonth',
            'totalNonAcademicClients', 'totalNonAcademicInteractions',
            'collegeInteractionStats', 'universityWiseCount', 'districtWiseCount', 'recentColleges',
            'myAcademicInteractionsCount', 'myNonAcademicInteractionsCount', 'myTotalInteractionsCount',
            'myRecentInteractions', 'myRecentNonAcademicInteractions', 'myUpcomingFollowups',
            'myPendingFollowupsCount', 'myMonthlyAuditCount',
            'myAddedCollegesCount', 'myAddedContactsCount', 'myAddedClientsCount'
        ));
    }
}
