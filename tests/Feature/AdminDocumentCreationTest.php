<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDocumentCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_document_creation_page()
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $response = $this->actingAs($admin)->get(route('elus.documents.create'));

        $response->assertStatus(200);
        $response->assertSee('Téléverser un document');
    }

    public function test_admin_can_see_document_creation_in_quick_actions()
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $response = $this->actingAs($admin)->get(route('elus.admin.index'));

        $response->assertStatus(200);
        $response->assertSee('Créer un document');
        $response->assertSee('Ajouter un nouveau document à la bibliothèque');
    }

    public function test_non_admin_cannot_access_document_creation_page()
    {
        $user = User::factory()->create(['is_admin' => false, 'is_elu' => true]);

        $response = $this->actingAs($user)->get(route('elus.documents.create'));

        $response->assertStatus(403);
    }
}
