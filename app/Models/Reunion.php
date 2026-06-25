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
        return ReunionStatus::tryFrom($this->status)?->color() ?? 'gray';
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
}
