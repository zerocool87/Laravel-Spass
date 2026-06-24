<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus\Concerns;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait FiltersDocuments
{
    /**
     * Get documents accessible to the specified user.
     *
     * @return Builder<Document>
     */
    protected function getUserAccessibleDocuments(User $user): Builder
    {
        if ($user->isAdmin()) {
            return Document::query();
        }

        return Document::query()
            ->where(function ($q) use ($user) {
                $q->where('visible_to_all', true)
                    ->orWhere('created_by', $user->id)
                    ->orWhereHas('users', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
                if ($user->fonction) {
                    $q->orWhereJsonContains('titres', $user->fonction);
                }
            });
    }
}
