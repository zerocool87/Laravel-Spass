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

    public function show(Request $request, Instance $instance): View
    {
        $upcomingReunions = $instance->upcomingReunions()
            ->byTitres($request->user())
            ->take(5)->get();

        $pastReunions = $instance->reunions()
            ->where('end_time', '<', now())
            ->byTitres($request->user())
            ->orderBy('start_time', 'desc')
            ->take(10)->get();

        return view('elus.instances.show', compact('instance', 'upcomingReunions', 'pastReunions'));
    }
}
