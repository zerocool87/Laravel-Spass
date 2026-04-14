<?php

namespace Database\Seeders;

use App\Models\Instance;
use App\Models\Reunion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReunionDemoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instances = Instance::all();

        if ($instances->isEmpty()) {
            $this->command->warn('Aucune instance trouvée. Veuillez exécuter InstanceSeeder d\'abord.');
            return;
        }

        foreach ($instances as $instance) {
            Reunion::factory()->count(3)->create([
                'instance_id' => $instance->id,
            ]);
        }

        $total = $instances->count() * 3;
        $this->command->info("✅ {$total} réunions fictives créées ({$instances->count()} instances × 3).");
    }
}
