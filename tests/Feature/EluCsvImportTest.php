<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class EluCsvImportTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'email' => 'admin@example.com',
        ]);
    }

    private function createCsvContent(array $rows): string
    {
        $lines = [];
        foreach ($rows as $row) {
            $lines[] = implode("\t", array_map(fn ($v) => '"'.$v.'"', $row));
        }

        return implode("\n", $lines);
    }

    public function test_import_form_is_accessible_by_admin(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('elus.admin.users.import.form'));

        $response->assertOk();
    }

    public function test_import_form_is_inaccessible_by_non_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get(route('elus.admin.users.import.form'));

        $response->assertForbidden();
    }

    public function test_import_creates_users_with_elu_profiles(): void
    {
        $admin = $this->createAdmin();

        $header = ['CODE INSEE', 'COLLECTIVITE', 'EPCI/COMMUNE', 'SECTEUR', 'Nom secteur', 'DATE DELIBERATION',
            'visa Préfecture', 'Problème DELIB', 'NOM', 'Prénom', '', 'Monsieur/Madame', 'RT/DS/DT', 'Titre',
            'ordre suppléants', 'Contact', 'mail personnel', 'Mail 2', 'téléphone', 'Adresse1', 'Adresse2',
            'Code postal', 'Commune', 'Profession', 'société', 'Date naissance', '', 'Newsletter', '',
            'Frais de route', 'RIB fourni', 'Chevaux fiscaux'];

        $data = ['87011', 'Haute-Vienne', 'Limoges Métropole', 'S1', 'Sud', '01/01/2025',
            'OK', '', 'DUPONT', 'Jean', '', 'Monsieur', 'DT', 'Maire',
            '1', 'contact@test.fr', 'jean.dupont@test.fr', '', '0555000000', '1 rue de la Paix', '',
            '87000', 'Limoges', 'Médecin', 'Cabinet médical', '15/06/1970', '', '1', '',
            '0', '0', '5'];

        $csv = $this->createCsvContent([$header, $data]);
        $file = UploadedFile::fake()->createWithContent('elus.csv', $csv);

        $response = $this->actingAs($admin)->post(route('elus.admin.users.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'jean.dupont@test.fr',
            'name' => 'DUPONT',
            'nom' => 'DUPONT',
            'prenom' => 'Jean',
            'fonction' => 'Maire',
            'is_elu' => true,
        ]);

        $user = User::where('email', 'jean.dupont@test.fr')->first();
        $this->assertNotNull($user);

        $this->assertDatabaseHas('elu_profiles', [
            'user_id' => $user->id,
            'code_insee' => '87011',
            'titre' => 'Maire',
            'civilite' => 'Monsieur',
            'epci_commune' => 'Limoges Métropole',
        ]);
    }

    public function test_import_skips_empty_email(): void
    {
        $admin = $this->createAdmin();

        $data = ['87011', 'Haute-Vienne', 'Limoges Métropole', 'S1', 'Sud', '',
            '', '', 'DUPONT', 'Jean', '', 'Monsieur', '', 'Maire',
            '', '', '', '', '', '', '',
            '', '', '', '', '', '', '', '',
            '', '', ''];

        $csv = $this->createCsvContent([$data]);
        $file = UploadedFile::fake()->createWithContent('elus.csv', $csv);

        $response = $this->actingAs($admin)->post(route('elus.admin.users.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('skipped');
        $this->assertDatabaseCount('users', 1); // only admin
    }

    public function test_import_skips_duplicate_email(): void
    {
        $admin = $this->createAdmin();
        User::factory()->create(['email' => 'existing@test.fr']);

        $data = ['87011', 'Haute-Vienne', 'Limoges Métropole', 'S1', 'Sud', '',
            '', '', 'DUPONT', 'Jean', '', 'Monsieur', '', 'Maire',
            '', '', 'existing@test.fr', '', '', '', '',
            '', '', '', '', '', '', '', '',
            '', '', ''];

        $csv = $this->createCsvContent([$data]);
        $file = UploadedFile::fake()->createWithContent('elus.csv', $csv);

        $response = $this->actingAs($admin)->post(route('elus.admin.users.import'), [
            'csv_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('skipped');
        $this->assertDatabaseCount('users', 2); // admin + existing
    }

    public function test_import_requires_csv_file(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('elus.admin.users.import'), []);

        $response->assertSessionHasErrors('csv_file');
    }
}
