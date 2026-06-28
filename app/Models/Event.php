<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'location',
        'is_all_day',
        'type',
        'created_by',
    ];

    public function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'is_all_day' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @param Builder<self> $query */
    public function scopeInRange(Builder $query, Carbon|string $start, Carbon|string $end): Builder
    {
        return $query->where(function (Builder $q) use ($start, $end) {
            $q->whereBetween('start_at', [$start, $end])
                ->orWhereBetween('end_at', [$start, $end])
                ->orWhere(function (Builder $q2) use ($start, $end) {
                    $q2->where('start_at', '<=', $start)
                        ->where(function (Builder $q3) use ($end) {
                            $q3->whereNull('end_at')->orWhere('end_at', '>=', $end);
                        });
                });
        });
    }
}
