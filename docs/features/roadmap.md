# Laravel-Spass — Plan Produit Optimisé

## Objectif

Fournir une plateforme fiable pour les élus et l'administration, centrée sur la gestion des documents, des réunions et des décisions.

## Contexte technique réel

- Framework: Laravel 12
- Frontend: Tailwind CSS v3 + Alpine.js + FullCalendar
- Tests: PHPUnit 11
- Qualité: Laravel Pint

## Périmètre actuel (déjà en place)

- Authentification + rôles (admin / élu)
- Gestion documentaire (upload, consultation, bibliothèque)
- Événements et calendrier
- Réunions (liste, calendrier, détail)
- Espace collaboratif élus

## Priorités produit (MVP)

### P0 — Essentiel

1. Fiabiliser les parcours critiques
   - Créer / modifier / consulter réunion
   - Publier et retrouver un document
2. Clarifier les permissions
   - Vérification stricte des accès admin vs élu
3. Stabiliser UX calendrier
   - Modales lisibles, navigation simple, état vide explicite
4. Renforcer la base de tests
   - Tests feature sur parcours clés

### P1 — Forte valeur

1. Recherche documentaire améliorée (filtres + pertinence)
2. Notifications utiles (documents publiés, réunion approchante)
3. Tableau de bord élus orienté action (prochaines réunions, documents récents)

### P2 — À planifier

1. 2FA
2. Versioning documentaire avancé
3. Reporting financier enrichi
4. Outils collaboratifs avancés

## Architecture fonctionnelle cible

```
Espace Élus
├── Tableau de bord
├── Réunions
│   ├── Calendrier
│   ├── Liste
│   └── Détail
├── Documents
│   ├── Bibliothèque
│   ├── Catégories
│   └── Recherche
├── Projets
└── Collaboration
```

## Backlog technique priorisé

1. Sécurité
   - Journal des actions sensibles
   - Validation renforcée des uploads
2. Performance
   - Optimisation des requêtes et eager loading
   - Caching ciblé sur listes et widgets
3. Qualité
   - Couverture de tests sur modules Réunions et Documents
   - Standardisation des formulaires via Form Requests

## Plan d'exécution (6 semaines)

### Sprint 1 (S1-S2)

- Stabilisation Réunions (UI + permissions + tests)
- Nettoyage parcours documents les plus utilisés

### Sprint 2 (S3-S4)

- Recherche documentaire améliorée
- Notifications de base

### Sprint 3 (S5-S6)

- Dashboard élus orienté priorités
- Durcissement sécurité + revue de perf

## Indicateurs de succès

1. 95% des actions principales en moins de 3 clics
2. Temps de réponse < 2 secondes sur les écrans clés
3. 0 régression critique sur Réunions/Documents
4. Adoption active des élus > 80%

## Prochaines actions immédiates

1. Valider ce plan MVP avec les parties prenantes
2. Geler le scope du Sprint 1
3. Écrire les tests manquants sur les parcours P0
4. Lancer un suivi hebdomadaire des métriques
