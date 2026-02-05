<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_user_shows_password_optional_message()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        
        $response = $this->actingAs($admin)
            ->get(route('admin.users.edit', $user));
        
        $response->assertStatus(200);
        // Check that the password optional message is displayed (French version since app is in French)
        $response->assertSee('Laissez vide pour conserver le mot de passe actuel');
    }

    public function test_edit_user_without_password_keeps_current_password()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['password' => bcrypt('old-password')]);
        
        $response = $this->actingAs($admin)
            ->patch(route('admin.users.update', $user), [
                'name' => 'Updated Name',
                'email' => $user->email,
                'fonction' => 'Updated Function',
                'commune' => 'Limoges',
                'is_admin' => false,
                // No password provided
            ]);
        
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User updated.');
        
        // Verify user was updated but password remained the same
        $updatedUser = User::find($user->id);
        $this->assertEquals('Updated Name', $updatedUser->name);
        $this->assertEquals('Updated Function', $updatedUser->fonction);
        $this->assertEquals('Limoges', $updatedUser->commune);
        $this->assertTrue(password_verify('old-password', $updatedUser->password));
    }

    public function test_edit_user_with_new_password_updates_password()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['password' => bcrypt('old-password')]);
        
        $response = $this->actingAs($admin)
            ->patch(route('admin.users.update', $user), [
                'name' => $user->name,
                'email' => $user->email,
                'fonction' => $user->fonction,
                'commune' => $user->commune,
                'is_admin' => $user->is_admin,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);
        
        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success', 'User updated.');
        
        // Verify password was updated
        $updatedUser = User::find($user->id);
        $this->assertTrue(password_verify('new-password', $updatedUser->password));
        $this->assertFalse(password_verify('old-password', $updatedUser->password));
    }
}
