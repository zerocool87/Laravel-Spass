<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThematiqueRequest;
use App\Models\ForumThread;
use App\Models\Thematique;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThematiqueController extends Controller
{
    public function index(): View
    {
        $thematiques = Thematique::query()
            ->withCount('forumThreads')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.thematiques.index', compact('thematiques'));
    }

    public function create(): View
    {
        return view('admin.thematiques.create');
    }

    public function store(ThematiqueRequest $request): RedirectResponse
    {
        Thematique::create($request->validated());

        return redirect()
            ->route('admin.thematiques.index')
            ->with('success', __('Thématique créée avec succès.'));
    }

    public function edit(Thematique $thematique): View
    {
        return view('admin.thematiques.edit', compact('thematique'));
    }

    public function update(ThematiqueRequest $request, Thematique $thematique): RedirectResponse
    {
        $thematique->update($request->validated());

        return redirect()
            ->route('admin.thematiques.index')
            ->with('success', __('Thématique mise à jour.'));
    }

    public function destroy(Thematique $thematique): RedirectResponse
    {
        if (ForumThread::where('thematique_id', $thematique->id)->exists()) {
            return back()->withErrors([
                'delete' => __('Impossible de supprimer cette thématique : elle contient des discussions du forum.'),
            ]);
        }

        $thematique->delete();

        return redirect()
            ->route('admin.thematiques.index')
            ->with('success', __('Thématique supprimée.'));
    }
}
