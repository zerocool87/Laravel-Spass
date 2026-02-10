<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_does_not_see_create_button_on_calendar()
    {
        $admin = User::factory()->create();
        // Give admin permission - assume isAdmin is based on 'is_admin' boolean
        $admin->is_admin = true;
        $admin->save();

        $this->actingAs($admin)
            ->get(route('events.calendar'))
            ->assertStatus(200)
            ->assertSee('data-can-edit="0"', false)
            ->assertDontSee('Créer un événement')
            ->assertDontSee('Create Event');
    }

    public function test_regular_user_does_not_see_create_button()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('events.calendar'))
            ->assertStatus(200)
            ->assertSee('data-can-edit="0"', false)
            ->assertDontSee('Créer un événement')
            ->assertDontSee('Create Event');
    }
}
