# Copilot instructions

Purpose: orient Copilot sessions to this repository: build/test/lint commands, high-level architecture, and project-specific agent conventions.

## Build, test, and lint commands

- PHP dependencies: `composer install`
- Node dependencies: `npm install`
- Setup environment: `cp .env.example .env && php artisan key:generate`
- Prepare DB: `php artisan migrate --seed`
- Start full local dev (server + queue + vite): `composer run dev` (composer "dev" runs multiple processes concurrently)
- Start backend only: `php artisan serve`
- Start frontend only: `npm run dev` (Vite)
- Build frontend (production): `npm run build`
- Run JS tests (Vitest): `npm run test:js`
  - Run a single JS test file: `npx vitest path/to/test` or `npm run test:js -- path/to/test`
- Run PHP tests (all): `php artisan test --compact`
  - Run a single test file: `php artisan test --compact tests/Feature/ExampleTest.php`
  - Run a single test by name/filter: `php artisan test --compact --filter=testName`
- Format/ lint: `vendor/bin/pint --dirty` (or `vendor/bin/pint` to fix everything)

Notes:
- `phpunit.xml` config uses an in-memory SQLite DB for tests (`DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`) and runs under `APP_ENV=testing`.
- Composer scripts of interest: `composer setup` (install, migrate, build) and `composer run dev` (dev processes).

## High-level architecture (big picture)

- Laravel 12 backend (PHP 8.x) using standard MVC and Eloquent; frontend built with Vite, Tailwind CSS v3, Alpine.js, and FullCalendar.
- Core domains: authentication/roles (admin / élu), document management (uploads, library), events/calendar, meetings, and an elected-officials collaborative space.
- Relevant locations: `app/` (models, controllers), `routes/` (web/api/console), `resources/` (views, js, css), `database/` (migrations, factories, seeders).
- Laravel 12 specifics: middleware and providers are declared in `bootstrap/app.php` and `bootstrap/providers.php` (check these for registration points rather than `app/Http/Kernel.php`).

## Key conventions (project-specific)

- Formatting: run `vendor/bin/pint --dirty` before finalizing changes.
- PHP conventions: prefer explicit return types, constructor property promotion, curly braces for control structures, and PHPDoc blocks for complex array shapes.
- Use `php artisan make:` commands to create models, controllers, requests, tests, etc.
- Validation: prefer Form Request classes over inline controller validation.
- Database/models: prefer Eloquent relationships with eager loading; use model factories and seeders in tests; when altering columns, include full column attributes in migrations.
- Tests: run the minimal set of tests after changes (use `--filter` or run a single file); create tests with `php artisan make:test --phpunit` and use factories for fixtures.
- Frontend: reuse existing Tailwind components; if assets are missing from Vite manifest, run `npm run build` or `composer run dev`.

## Copilot / Agent guidance

- Read `CLAUDE.md` first — it contains the project's "Laravel Boost" guidelines and important agent rules (pint usage, testing approach, Boost tools to use).
- When Boost/MCP tools are available, prefer `search-docs` (version-aware docs), `list-artisan-commands`, `tinker`, and `browser-logs` for debugging.
- For styling tasks, follow the `tailwindcss-development` guidance in CLAUDE.md.
- Respect project constraints: do not add new base folders or change dependencies without approval.

## Quick pointers (where to look first)

- `README.md` — project overview and quick commands
- `CLAUDE.md` — agent/boost-specific rules
- `composer.json` & `package.json` — scripts and dev workflow
- `phpunit.xml` — test environment configuration
- `bootstrap/app.php` — middleware/providers and Laravel 12 bootstrapping

References: README.md, CLAUDE.md, phpunit.xml, composer.json, package.json
