<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'path',
        'original_name',
        'created_by',
        'visible_to_all',
    ];

    protected $casts = [
        'visible_to_all' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'document_user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if a user can access this document.
     */
    public function isAccessibleBy(?User $user): bool
    {
        if ($this->visible_to_all) {
            return true;
        }
        if (!$user) {
            return false;
        }
        if ($this->created_by === $user->id) {
            return true;
        }
        if ($user->isAdmin()) {
            return true;
        }
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Get full path to stored file on disk.
     */
    public function getFullPath(): ?string
    {
        if (\Illuminate\Support\Facades\Storage::exists($this->path)) {
            return \Illuminate\Support\Facades\Storage::path($this->path);
        }
        $fallback = storage_path('app/' . $this->path);
        return file_exists($fallback) ? $fallback : null;
    }

    /**
     * Get MIME type of stored file.
     */
    public function getMimeType(): ?string
    {
        if (\Illuminate\Support\Facades\Storage::exists($this->path)) {
            return \Illuminate\Support\Facades\Storage::mimeType($this->path) ?: null;
        }
        $fallback = storage_path('app/' . $this->path);
        if (file_exists($fallback)) {
            return mime_content_type($fallback) ?: null;
        }
        return null;
    }

    /**
     * Get the file contents.
     */
    public function getFileContent(): ?string
    {
        if (\Illuminate\Support\Facades\Storage::exists($this->path)) {
            return \Illuminate\Support\Facades\Storage::get($this->path);
        }
        $fallback = storage_path('app/' . $this->path);
        if (file_exists($fallback)) {
            return file_get_contents($fallback);
        }
        return null;
    }

    public function isPreviewable(): bool
    {
        $mime = $this->getMimeType();
        if (!$mime) return false;
        $patterns = config('documents.preview_mime_patterns', ['image/', 'text/', 'application/pdf']);
        foreach ($patterns as $pattern) {
            if (str_ends_with($pattern, '/')) {
                if (str_starts_with($mime, $pattern)) return true;
            } elseif ($mime === $pattern) {
                return true;
            }
        }
        return false;
    }
}

