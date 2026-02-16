<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDocumentIndexFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_documents_by_search_and_visibility(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        Document::create([
            'title' => 'Budget 2026',
            'description' => 'Document budgetaire',
            'path' => 'documents/budget-2026.pdf',
            'original_name' => 'budget-2026.pdf',
            'created_by' => $admin->id,
            'visible_to_all' => true,
            'category' => 'Rapports',
        ]);

        Document::create([
            'title' => 'Note interne',
            'description' => 'Document interne',
            'path' => 'documents/note-interne.pdf',
            'original_name' => 'note-interne.pdf',
            'created_by' => $admin->id,
            'visible_to_all' => false,
            'category' => 'Guides',
        ]);

        Document::create([
            'title' => 'Rapport 2024',
            'description' => 'Rapport annuel',
            'path' => 'documents/rapport-2024.pdf',
            'original_name' => 'rapport-2024.pdf',
            'created_by' => $admin->id,
            'visible_to_all' => true,
            'category' => 'Rapports',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.documents.index', [
            'q' => 'Budget',
            'visibility' => 'public',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Budget 2026');
        $response->assertDontSee('Note interne');
        $response->assertDontSee('Rapport 2024');
    }

    public function test_admin_can_filter_documents_by_category(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        Document::create([
            'title' => 'Rapport fiscal',
            'description' => 'Rapport fiscal annuel',
            'path' => 'documents/rapport-fiscal.pdf',
            'original_name' => 'rapport-fiscal.pdf',
            'created_by' => $admin->id,
            'visible_to_all' => true,
            'category' => 'Rapports',
        ]);

        Document::create([
            'title' => 'Guide utilisateur',
            'description' => 'Guide interne',
            'path' => 'documents/guide-utilisateur.pdf',
            'original_name' => 'guide-utilisateur.pdf',
            'created_by' => $admin->id,
            'visible_to_all' => true,
            'category' => 'Guides',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.documents.index', [
            'category' => 'Rapports',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Rapport fiscal');
        $response->assertDontSee('Guide utilisateur');
    }
}
