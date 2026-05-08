# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

> **IMPORTANT:** `AGENTS.md` at the repository root is the authoritative source for workflow, coding style, testing, commit, and PR guidelines. Always read and follow `AGENTS.md` in full. The sections below expand on it with architecture detail and correct any inaccuracies.

---

## Commands

```bash
# PHP dependencies
composer install

# Frontend assets
npm install
npm run dev          # dev server with HMR
npm run build        # production build
npm run production   # production build alias

# Application
php artisan serve
php artisan migrate --seed

# Testing
php artisan test                                    # all tests
php artisan test --filter=ClassName                 # single test class
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
composer test:full                                  # full suite with timeout
composer test:quick                                 # Expediente|Empresa|Comision

# Dusk (browser tests) - requires running app at http://laravel.test
composer dusk:local
XDEBUG_MODE=off APP_URL=http://laravel.test DUSK_APP_URL=http://laravel.test php artisan dusk --env=local --filter=TestName
```

---

## Coding Rules (from AGENTS.md — enforce strictly)

- **PSR-12**, 4-space indent. Class names suffixed: `Controller`, `Job`, `Event`, `Policy`.
- Every modified or newly created class must include/update PHPDoc blocks (`/** ... */`) for the class and all relevant methods/properties.
- Blade layouts/components → `resources/views/layouts` and `resources/views/components`. Translatable strings → `resources/lang`.
- JS/SCSS: keep modular inside `resources/assets`; align class names with Blade markup; add new Vite entry points in `vite.config.mjs` when needed.
- Prefer extending code in `packages/` and `plugins/` over duplicating it elsewhere.
- Language for all comments, views, and UI strings: **Valencià**.

## Commit Rules (from AGENTS.md — enforce strictly)

- Prefix every commit title: `[MOD]` / `[ADD]` / `[DEL]` / `[FIX]`.
- Titles must be imperative and specific: `[FIX] Corregix null pointer en login`.
- **Before committing**: run `gh issue list` / `gh issue status` and reference related issues with `#number`.
- Multiple issues: `[MOD] Refactoritza autenticació #15 #23`.
- **Never** include `Co-Authored-By: Claude` or any AI attribution.

## PR Rules (from AGENTS.md — enforce strictly)

- PRs must summarize intent, link related issues/tasks, and list tests run (`phpunit`, `npm run production` when assets change).
- Add screenshots for UI updates; call out any new migrations or env vars.

## Testing Rules (from AGENTS.md — enforce strictly)

- `tests/Feature/` for HTTP/integration (`*FeatureTest.php`), `tests/Unit/` for pure logic (`*Test.php`).
- Use factories/seeders and `RefreshDatabase`. Assert responses, events, and DB effects for every new endpoint and policy.
- Run the full suite before PRs. Add a regression test with every bug fix.

## Security & Config (from AGENTS.md)

- Never commit `.env` or secrets.
- `php artisan config:cache` / `route:cache` only for production releases.
- Prefer Redis for queue/cache/session. Remove generated PDFs and logs from `storage/` before sharing artifacts.

---

## Architecture

### Namespace & Autoloading
The root PHP namespace is `Intranet\` (not the default `App\`). All application code lives in `app/` and is resolved under `Intranet\`.

### Layered Structure

- **`app/Entities/`** – Eloquent models. Referenced by string name via `$this->model` in controllers, auto-resolved under `Intranet\Entities\`.
- **`app/Domain/`** – Domain boundaries (AlumnoFct, Comision, Empresa, Expediente, FaltaProfesor, Fct, Grupo, Horario, Profesor), each containing a repository interface.
- **`app/Application/`** – Application services/use-cases per domain (mirrors `app/Domain/`). Example: `app/Application/Expediente/ExpedienteService.php`.
- **`app/Http/Controllers/`** – Controllers by role/feature. `Controller.php` is the base (adds `FindsModel` trait, `$model`/`$perfil`). `Core/BaseController.php` extends it for grid-based CRUD screens.
- **`app/Services/`** – Cross-cutting services (Auth, Calendar, Document, HR, Mail, Media, Notifications, School, Signature, UI, etc.).
- **`app/UI/`** – Panel and button rendering (`UI/Panels/Panel`, `UI/Botones/`).
- **`app/Policies/`** – Laravel policies per entity, registered in `AuthServiceProvider`.
- **`app/Livewire/`** – Livewire 3 components for reactive UI.
- **`app/Sao/`** – SAO external system integration actions.
- **`app/Support/Helpers/`** – Global helpers loaded by `HelperServiceProvider`: `MyHelpers.php`, `DateHelpers.php`. Key functions: `authUser()`, `userIsNameAllow($role)`, `isAdmin()`.
- **`app/Finders/`** – Query-builder classes for common filtered queries.
- **`packages/`**, **`plugins/`** – Shared add-ons; update these before duplicating code elsewhere.

### Routing & Access Control

Web routes are split by role and loaded by `RouteServiceProvider`:

- `public.php` – unauthenticated
- `todos.php` – any authenticated user
- `profesor.php`, `alumno.php`, `direccion.php` (prefix `direccion/`), `administrador.php`, `conserge.php`, `mantenimiento.php` (prefix `mantenimiento/`), `jefeDpto.php` (prefix `depto/`) – role-gated via `role:{rolename}` middleware
- `api.php` – under `/api/` prefix, controllers in `Intranet\Http\Controllers\API`, authenticated with `auth:sanctum`

The `RoleMiddleware` enforces access via `userIsNameAllow($role)`. Controllers set `$this->perfil` to apply the middleware automatically.

### BaseController Pattern

`Core/BaseController` is the CRUD scaffold for grid-based admin screens. Set `$model` (entity name), `$gridFields`, `$vista`, `$titulo`, and optionally `$formFields`/`$modal`. It auto-builds a `Panel` grid, provides `index()`/`indice()`/`confirm()`, and auto-filters by `idProfesor` when that column exists on the model.

### API Controllers

Namespace: `Intranet\Http\Controllers\API`. Auth via Sanctum. Legacy token exchange: `POST /api/auth/exchange`.

### Frontend

- Vite (`vite.config.mjs`): `resources/assets/js` + `resources/assets/sass` → `public/`.
- Bootstrap 5 + Gentelella admin theme + Vue 3 (datepicker/select widgets) + Livewire 3.
- Blade views: `resources/views/`; layouts: `resources/views/layouts`; partials: `resources/views/intranet/partials`.
