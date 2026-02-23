# Laravel-Spass

Plateforme Laravel 12 pour la gestion documentaire, le suivi des événements/réunions et un espace dédié aux élus.

## Stack

- PHP 8.4
- Laravel 12
- Tailwind CSS v3
- Alpine.js v3
- PHPUnit 11

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
- Contexte technique complémentaire: [.mistral-context.md](.mistral-context.md)

## Modules principaux

- Authentification et rôles (admin / élu)
- Gestion documentaire (upload, consultation, bibliothèque)
- Événements et calendrier
- Réunions (liste, calendrier, fiche)
- Espace collaboratif élus
