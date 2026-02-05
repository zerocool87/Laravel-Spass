<?php

namespace Database\Seeders;

use App\Models\Instance;
use App\Models\Project;
use App\Models\Reunion;
use Illuminate\Database\Seeder;

class ElusDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Instances
        $comiteSyndical = Instance::create([
            'name' => 'Comité Syndical',
            'type' => 'comite',
            'description' => 'Instance délibérante du syndicat, composée des délégués des communes membres.',
            'territory' => 'Ensemble du territoire',
            'members' => ['Jean Dupont - Président', 'Marie Martin - Vice-Présidente', 'Pierre Durand - Trésorier'],
        ]);

        $bureauExecutif = Instance::create([
            'name' => 'Bureau Exécutif',
            'type' => 'bureau',
            'description' => 'Organe exécutif du syndicat, chargé de la gestion courante.',
            'territory' => 'Ensemble du territoire',
            'members' => ['Jean Dupont - Président', 'Marie Martin - Vice-Présidente'],
        ]);

        $commissionFinances = Instance::create([
            'name' => 'Commission Finances',
            'type' => 'commission',
            'description' => 'Commission chargée de l\'examen des questions financières et budgétaires.',
            'territory' => null,
            'members' => ['Pierre Durand - Président', 'Sophie Bernard - Rapporteur'],
        ]);

        $commissionTravaux = Instance::create([
            'name' => 'Commission Travaux',
            'type' => 'commission',
            'description' => 'Commission chargée du suivi des projets d\'infrastructure.',
            'territory' => null,
            'members' => ['Marc Petit - Président', 'Anne Leroy - Rapporteur'],
        ]);

        // Create Reunions
        Reunion::create([
            'instance_id' => $comiteSyndical->id,
            'title' => 'Comité Syndical - Session ordinaire',
            'description' => 'Session ordinaire du premier trimestre.',
            'date' => now()->addDays(15)->setTime(14, 30),
            'location' => 'Salle du Conseil, Hôtel de Ville',
            'status' => 'confirmee',
            'ordre_du_jour' => "1. Approbation du compte rendu de la séance précédente\n2. Rapport d'activité 2025\n3. Budget primitif 2026\n4. Projets d'investissement\n5. Questions diverses",
            'participants' => ['Jean Dupont', 'Marie Martin', 'Pierre Durand', 'Sophie Bernard', 'Marc Petit'],
        ]);

        Reunion::create([
            'instance_id' => $bureauExecutif->id,
            'title' => 'Bureau Exécutif - Réunion mensuelle',
            'description' => 'Point mensuel sur les affaires courantes.',
            'date' => now()->addDays(7)->setTime(10, 0),
            'location' => 'Salle de réunion A',
            'status' => 'planifiee',
            'ordre_du_jour' => "1. Point sur les marchés en cours\n2. Validation des dépenses\n3. Préparation du Comité Syndical",
        ]);

        Reunion::create([
            'instance_id' => $commissionFinances->id,
            'title' => 'Commission Finances - Budget 2026',
            'description' => 'Examen du projet de budget 2026.',
            'date' => now()->addDays(10)->setTime(9, 0),
            'location' => 'Salle de réunion B',
            'status' => 'planifiee',
            'ordre_du_jour' => "1. Présentation du projet de budget\n2. Analyse des recettes\n3. Analyse des dépenses\n4. Avis de la commission",
        ]);

        Reunion::create([
            'instance_id' => $comiteSyndical->id,
            'title' => 'Comité Syndical - Session extraordinaire',
            'description' => 'Session passée pour validation du compte administratif.',
            'date' => now()->subDays(30)->setTime(14, 30),
            'location' => 'Salle du Conseil, Hôtel de Ville',
            'status' => 'terminee',
            'ordre_du_jour' => "1. Compte administratif 2025\n2. Affectation du résultat",
            'compte_rendu' => "La séance est ouverte à 14h30.\n\nLe compte administratif 2025 a été approuvé à l'unanimité.\n\nLe résultat de fonctionnement a été affecté en réserves.\n\nLa séance est levée à 16h00.",
        ]);

        // Create Projects
        Project::create([
            'title' => 'Rénovation du réseau d\'eau potable - Phase 1',
            'description' => 'Remplacement des canalisations vétustes dans le centre-ville. Ce projet vise à améliorer la qualité de l\'eau et réduire les pertes en réseau.',
            'type' => 'infrastructure',
            'status' => 'en_cours',
            'territories' => ['Centre-ville', 'Quartier Nord'],
            'budget' => 1500000.00,
            'start_date' => now()->subMonths(3),
            'end_date' => now()->addMonths(9),
            'indicators' => [
                'Linéaire remplacé' => '2.5 km / 5 km',
                'Taux d\'avancement' => '45%',
                'Réduction des pertes' => '-15%',
            ],
        ]);

        Project::create([
            'title' => 'Installation photovoltaïque - Bâtiments publics',
            'description' => 'Installation de panneaux photovoltaïques sur les toitures des bâtiments communaux pour réduire l\'empreinte carbone.',
            'type' => 'energie',
            'status' => 'planifie',
            'territories' => ['Mairie', 'École primaire', 'Gymnase'],
            'budget' => 450000.00,
            'start_date' => now()->addMonths(2),
            'end_date' => now()->addMonths(8),
            'indicators' => [
                'Puissance installée' => '150 kWc',
                'Production annuelle' => '180 MWh',
                'Économies annuelles' => '25 000 €',
            ],
        ]);

        Project::create([
            'title' => 'Aménagement de la place du Marché',
            'description' => 'Réaménagement complet de la place du marché avec création d\'espaces verts et de mobilier urbain.',
            'type' => 'amenagement',
            'status' => 'termine',
            'territories' => ['Centre-ville'],
            'budget' => 280000.00,
            'start_date' => now()->subMonths(12),
            'end_date' => now()->subMonths(2),
            'indicators' => [
                'Surface aménagée' => '2 500 m²',
                'Arbres plantés' => '25',
                'Bancs installés' => '12',
            ],
        ]);

        Project::create([
            'title' => 'Extension de la fibre optique',
            'description' => 'Déploiement de la fibre optique dans les zones non couvertes du territoire.',
            'type' => 'numerique',
            'status' => 'en_cours',
            'territories' => ['Zone rurale Est', 'Zone rurale Ouest'],
            'budget' => 850000.00,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(12),
            'indicators' => [
                'Foyers éligibles' => '1 200 / 2 500',
                'Km de fibre' => '35 km',
                'Taux de raccordement' => '48%',
            ],
        ]);

        Project::create([
            'title' => 'Plan de gestion des espaces naturels',
            'description' => 'Élaboration et mise en œuvre d\'un plan de gestion des espaces naturels sensibles.',
            'type' => 'environnement',
            'status' => 'planifie',
            'territories' => ['Zone humide Nord', 'Forêt communale'],
            'budget' => 120000.00,
            'start_date' => now()->addMonths(1),
            'end_date' => now()->addMonths(24),
            'indicators' => [
                'Surface concernée' => '150 ha',
                'Espèces protégées' => '12',
            ],
        ]);
    }
}
