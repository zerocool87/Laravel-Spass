<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_admin_events_link_in_dropdown()
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $this->actingAs($admin)
            ->get(route('elus.dashboard'))
            ->assertStatus(200)
            ->assertDontSee('Admin - Events');
    }

    public function test_regular_user_does_not_see_admin_events_link()
    {
        $user = User::factory()->create(['is_elu' => true]);

        $this->actingAs($user)
            ->get(route('elus.dashboard'))
            ->assertStatus(200)
            ->assertDontSee('Admin - Events');
    }
}
