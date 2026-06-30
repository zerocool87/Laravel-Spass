<?php

namespace Tests\Feature;

use App\Models\Instance;
use App\Models\Reunion;
use App\Models\User;
use Tests\TestCase;

class DashboardUpcomingReunionsTest extends TestCase
{
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
            'visible_to_all' => true,
        ]);

        $confirmeeReunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion confirmée',
            'start_time' => now()->addDays(2)->setTime(9, 0),
            'end_time' => now()->addDays(2)->setTime(11, 0),
            'status' => 'confirmee',
            'visible_to_all' => true,
        ]);

        $termineeReunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion terminée',
            'start_time' => now()->addDays(3)->setTime(9, 0),
            'end_time' => now()->addDays(3)->setTime(11, 0),
            'status' => 'terminee',
            'visible_to_all' => true,
        ]);

        // Visit elus dashboard
        $response = $this->actingAs($user)->get('/elus');

        $response->assertStatus(200);
        $response->assertSee($planifieeReunion->title);
        $response->assertSee($confirmeeReunion->title);
        $response->assertDontSee($termineeReunion->title);
    }

    public function test_dashboard_shows_max_4_upcoming_reunions()
    {
        $user = User::factory()->create(['is_elu' => true]);
        $instance = Instance::factory()->create();

        // Create 6 upcoming reunions (controller takes 4)
        for ($i = 1; $i <= 6; $i++) {
            Reunion::factory()->create([
                'instance_id' => $instance->id,
                'title' => 'Réunion '.$i,
                'start_time' => now()->addDays($i)->setTime(9, 0),
                'end_time' => now()->addDays($i)->setTime(11, 0),
                'status' => 'planifiee',
                'visible_to_all' => true,
            ]);
        }

        // Visit elus dashboard
        $response = $this->actingAs($user)->get('/elus');

        $response->assertStatus(200);

        // Should see the first 4 reunions
        for ($i = 1; $i <= 4; $i++) {
            $response->assertSee('Réunion '.$i);
        }

        // Should not see reunions beyond the first 4
        for ($i = 5; $i <= 6; $i++) {
            $response->assertDontSee('Réunion '.$i);
        }
    }

    public function test_dashboard_filters_reunions_by_user_titres()
    {
        $user = User::factory()->create([
            'is_elu' => true,
            'titres' => ['Maire'],
        ]);
        $instance = Instance::factory()->create();

        $maireReunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion du Maire',
            'start_time' => now()->addDays(1)->setTime(9, 0),
            'end_time' => now()->addDays(1)->setTime(11, 0),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Maire'],
        ]);

        $conseillerReunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion des conseillers',
            'start_time' => now()->addDays(1)->setTime(14, 0),
            'end_time' => now()->addDays(1)->setTime(16, 0),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Conseiller municipal'],
        ]);

        $nonAttribuee = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion sans attribution',
            'start_time' => now()->addDays(2)->setTime(9, 0),
            'end_time' => now()->addDays(2)->setTime(11, 0),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => null,
        ]);

        $response = $this->actingAs($user)->get('/elus');

        $response->assertStatus(200);
        $response->assertSee('Réunion du Maire');
        $response->assertDontSee('Réunion des conseillers');
        $response->assertDontSee('Réunion sans attribution');
    }
}
