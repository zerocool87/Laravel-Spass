<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'territories',
        'budget',
        'start_date',
        'end_date',
        'indicators',
        'documents',
        'geodata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'territories' => 'array',
            'indicators' => 'array',
            'documents' => 'array',
            'geodata' => 'array',
            'budget' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Types de projets disponibles.
     */
    public const TYPES = [
        'infrastructure' => 'Infrastructure',
        'energie' => 'Énergie',
        'amenagement' => 'Aménagement',
        'environnement' => 'Environnement',
        'numerique' => 'Numérique',
        'autre' => 'Autre',
    ];

    /**
     * Statuts de projets disponibles.
     */
    public const STATUSES = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'suspendu' => 'Suspendu',
        'annule' => 'Annulé',
    ];

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
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
            'planifie' => 'bg-blue-100 text-blue-800',
            'en_cours' => 'bg-yellow-100 text-yellow-800',
            'termine' => 'bg-green-100 text-green-800',
            'suspendu' => 'bg-orange-100 text-orange-800',
            'annule' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the formatted budget.
     */
    public function getFormattedBudgetAttribute(): string
    {
        return number_format((float) $this->budget, 2, ',', ' ') . ' €';
    }

    /**
     * Scope for active projects.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planifie', 'en_cours']);
    }

    /**
     * Scope for projects in a specific territory.
     */
    public function scopeInTerritory($query, string $territory)
    {
        return $query->whereJsonContains('territories', $territory);
    }
}
