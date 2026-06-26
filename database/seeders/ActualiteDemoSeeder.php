<?php

namespace Database\Seeders;

use App\Models\Actualite;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActualiteDemoSeeder extends Seeder
{
    public function run(): void
    {
        Actualite::truncate();

        $elus = User::where('is_elu', true)->get();

        if ($elus->isEmpty()) {
            $this->command->warn('Élus missing, skipping actualités seeding.');

            return;
        }

        $actualites = [
            [
                'title' => 'Avancement des travaux de voirie — Rue de la République',
                'content' => "Les travaux de rénovation de la rue de la République sont terminés avec une semaine d'avance. Les équipes du SEHV ont procédé au remplacement des canalisations d'eau potable, à la réfection de la chaussée et à l'installation de 12 candélabres LED connectés.\n\nLe montant total de l'opération s'élève à 180 000 €, financés à 40 % par le Fonds Vert. Les finitions (marquage au sol et signalétique) sont programmées pour la semaine prochaine.",
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Bilan carbone 2025 : une réduction de 8 % confirmée',
                'content' => "Les résultats du bilan carbone 2025 viennent d'être publiés par la commission Transition Énergétique et Climat du SEHV. Les émissions du territoire passent de 24 500 à 22 540 tonnes équivalent CO₂, soit une baisse de 8 % par rapport à 2023.\n\nCette progression est notamment portée par le déploiement des énergies renouvelables (+15 % de production photovoltaïque) et la rénovation énergétique des bâtiments publics.\n\nL'objectif 2030 reste fixé à -30 %, ce qui nécessitera d'accélérer sur la mobilité électrique et l'isolation des logements.",
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Déploiement de la fibre optique — le secteur Nord-Ouest en cours',
                'content' => "Les travaux de déploiement de la fibre optique se poursuivent conformément au calendrier. Après la livraison des zones Est et Sud, c'est désormais le secteur Nord-Ouest qui est en chantier.\n\nÀ ce jour, 1 200 foyers sur les 2 500 prévus sont éligibles au très haut débit. L'achèvement complet du programme est prévu pour le premier trimestre 2027, avec un investissement total de 850 000 € porté par le SEHV.",
                'published_at' => now()->subDays(8),
            ],
            [
                'title' => 'Budget prévisionnel 2026 — les arbitrages en cours',
                'content' => "La commission Administration-Finance du SEHV examine actuellement les arbitrages budgétaires pour l'exercice 2026. Les commissions thématiques ont jusqu'au 15 du mois pour faire remonter leurs ajustements.\n\nLe budget d'investissement prévisionnel s'élève à 8,5 M€, en hausse de 18 % par rapport à 2024. Les principaux postes concernent l'éclairage public (3 M€), les réseaux électriques (2,5 M€) et la mobilité durable (1,5 M€).\n\nLa séance de vote du budget est fixée au 12 décembre.",
                'published_at' => now()->subDays(12),
            ],
            [
                'title' => 'Suivi des concessions — renouvellement des contrats d\'eau potable',
                'content' => "La commission Concession et Délégation de Service Public s'est réunie pour faire un point d'étape sur l'état des concessions en cours. Plusieurs contrats arrivant à échéance cette année nécessitent une anticipation des renouvellements.\n\nLe cabinet d'études mandaté par le SEHV a transmis une proposition de nouveau cahier des charges pour la délégation du service d'eau potable, qui sera examiné en séance plénière du mois prochain.",
                'published_at' => now()->subDays(15),
            ],
            [
                'title' => 'Refonte du site internet du SEHV — les maquettes sont livrées',
                'content' => "L'agence de communication retenue par le SEHV a livré les premières maquettes du nouveau site internet. La refonte vise à améliorer l'accès aux informations pour les 150 communes adhérentes et à faciliter les démarches en ligne.\n\nParmi les nouveautés : un espace élus dédié, une cartographie interactive des projets en cours, et un module de suivi des demandes d'intervention. La mise en ligne est prévue pour la rentrée de septembre.",
                'published_at' => now()->subDays(18),
            ],
            [
                'title' => 'Avis de la CCPE — marchés à examiner pour le mois de juin',
                'content' => "La Commission Consultative des Prestations Extérieures (CCPE) du SEHV examinera plusieurs marchés lors de sa prochaine séance du 12 juin. Sont notamment à l'ordre du jour : le marché de fourniture d'électricité verte 2026-2029, le contrat de maintenance des bornes de recharge, et le renouvellement de la flotte de véhicules électriques.\n\nLes élus membres de la CCPE sont invités à consulter les dossiers en ligne sur l'espace documentaire du SEHV.",
                'published_at' => now()->subDays(20),
            ],
            [
                'title' => 'Appel d\'offres photovoltaïque — installation sur les bâtiments communaux',
                'content' => "Le SEHV lance un appel d'offres pour l'installation de panneaux photovoltaïques sur les toitures des bâtiments communaux. Sont concernés dans un premier temps : la mairie, l'école primaire et le gymnase.\n\nLa puissance totale installée sera de 150 kWc pour une production annuelle estimée à 180 MWh. Le budget alloué est de 450 000 €. Les candidatures sont attendues pour la fin du mois.",
                'published_at' => now()->subDays(25),
            ],
            [
                'title' => 'Cap des 100 bornes de recharge franchi en Haute-Vienne',
                'content' => "Le SEHV a franchi le cap des 100 points de charge publics installés sur l'ensemble du département. Avec 38 stations déployées depuis 2022, ce réseau constitue la dorsale de la mobilité électrique en Haute-Vienne.\n\nLes prochains déploiements concerneront les zones rurales afin d'assurer une couverture équilibrée du territoire. L'objectif 2027 est fixé à 250 points de charge, conformément au plan « Haute-Vienne Électromobilité 2027 ».",
                'published_at' => now()->subDays(30),
            ],
            [
                'title' => 'Rénovation du réseau d\'eau potable — phase 1 à 45 % d\'avancement',
                'content' => "Les travaux de rénovation du réseau d'eau potable se poursuivent dans le centre-ville et le quartier Nord. À ce jour, 2,5 km de canalisations sur les 5 km prévus ont été remplacés, soit un avancement de 45 %.\n\nL'investissement total de 1,5 M€ est cofinancé par le SEHV et l'Agence de l'Eau Adour-Garonne. La livraison de la phase 1 est attendue pour le premier trimestre 2027.",
                'published_at' => now()->subDays(35),
            ],
            [
                'title' => 'Compte administratif 2025 approuvé à l\'unanimité',
                'content' => "Le compte administratif 2025 du SEHV a été approuvé à l'unanimité lors de la dernière séance de la commission Administration-Finance. Le rapport fait état d'un investissement record de 7,2 M€ sur l'exercice 2024, avec un taux de réalisation de 94 %.\n\nLa séance, qui s'est tenue en salle du Conseil, a également acté le report des crédits non engagés sur l'exercice 2026, conformément à la réglementation en vigueur.",
                'published_at' => now()->subDays(40),
            ],
            [
                'title' => 'Délégation de service public — nouveau cahier des charges pour l\'eau potable',
                'content' => "Le nouveau cahier des charges pour la délégation du service d'eau potable a été présenté en commission DSP. Le document intègre des clauses de performance environnementale renforcées, avec un objectif de réduction des fuites de 20 % sur la durée du contrat.\n\nUn cabinet d'études a accompagné le SEHV dans la rédaction du document, qui sera soumis au vote du comité syndical lors de la prochaine séance plénière.",
                'published_at' => now()->subDays(45),
            ],
            [
                'title' => 'Opération « Éco-Énergie » dans les écoles : -15 % de consommation',
                'content' => "Lancée en septembre 2024 dans 25 écoles primaires de la Haute-Vienne, l'opération « Éco-Énergie » a permis de réduire de 15 % en moyenne la consommation énergétique des bâtiments scolaires participants.\n\nPlus de 1 200 élèves ont été sensibilisés aux écogestes et à la maîtrise de l'énergie dans le cadre d'ateliers pédagogiques animés par les conseillers du SEHV. Fort de ce succès, le syndicat reconduit l'opération à la rentrée 2026 avec 15 écoles supplémentaires.",
                'published_at' => now()->subDays(50),
            ],
            [
                'title' => 'Bulletin municipal — édition printemps : appel à contributions',
                'content' => "L'édition printanière du bulletin municipal du SEHV est en cours de préparation. Ce numéro spécial sera consacré au bilan des projets réalisés en 2025 et aux perspectives pour l'année à venir.\n\nLes élus sont invités à transmettre leurs contributions avant le 10 du mois prochain. Les articles peuvent porter sur les projets menés dans leurs communes, les retours d'expérience ou les initiatives locales en matière de transition énergétique.",
                'published_at' => now()->subDays(55),
            ],
            [
                'title' => 'Fibre optique : 1 200 foyers éligibles sur 2 500',
                'content' => "Le déploiement de la fibre optique porté par le SEHV atteint près de 50 % de réalisation. 1 200 foyers sont désormais éligibles au très haut débit sur l'ensemble du territoire.\n\nLes zones rurales sont prioritaires pour la suite du déploiement, avec un objectif de couverture intégrale des communes adhérentes d'ici 2028. Les élus peuvent consulter le calendrier prévisionnel des travaux sur l'espace documentaire du SEHV.",
                'published_at' => now()->subDays(60),
            ],
            [
                'title' => 'Brouillon : Convention de cofinancement avec le Département',
                'content' => 'Projet de convention en cours de rédaction en vue d\'un cofinancement des opérations de rénovation énergétique des bâtiments communaux. La signature est envisagée pour le mois de juin, sous réserve de la validation conjointe des services juridiques du Département et du SEHV.',
                'is_published' => false,
                'published_at' => null,
            ],
            [
                'title' => 'Brouillon : Projet d\'aménagement de la place du Marché',
                'content' => 'Le projet d\'aménagement de la place du Marché prévoit la création d\'espaces verts, l\'installation de mobiliers urbains en matériaux durables et le remplacement de l\'éclairage public par des luminaires LED connectés. Le dossier technique est en attente de validation par la commission Travaux.',
                'is_published' => false,
                'published_at' => null,
            ],
            [
                'title' => 'Brouillon : Rapport annuel 2025 — volet éclairage public',
                'content' => 'Synthèse des données de consommation et de maintenance du parc d\'éclairage public pour l\'exercice 2025. Les données sont en cours de consolidation avant publication officielle. Ce rapport servira de base aux orientations budgétaires 2026 en matière d\'éclairage.',
                'is_published' => false,
                'published_at' => null,
            ],
        ];

        foreach ($actualites as $data) {
            Actualite::create(array_merge($data, [
                'is_published' => $data['is_published'] ?? true,
                'created_by' => $elus->random()->id,
            ]));
        }

        $count = count($actualites);
        $published = count(array_filter($actualites, fn ($a) => $a['is_published'] ?? true));
        $this->command->info("✅ {$count} actualités SEHV créées ({$published} publiées, ".($count - $published).' brouillons).');
    }
}
