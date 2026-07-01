# AGENTS.md — Laravel-Spass

## Project identity

French-language Laravel 12 platform (Spass) for document management, events/meetings tracking, and an elected-officials ("élus") portal. Stack: PHP 8.4, Tailwind CSS v3, Alpine.js v3, Breeze v2 auth.

A comprehensive instruction file for agents already exists at [`CLAUDE.md`](CLAUDE.md) — read it. This file adds repo-specific operational facts.

## Key commands

```bash
# Run all tests (SQLite :memory: — no DB setup needed)
php artisan test --compact

# Run a single test file/name
php artisan test --compact tests/Feature/EventTest.php
php artisan test --compact --filter=testName

# Format PHP (—dirty only changes touched files; DO NOT use --test)
vendor/bin/pint --dirty

# Run JS tests (vitest)
npm run test:js

# Dev server (starts 4 processes: server, queue, logs, vite)
composer run dev
```

## Architecture quick-reference

| Directory | Purpose |
|-----------|---------|
| `app/Http/Controllers/Admin/` | Admin CRUD controllers (documents, instances, projects, réunions, actualités, users) |
| `app/Http/Controllers/Elus/` | Elected-official facing controllers (dashboard, collab, documents, admin, etc.) |
| `app/Http/Controllers/Elus/Concerns/` | Reusable traits (FiltersDocuments, RequiresAdmin) |
| `app/Http/Controllers/Auth/` | Breeze v2 auth controllers |
| `app/Http/Requests/` | 12 Form Request validation classes (array-based rules) |
| `app/Models/` | 10 models: User, EluProfile, Document, Event, Instance, Project, Reunion, Actualite, Conversation, Message |
| `app/Enums/` | 3 enums: ReunionStatus, ProjectType, ProjectStatus |
| `routes/web.php` | All web routes; `routes/auth.php` is required from web.php |

## Authorization model

- **`is_admin`** boolean — checked via `can:admin` gate
- **`is_elu`** boolean — the custom `elu` middleware (alias registered in `bootstrap/app.php`) grants access if `is_elu=true` OR `is_admin=true`
- Always use gates/policies, not inline auth checks

## Testing conventions

- **PHPUnit 11 only** — no Pest. Use `php artisan make:test --phpunit {name}` for features, `--unit` for unit tests.
- Use PHP 8 attributes for tests, never `@test` annotations.
- Use model factories for test data. Check factories for custom states before manually setting attributes.
- Tests that hit DB use SQLite `:memory:` automatically — no seeding required unless the test calls `->seed()`.
- JS tests live in `tests/js/` and use vitest.

## Code conventions (from CLAUDE.md, preserved here)

- PHP 8.4: constructor property promotion, `declare(strict_types=1)`.
- Put casts in `casts()` method, not `$casts` property.
- Use PHPDoc over inline comments. Add array-shape type definitions where useful.
- Curly braces on all control structures, even single-line.
- Explicit return type declarations everywhere.
- Enum keys in TitleCase.
- `vendor/bin/pint --dirty` before finalizing changes.

## Pages spécifiques

### Actualités (élus) — `/elus/actualites`

- Pagination personnalisée intégrée dans l'en-tête "Le Journal du SEHV" (masthead), pas en bas de page
- Template de pagination : `resources/views/vendor/pagination/tailwind.blade.php` (surcharge locale)
- Affiche "Articles précédents | Articles suivants" au lieu des numéros de page
- 7 articles par page (`paginate(7)` dans `Elus\ActualiteController`)
- Contenu avec `nl2br(e())` pour préserver les sauts de ligne (textarea → HTML)
- Modal Alpine.js avec `x-html="selected.content"` (pas `x-text`) pour le rendu HTML
- Titres en orange `text-[#faa21b]`

## Gotchas

- **No CI** — there are no GitHub Actions workflows. You must run tests locally.
- **No `app/Http/Kernel.php`** — middleware is configured in `bootstrap/app.php` (Laravel 12).
- **No `app/Console/Kernel.php`** — commands in `app/Console/Commands/` are auto-discovered.
- **The `composer run dev` command** kills-others on exit, so all sub-processes stop together.
- **Vite HMR** host is configurable via `VITE_HMR_HOST` env var (defaults to `localhost`).
- **`routes/events/json` is intentionally public** — no auth middleware; used by calendar widgets.
- **Database default is SQLite** (per `config/database.php`). The `DB_CONNECTION` env controls this.
- **`boost.json`** sets `"sail": false` — the app runs directly, not via Docker.
- **4-space indentation** for PHP per `.editorconfig` (not a typo — this project uses 4, not 2).
- **Titres-based access control** — documents/réunions/projects have `titres` (JSON array) and `visible_to_all` flags for scoping by user's mandate/function.
- **EluProfile** — separate model from User; stores extended elected-official fields (code_insee, profession, etc.). Always eager-load via `->with('eluProfile')` when editing a user.
