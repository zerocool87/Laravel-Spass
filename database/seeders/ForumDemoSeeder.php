<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ForumThread;
use App\Models\Thematique;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumDemoSeeder extends Seeder
{
    public function run(): void
    {
        $elus = User::where('is_elu', true)->get();

        if ($elus->isEmpty()) {
            $this->command->warn('Élus missing, skipping forum seeding.');

            return;
        }

        $thematiqueNames = [
            'Concession et délégation de service public',
            'Travaux',
            'Administration-Finance',
            'Transition énergétique et climat',
            'NTIC-Hygiène et sécurité',
            'Communication',
            'CCPE',
        ];

        $thematiques = [];
        foreach ($thematiqueNames as $name) {
            $thematiques[$name] = Thematique::firstOrCreate(['name' => $name]);
        }

        $threadData = [
            'Concession et délégation de service public' => [
                [
                    'title' => 'Suivi des concessions 2026 — Point d\'étape',
                    'body' => "Bonjour à tous,\n\nJe propose qu'on fasse un point d'étape sur l'état des concessions en cours. Plusieurs contrats arrivent à échéance cette année, il faut qu'on anticipe les renouvellements.\n\nQu'en pensez-vous ?",
                ],
                [
                    'title' => 'Nouveau cahier des charges — DSP eau potable',
                    'body' => "Le cabinet d'études nous a transmis une proposition de nouveau cahier des charges pour la délégation du service d'eau potable.\n\nJe vous joins les grandes lignes pour relecture avant la prochaine commission.",
                ],
            ],
            'Travaux' => [
                [
                    'title' => 'Travaux voirie — Rue de la République',
                    'body' => "Les travaux de rénovation de la rue de la République sont terminés avec une semaine d'avance. Bravo aux équipes !\n\nReste à programmer les finitions (marquage au sol, signalétique).",
                ],
            ],
            'Administration-Finance' => [
                [
                    'title' => 'Budget prévisionnel — Arbitrages en cours',
                    'body' => "Suite à notre dernière réunion, je vous partage le tableau des arbitrages budgétaires.\n\nLes commissions thématiques ont jusqu'au 15 du mois pour faire remonter leurs ajustements.",
                ],
                [
                    'title' => 'Marché public — Fournitures de bureau',
                    'body' => "Le nouveau marché de fournitures de bureau est attribué au prestataire BuroService.\n\nÉconomie estimée : 15 % par rapport au marché précédent.",
                ],
            ],
            'Transition énergétique et climat' => [
                [
                    'title' => 'Bilan carbone — Résultats 2025',
                    'body' => "Les résultats du bilan carbone 2025 viennent de tomber. On est à -8 % par rapport à 2023, ce qui est encourageant mais encore insuffisant pour atteindre nos objectifs 2030.\n\nJe propose qu'on organise un atelier de travail sur les leviers d'accélération.",
                ],
                [
                    'title' => 'Installation photovoltaïque — Appel d\'offres',
                    'body' => "L'appel d'offres pour l'installation de panneaux sur les bâtiments communaux est lancé.\n\nRetour des candidatures attendu pour fin de mois.",
                ],
            ],
            'NTIC-Hygiène et sécurité' => [
                [
                    'title' => 'Déploiement fibre — Zones prioritaires',
                    'body' => "Suite à la réunion avec l'opérateur, voici la liste actualisée des zones prioritaires pour le déploiement de la fibre.\n\nLes travaux commenceront par le secteur Nord-Ouest.",
                ],
            ],
            'Communication' => [
                [
                    'title' => 'Refonte du site internet — Maquettes',
                    'body' => "L'agence nous a livré les premières maquettes du nouveau site. Elles sont disponibles ici pour consultation.\n\nJ'attends vos retours avant la fin de semaine.",
                ],
                [
                    'title' => 'Bulletin municipal — Édition printemps',
                    'body' => "L'édition printanière du bulletin municipal est en cours de préparation. Merci de m'envoyer vos contributions avant le 10 du mois prochain.",
                ],
            ],
            'CCPE' => [
                [
                    'title' => 'Avis en cours — Marchés à examiner',
                    'body' => "Plusieurs marchés sont soumis à l'avis de la CCPE pour le mois prochain. La liste complète est en pièce jointe.\n\nProchaine réunion le 12.",
                ],
            ],
        ];

        $createdCount = 0;

        foreach ($threadData as $thematiqueName => $threads) {
            $thematique = $thematiques[$thematiqueName] ?? null;
            if (! $thematique) {
                continue;
            }

            foreach ($threads as $index => $data) {
                $creator = $elus->random();
                $body = $data['body'];
                $thread = ForumThread::create([
                    'thematique_id' => $thematique->id,
                    'title' => $data['title'],
                    'created_by' => $creator->id,
                ]);

                $thread->posts()->create([
                    'user_id' => $creator->id,
                    'body' => $body,
                ]);

                // Add 1–3 replies from other élus
                $repliers = $elus->where('id', '!=', $creator->id)->random(min(3, $elus->count() - 1));
                $replies = [
                    'Merci pour le partage. Je valide cette orientation. À discuter en commission.',
                    "Bonne initiative. J'aurais quelques suggestions à apporter sur le planning.",
                    "Prends en compte les remarques que je t'ai envoyées par mail. On en reparle en réunion.",
                    'Très bonne nouvelle pour les délais. Félicitations aux équipes terrain.',
                    "Je ne suis pas complètement d'accord avec la méthode proposée. Peut-on en débattre ?",
                    "J'ai hâte de voir les maquettes. Le site avait besoin d'un bon rafraîchissement.",
                    'Merci pour le tableau. Je transmets à mon groupe pour relecture.',
                    'Je serai présent à la réunion du 12. Compte sur moi pour préparer les dossiers.',
                    'Excellente nouvelle pour les économies réalisées. Continuons dans cette voie.',
                    "Je propose qu'on invite un représentant du prestataire à la prochaine commission.",
                ];

                $replyCount = random_int(1, 3);
                foreach ($repliers->take($replyCount) as $replier) {
                    $thread->posts()->create([
                        'user_id' => $replier->id,
                        'body' => $replies[array_rand($replies)],
                    ]);
                }

                // Mark as read by the creator
                $thread->readBy()->attach($creator->id, ['last_read_at' => now()]);

                $createdCount++;
            }
        }

        $this->command->info("✅ {$createdCount} sujets de forum créés avec leurs réponses.");
    }
}
