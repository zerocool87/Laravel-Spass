<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActualiteRequest;
use App\Models\Actualite;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActualiteController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('q', '');
        $status = $request->input('status', '');

        $actualites = Actualite::with('creator')
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($status === 'draft', fn ($q) => $q->where('is_published', false))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.actualites.index', compact('actualites', 'search', 'status'));
    }

    public function create(): View
    {
        return view('admin.actualites.create');
    }

    public function store(ActualiteRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        if ($data['is_published']) {
            $data['published_at'] = now();
        }

        Actualite::create($data);

        return redirect()->route('admin.actualites.index')
            ->with('success', __('Actualité publiée avec succès.'));
    }

    public function edit(Actualite $actualite): View
    {
        return view('admin.actualites.edit', compact('actualite'));
    }

    public function update(ActualiteRequest $request, Actualite $actualite): RedirectResponse
    {
        $data = $request->validated();

        if ($data['is_published'] && ! $actualite->is_published) {
            $data['published_at'] = now();
        } elseif (! $data['is_published']) {
            $data['published_at'] = null;
        }

        $actualite->update($data);

        return redirect()->route('admin.actualites.index')
            ->with('success', __('Actualité mise à jour.'));
    }

    public function destroy(Actualite $actualite): RedirectResponse
    {
        $actualite->delete();

        return redirect()->route('admin.actualites.index')
            ->with('success', __('Actualité supprimée.'));
    }
}
