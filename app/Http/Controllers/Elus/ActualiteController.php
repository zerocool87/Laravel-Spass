<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Models\Actualite;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActualiteController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search', '');

        $actualites = Actualite::with('creator')
            ->published()
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->orderBy('published_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('elus.actualites.index', compact('actualites', 'search'));
    }

    public function show(Actualite $actualite): View
    {
        abort_unless($actualite->is_published, 404);

        return view('elus.actualites.show', compact('actualite'));
    }
}
