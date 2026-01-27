<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardCalendarLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_embedded_calendar_and_create_button_on_dashboard()
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('<div id="dashboard-calendar"', false)
            ->assertSee('Create Event');
    }

    public function test_regular_user_sees_embedded_calendar_without_create_button()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('<div id="dashboard-calendar"', false)
            ->assertDontSee('Create Event');
    }
}
