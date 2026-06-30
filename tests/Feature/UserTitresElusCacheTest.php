<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserTitresElusCacheTest extends TestCase
{
    public function test_returns_unique_sorted_titres(): void
    {
        User::factory()->elu()->create(['titres' => ['Président']]);
        User::factory()->elu()->create(['titres' => ['Membre du bureau', 'Vice-président']]);
        User::factory()->elu()->create(['titres' => ['Président']]);

        $result = User::titresElus();

        $expected = ['Membre du bureau', 'Président', 'Vice-président'];
        $this->assertSame($expected, $result);
    }

    public function test_uses_cache_on_subsequent_calls(): void
    {
        User::factory()->elu()->create(['titres' => ['Membre du bureau']]);

        $result1 = User::titresElus();
        $this->assertSame(['Membre du bureau'], $result1);

        User::factory()->elu()->create(['titres' => ['Président']]);

        $result2 = User::titresElus();
        $this->assertSame(['Membre du bureau', 'Président'], $result2);
    }

    public function test_cache_is_cleared_on_user_creation(): void
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn(['Membre du bureau']);

        $result1 = User::titresElus();

        Cache::shouldReceive('forget')
            ->once()
            ->with('titres_elus');

        User::factory()->elu()->create();
    }

    public function test_cache_is_cleared_on_user_deletion(): void
    {
        $user = User::factory()->elu()->create();

        Cache::shouldReceive('forget')
            ->once()
            ->with('titres_elus');

        $user->delete();
    }

    public function test_excludes_non_elu_users(): void
    {
        User::factory()->create(['is_elu' => false, 'titres' => ['Président']]);

        $result = User::titresElus();

        $this->assertSame([], $result);
    }
}
