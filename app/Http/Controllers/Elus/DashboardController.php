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
        $projectsQuery = Project::query()->visibleToUser($user);

        // Get upcoming reunions (next 5)
        $upcomingReunions = Reunion::with('instance')
            ->upcoming()
            ->take(5)
            ->get();

        // Get active projects
        $activeProjects = (clone $projectsQuery)
            ->active()
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Get latest documents accessible to the user (limit to 3, newest first)
        $latestDocuments = $this->getUserAccessibleDocuments($user)
            ->latest()
            ->take(3)
            ->get();

        // Get instances (limit to 3, sorted alphabetically)
        $instances = Instance::withCount('reunions')
            ->orderBy('name')
            ->take(3)
            ->get();

        // Statistics
        $stats = [
            'total_projects' => (clone $projectsQuery)->count(),
            'active_projects' => (clone $projectsQuery)->active()->count(),
            'total_reunions' => Reunion::count(),
            'upcoming_reunions' => Reunion::upcoming()->count(),
            'total_instances' => Instance::count(),
            'total_budget' => (clone $projectsQuery)->active()->sum('budget'),
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
