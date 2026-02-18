<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reunion extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instance_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'location',
        'participants',
        'status',
        'ordre_du_jour',
        'compte_rendu',
        'documents',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'participants' => 'array',
            'documents' => 'array',
        ];
    }

    /**
     * Statuts de réunions disponibles.
     */
    public const STATUSES = [
        'planifiee' => 'Planifiée',
        'confirmee' => 'Confirmée',
        'terminee' => 'Terminée',
        'annulee' => 'Annulée',
    ];

    /**
     * Get the instance that owns the reunion.
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /**
     * Get the status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'planifiee' => 'bg-blue-100 text-blue-800',
            'confirmee' => 'bg-green-100 text-green-800',
            'terminee' => 'bg-gray-100 text-gray-800',
            'annulee' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Scope for upcoming reunions.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now())
            ->whereIn('status', ['planifiee', 'confirmee'])
            ->orderBy('start_time');
    }

    /**
     * Scope for past reunions.
     */
    public function scopePast($query)
    {
        return $query->where('end_time', '<', now())->orderBy('start_time', 'desc');
    }

    /**
     * Get start time.
     */
    public function getStartTimeAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value) : null;
    }

    /**
     * Get end time.
     */
    public function getEndTimeAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value) : null;
    }

    /**
     * Set start time with timezone conversion.
     */
    public function setStartTimeAttribute($value)
    {
        $this->attributes['start_time'] = $value ? \Carbon\Carbon::parse($value)->setTimezone('UTC') : null;
    }

    /**
     * Set end time with timezone conversion.
     */
    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = $value ? \Carbon\Carbon::parse($value)->setTimezone('UTC') : null;
    }
}
