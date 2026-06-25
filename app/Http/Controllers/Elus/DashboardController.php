<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Elus\Concerns\FiltersDocuments;
use App\Models\Actualite;
use App\Models\Instance;
use App\Models\Project;
use App\Models\Reunion;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use FiltersDocuments;

    public function __construct(
        private readonly WeatherService $weatherService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $upcomingReunions = Reunion::with('instance')
            ->upcoming()
            ->take(4)
            ->get();

        $activeProjects = Project::query()
            ->visibleToUser($user)
            ->active()
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $latestDocuments = $this->getUserAccessibleDocuments($user)
            ->with('creator')
            ->latest()
            ->take(5)
            ->get();

        $instances = Instance::withCount('reunions')
            ->orderBy('name')
            ->get();

        $latestActualites = Actualite::with('creator')
            ->where('is_published', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        $weather = $this->weatherService->getWeather(45.83, 1.26, 'Limoges');

        $showOnboarding = ! session('onboarding_completed', false);

        return view('elus.dashboard', compact(
            'user',
            'upcomingReunions',
            'activeProjects',
            'latestDocuments',
            'latestActualites',
            'instances',
            'weather',
            'showOnboarding',
        ));
    }

    public function weatherByCoords(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');

        $weather = $this->weatherService->getWeather($lat, $lng);

        return response()->json($weather);
    }

    public function onboardingComplete(Request $request): JsonResponse
    {
        session(['onboarding_completed' => true]);

        return response()->json(['ok' => true]);
    }
}
