<?php

namespace Tests\Feature;

use App\Models\Instance;
use App\Models\Reunion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElusReunionsCalendarJsonTest extends TestCase
{
    use RefreshDatabase;

    public function test_elus_reunions_calendar_json_contains_minimal_modal_fields(): void
    {
        $elu = User::factory()->create([
            'is_elu' => true,
            'is_admin' => false,
        ]);

        $instance = Instance::factory()->create([
            'name' => 'Commission Finances',
        ]);

        $reunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Budget primitif 2026',
            'start_time' => now()->addDay()->setTime(9, 30),
            'end_time' => now()->addDay()->setTime(11, 0),
            'status' => 'planifiee',
        ]);

        $response = $this->actingAs($elu)->getJson(route('elus.reunions.json'));

        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $reunion->id,
            'title' => 'Budget primitif 2026',
            'url' => route('elus.reunions.show', $reunion),
        ]);
        $response->assertJsonPath('0.extendedProps.instance', 'Commission Finances');

        $payload = $response->json();
        $this->assertIsArray($payload);
        $this->assertNotEmpty($payload);
        $this->assertArrayHasKey('start', $payload[0]);
        $this->assertArrayHasKey('end', $payload[0]);
    }
}
