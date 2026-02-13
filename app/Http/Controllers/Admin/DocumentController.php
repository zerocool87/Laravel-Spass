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
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->query('category');

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

    public function download(Document $document): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        abort_unless($document->isAccessibleBy(auth()->user()), 403);

        return Storage::download($document->path, $document->original_name ?? basename($document->path));
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

        $path = $document->getFullPath();
        if (! $path || ! file_exists($path)) {
            abort(404);
        }

        $mime = $document->getMimeType() ?: 'application/octet-stream';
        $filename = $document->original_name ?: basename($document->path);
        $filesize = filesize($path);

        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Accept-Ranges' => 'bytes',
        ];

        $rangeHeader = request()->header('Range');

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
            $fp = fopen($path, 'rb');
            while (! feof($fp)) {
                echo fread($fp, 8192);
                flush();
            }
            fclose($fp);
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
            $fp = fopen($path, 'rb');
            fseek($fp, $start);
            $content = fread($fp, $length);
            fclose($fp);

            return response($content, 206, $headers);
        }

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

        return new StreamedResponse($stream, 206, $headers);
    }
}
