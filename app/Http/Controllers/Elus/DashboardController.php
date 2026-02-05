<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Elus\Concerns\FiltersDocuments;
use App\Models\Instance;
use App\Models\Project;
use App\Models\Reunion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use FiltersDocuments;
    /**
     * Display the Espace Élus dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Get upcoming reunions (next 5)
        $upcomingReunions = Reunion::with('instance')
            ->upcoming()
            ->take(5)
            ->get();

        // Get active projects
        $activeProjects = Project::active()
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Get latest documents accessible to the user
        $latestDocuments = $this->getUserAccessibleDocuments($user)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Get instances
        $instances = Instance::withCount('reunions')
            ->orderBy('name')
            ->get();

        // Statistics
        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::active()->count(),
            'total_reunions' => Reunion::count(),
            'upcoming_reunions' => Reunion::upcoming()->count(),
            'total_instances' => Instance::count(),
            'total_budget' => Project::active()->sum('budget'),
            'total_documents' => $this->getUserAccessibleDocuments($user)->count(),
        ];

        return view('elus.dashboard', compact(
            'upcomingReunions',
            'activeProjects',
            'latestDocuments',
            'instances',
            'stats'
        ));
    }
}
