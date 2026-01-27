<?php

namespace Tests\Feature;

use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignDocumentCategoriesCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigns_categories_based_on_title()
    {
        $d1 = Document::create(['title' => 'Convocation: réunion', 'path' => 'documents/a.pdf', 'created_by' => null, 'visible_to_all' => true]);
        $d2 = Document::create(['title' => 'Ordre du jour - Assemblée', 'path' => 'documents/b.pdf', 'created_by' => null, 'visible_to_all' => true]);
        $d3 = Document::create(['title' => 'Compte rendu de la réunion', 'path' => 'documents/c.pdf', 'created_by' => null, 'visible_to_all' => true]);
        $d4 = Document::create(['title' => 'Random file', 'path' => 'documents/d.pdf', 'created_by' => null, 'visible_to_all' => true]);

        $this->artisan('documents:assign-categories')->assertExitCode(0);

        $this->assertDatabaseHas('documents', ['id' => $d1->id, 'category' => 'Convocations']);
        $this->assertDatabaseHas('documents', ['id' => $d2->id, 'category' => 'Ordres du jour']);
        $this->assertDatabaseHas('documents', ['id' => $d3->id, 'category' => 'Comptes rendus']);
        $this->assertDatabaseHas('documents', ['id' => $d4->id, 'category' => null]);
    }

    public function test_dry_run_does_not_modify_database()
    {
        $d = Document::create(['title' => 'Convocation spéciale', 'path' => 'documents/e.pdf', 'created_by' => null, 'visible_to_all' => true]);

        $this->artisan('documents:assign-categories --dry-run')->assertExitCode(0);

        $this->assertDatabaseHas('documents', ['id' => $d->id, 'category' => null]);
    }

    public function test_default_option_applies_for_unmatched()
    {
        $d = Document::create(['title' => 'Unknown Title', 'path' => 'documents/f.pdf', 'created_by' => null, 'visible_to_all' => true]);

        $this->artisan('documents:assign-categories --default="Comptes rendus"')->assertExitCode(0);

        $this->assertDatabaseHas('documents', ['id' => $d->id, 'category' => 'Comptes rendus']);
    }

    public function test_detects_new_categories()
    {
        $d1 = Document::create(['title' => 'Rapport annuel 2025', 'path' => 'documents/g.pdf', 'created_by' => null, 'visible_to_all' => true]);
        $d2 = Document::create(['title' => 'Délibération - Conseil municipal', 'path' => 'documents/h.pdf', 'created_by' => null, 'visible_to_all' => true]);
        $d3 = Document::create(['title' => 'Guide d\'utilisation', 'path' => 'documents/i.pdf', 'created_by' => null, 'visible_to_all' => true]);

        $this->artisan('documents:assign-categories')->assertExitCode(0);

        $this->assertDatabaseHas('documents', ['id' => $d1->id, 'category' => 'Rapports']);
        $this->assertDatabaseHas('documents', ['id' => $d2->id, 'category' => 'Délibérations']);
        $this->assertDatabaseHas('documents', ['id' => $d3->id, 'category' => 'Guides']);
    }
}
