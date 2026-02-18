<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'members',
        'territory',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'members' => 'array',
        ];
    }

    /**
     * Types d'instances disponibles.
     */
    public const TYPES = [
        'comite' => 'Comité',
        'bureau' => 'Bureau',
        'commission' => 'Commission',
        'conseil' => 'Conseil',
    ];

    /**
     * Get the reunions for the instance.
     */
    public function reunions(): HasMany
    {
        return $this->hasMany(Reunion::class);
    }

    /**
     * Get the upcoming reunions.
     */
    public function upcomingReunions(): HasMany
    {
        return $this->reunions()
            ->where('start_time', '>=', now())
            ->whereIn('status', ['planifiee', 'confirmee'])
            ->orderBy('start_time');
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
