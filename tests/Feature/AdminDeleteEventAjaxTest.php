<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDeleteEventAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_event_via_ajax()
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        $event = Event::create(['title' => 'To be deleted', 'start_at' => now(), 'created_by' => $admin->id]);

        $this->actingAs($admin)
            ->deleteJson(route('admin.events.destroy', $event))
            ->assertStatus(204);

        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }

    public function test_non_admin_cannot_delete_event_via_ajax()
    {
        $user = User::factory()->create();
        $event = Event::create(['title' => 'Cannot delete', 'start_at' => now(), 'created_by' => $user->id]);

        $this->actingAs($user)
            ->deleteJson(route('admin.events.destroy', $event))
            ->assertStatus(403);
    }
}
