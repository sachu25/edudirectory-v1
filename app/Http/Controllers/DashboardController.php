<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\College;
use App\Models\University;
use App\Models\ContactPerson;
use App\Models\Interaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalColleges = College::count();
        $totalUniversities = University::count();
        $totalContacts = ContactPerson::count();
        $autonomousColleges = College::where('type', 'Autonomous')->count();
        $affiliatedColleges = College::where('type', 'Affiliated')->count();
        $addedThisMonth = College::whereMonth('created_at', Carbon::now()->month)
                                 ->whereYear('created_at', Carbon::now()->year)
                                 ->count();

        // Chart Data (Widgets)
        $interactedCount = College::has('interactions')->count();
        $nonInteractedCount = College::doesntHave('interactions')->count();
        $collegeInteractionStats = [
            'Interacted' => $interactedCount,
            'Not Interacted' => $nonInteractedCount
        ];
        
        $universityWiseCount = College::with('university')
            ->selectRaw('university_id, count(*) as count')
            ->groupBy('university_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [($item->university->short_name ?? $item->university->name ?? 'Unknown') => $item->count];
            });
            
            
        $districtWiseCount = College::selectRaw('district, count(*) as count')
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->groupBy('district')
            ->pluck('count', 'district');
            
        $recentColleges = College::with('university')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalColleges', 'totalUniversities', 'totalContacts', 
            'autonomousColleges', 'affiliatedColleges', 'addedThisMonth',
            'collegeInteractionStats', 'universityWiseCount', 'districtWiseCount', 'recentColleges'
        ));
    }
}
