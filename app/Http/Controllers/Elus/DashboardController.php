<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Elus\Concerns\FiltersDocuments;
use App\Models\Actualite;
use App\Models\Instance;
use App\Models\Project;
use App\Models\Reunion;
use Illuminate\Http\JsonResponse;
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
        $activeProjects = Project::query()
            ->visibleToUser($user)
            ->active()
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Get latest documents accessible to the user (newest first)
        $latestDocuments = $this->getUserAccessibleDocuments($user)
            ->with('creator')
            ->latest()
            ->take(5)
            ->get();

        // Get instances (sorted alphabetically)
        $instances = Instance::withCount('reunions')
            ->orderBy('name')
            ->take(5)
            ->get();

        // Get latest published actualités
        $latestActualites = Actualite::with('creator')
            ->where('is_published', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        // Onboarding tour (show once per session)
        $showOnboarding = ! session('onboarding_completed', false);

        return view('elus.dashboard', compact(
            'user',
            'upcomingReunions',
            'activeProjects',
            'latestDocuments',
            'latestActualites',
            'instances',
            'showOnboarding',
        ));
    }

    /**
     * Mark onboarding as completed for this session.
     */
    public function onboardingComplete(Request $request): JsonResponse
    {
        session(['onboarding_completed' => true]);

        return response()->json(['ok' => true]);
    }
}
