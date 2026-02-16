<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Elus\Concerns\FiltersDocuments;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    use FiltersDocuments;

    /**
     * Display a listing of the documents.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = $this->getUserAccessibleDocuments($user);

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

        $documents = $query->latest()->get();
        $documentsByCategory = $documents->groupBy(function ($d) {
            return $d->category ?: __('Non catégorisé');
        });

        $categories = Document::query()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort();

        return view('elus.documents.index', compact('documentsByCategory', 'categories'));
    }

    /**
     * Show the form for creating a new document.
     */
    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('elus.documents.create', compact('users'));
    }

    /**
     * Store a newly created document in storage.
     */
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

        return redirect()->route('elus.documents.index')->with('success', 'Document créé.');
    }
}
