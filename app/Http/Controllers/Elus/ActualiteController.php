<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Models\Actualite;
use Illuminate\View\View;

class ActualiteController extends Controller
{
    public function index(): View
    {
        $actualites = Actualite::with('creator')
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        return view('elus.actualites.index', compact('actualites'));
    }

    public function show(Actualite $actualite): View
    {
        abort_unless($actualite->is_published, 404);

        return view('elus.actualites.show', compact('actualite'));
    }
}
