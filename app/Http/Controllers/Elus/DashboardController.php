<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Models\Project;
use App\Models\Reunion;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Espace Élus dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Get upcoming reunions (next 2)
        $upcomingReunions = Reunion::with('instance')
            ->upcoming()
            ->take(2)
            ->get();

        // Get active projects
        $activeProjects = Project::active()
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Get latest documents accessible to the user
        $latestDocuments = Document::where(function ($q) use ($user) {
                $q->where('visible_to_all', true)
                    ->orWhere('created_by', $user->id)
                    ->orWhereHas('users', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
            })
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
            'total_documents' => Document::where(function ($q) use ($user) {
                $q->where('visible_to_all', true)
                    ->orWhere('created_by', $user->id)
                    ->orWhereHas('users', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
            })->count(),
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
