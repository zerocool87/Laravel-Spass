<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_create_button()
    {
        // Create an admin user
        $admin = User::factory()->create(["email" => "admin2@example.com", "is_admin" => true]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        // The UI is localized to French by default; assert the translated label is present
        $response->assertSee(__('Create User'));
    }
}
