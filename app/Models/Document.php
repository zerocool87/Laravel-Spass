<?php

declare(strict_types=1);

namespace App\Models;

use App\Http\Requests\DocumentRequest;
use Illuminate\Database\Eloquent\Builder;
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
        'titres',
        'category',
    ];

    public function casts(): array
    {
        return [
            'visible_to_all' => 'boolean',
            'titres' => 'array',
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
     * Create a document from an uploaded file, storing it and syncing assigned users
     * when the document is not visible to everyone.
     *
     * @param  array{titres?: mixed, category?: mixed}  $extra  Admin-only fields (titres/category).
     */
    public static function createFromRequest(DocumentRequest $request, User $creator, array $extra = []): self
    {
        $data = $request->validated();
        $file = $request->file('file');
        $visibleToAll = (bool) $data['visible_to_all'];

        $document = self::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'path' => $file->store('documents'),
            'original_name' => $file->getClientOriginalName(),
            'created_by' => $creator->id,
            'visible_to_all' => $visibleToAll,
            'titres' => $visibleToAll ? null : ($extra['titres'] ?? null),
            'category' => $extra['category'] ?? null,
        ]);

        if (! $visibleToAll && ! empty($data['assigned_users'])) {
            $document->users()->sync($data['assigned_users']);
        }

        return $document;
    }

    /**
     * Scope documents accessible to a user (admin sees all; others see visible_to_all,
     * own, shared via pivot, or matched by titres).
     *
     * @param  Builder<self>  $query
     */
    public function scopeAccessibleTo(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('visible_to_all', true)
                ->orWhere('created_by', $user->id)
                ->orWhereHas('users', fn ($query) => $query->where('user_id', $user->id));

            if ($user->titres) {
                foreach ($user->titres as $titre) {
                    $q->orWhereJsonContains('titres', $titre);
                }
            }
        });
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
        if ($user->titres && $this->titres && array_intersect($user->titres, $this->titres)) {
            return true;
        }

        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Get full path to stored file on disk.
     */
    public function getFullPath(): ?string
    {
        return $this->resolveStoragePath();
    }

    /**
     * Get MIME type of stored file.
     */
    public function getMimeType(): ?string
    {
        if (Storage::exists($this->path)) {
            return Storage::mimeType($this->path) ?: null;
        }

        $path = $this->resolveStoragePath();

        return $path !== null ? (mime_content_type($path) ?: null) : null;
    }

    /**
     * Resolve the filesystem path, checking Storage first then the local fallback.
     */
    private function resolveStoragePath(): ?string
    {
        if (Storage::exists($this->path)) {
            return Storage::path($this->path);
        }

        $fallback = storage_path('app/'.$this->path);

        return file_exists($fallback) ? $fallback : null;
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
