<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_calendar_widget_for_authenticated_user()
    {
        $user = User::factory()->create(['is_elu' => true]);

        $response = $this->actingAs($user)->get(route('elus.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Espace Élus');
        $response->assertDontSee('<div id="dashboard-calendar"', false);
    }
}
