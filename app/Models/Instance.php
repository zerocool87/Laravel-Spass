<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ReunionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function reunions(): HasMany
    {
        return $this->hasMany(Reunion::class);
    }

    public function upcomingReunions(): HasMany
    {
        return $this->reunions()
            ->where('start_time', '>=', now())
            ->whereIn('status', [
                ReunionStatus::Planifiee->value,
                ReunionStatus::Confirmee->value,
            ])
            ->orderBy('start_time');
    }
}
