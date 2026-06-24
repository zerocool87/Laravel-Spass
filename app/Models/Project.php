<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use Illuminate\Database\Eloquent\Builder;
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
        'commune',
        'territories',
        'budget',
        'start_date',
        'end_date',
        'indicators',
        'documents',
        'geodata',
    ];

    public function casts(): array
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
     *
     * @deprecated Use \App\Enums\ProjectType directly for new code.
     *
     * @var array<string, string>
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
     *
     * @deprecated Use \App\Enums\ProjectStatus directly for new code.
     *
     * @var array<string, string>
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
        return ProjectType::tryFrom($this->type)?->label() ?? $this->type;
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return ProjectStatus::tryFrom($this->status)?->label() ?? $this->status;
    }

    /**
     * Get the status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        $color = ProjectStatus::tryFrom($this->status)?->color() ?? 'gray';

        return match ($color) {
            'blue' => 'bg-blue-100 text-blue-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'green' => 'bg-green-100 text-green-800',
            'orange' => 'bg-orange-100 text-orange-800',
            'red' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the formatted budget.
     */
    public function getFormattedBudgetAttribute(): string
    {
        return number_format((float) $this->budget, 2, ',', ' ').' €';
    }

    /**
     * Scope for active projects.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            ProjectStatus::Planifie->value,
            ProjectStatus::EnCours->value,
        ]);
    }

    /**
     * Scope for projects in a specific territory.
     */
    public function scopeInTerritory(Builder $query, string $territory): Builder
    {
        return $query->whereJsonContains('territories', $territory);
    }

    /**
     * Scope for projects visible to a user based on commune.
     */
    public function scopeVisibleToUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if (blank($user->commune)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('commune', $user->commune);
    }
}
