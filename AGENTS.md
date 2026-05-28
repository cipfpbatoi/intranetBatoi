# Repository Guidelines

> **Font autoritzada compartida.** Aquest fitxer és la guia única per a qualsevol agent (Codex, Claude, Cursor, etc.). El coneixement de domini i les referències detallades viuen a [`docs/agents/`](docs/agents/) i s'enllacen al final.

## Project Structure & Module Organization
- `app/` contains domain logic; web routes are split by role in `routes/` (`public.php`, `todos.php`, `profesor.php`, `alumno.php`, `direccion.php`, `administrador.php`, `conserge.php`, `mantenimiento.php`, `jefeDpto.php`) and API routes in `routes/api.php` (JSON).
- UI assets: `resources/views/` for Blade, `resources/assets/js` and `resources/assets/sass` compiled via Mix into `public/`.
- Data layers: `database/migrations` and `database/seeders`; runtime files in `storage/`; tests split into `tests/Feature` and `tests/Unit`.
- Shared add-ons live in `packages/` and `plugins/`; prefer updating these before duplicating code.

## Architecture

### Namespace & Autoloading
El namespace arrel és `Intranet\` (no el `App\` per defecte). Tot el codi viu en `app/` i es resol sota `Intranet\`.

### Capes

- **`app/Entities/`** – Models Eloquent. Es referencien per nom via `$this->model` en controladors, auto-resolts sota `Intranet\Entities\`.
- **`app/Domain/`** – Fronteres de domini (AlumnoFct, Comision, Empresa, Expediente, FaltaProfesor, Fct, Grupo, Horario, Profesor), cadascuna amb una interfície de repositori.
- **`app/Application/`** – Serveis/casos d'ús per domini (reflecteix `app/Domain/`). Exemple: `app/Application/Expediente/ExpedienteService.php`.
- **`app/Http/Controllers/`** – Controladors per rol/feature. `Controller.php` és la base (afig el trait `FindsModel`, `$model`/`$perfil`). `Core/BaseController.php` l'estén per a pantalles CRUD basades en graella.
- **`app/Services/`** – Serveis transversals (Auth, Calendar, Document, HR, Mail, Media, Notifications, School, Signature, UI, etc.).
- **`app/UI/`** – Renderitzat de panells i botons (`UI/Panels/Panel`, `UI/Botones/`).
- **`app/Policies/`** – Policies de Laravel per entitat, registrades a `AuthServiceProvider`.
- **`app/Livewire/`** – Components Livewire 3 per a UI reactiva.
- **`app/Sao/`** – Accions d'integració amb el sistema extern SAO.
- **`app/Support/Helpers/`** – Helpers globals carregats per `HelperServiceProvider`: `MyHelpers.php`, `DateHelpers.php`. Funcions clau: `authUser()`, `userIsNameAllow($role)`, `isAdmin()`.
- **`app/Finders/`** – Classes query-builder per a consultes filtrades habituals.
- **`packages/`**, **`plugins/`** – Add-ons compartits; actualitza-ho abans de duplicar codi.

### Routing & Access Control

Les rutes web estan separades per rol i carregades per `RouteServiceProvider`:

- `public.php` – no autenticat
- `todos.php` – qualsevol usuari autenticat
- `profesor.php`, `alumno.php`, `direccion.php` (prefix `direccion/`), `administrador.php`, `conserge.php`, `mantenimiento.php` (prefix `mantenimiento/`), `jefeDpto.php` (prefix `depto/`) – protegides per rol via middleware `role:{rolename}`
- `api.php` – prefix `/api/`, controladors en `Intranet\Http\Controllers\API`, autenticació `auth:sanctum`

El `RoleMiddleware` aplica l'accés via `userIsNameAllow($role)`. Els controladors fixen `$this->perfil` per aplicar el middleware automàticament.

### BaseController Pattern

`Core/BaseController` és la base CRUD per a pantalles d'administració basades en graella. Defineix `$model` (nom d'entitat), `$gridFields`, `$vista`, `$titulo`, i opcionalment `$formFields`/`$modal`. Construeix una graella `Panel`, proveeix `index()`/`indice()`/`confirm()`, i filtra automàticament per `idProfesor` quan eixa columna existeix al model.

### API Controllers

Namespace: `Intranet\Http\Controllers\API`. Auth via Sanctum. Intercanvi de token llegat: `POST /api/auth/exchange`.

### Frontend

- Laravel Mix (`webpack.mix.js`): `resources/assets/js` + `resources/assets/sass` → `public/`.
- Bootstrap 4 + tema admin Gentelella + Vue 2 (widgets datepicker/select) + Livewire 3.
- Vistes Blade: `resources/views/`; layouts: `resources/views/layouts`; partials: `resources/views/intranet/partials`.

## Build, Test, and Development Commands
- `composer install` for PHP deps (copy `.env.example`, run `php artisan key:generate` after cloning).
- Front-end setup with `npm install`; `npm run dev` builds once, `npm run watch` rebuilds on change, `npm run production` creates minified bundles (`NODE_OPTIONS=--openssl-legacy-provider`).
- `php artisan serve` runs the app; `php artisan migrate --seed` prepares the DB.
- Test with `phpunit` or `php artisan test`; use `--filter` to target cases.
- Composer scripts: `composer test:focus`, `composer test:quick` (Expediente|Empresa|Comision), `composer test:full`, `composer test:auth-migration`, `composer dusk:local`.
- Dusk (requereix app a `http://laravel.test`): `XDEBUG_MODE=off APP_URL=http://laravel.test DUSK_APP_URL=http://laravel.test php artisan dusk --env=local --filter=TestName`.
- If local PHP is unavailable, run tests inside Docker with `docker compose exec -T laravel.test php artisan test` (for example `docker compose exec -T laravel.test php artisan test --filter=ApiGuardiaControllerFeatureTest`).
- Use `docker compose ps` to confirm the project containers are running before Docker-based validation; prefer the `laravel.test` service for PHP/Laravel commands.

## Coding Style & Naming Conventions
- PSR-12: 4-space indent, clear method names; suffix classes with `Controller`, `Job`, `Event`, `Policy` where applicable.
- Every modified or newly created class must include/update `phpDoc` documentation blocks using standard PHPDoc nomenclature (`/** ... */`) for class and relevant methods/properties.
- Prefer Blade layouts/components under `resources/views/layouts` and `resources/views/components`; keep strings in `resources/lang`.
- Keep JS/SCSS modular inside `resources/assets`; align class names with Blade markup and add new Mix entry points in `webpack.mix.js` when needed.
- Alertes: `use Intranet\Services\UI\AppAlert as Alert;` (no `Styde\Html\Facades\Alert`).
- No dupliques lògica si ja existeix a `Application/*`, `Services/*`, `Finders/*` o `Presentation/*`.

## Testing Guidelines
- PHPUnit configured in `phpunit.xml` with coverage aimed at `app/`. Create `*Test.php` files under `tests/Feature` for HTTP/integration and `tests/Unit` for pure logic.
- Use factories/seeders and traits like `RefreshDatabase`; assert responses, events, and DB effects for new endpoints and policies.
- Run the suite before PRs and add regression tests with every bug fix.

## Commit & Pull Request Guidelines
- Prefix commit titles with a tag that categorizes the change:
  - `[MOD]` – Modification of existing code or files.
  - `[ADD]` – Addition of new files or features.
  - `[DEL]` – Deletion of files or code.
  - `[FIX]` – Bug fix or error correction.
- Write imperative, specific commit titles after the tag (e.g., `[ADD] Calendar export to events`, `[FIX] Null pointer in user login`); keep related changes together.
- **Before committing**: Check if there are related GitHub issues using `gh issue list` or `gh issue status`. If your changes address or relate to an issue, reference it in the commit message using `#issue_number` for traceability (e.g., `[FIX] Null pointer in user login #42`).
- For multiple related issues, include all references (e.g., `[MOD] Refactor authentication flow #15 #23`).
- **Do not include** `Co-Authored-By: Claude` or similar AI attribution in commit messages.
- PRs should summarize intent, link issues/tasks, and list tests run (`phpunit`, `npm run production` when assets change). Add screenshots for UI updates and call out migrations or new env vars.

## Security & Configuration Tips
- Never commit secrets or `.env`. Cache config/routes (`php artisan config:cache`, `route:cache`) only for releases.
- Match queue/cache/mail drivers to `.env`; prefer Redis when available. Drop generated PDFs or logs from `storage/` before sharing artifacts.

## Language
- Valencià per a tot text d'usuari, comentaris de codi i vistes (excepte plantilles ja explícitament bilingües).

## Domain Knowledge Index (`docs/agents/`)

Coneixement de domini compartit per a tots els agents. Llig el fitxer rellevant abans de tocar el seu àrea:

- [`docs/agents/conventions.md`](docs/agents/conventions.md) — convencions generals del repo (resum operatiu).
- [`docs/agents/testing-docker.md`](docs/agents/testing-docker.md) — execució de tests, scripts Composer, Selenium/Docker.
- **FCT** (annexos, signatures, SAO):
  - [`docs/agents/fct/fct-map.md`](docs/agents/fct/fct-map.md) — rutes, controladors, entitats, vistes, correus.
  - [`docs/agents/fct/signatures.md`](docs/agents/fct/signatures.md) — flux `/signatura`, `sendTo`/`signed`, plantilles Annex I/II/III/V.
  - [`docs/agents/fct/sao-selenium.md`](docs/agents/fct/sao-selenium.md) — descàrregues SAO i depuració Selenium.
- **Activitats** (complementàries/extraescolars):
  - [`docs/agents/activitats/activitats-map.md`](docs/agents/activitats/activitats-map.md) — rutes, fitxers clau, camps llegats, coordinador, PDFs.

> Per a Codex, aquestes fonts s'invoquen via les skills de `.codex/skills/` (`intranet-batoi-general`, `intranet-batoi-fct`, `intranet-batoi-activitats`), que són embolcalls lleugers cap als fitxers anteriors. Altres agents (Claude, Cursor, etc.) poden llegir-los directament.
