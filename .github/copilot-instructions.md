# Copilot instructions — Laravel-Spass

Purpose: Provide repository-specific guidance for Copilot/AI sessions so suggestions and automation stay aligned with project tooling and conventions.

---

## Quick build, test and lint commands

- Setup (install deps + frontend):
  - composer install && npm install
  - or run the setup script: `composer run setup`

- Environment & DB:
  - Copy env and generate key: `cp .env.example .env && php artisan key:generate`
  - Prepare DB: `php artisan migrate --seed`

- Development (recommended):
  - Full dev environment (backend + queue + vite): `composer run dev` (runs concurrently: artisan serve, queue listener, pail, and vite)
  - Frontend only (Vite dev server): `npm run dev`
  - Serve backend only: `php artisan serve`

- Production build (frontend):
  - `npm run build`

- PHP tests (recommended):
  - Run all tests: `php artisan test --compact`
  - Run a single test file: `php artisan test --compact tests/Feature/ExampleTest.php`
  - Run a single test method: `php artisan test --compact --filter=testName`
  - Composer test shortcut: `composer test` (calls artisan test)
  - Direct PHPUnit (if needed): `vendor/bin/phpunit --filter ClassName::testMethod`

- JS tests (Vitest):
  - Run all JS tests: `npm run test:js`
  - Run a single JS test: `npx vitest -t "test name"` or `npx vitest path/to/file`

- Format / lint:
  - Check changed files: `vendor/bin/pint --dirty`
  - Auto-fix formatting: `vendor/bin/pint`

- Common troubleshooting:
  - If you see `Unable to locate file in Vite manifest` run `npm run build` or `composer run dev`.

---

## High-level architecture (big picture)

- Laravel 12 backend (PHP 8.4 features encouraged). Core app code lives under `app/`, routing under `routes/`, config under `config/`, migrations under `database/migrations` and tests under `tests/`.
- Middleware and provider registration follow Laravel 12 conventions: middleware are configured in `bootstrap/app.php` and project-specific providers in `bootstrap/providers.php`.
- Frontend lives in `resources/js` and `resources/css`, built with Vite via `laravel-vite-plugin`. Tailwind CSS v3 and Alpine.js v3 are used for styling and small interactive components.
- Authentication uses Laravel Breeze scaffolding. Fullcalendar packages and other frontend libs are declared in `package.json`.
- Tests use the phpunit.xml configuration which sets an in-memory SQLite DB for fast, isolated test runs (see `phpunit.xml`).
- Project includes MCP/Boost integrations (see `.mcp.json` and `boost.json`) which expose tools like search-docs and tinker when available.

---

## Key conventions & repository-specific patterns

These are not generic rules — they come from the project’s CLAUDE.md and .mistral-context.md and are enforced or relied on by CI/dev workflows:

- Coding style & formatting
  - Use Laravel Pint for formatting. Run `vendor/bin/pint --dirty` before finalizing changes.
  - Prefer explicit return types and PHP 8 constructor property promotion where appropriate.
  - Always use curly braces for control structures (even single-line bodies).

- Laravel / PHP conventions
  - Use `php artisan make:` commands to scaffold controllers, models, requests, etc.; use `--no-interaction` in scripts.
  - Prefer Eloquent (`Model::query()`) and relationships; eager-load to avoid N+1 queries.
  - Use Form Request classes for validation (check sibling files for rule style: array vs string rules).
  - When adding models, also add factories and seeders.
  - Use named routes and `route()` when generating URLs.

- Testing
  - Every change should have programmatic tests; run the minimum targeted tests for speed (use `--filter` or target a file).
  - Prefer feature tests; use `php artisan make:test --phpunit` to generate tests that follow project conventions.

- Frontend / styling
  - Use Tailwind CSS utilities; follow existing component patterns in `resources/`.
  - If UI changes are not visible, ask to run `npm run dev`, `npm run build` or `composer run dev`.

- Project governance
  - Do not add new top-level folders or change dependencies without approval.
  - Follow existing sibling files for structure and naming when adding components.

---

## AI / copilot-specific notes

- CLAUDE.md is authoritative for project-specific rules and agent skills (e.g., `tailwindcss-development`). Always consult it for domain rules.
- Use the laravel-boost tools (configured in `.mcp.json`) to query version-specific docs (`search-docs`), list artisan commands, run `tinker`, or inspect the DB schema when available.
- Keep suggestions concise and follow the concrete commands above for running and testing code.

---

## Where to look for more detail

- README.md — quickstart & common commands
- CLAUDE.md — project rules and agent guidance (must follow)
- .mistral-context.md — strict coding rules and environment expectations (PHP/Tailwind/Pint hints)
- .mcp.json / boost.json — MCP server configuration and available agent skills
- phpunit.xml, composer.json, package.json, vite.config.js — for test, script and build details

---

This file was generated from the repository's README and CLAUDE guidelines; update it when project scripts, tools, or structure change.

## MCP servers

- Configured MCP servers (see `.mcp.json`):
  - `laravel-boost` — started via `php artisan boost:mcp`. Exposes capabilities: `browser-logs`, `database-connections`, `database-query`, `database-schema`, `get-absolute-url`, `get-config`, `last-error`, `list-artisan-commands`, `list-available-config-keys`, `list-available-env-vars`, `list-routes`, `read-log-entries`, `search-docs`, and `tinker`.
  - `context7` — started via `php artisan context7:mcp`. Exposes capabilities: `search-docs`, `tinker`, `list-artisan-commands`, `read-log-entries`, `database-query`, `database-schema`, and `get-absolute-url`.

- Guidance: When starting agent sessions, use the appropriate server for the task (e.g., `laravel-boost` for Laravel-specific docs/tools; `context7` for additional contextual tooling). When adding servers, add an entry to `.mcp.json` with the start command and allowed capabilities and mirror the change here.
