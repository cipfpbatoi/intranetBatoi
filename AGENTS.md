# Repository Guidelines

## Project Structure & Module Organization
- `app/` contains domain logic; routes live in `routes/web.php` (HTML) and `routes/api.php` (JSON).
- UI assets: `resources/views/` for Blade, `resources/assets/js` and `resources/assets/sass` compiled via Mix into `public/`.
- Data layers: `database/migrations` and `database/seeders`; runtime files in `storage/`; tests split into `tests/Feature` and `tests/Unit`.
- Shared add-ons live in `packages/` and `plugins/`; prefer updating these before duplicating code.

## Build, Test, and Development Commands
- `composer install` for PHP deps (copy `.env.example`, run `php artisan key:generate` after cloning).
- Front-end setup with `npm install`; `npm run dev` builds once, `npm run watch` rebuilds on change, `npm run production` creates minified bundles.
- `php artisan serve` runs the app; `php artisan migrate --seed` prepares the DB.
- Test with `phpunit` or `php artisan test`; use `--filter` to target cases.

## Coding Style & Naming Conventions
- PSR-12: 4-space indent, clear method names; suffix classes with `Controller`, `Job`, `Event`, `Policy` where applicable.
- Every modified or newly created class must include/update `phpDoc` documentation blocks using standard PHPDoc nomenclature (`/** ... */`) for class and relevant methods/properties.
- Prefer Blade layouts/components under `resources/views/layouts` and `resources/views/components`; keep strings in `resources/lang`.
- Keep JS/SCSS modular inside `resources/assets`; align class names with Blade markup and add new Mix entry points in `webpack.mix.js` when needed.

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
- Valencià
