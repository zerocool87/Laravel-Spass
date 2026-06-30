<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Tests\TestCase;

class ProfileUpdateRequestTest extends TestCase
{
    public function test_authorize_returns_true(): void
    {
        $request = new ProfileUpdateRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_authenticated_user_can_update_profile(): void
    {
        $user = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => 'original@example.com',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_unauthenticated_user_cannot_update_profile(): void
    {
        $response = $this->patch(route('profile.update'), [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
        ]);

        $response->assertRedirect(route('login'));
    }
}
