<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Instance;
use App\Models\Reunion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EluTitreAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_elu_sees_only_documents_matching_titre(): void
    {
        $eluMaire = User::factory()->create([
            'is_elu' => true,
            'fonction' => 'Maire',
        ]);

        $eluConseiller = User::factory()->create([
            'is_elu' => true,
            'fonction' => 'Conseiller municipal',
        ]);

        Storage::fake('documents');
        $file = UploadedFile::fake()->create('doc.pdf', 100);

        $docMaire = Document::create([
            'title' => 'Document Maires',
            'path' => $file->store('documents'),
            'visible_to_all' => false,
            'titres' => ['Maire'],
        ]);

        $docConseiller = Document::create([
            'title' => 'Document Conseillers',
            'path' => $file->store('documents'),
            'visible_to_all' => false,
            'titres' => ['Conseiller municipal'],
        ]);

        $docPublic = Document::create([
            'title' => 'Document public',
            'path' => $file->store('documents'),
            'visible_to_all' => true,
        ]);

        // Maire sees Maire doc + public doc
        $responseMaire = $this->actingAs($eluMaire)->get(route('elus.documents.index'));
        $responseMaire->assertOk();
        $responseMaire->assertSee('Document Maires');
        $responseMaire->assertSee('Document public');
        $responseMaire->assertDontSee('Document Conseillers');

        // Conseiller sees Conseiller doc + public doc
        $responseConseiller = $this->actingAs($eluConseiller)->get(route('elus.documents.index'));
        $responseConseiller->assertOk();
        $responseConseiller->assertSee('Document Conseillers');
        $responseConseiller->assertSee('Document public');
        $responseConseiller->assertDontSee('Document Maires');
    }

    public function test_admin_sees_all_documents(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Storage::fake('documents');
        $file = UploadedFile::fake()->create('doc.pdf', 100);

        Document::create([
            'title' => 'Document Maires',
            'path' => $file->store('documents'),
            'visible_to_all' => false,
            'titres' => ['Maire'],
        ]);

        Document::create([
            'title' => 'Document Conseillers',
            'path' => $file->store('documents'),
            'visible_to_all' => false,
            'titres' => ['Conseiller municipal'],
        ]);

        $response = $this->actingAs($admin)->get(route('elus.documents.index'));
        $response->assertOk();
        $response->assertSee('Document Maires');
        $response->assertSee('Document Conseillers');
    }

    public function test_elu_sees_only_reunions_matching_titre(): void
    {
        $instance = Instance::factory()->create();

        $eluMaire = User::factory()->create([
            'is_elu' => true,
            'fonction' => 'Maire',
        ]);

        $eluConseiller = User::factory()->create([
            'is_elu' => true,
            'fonction' => 'Conseiller municipal',
        ]);

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion Maires',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Maire'],
        ]);

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion Conseillers',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Conseiller municipal'],
        ]);

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion publique',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => true,
        ]);

        $responseMaire = $this->actingAs($eluMaire)->get(route('elus.reunions.index'));
        $responseMaire->assertOk();
        $responseMaire->assertSee('Réunion Maires');
        $responseMaire->assertSee('Réunion publique');
        $responseMaire->assertDontSee('Réunion Conseillers');

        $responseConseiller = $this->actingAs($eluConseiller)->get(route('elus.reunions.index'));
        $responseConseiller->assertOk();
        $responseConseiller->assertSee('Réunion Conseillers');
        $responseConseiller->assertSee('Réunion publique');
        $responseConseiller->assertDontSee('Réunion Maires');
    }

    public function test_admin_sees_all_reunions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $instance = Instance::factory()->create();

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion Maires',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Maire'],
        ]);

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion Conseillers',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Conseiller municipal'],
        ]);

        $response = $this->actingAs($admin)->get(route('elus.reunions.index'));
        $response->assertOk();
        $response->assertSee('Réunion Maires');
        $response->assertSee('Réunion Conseillers');
    }

    public function test_elu_access_denied_to_reunion_not_matching_titre(): void
    {
        $instance = Instance::factory()->create();

        $elu = User::factory()->create([
            'is_elu' => true,
            'fonction' => 'Maire',
        ]);

        $reunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion Conseillers',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Conseiller municipal'],
        ]);

        $response = $this->actingAs($elu)->get(route('elus.reunions.show', $reunion));
        $response->assertForbidden();
    }
}
