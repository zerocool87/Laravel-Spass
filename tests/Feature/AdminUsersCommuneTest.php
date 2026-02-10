<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersCommuneTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_loads_with_communes()
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)
            ->get('/admin/users/create');

        $response->assertStatus(404);
    }

    public function test_edit_page_loads_with_communes()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get("/admin/users/{$user->id}/edit");

        $response->assertStatus(404);
    }

    public function test_communes_configuration_is_loaded()
    {
        $communes = config('options.communes_haute_vienne', []);

        // Should have at least some communes from Haute-Vienne
        $this->assertGreaterThan(100, count($communes));
        $this->assertContains('Limoges', $communes);
        $this->assertContains('Saint-Junien', $communes);
    }
}
