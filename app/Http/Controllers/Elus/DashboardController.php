<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Elus\Concerns\FiltersDocuments;
use App\Models\Actualite;
use App\Models\Instance;
use App\Models\Project;
use App\Models\Reunion;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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

        // Get upcoming reunions (4 dernières)
        $upcomingReunions = Reunion::with('instance')
            ->upcoming()
            ->take(4)
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

        // Get all instances (sorted alphabetically)
        $instances = Instance::withCount('reunions')
            ->orderBy('name')
            ->get();

        // Get latest published actualités
        $latestActualites = Actualite::with('creator')
            ->where('is_published', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        // Current weather (Limoges)
        $weather = $this->getWeather();

        // Onboarding tour (show once per session)
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

    /**
     * Get current weather for Limoges from Open-Meteo.
     */
    private function getWeather(): array
    {
        return Cache::remember('weather_limoges', 1800, function () {
            try {
                $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => 45.83,
                    'longitude' => 1.26,
                    'current_weather' => true,
                    'timezone' => 'auto',
                ]);

                if ($response->failed()) {
                    return ['icon' => '❓', 'temp' => '--'];
                }

                $data = $response->json();
                $code = $data['current_weather']['weathercode'] ?? 0;
                $temp = round($data['current_weather']['temperature'] ?? 0);

                $icon = match (true) {
                    $code === 0 => '☀️',
                    $code <= 3 => '⛅',
                    $code >= 95 => '⛈️',
                    $code >= 80 => '🌦️',
                    $code >= 71 => '❄️',
                    $code >= 61 => '🌧️',
                    $code >= 51 => '🌦️',
                    $code >= 45 => '🌫️',
                    default => '☀️',
                };

                return ['icon' => $icon, 'temp' => $temp.'°C'];
            } catch (RequestException) {
                return ['icon' => '❓', 'temp' => '--'];
            }
        });
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
