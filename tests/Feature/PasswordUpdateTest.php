<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_user_route_is_removed_for_admins()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get("/admin/users/{$user->id}/edit");

        $response->assertStatus(404);
    }

    public function test_update_user_route_is_removed_for_admins_without_password()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['password' => bcrypt('old-password')]);
        $originalName = $user->name;
        $originalEmail = $user->email;

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}", [
                'name' => 'Updated Name',
                'email' => $user->email,
                'fonction' => 'Updated Function',
                'commune' => 'Limoges',
                'is_admin' => false,
                // No password provided
            ]);

        $response->assertStatus(404);

        $updatedUser = User::find($user->id);
        $this->assertEquals($originalName, $updatedUser->name);
        $this->assertEquals($originalEmail, $updatedUser->email);
        $this->assertTrue(password_verify('old-password', $updatedUser->password));
    }

    public function test_update_user_route_is_removed_for_admins_with_password()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['password' => bcrypt('old-password')]);

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}", [
                'name' => $user->name,
                'email' => $user->email,
                'fonction' => $user->fonction,
                'commune' => $user->commune,
                'is_admin' => $user->is_admin,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertStatus(404);

        $updatedUser = User::find($user->id);
        $this->assertFalse(password_verify('new-password', $updatedUser->password));
        $this->assertTrue(password_verify('old-password', $updatedUser->password));
    }
}
