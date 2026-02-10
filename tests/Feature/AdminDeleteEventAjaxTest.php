<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDeleteEventAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_delete_event_via_removed_admin_route()
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        $event = Event::create(['title' => 'To be deleted', 'start_at' => now(), 'created_by' => $admin->id]);

        $this->actingAs($admin)
            ->deleteJson("/admin/events/{$event->id}")
            ->assertStatus(404);

        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }

    public function test_non_admin_cannot_delete_event_via_removed_admin_route()
    {
        $user = User::factory()->create();
        $event = Event::create(['title' => 'Cannot delete', 'start_at' => now(), 'created_by' => $user->id]);

        $this->actingAs($user)
            ->deleteJson("/admin/events/{$event->id}")
            ->assertStatus(404);

        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }
}
