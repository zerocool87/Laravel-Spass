<?php

declare(strict_types=1);

namespace App\Models;

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

    public function isAccessibleBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return self::query()->accessibleTo($user)->whereKey($this->id)->exists();
    }

    public function isPreviewable(): bool
    {
        $mime = $this->getMimeType();
        if (! $mime) {
            return false;
        }

        $patterns = config('documents.preview_mime_patterns', ['image/', 'text/', 'application/pdf']);

        foreach ($patterns as $pattern) {
            if (str_ends_with($pattern, '/') && str_starts_with($mime, $pattern)) {
                return true;
            }
            if ($mime === $pattern) {
                return true;
            }
        }

        return false;
    }

    public function getMimeType(): ?string
    {
        if (Storage::exists($this->path)) {
            return Storage::mimeType($this->path) ?: null;
        }

        $fallback = storage_path('app/'.$this->path);

        return file_exists($fallback) ? (mime_content_type($fallback) ?: null) : null;
    }

    public function getCategoryColor(): string
    {
        $colors = config('documents.category_colors', []);

        return $colors[$this->category] ?? 'bg-[#faa21b]';
    }

    public function getCategoryIconComponent(): string
    {
        $componentMap = [
            'Convocations' => 'icon.document-convocations',
            'Ordres du jour' => 'icon.document-ordres-du-jour',
            'Comptes rendus' => 'icon.document-comptes-rendus',
            'Rapports' => 'icon.document-rapports',
            'Délibérations' => 'icon.document-deliberations',
            'Guides' => 'icon.document-guides',
        ];

        return $componentMap[$this->category] ?? 'icon.document-default';
    }

    /** @return array<string, string> */
    public function toArrayForPreview(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'original_name' => $this->original_name,
            'mime_type' => $this->getMimeType(),
            'category' => $this->category,
        ];
    }
}
