<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryGroupingTest extends TestCase
{
    use RefreshDatabase;

    public function test_library_shows_anchors_and_view_all_links()
    {
        $user = User::factory()->create();

        Document::create(['title' => 'Convocation A', 'category' => 'Convocations', 'path' => 'd1.pdf', 'created_by' => $user->id, 'visible_to_all' => true]);
        Document::create(['title' => 'Ordre A', 'category' => 'Ordres du jour', 'path' => 'd2.pdf', 'created_by' => $user->id, 'visible_to_all' => true]);

        $this->actingAs($user)
            ->get(route('library.index'))
            ->assertStatus(200)
            ->assertSee('Convocations')
            ->assertSee('?category=Convocations')
            ->assertSee('Ordres du jour')
            ->assertSee('Tous'); // French translation for "All"
    }

    public function test_library_category_pagination()
    {
        $user = User::factory()->create();

        // create 20 items -> page size is 15
        foreach (range(1, 20) as $i) {
            Document::create(['title' => "Convocation $i", 'category' => 'Convocations', 'path' => "c$i.pdf", 'created_by' => $user->id, 'visible_to_all' => true]);
        }

        $response = $this->actingAs($user)
            ->get(route('library.index', ['category' => 'Convocations']))
            ->assertStatus(200)
            ->assertSee('Convocations');

        // Check that pagination is present (page 2 link)
        $response->assertSee('page=2', false);
    }
}
