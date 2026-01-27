<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryPublicPrivateBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_library_shows_public_badge_for_visible_to_all_documents()
    {
        $user = User::factory()->create();

        Document::create([
            'title' => 'Public Doc',
            'category' => 'Convocations',
            'path' => 'public.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);

        $this->actingAs($user)
            ->get(route('library.index'))
            ->assertStatus(200)
            ->assertSee('Public Doc')
            ->assertSee('Public', false); // Escaped HTML check
    }

    public function test_library_shows_private_badge_for_restricted_documents()
    {
        $user = User::factory()->create();

        $doc = Document::create([
            'title' => 'Private Doc',
            'category' => 'Ordres du jour',
            'path' => 'private.pdf',
            'created_by' => $user->id,
            'visible_to_all' => false,
        ]);

        // Assign document to user
        $doc->users()->attach($user->id);

        $this->actingAs($user)
            ->get(route('library.index'))
            ->assertStatus(200)
            ->assertSee('Private Doc')
            ->assertSee('Privé', false); // French translation for "Private"
    }

    public function test_library_shows_all_categories_including_comptes_rendus()
    {
        $user = User::factory()->create();

        // Create documents in all categories
        $convocation = Document::create([
            'title' => 'Convocation Test',
            'category' => 'Convocations',
            'path' => 'conv.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);

        $ordre = Document::create([
            'title' => 'Ordre Test',
            'category' => 'Ordres du jour',
            'path' => 'ordre.pdf',
            'created_by' => $user->id,
            'visible_to_all' => true,
        ]);

        $compte = Document::create([
            'title' => 'Compte Rendu Test',
            'category' => 'Comptes rendus',
            'path' => 'cr.pdf',
            'created_by' => $user->id,
            'visible_to_all' => false,
        ]);

        // Assign private document to user
        $compte->users()->attach($user->id);

        $response = $this->actingAs($user)
            ->get(route('library.index'))
            ->assertStatus(200)
            ->assertSee('Convocation Test')
            ->assertSee('Ordre Test')
            ->assertSee('Compte Rendu Test')
            ->assertSee('Convocations')
            ->assertSee('Ordres du jour')
            ->assertSee('Comptes rendus');
    }
}
