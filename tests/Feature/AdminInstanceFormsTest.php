<?php

namespace Tests\Feature;

use App\Models\Instance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInstanceFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_instance_create_form_with_expected_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $response = $this->actingAs($admin)->get(route('admin.instances.create'));

        $response->assertStatus(200);
        $response->assertSee('Créer une instance');
        $response->assertSee('Ajouter une nouvelle instance');
        $response->assertSee('name="name"', false);
        $response->assertSee('name="type"', false);
        $response->assertSee('name="territory"', false);
        $response->assertSee('name="description"', false);
        $response->assertSee('Informations principales');
        $response->assertSee('Membres');
        $response->assertSee('Créer l\'instance');
    }

    public function test_admin_can_access_instance_edit_form_with_instance_name(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);
        $instance = Instance::factory()->create(['name' => 'Conseil Municipal Test']);

        $response = $this->actingAs($admin)->get(route('admin.instances.edit', $instance));

        $response->assertStatus(200);
        $response->assertSee('Modifier l');
        $response->assertSee('Conseil Municipal Test');
        $response->assertSee('Informations principales');
        $response->assertSee('Membres');
        $response->assertSee('name="name"', false);
        $response->assertSee('name="type"', false);
        $response->assertSee('Enregistrer les modifications');
        $response->assertSee('Supprimer l\'instance');
    }
}
