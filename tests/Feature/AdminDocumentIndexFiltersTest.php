<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Tests\TestCase;

class AdminDocumentIndexFiltersTest extends TestCase
{
    public function test_admin_can_filter_documents_by_search_and_visibility(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        Document::factory()->create([
            'title' => 'Budget 2026',
            'description' => 'Document budgetaire',
            'created_by' => $admin->id,
            'visible_to_all' => true,
            'category' => 'Rapports',
        ]);

        Document::factory()->create([
            'title' => 'Note interne',
            'description' => 'Document interne',
            'created_by' => $admin->id,
            'visible_to_all' => false,
            'category' => 'Guides',
        ]);

        Document::factory()->create([
            'title' => 'Rapport 2024',
            'description' => 'Rapport annuel',
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

        Document::factory()->create([
            'title' => 'Rapport fiscal',
            'description' => 'Rapport fiscal annuel',
            'created_by' => $admin->id,
            'visible_to_all' => true,
            'category' => 'Rapports',
        ]);

        Document::factory()->create([
            'title' => 'Guide utilisateur',
            'description' => 'Guide interne',
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
