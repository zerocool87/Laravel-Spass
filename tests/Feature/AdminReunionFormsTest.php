<?php

namespace Tests\Feature;

use App\Models\Instance;
use App\Models\Reunion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReunionFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_reunion_create_form_with_expected_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);
        $instance = Instance::factory()->create(['name' => 'Conseil Municipal']);

        $response = $this->actingAs($admin)->get(route('admin.reunions.create', [
            'instance_id' => $instance->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Créer une réunion');
        $response->assertSee('Planifier une nouvelle réunion');
        $response->assertSee('name="instance_id"', false);
        $response->assertSee('name="title"', false);
        $response->assertSee('name="ordre_du_jour"', false);
        $response->assertSee('name="compte_rendu"', false);
    }

    public function test_admin_can_access_reunion_edit_form_with_reunion_title(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);
        $instance = Instance::factory()->create();
        $reunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion de suivi budgétaire',
            'participants' => ['Alice', 'Bob'],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reunions.edit', $reunion));

        $response->assertStatus(200);
        $response->assertSee('Modifier la réunion');
        $response->assertSee('Réunion de suivi budgétaire');
        $response->assertSee('name="participants_text"', false);
        $response->assertSee('Enregistrer les modifications');
    }
}
