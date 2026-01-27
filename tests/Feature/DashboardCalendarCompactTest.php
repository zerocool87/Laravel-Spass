<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardCalendarCompactTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_has_compact_toggle_buttons()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('Compact');
        $response->assertDontSee('Full');
    }
}
