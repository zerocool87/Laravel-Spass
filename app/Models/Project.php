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

    public function getTypeLabelAttribute(): string
    {
        return ProjectType::tryFrom($this->type)?->label() ?? $this->type;
    }

    public function getStatusLabelAttribute(): string
    {
        return ProjectStatus::tryFrom($this->status)?->label() ?? $this->status;
    }

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

    public function getFormattedBudgetAttribute(): string
    {
        return number_format((float) $this->budget, 2, ',', ' ').' €';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            ProjectStatus::Planifie->value,
            ProjectStatus::EnCours->value,
        ]);
    }

    public function scopeVisibleToUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if (blank($user->commune)) {
            return $query->whereKey(0);
        }

        return $query->where('commune', $user->commune);
    }
}
