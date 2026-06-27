<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ForumPost;
use App\Models\User;

class ForumPostPolicy
{
    /**
     * Determine whether the user can update/detach the forum post.
     * Authors and admins are allowed.
     */
    public function update(User $user, ForumPost $post): bool
    {
        return $user->isAdmin() || $post->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the forum post.
     * Authors and admins are allowed.
     */
    public function delete(User $user, ForumPost $post): bool
    {
        return $this->update($user, $post);
    }
}
