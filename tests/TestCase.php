<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function admin(array $overrides = []): User
    {
        return User::factory()->create(['is_admin' => true, 'is_elu' => true, ...$overrides]);
    }

    protected function elu(array $overrides = []): User
    {
        return User::factory()->create(['is_elu' => true, 'is_admin' => false, ...$overrides]);
    }

    protected function actingAsAdmin(array $overrides = []): User
    {
        $user = $this->admin($overrides);

        $this->actingAs($user);

        return $user;
    }

    protected function actingAsElu(array $overrides = []): User
    {
        $user = $this->elu($overrides);

        $this->actingAs($user);

        return $user;
    }
}
