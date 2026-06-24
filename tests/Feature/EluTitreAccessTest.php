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
        $eluPresident = User::factory()->create([
            'is_elu' => true,
            'titres' => ['Président'],
        ]);

        $eluMembre = User::factory()->create([
            'is_elu' => true,
            'titres' => ['Membre du bureau'],
        ]);

        Storage::fake('documents');
        $file = UploadedFile::fake()->create('doc.pdf', 100);

        $docPresident = Document::create([
            'title' => 'Document Présidents',
            'path' => $file->store('documents'),
            'visible_to_all' => false,
            'titres' => ['Président'],
        ]);

        $docMembre = Document::create([
            'title' => 'Document Membres',
            'path' => $file->store('documents'),
            'visible_to_all' => false,
            'titres' => ['Membre du bureau'],
        ]);

        $docPublic = Document::create([
            'title' => 'Document public',
            'path' => $file->store('documents'),
            'visible_to_all' => true,
        ]);

        $responsePresident = $this->actingAs($eluPresident)->get(route('elus.documents.index'));
        $responsePresident->assertOk();
        $responsePresident->assertSee('Document Présidents');
        $responsePresident->assertSee('Document public');
        $responsePresident->assertDontSee('Document Membres');

        $responseMembre = $this->actingAs($eluMembre)->get(route('elus.documents.index'));
        $responseMembre->assertOk();
        $responseMembre->assertSee('Document Membres');
        $responseMembre->assertSee('Document public');
        $responseMembre->assertDontSee('Document Présidents');
    }

    public function test_admin_sees_all_documents(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Storage::fake('documents');
        $file = UploadedFile::fake()->create('doc.pdf', 100);

        Document::create([
            'title' => 'Document Présidents',
            'path' => $file->store('documents'),
            'visible_to_all' => false,
            'titres' => ['Président'],
        ]);

        Document::create([
            'title' => 'Document Membres',
            'path' => $file->store('documents'),
            'visible_to_all' => false,
            'titres' => ['Membre du bureau'],
        ]);

        $response = $this->actingAs($admin)->get(route('elus.documents.index'));
        $response->assertOk();
        $response->assertSee('Document Présidents');
        $response->assertSee('Document Membres');
    }

    public function test_elu_sees_only_reunions_matching_titre(): void
    {
        $instance = Instance::factory()->create();

        $eluPresident = User::factory()->create([
            'is_elu' => true,
            'titres' => ['Président'],
        ]);

        $eluMembre = User::factory()->create([
            'is_elu' => true,
            'titres' => ['Membre du bureau'],
        ]);

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion Présidents',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Président'],
        ]);

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion Membres',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Membre du bureau'],
        ]);

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion publique',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => true,
        ]);

        $responsePresident = $this->actingAs($eluPresident)->get(route('elus.reunions.index'));
        $responsePresident->assertOk();
        $responsePresident->assertSee('Réunion Présidents');
        $responsePresident->assertSee('Réunion publique');
        $responsePresident->assertDontSee('Réunion Membres');

        $responseMembre = $this->actingAs($eluMembre)->get(route('elus.reunions.index'));
        $responseMembre->assertOk();
        $responseMembre->assertSee('Réunion Membres');
        $responseMembre->assertSee('Réunion publique');
        $responseMembre->assertDontSee('Réunion Présidents');
    }

    public function test_admin_sees_all_reunions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $instance = Instance::factory()->create();

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion Présidents',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Président'],
        ]);

        Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion Membres',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Membre du bureau'],
        ]);

        $response = $this->actingAs($admin)->get(route('elus.reunions.index'));
        $response->assertOk();
        $response->assertSee('Réunion Présidents');
        $response->assertSee('Réunion Membres');
    }

    public function test_elu_access_denied_to_reunion_not_matching_titre(): void
    {
        $instance = Instance::factory()->create();

        $elu = User::factory()->create([
            'is_elu' => true,
            'titres' => ['Président'],
        ]);

        $reunion = Reunion::factory()->create([
            'instance_id' => $instance->id,
            'title' => 'Réunion Membres',
            'start_time' => now()->addDay(),
            'end_time' => now()->addDay()->addHours(2),
            'status' => 'planifiee',
            'visible_to_all' => false,
            'titres' => ['Membre du bureau'],
        ]);

        $response = $this->actingAs($elu)->get(route('elus.reunions.show', $reunion));
        $response->assertForbidden();
    }
}
