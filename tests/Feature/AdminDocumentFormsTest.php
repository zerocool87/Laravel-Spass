<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDocumentFormsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_document_create_form_with_expected_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $response = $this->actingAs($admin)->get(route('admin.documents.create'));

        $response->assertStatus(200);
        $response->assertSee('Nouveau document');
        $response->assertSee('Ajouter un document à la bibliothèque');
        $response->assertSee('name="title"', false);
        $response->assertSee('name="description"', false);
        $response->assertSee('name="category"', false);
        $response->assertSee('name="file"', false);
        $response->assertSee('name="visible_to_all"', false);
        $response->assertSee('Informations du document');
        $response->assertSee('Fichier et accès');
        $response->assertSee('Créer le document');
    }

    public function test_admin_can_access_document_edit_form_with_document_title(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $document = Document::create([
            'title' => 'Budget prévisionnel 2026',
            'description' => 'Document de budget',
            'path' => 'documents/test.pdf',
            'original_name' => 'test.pdf',
            'created_by' => $admin->id,
            'visible_to_all' => true,
            'category' => 'Rapports',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.documents.edit', $document));

        $response->assertStatus(200);
        $response->assertSee('Modifier le document');
        $response->assertSee('Budget prévisionnel 2026');
        $response->assertSee('Informations du document');
        $response->assertSee('Fichier et accès');
        $response->assertSee('Enregistrer les modifications');
        $response->assertSee('Supprimer le document');
    }
}
