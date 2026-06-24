<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstanceController extends Controller
{
    public function index(Request $request): View
    {
        $instances = Instance::withCount('reunions')
            ->orderBy('name')
            ->get();

        return view('elus.instances.index', compact('instances'));
    }

    public function show(Instance $instance): View
    {
        $instance->load(['reunions' => function ($query) {
            $query->orderBy('start_time', 'desc');
        }]);

        $upcomingReunions = $instance->upcomingReunions()->take(5)->get();
        $pastReunions = $instance->reunions()->where('end_time', '<', now())->orderBy('start_time', 'desc')->take(10)->get();

        return view('elus.instances.show', compact('instance', 'upcomingReunions', 'pastReunions'));
    }
}
