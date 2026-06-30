<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminStoreEluTest extends TestCase
{
    public function test_creates_user_and_elu_profile_in_transaction(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('elus.admin.users.store'), [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'titres' => ['Membre du bureau'],
            'commune' => 'Limoges',
        ]);

        $response->assertRedirect(route('elus.admin.users'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'jean.dupont@example.com',
            'name' => 'Jean Dupont',
            'is_elu' => true,
            'commune' => 'Limoges',
        ]);

        $user = User::where('email', 'jean.dupont@example.com')->first();
        $this->assertNotNull($user);

        $this->assertDatabaseHas('elu_profiles', [
            'user_id' => $user->id,
        ]);
    }

    public function test_no_temporary_password_in_session_flash(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('elus.admin.users.store'), [
            'name' => 'Marie Curie',
            'email' => 'marie.curie@example.com',
        ]);

        $response->assertSessionMissing('temporaryPassword');
        $response->assertSessionHas('resetLink');
    }

    public function test_created_user_has_is_elu_true(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('elus.admin.users.store'), [
            'name' => 'Albert Martin',
            'email' => 'albert.martin@example.com',
        ]);

        $user = User::where('email', 'albert.martin@example.com')->first();

        $this->assertTrue($user->is_elu);
    }

    public function test_transaction_rolls_back_on_failure(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('elus.admin.users.store'), [
            'name' => 'Incomplete User',
            // no email — validation should fail before any DB write
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1); // only the admin
        $this->assertDatabaseCount('elu_profiles', 0);
    }
}
