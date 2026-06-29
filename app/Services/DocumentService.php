<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\User;
use App\Notifications\DocumentActionNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentService
{
    public function create(
        string $title,
        UploadedFile $file,
        User $creator,
        ?string $description = null,
        bool $visibleToAll = false,
        ?array $titres = null,
        ?string $category = null,
        ?array $assignedUserIds = null,
    ): Document {
        $path = $file->store('documents');

        $document = Document::create([
            'title' => $title,
            'description' => $description,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'created_by' => $creator->id,
            'visible_to_all' => $visibleToAll,
            'titres' => $visibleToAll ? null : $titres,
            'category' => $category,
        ]);

        if (! $visibleToAll && $assignedUserIds) {
            $document->users()->sync($assignedUserIds);
        }

        return $document;
    }

    public function replaceFile(Document $document, UploadedFile $file): Document
    {
        $oldPath = $document->path;

        $document->update([
            'path' => $file->store('documents'),
            'original_name' => $file->getClientOriginalName(),
        ]);

        if ($oldPath && Storage::exists($oldPath)) {
            Storage::delete($oldPath);
        }

        return $document;
    }

    public function delete(Document $document): void
    {
        $document->users()->detach();

        $path = $document->path;

        $document->delete();

        if ($path && Storage::exists($path)) {
            Storage::delete($path);
        }
    }

    public function notifyAssignedUsers(Document $document, User $sender): void
    {
        $users = $document->users()->where('user_id', '!=', $sender->id)->get();

        if ($users->isNotEmpty()) {
            Notification::send($users, new DocumentActionNotification(
                subject: __('Nouveau document partagé'),
                line: __('Un document vous a été partagé : :title', ['title' => $document->title]),
                actionLabel: __('Voir le document'),
                actionUrl: route('documents.download', $document),
            ));
        }
    }

    public function streamToResponse(Document $document): StreamedResponse
    {
        $filePath = $this->resolveStoragePath($document);

        if ($filePath === null || ! file_exists($filePath)) {
            abort(404, __('Fichier non trouvé.'));
        }

        $fileSize = filesize($filePath);
        $mimeType = $this->resolveMimeType($document, $filePath);

        return new StreamedResponse(function () use ($filePath) {
            readfile($filePath);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'inline; filename="'.rawurlencode($document->original_name).'"',
            'Accept-Ranges' => 'bytes',
        ]);
    }

    public function streamPartial(Document $document, string $range): StreamedResponse|\Illuminate\Http\Response
    {
        $filePath = $this->resolveStoragePath($document);

        if ($filePath === null || ! file_exists($filePath)) {
            abort(404);
        }

        $fileSize = filesize($filePath);
        $mimeType = $this->resolveMimeType($document, $filePath);

        $parsed = $this->parseRange($range, $fileSize);

        if ($parsed === null) {
            return response('', 416, [
                'Content-Type' => $mimeType,
                'Content-Range' => "bytes */{$fileSize}",
            ]);
        }

        [$start, $end] = $parsed;
        $length = $end - $start + 1;

        return new StreamedResponse(function () use ($filePath, $start, $length) {
            $handle = fopen($filePath, 'rb');
            fseek($handle, $start);
            echo fread($handle, $length);
            fclose($handle);
        }, 206, [
            'Content-Type' => $mimeType,
            'Content-Length' => (string) $length,
            'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
            'Content-Disposition' => 'inline; filename="'.rawurlencode($document->original_name).'"',
            'Accept-Ranges' => 'bytes',
        ]);
    }

    /** @return array{int, int}|null */
    private function parseRange(string $range, int $fileSize): ?array
    {
        $parts = explode('=', $range, 2);
        $boundaries = explode('-', $parts[1] ?? '');

        $start = max(0, (int) ($boundaries[0] ?? 0));
        $end = min($fileSize - 1, (int) ($boundaries[1] ?? $fileSize - 1));

        if ($start > $end || $start >= $fileSize) {
            return null;
        }

        return [$start, $end];
    }

    private function resolveStoragePath(Document $document): ?string
    {
        if (Storage::exists($document->path)) {
            return Storage::path($document->path);
        }

        $fallback = storage_path('app/'.$document->path);

        return file_exists($fallback) ? $fallback : null;
    }

    private function resolveMimeType(Document $document, string $filePath): string
    {
        $mimeFromStorage = Storage::exists($document->path) ? Storage::mimeType($document->path) : null;

        return $mimeFromStorage ?: (mime_content_type($filePath) ?: 'application/octet-stream');
    }
}
