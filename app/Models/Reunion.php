<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReunionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reunion extends Model
{
    /** @use HasFactory<\Database\Factories\ReunionFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'instance_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'participants',
        'status',
        'titres',
        'visible_to_all',
        'ordre_du_jour',
        'compte_rendu',
        'documents',
    ];

    public function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'participants' => 'array',
            'titres' => 'array',
            'visible_to_all' => 'boolean',
            'documents' => 'array',
        ];
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Scope for upcoming (future) reunions with active status.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query
            ->where('start_time', '>=', now())
            ->whereIn('status', [ReunionStatus::Planifiee->value, ReunionStatus::Confirmee->value]);
    }

    public function getStatusLabelAttribute(): string
    {
        return ReunionStatus::tryFrom($this->status)?->label() ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        $color = ReunionStatus::tryFrom($this->status)?->color() ?? 'gray';

        return match ($color) {
            'blue' => 'bg-blue-100 text-blue-800',
            'green' => 'bg-green-100 text-green-800',
            'gray' => 'bg-gray-100 text-gray-800',
            'red' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /** @param Builder<self> $query */
    public function scopeByTitres(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('visible_to_all', true);

            if ($user->titres) {
                foreach ($user->titres as $titre) {
                    $q->orWhereJsonContains('titres', $titre);
                }
            }
        });
    }

    /**
     * Apply common index filters (instance, status, date range, search).
     *
     * @param  array<string, mixed>  $filters
     * @param  Builder<self>  $query
     */
    public function scopeFiltered(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['instance_id'] ?? null, fn ($q, $v) => $q->where('instance_id', $v))
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['from_date'] ?? null, fn ($q, $v) => $q->where('start_time', '>=', $v))
            ->when($filters['to_date'] ?? null, fn ($q, $v) => $q->where('end_time', '<=', $v))
            ->when($filters['search'] ?? null, function ($q, $search) {
                $like = '%'.$search.'%';
                $q->where(function ($subQuery) use ($like) {
                    $subQuery->where('title', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            });
    }
}
