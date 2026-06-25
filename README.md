# Laravel-Spass

Plateforme Laravel 12 pour la gestion documentaire, le suivi des événements/réunions et un espace dédié aux élus.

**Dernière mise à jour :** 25 juin 2026

## Stack

- PHP 8.4
- Laravel 12
- Tailwind CSS v3
- Alpine.js v3
- PHPUnit 11
- **Responsive design** — adaptation mobile (Android/iOS) et desktop (Windows)

## Démarrage rapide

1. Installer les dépendances
	- `composer install`
	- `npm install`
2. Configurer l'environnement
	- `cp .env.example .env`
	- `php artisan key:generate`
3. Préparer la base
	- `php artisan migrate --seed`
4. Lancer en local
	- `composer run dev`

## Commandes utiles

- Lancer les tests: `php artisan test --compact`
- Lancer un test ciblé: `php artisan test --compact tests/Feature/ExampleTest.php`
- Formater le code: `vendor/bin/pint --dirty`
- Build front: `npm run build`

## Documentation

- Index documentation: [docs/README.md](docs/README.md)
- Plan produit et roadmap: [docs/features/roadmap.md](docs/features/roadmap.md)
- Conventions agent/projet: [CLAUDE.md](CLAUDE.md)
- Guide agent opérationnel: [AGENTS.md](AGENTS.md)

## Modules principaux

- Authentification et rôles (admin / élu)
- Actualités (flux de publications, CRUD admin, vue liste/détail élus)
- Gestion documentaire (upload, consultation, bibliothèque, preview, embed avec Range headers)
- Événements et calendrier (FullCalendar, 3 types, JSON feed public)
- Réunions (liste, calendrier, fiche, JSON feed, détection de conflits)
- Instances (Comités, Bureaux, Commissions — liste simplifiée et fixe)
- Projets (CRUD + GeoJSON, filtres par territoire/type/statut)
- Messagerie collaborative inter-élus (Collab : conversations + messages, accusés de lecture)
- Profil élu étendu (EluProfile : code INSEE, collectivité, civilité, profession, etc.)
- Administration avancée (dashboard admin, import CSV massif, gestion des utilisateurs et profils)
- Contrôle d'accès par titres (documents, réunions projetés par mandat/fonction)
