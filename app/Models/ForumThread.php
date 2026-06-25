<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ForumThreadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ForumThread extends Model
{
    /** @use HasFactory<ForumThreadFactory> */
    use HasFactory;

    protected $fillable = [
        'instance_id',
        'title',
        'created_by',
        'is_pinned',
    ];

    public function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
        ];
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(Instance::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    public function latestPost(): HasOne
    {
        return $this->hasOne(ForumPost::class)->latestOfMany();
    }

    public function readBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'forum_thread_user')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }
}
