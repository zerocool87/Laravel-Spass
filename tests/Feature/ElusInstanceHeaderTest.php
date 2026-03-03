<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Instance;

class ElusInstanceHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_links_absent_for_admin()
    {
        $instance = Instance::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get(route('elus.instances.show', $instance));
        $response->assertStatus(200);
        $response->assertDontSee('Planifier une réunion');
        $response->assertDontSee('Modifier');
    }

    public function test_header_links_absent_for_regular_user()
    {
        $instance = Instance::factory()->create();
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('elus.instances.show', $instance));
        $response->assertStatus(200);
        $response->assertDontSee('Planifier une réunion');
        $response->assertDontSee('Modifier');
    }
}
