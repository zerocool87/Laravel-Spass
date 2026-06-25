<?php

namespace Database\Seeders;

use App\Models\EluProfile;
use App\Models\Instance;
use App\Models\Project;
use App\Models\Reunion;
use App\Models\User;
use Illuminate\Database\Seeder;

class ElusDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create élu users with various titres
        $elu1 = User::create([
            'name' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'password' => bcrypt('password'),
            'is_elu' => true,
            'commune' => 'Limoges',
            'titres' => ['Président', 'Membre du bureau'],
        ]);
        EluProfile::create([
            'user_id' => $elu1->id,
            'civilite' => 'Monsieur',
            'code_insee' => '87085',
            'collectivite' => 'Haute-Vienne',
            'epci_commune' => 'Limoges Métropole',
            'titres' => ['Président', 'Membre du bureau'],
        ]);

        $elu2 = User::create([
            'name' => 'Martin',
            'prenom' => 'Marie',
            'email' => 'marie.martin@example.com',
            'password' => bcrypt('password'),
            'is_elu' => true,
            'commune' => 'Saint-Yrieix-la-Perche',
            'titres' => ['Vice-président', 'Membre de commission'],
        ]);
        EluProfile::create([
            'user_id' => $elu2->id,
            'civilite' => 'Madame',
            'code_insee' => '87187',
            'collectivite' => 'Haute-Vienne',
            'epci_commune' => 'CC Pays de Saint-Yrieix',
            'titres' => ['Vice-président', 'Membre de commission'],
        ]);

        $elu3 = User::create([
            'name' => 'Durand',
            'prenom' => 'Pierre',
            'email' => 'pierre.durand@example.com',
            'password' => bcrypt('password'),
            'is_elu' => true,
            'commune' => 'Panazol',
            'titres' => ['Délégué titulaire'],
        ]);
        EluProfile::create([
            'user_id' => $elu3->id,
            'civilite' => 'Monsieur',
            'code_insee' => '87114',
            'collectivite' => 'Haute-Vienne',
            'epci_commune' => 'Limoges Métropole',
            'titres' => ['Délégué titulaire'],
        ]);

        $elu4 = User::create([
            'name' => 'Bernard',
            'prenom' => 'Sophie',
            'email' => 'sophie.bernard@example.com',
            'password' => bcrypt('password'),
            'is_elu' => true,
            'commune' => 'Couzeix',
            'titres' => ['Délégué suppléant'],
        ]);
        EluProfile::create([
            'user_id' => $elu4->id,
            'civilite' => 'Madame',
            'code_insee' => '87050',
            'collectivite' => 'Haute-Vienne',
            'epci_commune' => 'Limoges Métropole',
            'titres' => ['Délégué suppléant'],
        ]);

        $elu5 = User::create([
            'name' => 'Leroy',
            'prenom' => 'Anne',
            'email' => 'anne.leroy@example.com',
            'password' => bcrypt('password'),
            'is_elu' => true,
            'commune' => 'Limoges',
            'titres' => ['Représentant'],
        ]);
        EluProfile::create([
            'user_id' => $elu5->id,
            'civilite' => 'Madame',
            'code_insee' => '87085',
            'collectivite' => 'Haute-Vienne',
            'epci_commune' => 'Limoges Métropole',
            'titres' => ['Représentant'],
        ]);

        // Create 7 fixed instances
        $instanceNames = [
            'Concession et délégation de service public',
            'Travaux',
            'Administration-Finance',
            'Transition énergétique et climat',
            'NTIC-Hygiène et sécurité',
            'Communication',
            'CCPE',
        ];

        $instances = [];
        foreach ($instanceNames as $name) {
            $instances[] = Instance::create(['name' => $name]);
        }

        // Create reunions for each instance
        $reunionData = [
            ['instance' => 0, 'title' => 'Commission DSP - Suivi des concessions', 'start' => '+15 days', 'location' => 'Salle A'],
            ['instance' => 0, 'title' => 'Délégation de service public - Point trimestriel', 'start' => '+45 days', 'location' => 'Salle A'],
            ['instance' => 1, 'title' => 'Suivi des travaux - Voirie', 'start' => '+7 days', 'location' => 'Salle B'],
            ['instance' => 1, 'title' => 'Commission Travaux - Budget', 'start' => '+30 days', 'location' => 'Salle B'],
            ['instance' => 2, 'title' => 'Budget prévisionnel 2026', 'start' => '+10 days', 'location' => 'Salle du Conseil'],
            ['instance' => 2, 'title' => 'Administration-Finance - Revue des comptes', 'start' => '+20 days', 'location' => 'Salle du Conseil'],
            ['instance' => 3, 'title' => 'Transition énergétique - Bilan carbone', 'start' => '+5 days', 'location' => 'Salle C'],
            ['instance' => 3, 'title' => 'Plan climat territorial', 'start' => '+60 days', 'location' => 'Salle C'],
            ['instance' => 4, 'title' => 'Hygiène et sécurité - Point règlementaire', 'start' => '+12 days', 'location' => 'Salle D'],
            ['instance' => 4, 'title' => 'NTIC - Déploiement fibre', 'start' => '+25 days', 'location' => 'Salle D'],
            ['instance' => 5, 'title' => 'Stratégie de communication', 'start' => '+8 days', 'location' => 'Salle E'],
            ['instance' => 5, 'title' => 'Communication - Revue de presse', 'start' => '+35 days', 'location' => 'Salle E'],
            ['instance' => 6, 'title' => 'CCPE - Commission consultative', 'start' => '+3 days', 'location' => 'Salle F'],
            ['instance' => 6, 'title' => 'CCPE - Suivi des marchés', 'start' => '+40 days', 'location' => 'Salle F'],
        ];

        foreach ($reunionData as $r) {
            Reunion::create([
                'instance_id' => $instances[$r['instance']]->id,
                'title' => $r['title'],
                'start_time' => now()->modify($r['start'])->setTime(9, 30),
                'end_time' => now()->modify($r['start'])->setTime(11, 30),
                'location' => $r['location'],
                'status' => 'planifiee',
                'participants' => [],
            ]);
        }

        // Create a past reunion with compte rendu
        Reunion::create([
            'instance_id' => $instances[2]->id,
            'title' => 'Commission Administration-Finance - Compte admin',
            'start_time' => now()->subDays(30)->setTime(14, 30),
            'end_time' => now()->subDays(30)->setTime(16, 30),
            'location' => 'Salle du Conseil',
            'status' => 'terminee',
            'compte_rendu' => "La séance est ouverte à 14h30.\n\nLe compte administratif 2025 a été approuvé à l'unanimité.\n\nLa séance est levée à 16h00.",
            'participants' => [],
        ]);

        // Create Projects
        Project::create([
            'title' => 'Rénovation du réseau d\'eau potable - Phase 1',
            'description' => 'Remplacement des canalisations vétustes dans le centre-ville.',
            'type' => 'infrastructure',
            'status' => 'en_cours',
            'territories' => ['Centre-ville', 'Quartier Nord'],
            'budget' => 1500000.00,
            'start_date' => now()->subMonths(3),
            'end_date' => now()->addMonths(9),
            'indicators' => [
                'Linéaire remplacé' => '2.5 km / 5 km',
                'Taux d\'avancement' => '45%',
            ],
        ]);

        Project::create([
            'title' => 'Installation photovoltaïque - Bâtiments publics',
            'description' => 'Installation de panneaux photovoltaïques sur les toitures des bâtiments communaux.',
            'type' => 'energie',
            'status' => 'planifie',
            'territories' => ['Mairie', 'École primaire', 'Gymnase'],
            'budget' => 450000.00,
            'start_date' => now()->addMonths(2),
            'end_date' => now()->addMonths(8),
            'indicators' => [
                'Puissance installée' => '150 kWc',
                'Production annuelle' => '180 MWh',
            ],
        ]);

        Project::create([
            'title' => 'Aménagement de la place du Marché',
            'description' => 'Réaménagement complet avec création d\'espaces verts.',
            'type' => 'amenagement',
            'status' => 'termine',
            'territories' => ['Centre-ville'],
            'budget' => 280000.00,
            'start_date' => now()->subMonths(12),
            'end_date' => now()->subMonths(2),
            'indicators' => [
                'Surface aménagée' => '2 500 m²',
                'Arbres plantés' => '25',
            ],
        ]);

        Project::create([
            'title' => 'Extension de la fibre optique',
            'description' => 'Déploiement de la fibre dans les zones non couvertes.',
            'type' => 'numerique',
            'status' => 'en_cours',
            'territories' => ['Zone rurale Est', 'Zone rurale Ouest'],
            'budget' => 850000.00,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(12),
            'indicators' => [
                'Foyers éligibles' => '1 200 / 2 500',
                'Km de fibre' => '35 km',
            ],
        ]);
    }
}
