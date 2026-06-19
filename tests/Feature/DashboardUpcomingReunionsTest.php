<?php

namespace Tests\Feature;

use App\Models\Instance;
use App\Models\Reunion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardUpcomingReunionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_upcoming_reunions()
    {
        $user = User::factory()->create(['is_elu' => true]);
        $instance = Instance::factory()->create();

        // Create reunions with different statuses
        $planifieeReunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion planifiée',
            'start_time' => now()->addDays(1)->setTime(9, 0),
            'end_time' => now()->addDays(1)->setTime(11, 0),
            'status' => 'planifiee',
        ]);

        $confirmeeReunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion confirmée',
            'start_time' => now()->addDays(2)->setTime(9, 0),
            'end_time' => now()->addDays(2)->setTime(11, 0),
            'status' => 'confirmee',
        ]);

        $termineeReunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion terminée',
            'start_time' => now()->addDays(3)->setTime(9, 0),
            'end_time' => now()->addDays(3)->setTime(11, 0),
            'status' => 'terminee',
        ]);

        // Visit elus dashboard
        $response = $this->actingAs($user)->get('/elus');

        $response->assertStatus(200);
        $response->assertSee($planifieeReunion->title);
        $response->assertSee($confirmeeReunion->title);
        $response->assertDontSee($termineeReunion->title);
    }

    public function test_dashboard_shows_max_3_upcoming_reunions()
    {
        $user = User::factory()->create(['is_elu' => true]);
        $instance = Instance::factory()->create();

        // Create 6 upcoming reunions
        for ($i = 1; $i <= 6; $i++) {
            Reunion::factory()->create([
                'instance_id' => $instance->id,
                'title' => 'Réunion '.$i,
                'start_time' => now()->addDays($i)->setTime(9, 0),
                'end_time' => now()->addDays($i)->setTime(11, 0),
                'status' => 'planifiee',
            ]);
        }

        // Visit elus dashboard
        $response = $this->actingAs($user)->get('/elus');

        $response->assertStatus(200);

        // Should see the first 3 reunions (dashboard view takes 3)
        for ($i = 1; $i <= 3; $i++) {
            $response->assertSee('Réunion '.$i);
        }

        // Should not see reunions beyond the first 3
        for ($i = 4; $i <= 6; $i++) {
            $response->assertDontSee('Réunion '.$i);
        }
    }
}
