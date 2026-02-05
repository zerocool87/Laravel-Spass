<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'category',
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
        if (! $user) {
            return false;
        }
        if ($this->created_by === $user->id) {
            return true;
        }
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Scope to get documents visible to a given user (public or assigned to them)
     */
    public function scopeVisibleToUser(Builder $query, ?int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('visible_to_all', true);

            if ($userId) {
                $q->orWhereHas('users', function ($q2) use ($userId) {
                    $q2->where('users.id', $userId);
                });
            }
        });
    }

    /**
     * Get full path to stored file on disk.
     */
    public function getFullPath(): ?string
    {
        if (\Illuminate\Support\Facades\Storage::exists($this->path)) {
            return \Illuminate\Support\Facades\Storage::path($this->path);
        }
        $fallback = storage_path('app/'.$this->path);

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
        $fallback = storage_path('app/'.$this->path);
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
        $fallback = storage_path('app/'.$this->path);
        if (file_exists($fallback)) {
            return file_get_contents($fallback);
        }

        return null;
    }

    public function isPreviewable(): bool
    {
        $mime = $this->getMimeType();
        if (! $mime) {
            return false;
        }
        $patterns = config('documents.preview_mime_patterns', ['image/', 'text/', 'application/pdf']);
        foreach ($patterns as $pattern) {
            if (str_ends_with($pattern, '/')) {
                if (str_starts_with($mime, $pattern)) {
                    return true;
                }
            } elseif ($mime === $pattern) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the color class for this document's category.
     */
    public function getCategoryColor(): string
    {
        $categoryColors = config('documents.category_colors', []);
        return $categoryColors[$this->category] ?? 'bg-[#faa21b]';
    }

    /**
     * Get the icon SVG for this document's category.
     */
    public function getCategoryIcon(): string
    {
        $categoryIcons = config('documents.category_icons', []);
        return $categoryIcons[$this->category] ?? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>';
    }
}
