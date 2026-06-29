<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', '')) ?: null;
        $category = $request->query('category');
        $visibility = match ($request->query('visibility')) {
            'public', 'private' => $request->query('visibility'),
            default => null,
        };

        $documents = Document::with(['creator', 'users'])
            ->when($category, fn ($q, $cat) => $q->where('category', $cat))
            ->when($search, function ($q, $search) {
                $like = '%'.$search.'%';
                $q->where(fn ($sq) => $sq
                    ->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('original_name', 'like', $like)
                );
            })
            ->when($visibility, fn ($q, $v) => $q->where('visible_to_all', $v === 'public'))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.documents.index', compact('documents', 'category', 'search', 'visibility'));
    }

    public function create(): View
    {
        return view('admin.documents.create', [
            'users' => User::orderBy('name')->get(),
            'titres' => User::titresElus(),
        ]);
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $document = $this->documentService->create(
            title: $data['title'],
            file: $request->file('file'),
            creator: $request->user(),
            description: $data['description'] ?? null,
            visibleToAll: (bool) $data['visible_to_all'],
            titres: $data['titres'] ?? null,
            category: $data['category'] ?? null,
            assignedUserIds: $data['assigned_users'] ?? null,
        );

        if (! $document->visible_to_all && ! empty($data['assigned_users'])) {
            $this->documentService->notifyAssignedUsers($document, $request->user());
        }

        return redirect()->route('admin.documents.index')
            ->with('success', __('Document créé.'))->with('celebrate', true);
    }

    public function edit(Document $document): View
    {
        return view('admin.documents.edit', [
            'document' => $document,
            'users' => User::orderBy('name')->get(),
            'assigned' => $document->users()->pluck('users.id')->toArray(),
            'titres' => User::titresElus(),
        ]);
    }

    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            $this->documentService->replaceFile($document, $request->file('file'));
        }

        $document->timestamps = false;

        $document->forceFill([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'visible_to_all' => boolval($data['visible_to_all']),
            'titres' => $data['visible_to_all'] ? null : ($data['titres'] ?? null),
            'category' => $data['category'] ?? null,
        ])->save();

        if (! $document->visible_to_all && ! empty($data['assigned_users'])) {
            $document->users()->sync($data['assigned_users']);
            $this->documentService->notifyAssignedUsers($document, $request->user());
        } else {
            $document->users()->detach();
        }

        return redirect()->route('admin.documents.index')
            ->with('success', __('Document mis à jour.'));
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->documentService->delete($document);

        return redirect()->route('admin.documents.index')
            ->with('success', __('Document supprimé.'));
    }

    public function download(Request $request, Document $document): BinaryFileResponse|StreamedResponse
    {
        abort_unless($document->isAccessibleBy($request->user()), 403);

        return $this->documentService->streamToResponse($document);
    }

    public function info(Request $request, Document $document): JsonResponse
    {
        abort_unless($document->isAccessibleBy($request->user()), 403);

        return response()->json([
            'mime' => $document->getMimeType(),
            'previewable' => $document->isPreviewable(),
            'embed_url' => route('documents.embed', $document),
            'download_url' => route('documents.download', $document),
        ]);
    }

    public function embed(Request $request, Document $document): StreamedResponse|Response
    {
        abort_unless($document->isAccessibleBy($request->user()), 403);

        $rangeHeader = $request->header('Range');

        if (! empty($rangeHeader)) {
            return $this->documentService->streamPartial($document, $rangeHeader);
        }

        return $this->documentService->streamToResponse($document);
    }
}
