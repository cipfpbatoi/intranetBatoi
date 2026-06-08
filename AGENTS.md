# Repository Guidelines

> **Font autoritzada compartida.** Aquest fitxer és la guia única per a qualsevol agent (Codex, Claude, Cursor, etc.). El coneixement de domini i les referències detallades viuen a [`docs/agents/`](docs/agents/) i s'enllacen al final.
>
> **Com està organitzat el repo per a agents i com s'usa al dia a dia:** [`docs/agents/tetris.md`](docs/agents/tetris.md) (les 4 peces, la Regla Zero, receptes per escenari i la regla de l'adaptador prim per a configs d'IA).

## Pre-flight Checklist

Completa estos passos **abans d'escriure codi**. Si en falta algun, retorna `Status: need_input` i indica exactament què manca.

- [ ] Llegir este fitxer (`AGENTS.md`) complet.
- [ ] Identificar el domini afectat i llegir el doc corresponent de [`docs/agents/`](docs/agents/).
  - FCT → [`docs/agents/fct/fct-map.md`](docs/agents/fct/fct-map.md)
  - Activitats → [`docs/agents/activitats/activitats-map.md`](docs/agents/activitats/activitats-map.md)
  - UI/graelles → [`docs/agents/ui/grid-datatables.md`](docs/agents/ui/grid-datatables.md)
  - Notificacions/correu → [`docs/agents/notificacions/notificacions-map.md`](docs/agents/notificacions/notificacions-map.md)
- [ ] Confirmar el bounded context: quin model/servei/controlador és el punt d'entrada.
- [ ] Si existeix un `spec.md` per al domini a [`specs/`](specs/), llegir-lo abans d'implementar.

## Regles d'execució

- Detecta l'stack aplicable (PHP/Laravel, Blade, Livewire, API Sanctum). Si és ambigu, fes **una sola pregunta** concreta; no suposies.
- Prioritza `docs/agents/` sobre el codi llegat quan hi haja conflicte de patrons.
- Si hi ha un `spec.md` per al domini, implementa estrictament el que descriu; no afegisques comportament no especificat.
- **Regla de les 3 vegades**: si una norma apareix en >3 prompts, pertany ací (AGENTS.md) o a `docs/agents/<domini>.md`.
- **Menys és més**: proporciona la informació mínima vital per a la tasca; no bolques tot el context.
- No implementes res fora de l'abast: sense refactors no sol·licitats, sense noves funcionalitats adjacents.

## Estructura del projecte i organització de mòduls

- `app/` conté la lògica de domini; les rutes web estan separades per rol a `routes/` (`public.php`, `todos.php`, `profesor.php`, `alumno.php`, `direccion.php`, `administrador.php`, `conserge.php`, `mantenimiento.php`, `jefeDpto.php`) i les rutes API a `routes/api.php` (JSON).
- Assets d'UI: `resources/views/` per a Blade, `resources/assets/js` i `resources/assets/sass` compilats via Mix cap a `public/`.
- Capes de dades: `database/migrations` i `database/seeders`; fitxers en execució a `storage/`; tests dividits entre `tests/Feature` i `tests/Unit`.
- Els add-ons compartits viuen a `packages/` i `plugins/`; actualitza'ls abans de duplicar codi.

## Arquitectura

### Namespace i càrrega automàtica

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

### Enrutament i control d'accés

Les rutes web estan separades per rol i carregades per `RouteServiceProvider`:

- `public.php` – no autenticat
- `todos.php` – qualsevol usuari autenticat
- `profesor.php`, `alumno.php`, `direccion.php` (prefix `direccion/`), `administrador.php`, `conserge.php`, `mantenimiento.php` (prefix `mantenimiento/`), `jefeDpto.php` (prefix `depto/`) – protegides per rol via middleware `role:{rolename}`
- `api.php` – prefix `/api/`, controladors en `Intranet\Http\Controllers\API`, autenticació `auth:sanctum`

El `RoleMiddleware` aplica l'accés via `userIsNameAllow($role)`. Els controladors fixen `$this->perfil` per aplicar el middleware automàticament.

### Patró BaseController

`Core/BaseController` és la base CRUD per a pantalles d'administració basades en graella. Defineix `$model` (nom d'entitat), `$gridFields`, `$vista`, `$titulo`, i opcionalment `$formFields`/`$modal`. Construeix una graella `Panel`, proveeix `index()`/`indice()`/`confirm()`, i filtra automàticament per `idProfesor` quan eixa columna existeix al model.

### Controladors API

Namespace: `Intranet\Http\Controllers\API`. Autenticació via Sanctum. Intercanvi de token llegat: `POST /api/auth/exchange`.

### Frontend

- Laravel Mix (`webpack.mix.js`): `resources/assets/js` + `resources/assets/sass` → `public/`.
- Bootstrap 4 + tema admin Gentelella + Vue 2 (widgets datepicker/select) + Livewire 3.
- Vistes Blade: `resources/views/`; layouts: `resources/views/layouts`; partials: `resources/views/intranet/partials`.

## Ordres de compilació, test i desenvolupament

- `composer install` per a dependències PHP (copia `.env.example`, executa `php artisan key:generate` després de clonar).
- Frontend: `npm install`; `npm run dev` compila una vegada, `npm run watch` recompila en cada canvi, `npm run production` genera bundles minificats (`NODE_OPTIONS=--openssl-legacy-provider`).
- `php artisan serve` arrenca l'app; `php artisan migrate --seed` prepara la BD.
- Tests amb `phpunit` o `php artisan test`; usa `--filter` per a apuntar casos concrets.
- Scripts Composer: `composer test:focus`, `composer test:quick` (Expediente|Empresa|Comision), `composer test:full`, `composer test:auth-migration`, `composer dusk:local`.
- Dusk (requereix app a `http://laravel.test`): `XDEBUG_MODE=off APP_URL=http://laravel.test DUSK_APP_URL=http://laravel.test php artisan dusk --env=local --filter=TestName`.
- Si PHP local no és disponible, executa els tests dins Docker amb `docker compose exec -T laravel.test php artisan test` (p. ex. `docker compose exec -T laravel.test php artisan test --filter=ApiGuardiaControllerFeatureTest`).
- Usa `docker compose ps` per confirmar que els contenidors estan en marxa abans de validar amb Docker; prefereix el servei `laravel.test` per a ordres PHP/Laravel.

## Estil de codi i convencions de noms

- PSR-12: indentació de 4 espais, noms de mètode clars; sufixa les classes amb `Controller`, `Job`, `Event`, `Policy` on escaiga.
- Tota classe modificada o nova ha d'incloure/actualitzar blocs `phpDoc` (`/** ... */`) per a la classe i els mètodes/propietats rellevants.
- Prefereix layouts/components Blade a `resources/views/layouts` i `resources/views/components`; manté les cadenes de text a `resources/lang`.
- Mantén JS/SCSS modular dins `resources/assets`; alinea els noms de classe amb el marcat Blade i afig nous punts d'entrada a `webpack.mix.js` quan calga.
- Alertes: `use Intranet\Services\UI\AppAlert as Alert;` (no `Styde\Html\Facades\Alert`).
- No dupliques lògica si ja existeix a `Application/*`, `Services/*`, `Finders/*` o `Presentation/*`.

## Directrius de testing

- PHPUnit configurat a `phpunit.xml` amb cobertura apuntada a `app/`. Crea fitxers `*Test.php` a `tests/Feature` per a tests HTTP/integració i a `tests/Unit` per a lògica pura.
- Usa factories/seeders i traits com `RefreshDatabase`; comprova respostes, events i efectes a la BD per a nous endpoints i policies.
- Executa la suite abans de cada PR i afig tests de regressió amb cada correcció de bug.

## Guia de commits i pull requests

- Prefija els títols de commit amb una etiqueta que categoritza el canvi:
  - `[MOD]` – Modificació de codi o fitxers existents.
  - `[ADD]` – Addició de fitxers nous o funcionalitats.
  - `[DEL]` – Eliminació de fitxers o codi.
  - `[FIX]` – Correcció de bug o error.
- Escriu títols imperatius i específics (p. ex. `[ADD] Exportació de calendari a events`, `[FIX] Null pointer en login d'usuari`); agrupa els canvis relacionats.
- **Abans de fer commit**: comprova si hi ha issues de GitHub relacionades amb `gh issue list` o `gh issue status`. Si els canvis resolen o afecten una issue, referencia-la al missatge (`#numero_issue`), p. ex. `[FIX] Null pointer en login d'usuari #42`.
- Per a múltiples issues relacionades, inclou totes les referències (p. ex. `[MOD] Refactoritza el flux d'autenticació #15 #23`).
- **No inclogues** `Co-Authored-By: Claude` ni atribució d'IA similar als missatges de commit.
- Els PRs han de resumir la intenció, enllaçar issues/tasques i llistar els tests executats (`phpunit`, `npm run production` quan canvien assets). Afig captures de pantalla per a canvis d'UI i menciona migracions o variables d'entorn noves.

## Seguretat i configuració

- No facis commit de secrets ni del `.env`. La cache de config/rutes (`php artisan config:cache`, `route:cache`) només és per a releases.
- Ajusta els drivers de cua/cache/correu al `.env`; prefereix Redis quan estiga disponible. Elimina PDFs generats o logs de `storage/` abans de compartir artefactes.

## Llengua

- Valencià per a tot text d'usuari, comentaris de codi i vistes (excepte plantilles ja explícitament bilingües).

## Índex de coneixement de domini (`docs/agents/`)

Coneixement de domini compartit per a tots els agents. Llig el fitxer rellevant abans de tocar el seu àrea. Índex complet: [`docs/agents/README.md`](docs/agents/README.md).

- [`docs/agents/tetris.md`](docs/agents/tetris.md) — mapa del repo i **guia d'ús**: les 4 peces, la Regla Zero, receptes per escenari i la regla de l'adaptador prim per a configs d'IA.
- [`docs/agents/conventions.md`](docs/agents/conventions.md) — convencions generals del repo (resum operatiu).
- [`docs/agents/testing-docker.md`](docs/agents/testing-docker.md) — execució de tests, scripts Composer, Selenium/Docker.
- [`docs/agents/ui/grid-datatables.md`](docs/agents/ui/grid-datatables.md) — graelles, Panel/Pestana, components Blade i DataTables.
- [`docs/agents/notificacions/notificacions-map.md`](docs/agents/notificacions/notificacions-map.md) — notificacions de panell, avisos, `MyMail` i reutilització de missatgeria.
- **FCT** (annexos, signatures, SAO):
  - [`docs/agents/fct/fct-map.md`](docs/agents/fct/fct-map.md) — rutes, controladors, entitats, vistes, correus.
  - [`docs/agents/fct/signatures.md`](docs/agents/fct/signatures.md) — flux `/signatura`, `sendTo`/`signed`, plantilles Annex I/II/III/V.
  - [`docs/agents/fct/sao-selenium.md`](docs/agents/fct/sao-selenium.md) — descàrregues SAO i depuració Selenium.
- **Activitats** (complementàries/extraescolars):
  - [`docs/agents/activitats/activitats-map.md`](docs/agents/activitats/activitats-map.md) — rutes, fitxers clau, camps llegats, coordinador, PDFs.

## Specs de comportament (`specs/`)

Especificacions BDD (Given/When/Then) per domini. Tecnologia-agnòstiques: defineixen **què** ha de passar, no com. Llig la spec del domini afectat abans d'implementar.

- [`specs/fct.md`](specs/fct.md) — FCT: signatures, enviament d'annexos, PDF, autorització.
- [`specs/activitats.md`](specs/activitats.md) — Activitats: creació, tipus/ubicació, coordinador, PDF.
- [`specs/comisions.md`](specs/comisions.md) — Comissions de servei: cicle d'estats, FCTs associades, PDF.
- [`specs/guardies.md`](specs/guardies.md) — Guàrdies: presència, panell `donde`, extraescolars, comissions.
- [`specs/horaris.md`](specs/horaris.md) — Horaris: canvi temporal, flux proposta JSON, bulk apply.

## Pipeline de revisió

La IA que genera el codi no el revisa. Usar agents de motors diferents com a revisor independent. Documentació: [`docs/agents/ia-review-pipeline.md`](docs/agents/ia-review-pipeline.md).

- Slash command revisor: `/ia-review [domini]` (`.claude/commands/ia-review.md`)
- Checklist manual: [`prompts/review-checklist.md`](prompts/review-checklist.md)

## Prompts reutilitzables (`prompts/`)

Plantilles per a tasques repetides (regla de les 3 vegades). Índex: [`prompts/README.md`](prompts/README.md).

## Configuracions específiques per motor

Les instruccions (fluxos, criteris, coneixement) viuen **una sola vegada** en una font canònica agnòstica (`docs/`, `specs/`, `prompts/`). Els fitxers de cada motor són **adaptadors prims** que només afigen el seu *glue* i apunten a eixa font. Detall i taula: [`docs/agents/tetris.md`](docs/agents/tetris.md) § «regla de l'adaptador prim».

- **Codex**: skills a `.codex/skills/` (`intranet-batoi-general`, `intranet-batoi-fct`, `intranet-batoi-activitats`, `openspec`).
- **Claude Code**: slash commands a `.claude/commands/` (`opsx-propose`, `opsx-apply`, `opsx-archive`, `ia-review`).
- **Altres** (Cursor, etc.): poden llegir les fonts canòniques directament.
