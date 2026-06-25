<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ThematiqueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thematique extends Model
{
    /** @use HasFactory<ThematiqueFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class, 'thematique_id');
    }
}
