<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

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

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visible_to_all' => 'boolean',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'document_user');
    }

    public function creator(): BelongsTo
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
        if (Storage::exists($this->path)) {
            return Storage::path($this->path);
        }
        $fallback = storage_path('app/'.$this->path);

        return file_exists($fallback) ? $fallback : null;
    }

    /**
     * Get MIME type of stored file.
     */
    public function getMimeType(): ?string
    {
        if (Storage::exists($this->path)) {
            return Storage::mimeType($this->path) ?: null;
        }
        $fallback = storage_path('app/'.$this->path);
        if (file_exists($fallback)) {
            return mime_content_type($fallback) ?: null;
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
