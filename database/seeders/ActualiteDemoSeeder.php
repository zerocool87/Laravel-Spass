<?php

namespace Database\Seeders;

use App\Models\Actualite;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActualiteDemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = User::first()?->id;

        // 8 actualités publiées
        Actualite::factory()->count(8)->create([
            'created_by' => $userId,
        ]);

        // 2 actualités en brouillon
        Actualite::factory()->draft()->count(2)->create([
            'created_by' => $userId,
        ]);

        $this->command->info('✅ 10 actualités fictives créées (8 publiées, 2 brouillons).');
    }
}
