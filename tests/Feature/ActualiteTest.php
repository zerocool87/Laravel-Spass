<?php

namespace Tests\Feature;

use App\Models\Actualite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActualiteTest extends TestCase
{
    use RefreshDatabase;

    // ── Admin tests ──────────────────────────────────────────────────

    public function test_admin_can_access_actualites_index(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $this->actingAs($admin)
            ->get(route('admin.actualites.index'))
            ->assertStatus(200)
            ->assertSee('Actualités');
    }

    public function test_admin_sees_actualites_in_list(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);
        $actualite = Actualite::factory()->create(['title' => 'Info importante']);

        $this->actingAs($admin)
            ->get(route('admin.actualites.index'))
            ->assertSee('Info importante');
    }

    public function test_admin_can_access_create_form(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $this->actingAs($admin)
            ->get(route('admin.actualites.create'))
            ->assertStatus(200);
    }

    public function test_admin_can_create_published_actualite(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $this->actingAs($admin)
            ->post(route('admin.actualites.store'), [
                'title' => 'Résultats du comité',
                'content' => 'Le comité syndical a voté en faveur du projet.',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.actualites.index'));

        $this->assertDatabaseHas('actualites', [
            'title' => 'Résultats du comité',
            'is_published' => true,
            'created_by' => $admin->id,
        ]);
    }

    public function test_admin_can_create_draft_actualite(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $this->actingAs($admin)
            ->post(route('admin.actualites.store'), [
                'title' => 'Brouillon',
                'content' => 'Contenu non publié.',
                'is_published' => '0',
            ])
            ->assertRedirect(route('admin.actualites.index'));

        $this->assertDatabaseHas('actualites', [
            'title' => 'Brouillon',
            'is_published' => false,
        ]);
    }

    public function test_admin_can_update_actualite(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);
        $actualite = Actualite::factory()->create(['title' => 'Titre original']);

        $this->actingAs($admin)
            ->patch(route('admin.actualites.update', $actualite), [
                'title' => 'Titre modifié',
                'content' => $actualite->content,
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.actualites.index'));

        $this->assertDatabaseHas('actualites', ['title' => 'Titre modifié']);
    }

    public function test_admin_can_delete_actualite(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);
        $actualite = Actualite::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.actualites.destroy', $actualite))
            ->assertRedirect(route('admin.actualites.index'));

        $this->assertDatabaseMissing('actualites', ['id' => $actualite->id]);
    }

    public function test_non_admin_cannot_access_admin_actualites(): void
    {
        $elu = User::factory()->create(['is_admin' => false, 'is_elu' => true]);

        $this->actingAs($elu)
            ->get(route('admin.actualites.index'))
            ->assertStatus(403);
    }

    public function test_store_requires_title_and_content(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'is_elu' => true]);

        $this->actingAs($admin)
            ->post(route('admin.actualites.store'), [])
            ->assertSessionHasErrors(['title', 'content']);
    }

    // ── Élus tests ────────────────────────────────────────────────────

    public function test_elu_can_access_actualites_index(): void
    {
        $elu = User::factory()->create(['is_elu' => true]);
        Actualite::factory()->create(['title' => 'Actu visible']);

        $this->actingAs($elu)
            ->get(route('elus.actualites.index'))
            ->assertStatus(200)
            ->assertSee('Actu visible');
    }

    public function test_elu_does_not_see_drafts(): void
    {
        $elu = User::factory()->create(['is_elu' => true]);
        Actualite::factory()->draft()->create(['title' => 'Brouillon caché']);

        $this->actingAs($elu)
            ->get(route('elus.actualites.index'))
            ->assertDontSee('Brouillon caché');
    }

    public function test_elu_can_read_published_actualite(): void
    {
        $elu = User::factory()->create(['is_elu' => true]);
        $actualite = Actualite::factory()->create(['title' => 'Bonne nouvelle']);

        $this->actingAs($elu)
            ->get(route('elus.actualites.show', $actualite))
            ->assertStatus(200)
            ->assertSee('Bonne nouvelle');
    }

    public function test_elu_cannot_read_draft_actualite(): void
    {
        $elu = User::factory()->create(['is_elu' => true]);
        $actualite = Actualite::factory()->draft()->create();

        $this->actingAs($elu)
            ->get(route('elus.actualites.show', $actualite))
            ->assertStatus(404);
    }

    public function test_guest_cannot_access_actualites(): void
    {
        $this->get(route('elus.actualites.index'))
            ->assertRedirect(route('login'));
    }
}
