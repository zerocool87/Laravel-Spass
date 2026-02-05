<?php

namespace Database\Seeders;

use App\Models\Instance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstanceSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si des instances existent déjà
        $existingInstances = Instance::count();
        
        if ($existingInstances > 0) {
            $this->command->info('Des instances existent déjà dans la base de données.');
            $this->command->info('Nombre d\'instances existantes: ' . $existingInstances);
            return;
        }

        // Créer des instances exemples
        $instances = [
            [
                'name' => 'Conseil Municipal',
                'type' => 'conseil',
                'description' => 'Instance principale de décision de la commune.',
                'territory' => 'Commune principale',
                'members' => ['Maire', 'Adjoints', 'Conseillers municipaux']
            ],
            [
                'name' => 'Commission Urbanisme',
                'type' => 'commission',
                'description' => 'Commission chargée des questions d\'urbanisme et d\'aménagement.',
                'territory' => 'Commune principale',
                'members' => ['Adjoint à l\'urbanisme', 'Techniciens', 'Représentants citoyens']
            ],
            [
                'name' => 'Comité des Finances',
                'type' => 'comite',
                'description' => 'Comité de gestion et de suivi des finances communales.',
                'territory' => 'Commune principale',
                'members' => ['Adjoint aux finances', 'Comptable', 'Élus référents']
            ],
            [
                'name' => 'Bureau Municipal',
                'type' => 'bureau',
                'description' => 'Instance de coordination et de préparation des décisions.',
                'territory' => 'Commune principale',
                'members' => ['Maire', 'Adjoints principaux', 'Secrétaire général']
            ],
            [
                'name' => 'Commission Environnement',
                'type' => 'commission',
                'description' => 'Commission dédiée aux questions environnementales et de développement durable.',
                'territory' => 'Commune principale',
                'members' => ['Adjoint à l\'environnement', 'Experts', 'Associations locales']
            ]
        ];

        foreach ($instances as $instance) {
            Instance::create($instance);
        }

        $this->command->info('✅ ' . count($instances) . ' instances ont été créées avec succès !');
        
        foreach ($instances as $instance) {
            $this->command->info('📋 ' . $instance['name'] . ' (' . $instance['type'] . ')');
        }
    }
}