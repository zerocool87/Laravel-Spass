<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'is_admin',
        'is_elu',
        'onboarding_completed',
        'titres',
        'commune',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_elu' => 'boolean',
            'onboarding_completed' => 'boolean',
            'titres' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isElu(): bool
    {
        return (bool) $this->is_elu;
    }

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(Document::class, 'document_user');
    }

    public function forumPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    public function eluProfile(): HasOne
    {
        return $this->hasOne(EluProfile::class);
    }

    public function forumThreads(): HasMany
    {
        return $this->hasMany(ForumThread::class, 'user_id');
    }

    public function actualites(): HasMany
    {
        return $this->hasMany(Actualite::class, 'created_by');
    }

    /** @return array<int, string> */
    public static function titresElus(): array
    {
        return self::where('is_elu', true)
            ->whereNotNull('titres')
            ->get()
            ->pluck('titres')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }
}
