<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ForumPostFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumPost extends Model
{
    /** @use HasFactory<ForumPostFactory> */
    use HasFactory;

    protected $fillable = [
        'forum_thread_id',
        'user_id',
        'body',
    ];

    public function casts(): array
    {
        return [];
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(ForumThread::class, 'forum_thread_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
