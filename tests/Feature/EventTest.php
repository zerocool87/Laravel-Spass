<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_event()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.events.store'), [
            'title' => 'Team Meeting',
            'description' => 'Discuss project status',
            'start_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_at' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
            'location' => 'Room 1',
            'is_all_day' => false,
        ]);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', ['title' => 'Team Meeting']);
    }

    public function test_user_can_view_events_index()
    {
        $user = User::factory()->create();
        Event::factory()->create(['title' => 'Public Event', 'start_at' => now()->addDay(), 'created_by' => $user->id]);

        $response = $this->actingAs($user)->get(route('events.index'));
        $response->assertStatus(200);
        $response->assertSee('Public Event');
    }

    public function test_event_validation_fails_on_missing_title()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.events.store'), [
            'description' => 'No title',
            'start_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors('title');
    }
}
