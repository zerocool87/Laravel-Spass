<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'date',
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
            'date' => 'datetime',
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
        return $query->where('date', '>=', now())
                     ->whereIn('status', ['planifiee', 'confirmee'])
                     ->orderBy('date');
    }

    /**
     * Scope for past reunions.
     */
    public function scopePast($query)
    {
        return $query->where('date', '<', now())->orderBy('date', 'desc');
    }
}
