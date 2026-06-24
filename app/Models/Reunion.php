<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReunionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @deprecated Use \App\Enums\ReunionStatus directly for new code.
 */
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

    public function scopeUpcoming($query)
    {
        return $query
            ->where('start_time', '>=', now())
            ->whereIn('status', [ReunionStatus::Planifiee->value, ReunionStatus::Confirmee->value]);
    }

    public function scopePast($query)
    {
        return $query
            ->where('start_time', '<', now())
            ->orderBy('start_time', 'desc');
    }

    public function getStatusLabelAttribute(): string
    {
        return ReunionStatus::tryFrom($this->status)?->label() ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return ReunionStatus::tryFrom($this->status)?->color() ?? 'gray';
    }
}
