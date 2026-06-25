<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Actualite>
 */
class ActualiteFactory extends Factory
{
    public function definition(): array
    {
        $titles = [
            'Avancement du programme d\'électrification rurale 2025',
            'Installation de 12 bornes de recharge dans le département',
            'Bilan du plan de rénovation énergétique des bâtiments publics',
            'Modernisation de l\'éclairage public : 500 points lumineux remplacés',
            'Signature d\'une convention avec Enedis pour le déploiement des smart grids',
            'Campagne de sensibilisation aux écogestes dans les communes',
            'Nouveau marché de fourniture d\'électricité verte pour le syndicat',
            'Remplacement des candélabres vétustes : 200 000 € d\'économie annuelle',
            'État d\'avancement du Schéma Directeur d\'Électrification',
            'Lancement de l\'appel à projets « Territoire Énergie Positive »',
            'Pose de compteurs Linky : fin de la première phase',
            'Résultats de l\'étude de potentiel solaire sur le territoire',
            'Mise en service de l\'unité de méthanisation intercommunale',
            'Plan de résilience du réseau face aux aléas climatiques',
            'Contrat de performance énergétique signé avec 15 communes',
        ];

        $paragraphs = [
            'Le syndicat poursuit son engagement pour la transition énergétique sur l\'ensemble du territoire. Cette nouvelle étape marque une avancée significative dans la stratégie de développement durable, avec des retombées concrètes pour les collectivités adhérentes et leurs habitants.',
            'Ce projet s\'inscrit dans le cadre du programme pluriannuel d\'investissements adopté par le comité syndical. Les travaux, qui débuteront dès le mois prochain, sont financés à hauteur de 40 % par le Fonds Vert et 30 % par le Département.',
            'Les élus des communes adhérentes ont salué cette initiative qui permettra de réduire la facture énergétique collective tout en améliorant le confort des usagers. Le syndicat assure l\'assistance à maîtrise d\'ouvrage pour accompagner les collectivités dans ces projets.',
            'Une réunion d\'information destinée aux maires du territoire sera organisée prochainement pour présenter les modalités techniques et financières. Les inscriptions sont ouvertes via l\'espace adhérent du site internet du syndicat.',
            'Ce chantier s\'inscrit dans la feuille de route « Climat-Air-Énergie » adoptée à l\'échelle régionale. Le syndicat agit en coordination avec l\'ADEME, la Région et le Département pour maximiser les cofinancements.',
        ];

        return [
            'title' => $this->faker->randomElement($titles),
            'content' => collect($this->faker->randomElements($paragraphs, rand(1, 3)))->implode("\n\n"),
            'created_by' => null,
            'is_published' => true,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(['is_published' => false, 'published_at' => null]);
    }
}
