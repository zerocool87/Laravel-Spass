<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_create_event_via_removed_admin_route()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post('/admin/events', [
            'title' => 'Team Meeting',
            'description' => 'Discuss project status',
            'start_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_at' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
            'location' => 'Room 1',
            'is_all_day' => false,
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('events', ['title' => 'Team Meeting']);
    }

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
