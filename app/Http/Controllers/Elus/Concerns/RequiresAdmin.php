<?php

namespace App\Http\Controllers\Elus\Concerns;

trait RequiresAdmin
{
    /**
     * Ensure the current user is an admin.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function requireAdmin(): void
    {
        abort_unless(
            request()->user()?->isAdmin(),
            403,
            __('Vous n\'avez pas l\'autorisation d\'effectuer cette action.')
        );
    }
}
