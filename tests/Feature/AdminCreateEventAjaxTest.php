<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCreateEventAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_create_event_via_removed_admin_route()
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
            ->postJson('/admin/events', $payload)
            ->assertStatus(404);

        $this->assertDatabaseMissing('events', ['title' => 'AJAX Event']);
    }
}
