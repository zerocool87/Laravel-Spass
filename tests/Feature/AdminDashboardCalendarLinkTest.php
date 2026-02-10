<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardCalendarLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_embedded_calendar_without_create_button_on_dashboard()
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $this->actingAs($admin)
            ->get(route('elus.dashboard'))
            ->assertStatus(200)
            ->assertSee('Espace Élus')
            ->assertDontSee('<div id="dashboard-calendar"', false)
            ->assertDontSee('Create Event');
    }

    public function test_regular_user_sees_embedded_calendar_without_create_button()
    {
        $user = User::factory()->create(['is_elu' => true]);

        $this->actingAs($user)
            ->get(route('elus.dashboard'))
            ->assertStatus(200)
            ->assertSee('Espace Élus')
            ->assertDontSee('<div id="dashboard-calendar"', false)
            ->assertDontSee('Create Event');
    }
}
