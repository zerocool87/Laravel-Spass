<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct()
    {
        // All users must be authenticated; administrative actions require admin
        $this->middleware('auth');
        $this->middleware('can:admin')->except(['download', 'view', 'embed', 'info']);
    }

    public function index(Request $request): View
    {
        $category = $request->query('category');

        // Admin doit voir tous les documents, publics et privés
        $documents = Document::with(['creator', 'users'])
            ->when($category, function ($q, $cat) {
                $q->where('category', $cat);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.documents.index', compact('documents', 'category'));
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
        }

        return Redirect::route('admin.documents.index')->with('success', 'Document uploaded.');
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
            // delete old
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
        } else {
            $document->users()->detach();
        }

        return Redirect::route('admin.documents.index')->with('success', 'Document updated.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        Storage::delete($document->path);
        $document->delete();

        return Redirect::route('admin.documents.index')->with('success', 'Document deleted.');
    }

    public function download(Document $document)
    {
        // Authorization: must be visible to all or assigned to user or admin
        $user = auth()->user();
        if (! $document->visible_to_all && ! $user->isAdmin() && ! $document->users()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        return Storage::download($document->path, $document->original_name ?? basename($document->path));
    }

    public function info(Document $document)
    {
        $user = auth()->user();
        if (! $document->visible_to_all && ! $user->isAdmin() && ! $document->users()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        return response()->json([
            'mime' => $document->getMimeType(),
            'previewable' => $document->isPreviewable(),
            'embed_url' => route('documents.embed', $document),
            'download_url' => route('documents.download', $document),
            'preview_types' => array_values(config('documents.preview_examples', [])),
        ]);
    }

    public function embed(Document $document)
    {
        // Authorization similar to download
        $user = auth()->user();
        if (! $document->visible_to_all && ! $user->isAdmin() && ! $document->users()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        $path = $document->getFullPath();
        if (! $path || ! file_exists($path)) {
            abort(404);
        }

        $mime = $document->getMimeType() ?: 'application/octet-stream';
        $filename = $document->original_name ?: basename($document->path);
        $filesize = filesize($path);

        $rangeHeader = request()->header('Range');

        // No Range requested -> return full file
        if (empty($rangeHeader)) {
            if (app()->environment('testing')) {
                $content = file_get_contents($path);

                return response($content, 200, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="'.$filename.'"',
                    'Accept-Ranges' => 'bytes',
                    'Content-Length' => (string) $filesize,
                ]);
            }

            $stream = function () use ($path) {
                $fp = fopen($path, 'rb');
                while (! feof($fp)) {
                    echo fread($fp, 8192);
                    flush();
                }
                fclose($fp);
            };

            return new \Symfony\Component\HttpFoundation\StreamedResponse($stream, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
                'Accept-Ranges' => 'bytes',
                'Content-Length' => (string) $filesize,
            ]);
        }

        // Parse Range header (bytes=start-end)
        if (! preg_match('/bytes=([0-9]*)-([0-9]*)/', $rangeHeader, $matches)) {
            return response('', 416, [
                'Content-Range' => "bytes */$filesize",
            ]);
        }

        $start = $matches[1] === '' ? null : (int) $matches[1];
        $end = $matches[2] === '' ? null : (int) $matches[2];

        if ($start === null && $end !== null) {
            // suffix-byte-range-spec: last N bytes
            $start = max(0, $filesize - $end);
            $end = $filesize - 1;
        } elseif ($start !== null && $end === null) {
            $end = $filesize - 1;
        }

        if ($start < 0 || $end < $start || $start >= $filesize) {
            return response('', 416, [
                'Content-Range' => "bytes */$filesize",
            ]);
        }

        $end = min($end, $filesize - 1);
        $length = $end - $start + 1;

        // Stream partial outside testing to avoid memory pressure
        if (! app()->environment('testing')) {
            $stream = function () use ($path, $start, $length) {
                $fp = fopen($path, 'rb');
                fseek($fp, $start);
                $remaining = $length;
                while ($remaining > 0 && ! feof($fp)) {
                    $read = min(8192, $remaining);
                    $data = fread($fp, $read);
                    echo $data;
                    flush();
                    $remaining -= strlen($data);
                }
                fclose($fp);
            };

            return new \Symfony\Component\HttpFoundation\StreamedResponse($stream, 206, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
                'Accept-Ranges' => 'bytes',
                'Content-Range' => "bytes $start-$end/$filesize",
                'Content-Length' => (string) $length,
            ]);
        }

        // In testing, return in-memory partial content for assertions
        $fp = fopen($path, 'rb');
        fseek($fp, $start);
        $content = '';
        $remaining = $length;
        while ($remaining > 0 && ! feof($fp)) {
            $read = min(8192, $remaining);
            $chunk = fread($fp, $read);
            $content .= $chunk;
            $remaining -= strlen($chunk);
        }
        fclose($fp);

        return response($content, 206, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Accept-Ranges' => 'bytes',
            'Content-Range' => "bytes $start-$end/$filesize",
            'Content-Length' => (string) $length,
        ]);
    }
}
