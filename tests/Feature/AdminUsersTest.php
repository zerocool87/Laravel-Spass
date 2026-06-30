<?php

namespace Tests\Feature;

use App\Models\EluProfile;
use App\Models\User;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    public function test_admin_can_delete_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_elu' => true]);
        EluProfile::create(['user_id' => $user->id]);

        $this->assertDatabaseCount('users', 2);

        $response = $this->actingAs($admin)
            ->delete(route('elus.admin.users.destroy', $user));

        $response->assertRedirect(route('elus.admin.users'));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseMissing('elu_profiles', ['user_id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->delete(route('elus.admin.users.destroy', $admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_non_admin_cannot_delete_user(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $target = User::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('elus.admin.users.destroy', $target));

        $response->assertForbidden();
        $this->assertDatabaseCount('users', 2);
    }
}
