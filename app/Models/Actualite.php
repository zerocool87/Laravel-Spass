<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Actualite extends Model
{
    /** @use HasFactory<\Database\Factories\ActualiteFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'created_by',
        'is_published',
        'published_at',
    ];

    public function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query): void
    {
        $query->where('is_published', true);
    }
}
