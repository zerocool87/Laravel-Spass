<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

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
        return view('elus.documents.create', [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $this->documentService->create(
            title: $data['title'],
            file: $request->file('file'),
            creator: $request->user(),
            description: $data['description'] ?? null,
            visibleToAll: (bool) $data['visible_to_all'],
            titres: $data['titres'] ?? null,
            category: $data['category'] ?? null,
            assignedUserIds: $data['assigned_users'] ?? null,
        );

        return redirect()->route('elus.documents.index')
            ->with('success', __('Document créé.'))
            ->with('celebrate', true);
    }
}
