<?php

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
        return Document::query()
            ->where(function ($q) use ($user) {
                $q->where('visible_to_all', true)
                    ->orWhere('created_by', $user->id)
                    ->orWhereHas('users', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
            });
    }
}
