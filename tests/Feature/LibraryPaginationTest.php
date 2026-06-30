<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Tests\TestCase;

class LibraryPaginationTest extends TestCase
{
    public function test_library_uses_pagination(): void
    {
        $user = User::factory()->create();

        Document::factory(22)->public()->create([
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('library.index'));

        $response->assertStatus(200);
        $response->assertViewHas('documents');

        $documents = $response->viewData('documents');
        $this->assertInstanceOf(\Illuminate\Contracts\Pagination\Paginator::class, $documents);
        $this->assertCount(20, $documents->items());
        $this->assertTrue($documents->hasMorePages());
    }

    public function test_library_groups_documents_by_category(): void
    {
        $user = User::factory()->create();

        Document::factory()->public()->create([
            'title' => 'Doc A',
            'category' => 'Convocations',
            'created_by' => $user->id,
        ]);
        Document::factory()->public()->create([
            'title' => 'Doc B',
            'category' => 'Convocations',
            'created_by' => $user->id,
        ]);
        Document::factory()->public()->create([
            'title' => 'Doc C',
            'category' => 'Ordres du jour',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('library.index'));

        $response->assertStatus(200);
        $response->assertViewHas('documentsByCategory');

        $grouped = $response->viewData('documentsByCategory');
        $this->assertArrayHasKey('Convocations', $grouped);
        $this->assertArrayHasKey('Ordres du jour', $grouped);
        $this->assertCount(2, $grouped['Convocations']);
        $this->assertCount(1, $grouped['Ordres du jour']);
    }

    public function test_library_shows_all_categories(): void
    {
        $user = User::factory()->create();

        Document::factory()->public()->create([
            'category' => 'Rapports',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('library.index'));

        $response->assertStatus(200);
        $response->assertViewHas('allCategories');

        $categories = $response->viewData('allCategories');
        $this->assertContains('Rapports', $categories);
    }
}
