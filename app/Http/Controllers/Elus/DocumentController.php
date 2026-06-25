<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    /**
     * Display a listing of the documents.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Document::accessibleTo($user);

        if ($request->filled('category')) {
            if ($request->category === 'uncategorized') {
                $query->whereNull('category');
            } else {
                $query->where('category', $request->category);
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        $documents = $query->with(['creator', 'users'])->latest()->paginate(20)->withQueryString();
        $documentsByCategory = $documents->getCollection()->groupBy(function ($d) {
            return $d->category ?: __('Non catégorisé');
        });

        $categories = Document::query()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort();

        return view('elus.documents.index', compact('documentsByCategory', 'categories', 'documents'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('elus.documents.create', compact('users'));
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $file = $request->file('file');
        $path = $file->store('documents');

        $document = Document::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'created_by' => $request->user()->id,
            'visible_to_all' => boolval($data['visible_to_all']),
            'category' => $data['category'] ?? null,
        ]);

        if (! $document->visible_to_all && ! empty($data['assigned_users'])) {
            $document->users()->sync($data['assigned_users']);
        }

        return redirect()->route('elus.documents.index')->with('success', __('Document créé.'))->with('celebrate', true);
    }
}
