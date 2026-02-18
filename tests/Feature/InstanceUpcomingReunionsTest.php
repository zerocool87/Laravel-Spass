<?php

namespace Tests\Feature;

use App\Models\Instance;
use App\Models\Reunion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstanceUpcomingReunionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_upcoming_reunions_filters_by_status()
    {
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

        $annuleeReunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion annulée',
            'start_time' => now()->addDays(4)->setTime(9, 0),
            'end_time' => now()->addDays(4)->setTime(11, 0),
            'status' => 'annulee',
        ]);

        // Get upcoming reunions
        $upcomingReunions = $instance->upcomingReunions()->get();

        // Should only include 'planifiee' and 'confirmee' statuses
        $this->assertCount(2, $upcomingReunions);
        $this->assertContains($planifieeReunion->id, $upcomingReunions->pluck('id'));
        $this->assertContains($confirmeeReunion->id, $upcomingReunions->pluck('id'));
        $this->assertNotContains($termineeReunion->id, $upcomingReunions->pluck('id'));
        $this->assertNotContains($annuleeReunion->id, $upcomingReunions->pluck('id'));
    }

    public function test_upcoming_reunions_filters_by_date()
    {
        $instance = Instance::factory()->create();

        // Create reunions with different dates
        $futureReunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion future',
            'start_time' => now()->addDays(1)->setTime(9, 0),
            'end_time' => now()->addDays(1)->setTime(11, 0),
            'status' => 'planifiee',
        ]);

        $pastReunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion passée',
            'start_time' => now()->subDays(1)->setTime(9, 0),
            'end_time' => now()->subDays(1)->setTime(11, 0),
            'status' => 'planifiee',
        ]);

        // Get upcoming reunions
        $upcomingReunions = $instance->upcomingReunions()->get();

        // Should only include future reunions
        $this->assertCount(1, $upcomingReunions);
        $this->assertContains($futureReunion->id, $upcomingReunions->pluck('id'));
        $this->assertNotContains($pastReunion->id, $upcomingReunions->pluck('id'));
    }
}
