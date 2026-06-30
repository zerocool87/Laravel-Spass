<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    public function test_admin_sees_administration_link_in_header()
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $this->actingAs($admin)
            ->get(route('elus.dashboard'))
            ->assertStatus(200)
            ->assertSee(__('Administration'));
    }

    public function test_regular_elu_does_not_see_administration_link_in_header()
    {
        $user = User::factory()->create(['is_elu' => true, 'is_admin' => false]);

        $this->actingAs($user)
            ->get(route('elus.dashboard'))
            ->assertStatus(200)
            ->assertDontSee(__('Administration'));
    }
}
