<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Tests\TestCase;

class EventTest extends TestCase
{
    public function test_user_can_view_events_index()
    {
        $user = User::factory()->create();
        Event::factory()->create(['title' => 'Public Event', 'start_at' => now()->addDay(), 'created_by' => $user->id]);

        $response = $this->actingAs($user)->get(route('events.index'));
        $response->assertStatus(200);
        $response->assertSee('Public Event');
    }

    public function test_event_validation_route_is_removed_for_admins()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post('/admin/events', [
            'description' => 'No title',
            'start_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(404);
    }
}
