<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCreateEventAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_event_via_ajax()
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        $payload = [
            'title' => 'AJAX Event',
            'description' => 'Created via AJAX',
            'start_at' => now()->toIso8601String(),
            'end_at' => now()->addHour()->toIso8601String(),
            'is_all_day' => false,
            'location' => 'Salle A',
        ];

        $this->actingAs($admin)
            ->postJson(route('admin.events.store'), $payload)
            ->assertStatus(201)
            ->assertJsonStructure(['id', 'title', 'start', 'end', 'allDay', 'url']);

        $this->assertDatabaseHas('events', ['title' => 'AJAX Event']);
    }
}
