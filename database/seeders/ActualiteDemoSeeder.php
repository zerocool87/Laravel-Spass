<?php

namespace Database\Seeders;

use App\Models\Actualite;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActualiteDemoSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::first()?->id;

        $actualites = [
            [
                'title' => 'Lancement des travaux d\'électrification rurale dans le canton d\'Ambazac',
                'content' => 'Le SEDHV engage un programme de modernisation des réseaux électriques sur 12 communes du canton d\'Ambazac. Ces travaux, d\'un montant de 1,8 M€, concernent le remplacement de 15 km de lignes basse-tension et la sécurisation de 15 postes de transformation. Livraison prévue au 2e trimestre 2026.',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Nouvelle station de recharge rapide inaugurée à Limoges',
                'content' => 'Le Syndicat d\'Énergies de la Haute-Vienne a inauguré une station de recharge rapide comprenant 4 points de charge de 150 kW chacun, située avenue de la Révolution à Limoges. Cette installation s\'inscrit dans le plan de déploiement « Haute-Vienne Électromobilité 2027 » qui prévoit 30 stations supplémentaires d\'ici deux ans.',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Appel à projets « Éclairage Public Intelligent » : 25 communes lauréates',
                'content' => 'Le comité syndical du SEDHV a dévoilé les 25 communes lauréates de l\'appel à projets « Éclairage Public Intelligent ». Doté d\'une enveloppe de 500 000 €, ce dispositif vise à accompagner les collectivités dans la rénovation de leur parc lumineux avec des solutions connectées permettant une économie d\'énergie pouvant atteindre 70 %. Les travaux débuteront à l\'automne.',
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Signature d\'un partenariat avec l\'ADEME pour la rénovation énergétique',
                'content' => 'Le SEDHV et l\'ADEME Nouvelle-Aquitaine ont signé une convention de partenariat pour les trois prochaines années. Ce programme prévoit un accompagnement technique et financier pour la rénovation énergétique de 150 bâtiments publics sur le territoire haut-viennois. L\'enveloppe totale s\'élève à 2,5 M€, dont 60 % apportés par l\'ADEME.',
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'Assemblée Générale du SEDHV : bilan 2024 et perspectives 2025',
                'content' => 'L\'Assemblée Générale du Syndicat d\'Énergies de la Haute-Vienne s\'est tenue le 15 mars à la salle des fêtes de Saint-Léonard-de-Noblat. Le rapport d\'activité 2024 fait état d\'un investissement record de 7,2 M€ sur l\'exercice, dont 3,5 M€ consacrés à l\'éclairage public et 2,1 M€ aux réseaux électriques. Les perspectives 2025 confirment cette dynamique avec un budget d\'investissement de 8,5 M€.',
                'published_at' => now()->subDays(15),
            ],
            [
                'title' => 'Bilan positif pour l\'opération « Éco-Énergie » dans les écoles',
                'content' => 'Lancée en septembre 2024 dans 25 écoles primaires de la Haute-Vienne, l\'opération « Éco-Énergie » a permis de réduire de 15 % en moyenne la consommation énergétique des bâtiments scolaires participants. Plus de 1 200 élèves ont été sensibilisés aux écogestes et à la maîtrise de l\'énergie. Le SEDHV reconduit l\'opération à la rentrée 2025.',
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => 'Déploiement des bornes de recharge : cap des 100 points de charge atteint',
                'content' => 'Le SEDHV a franchi le cap des 100 points de charge publics installés sur l\'ensemble du département. Avec 38 stations déployées depuis 2022, ce réseau constitue la dorsale de la mobilité électrique en Haute-Vienne. Les prochains déploiements concerneront les zones rurales afin d\'assurer une couverture équilibrée du territoire. L\'objectif 2027 est fixé à 250 points de charge.',
                'published_at' => now()->subDays(25),
            ],
            [
                'title' => 'Appel d\'offres pour la fourniture d\'électricité verte 2026-2029',
                'content' => 'Le SEDHV lance un appel d\'offres groupé pour la fourniture d\'électricité d\'origine renouvelable à destination de ses 150 communes adhérentes. Ce marché, d\'une durée de 3 ans, porte sur un volume estimé de 45 GWh par an. Les offres sont à déposer avant le 15 juin 2025. Ce contrat permettra aux communes de maîtriser leurs coûts tout en soutenant la production locale d\'énergie verte.',
                'published_at' => now()->subMonths(1),
            ],
            [
                'title' => 'Brouillon : Projet de centrale solaire sur les toits des bâtiments communaux',
                'content' => 'Projet en cours d\'étude. La fiche technique est en attente de validation.',
                'is_published' => false,
                'published_at' => null,
            ],
            [
                'title' => 'Brouillon : Convention de cofinancement avec le Département',
                'content' => 'Document préparatoire en vue de la signature prévue en juin.',
                'is_published' => false,
                'published_at' => null,
            ],
        ];

        foreach ($actualites as $data) {
            Actualite::create(array_merge($data, ['created_by' => $userId]));
        }

        $count = count($actualites);
        $published = count(array_filter($actualites, fn ($a) => $a['is_published'] ?? true));
        $this->command->info("✅ {$count} actualités SEDHV créées ({$published} publiées, ".($count - $published).' brouillons).');
    }
}
