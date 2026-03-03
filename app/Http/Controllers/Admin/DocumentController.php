<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentActionNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category');
        $search = trim((string) $request->query('q', ''));
        $search = $search !== '' ? $search : null;
        $visibility = $request->query('visibility');

        if (! in_array($visibility, ['public', 'private'], true)) {
            $visibility = null;
        }

        $documents = Document::with(['creator', 'users'])
            ->when($category, function ($q, $cat) {
                $q->where('category', $cat);
            })
            ->when($search, function ($q, $search) {
                $q->where(function ($subQuery) use ($search) {
                    $like = '%'.$search.'%';

                    $subQuery->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('original_name', 'like', $like);
                });
            })
            ->when($visibility, function ($q, $visibility) {
                $q->where('visible_to_all', $visibility === 'public');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.documents.index', compact('documents', 'category', 'search', 'visibility'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.documents.create', compact('users'));
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

            $users = User::whereIn('id', $data['assigned_users'])->get();
            foreach ($users as $user) {
                $user->notify(new DocumentActionNotification(
                    'Nouveau document partagé avec vous',
                    'Un document vous a été partagé : '.$document->title,
                    'Voir le document',
                    route('elus.documents.index'),
                ));
            }
        }

        return redirect()->route('admin.documents.index')->with('success', 'Document créé.');
    }

    public function edit(Document $document): View
    {
        $users = User::orderBy('name')->get();
        $assigned = $document->users()->pluck('users.id')->toArray();

        return view('admin.documents.edit', compact('document', 'users', 'assigned'));
    }

    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('file')) {
            Storage::delete($document->path);
            $file = $request->file('file');
            $path = $file->store('documents');
            $document->path = $path;
            $document->original_name = $file->getClientOriginalName();
        }

        $document->title = $data['title'];
        $document->description = $data['description'] ?? null;
        $document->visible_to_all = boolval($data['visible_to_all']);
        $document->category = $data['category'] ?? null;
        $document->save();

        if (! $document->visible_to_all && ! empty($data['assigned_users'])) {
            $document->users()->sync($data['assigned_users']);

            $users = User::whereIn('id', $data['assigned_users'])->get();
            foreach ($users as $user) {
                $user->notify(new DocumentActionNotification(
                    'Document mis à jour : '.$document->title,
                    'Le document a été mis à jour : '.$document->title,
                    'Voir le document',
                    route('elus.documents.index'),
                ));
            }
        } else {
            $document->users()->detach();
        }

        return redirect()->route('admin.documents.index')->with('success', 'Document mis à jour.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        Storage::delete($document->path);
        $document->delete();

        return redirect()->route('admin.documents.index')->with('success', 'Document supprimé.');
    }

    public function download(Document $document): BinaryFileResponse|StreamedResponse
    {
        abort_unless($document->isAccessibleBy(auth()->user()), 403);

        $raw = $document->original_name ?? basename($document->path);
        // strip control characters and limit length
        $filename = preg_replace('/[\\x00-\\x1F\\x7F]+/', '', (string) $raw);
        $filename = trim($filename) ?: basename($document->path);
        if (mb_strlen($filename) > 200) {
            $filename = mb_substr($filename, 0, 200);
        }

        return Storage::download($document->path, $filename);
    }

    public function info(Document $document): JsonResponse
    {
        abort_unless($document->isAccessibleBy(auth()->user()), 403);

        return response()->json([
            'mime' => $document->getMimeType(),
            'previewable' => $document->isPreviewable(),
            'embed_url' => route('documents.embed', $document),
            'download_url' => route('documents.download', $document),
        ]);
    }

    public function embed(Document $document): Response|StreamedResponse
    {
        abort_unless($document->isAccessibleBy(auth()->user()), 403);

        $mime = $document->getMimeType() ?: 'application/octet-stream';

        // sanitize filename for headers
        $raw = $document->original_name ?: basename($document->path);
        $filename = preg_replace('/[\\x00-\\x1F\\x7F]+/', '', (string) $raw);
        $filename = trim($filename) ?: basename($document->path);
        if (mb_strlen($filename) > 200) {
            $filename = mb_substr($filename, 0, 200);
        }
        $safeQuoted = str_replace(['\\', '"'], ['\\\\', '\\"'], $filename);
        $disposition = 'inline; filename="'.$safeQuoted.'"; filename*=UTF-8\'\''.rawurlencode($filename);

        $rangeHeader = request()->header('Range');

        // Prefer streaming via Storage (supports local and remote disks)
        if (Storage::exists($document->path)) {
            $size = Storage::size($document->path) ?: null;

            $headers = [
                'Content-Type' => $mime,
                'Content-Disposition' => $disposition,
                'Accept-Ranges' => 'bytes',
            ];
            if ($size !== null) {
                $filesize = (int) $size;
                $headers['Content-Length'] = (string) $filesize;
            }

            if (empty($rangeHeader)) {
                $stream = function () use ($document) {
                    $readStream = Storage::readStream($document->path);
                    if ($readStream === false) {
                        abort(404);
                    }
                    try {
                        while (! feof($readStream)) {
                            echo fread($readStream, 8192);
                            flush();
                        }
                    } finally {
                        if (is_resource($readStream)) {
                            fclose($readStream);
                        }
                    }
                };

                return new StreamedResponse($stream, 200, $headers);
            }

            // If Range header present, fall back to local file handling for partial responses.
        }

        $path = $document->getFullPath();
        if (! $path || ! file_exists($path)) {
            abort(404);
        }

        $filesize = filesize($path);
        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => $disposition,
            'Accept-Ranges' => 'bytes',
        ];

        if (empty($rangeHeader)) {
            return $this->respondFullFile($path, $filesize, $headers);
        }

        return $this->respondPartialFile($path, $filesize, $rangeHeader, $headers);
    }

    private function respondFullFile(string $path, int $filesize, array $headers): Response|StreamedResponse
    {
        $headers['Content-Length'] = (string) $filesize;

        if (app()->environment('testing')) {
            return response(file_get_contents($path), 200, $headers);
        }

        $stream = function () use ($path) {
            $fp = @fopen($path, 'rb');
            if ($fp === false) {
                abort(404);
            }
            try {
                while (! feof($fp)) {
                    echo fread($fp, 8192);
                    flush();
                }
            } finally {
                if (is_resource($fp)) {
                    fclose($fp);
                }
            }
        };

        return new StreamedResponse($stream, 200, $headers);
    }

    private function respondPartialFile(string $path, int $filesize, string $rangeHeader, array $headers): Response|StreamedResponse
    {
        if (! preg_match('/bytes=([0-9]*)-([0-9]*)/', $rangeHeader, $matches)) {
            return response('', 416, ['Content-Range' => "bytes */$filesize"]);
        }

        $start = $matches[1] === '' ? null : (int) $matches[1];
        $end = $matches[2] === '' ? null : (int) $matches[2];

        if ($start === null && $end !== null) {
            $start = max(0, $filesize - $end);
            $end = $filesize - 1;
        } elseif ($start !== null && $end === null) {
            $end = $filesize - 1;
        }

        if ($start < 0 || $end < $start || $start >= $filesize) {
            return response('', 416, ['Content-Range' => "bytes */$filesize"]);
        }

        $end = min($end, $filesize - 1);
        $length = $end - $start + 1;

        $headers['Content-Range'] = "bytes $start-$end/$filesize";
        $headers['Content-Length'] = (string) $length;

        if (app()->environment('testing')) {
            $fp = @fopen($path, 'rb');
            if ($fp === false) {
                abort(404);
            }
            try {
                fseek($fp, $start);
                $content = fread($fp, $length);
            } finally {
                if (is_resource($fp)) {
                    fclose($fp);
                }
            }

            return response($content, 206, $headers);
        }

        $stream = function () use ($path, $start, $length) {
            $fp = @fopen($path, 'rb');
            if ($fp === false) {
                abort(404);
            }
            try {
                fseek($fp, $start);
                $remaining = $length;
                while ($remaining > 0 && ! feof($fp)) {
                    $read = min(8192, $remaining);
                    $data = fread($fp, $read);
                    if ($data === false) {
                        break;
                    }
                    echo $data;
                    flush();
                    $remaining -= strlen($data);
                }
            } finally {
                if (is_resource($fp)) {
                    fclose($fp);
                }
            }
        };

        return new StreamedResponse($stream, 206, $headers);
    }
}
