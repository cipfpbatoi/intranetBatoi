# Arquitectura actual de l'aplicació

Este document descriu l'aplicació tal com està implementada en la branca
principal. És el punt d'entrada tècnic; els detalls de cada domini viuen en
[`docs/agents/`](../agents/README.md) i el comportament esperat, en
[`specs/`](../../specs/).

## Stack

- PHP 8.2 o superior i Laravel 13, amb namespace arrel `Intranet\`.
- Eloquent per a persistència.
- Blade i Bootstrap 5.3 per a la interfície renderitzada al servidor.
- Livewire 3 per a panells reactius.
- Vue 3 per als punts d'entrada que necessiten components de client.
- Vite 5 per compilar JavaScript i Sass.
- PHPUnit per als tests PHP i Dusk per als fluxos de navegador.

El frontend conserva un [runtime híbrid](js-runtime-hibrid.md) perquè encara
conviuen pantalles modernes amb JavaScript llegat.

## Flux d'una petició web

```text
Navegador
  -> ruta del perfil (`routes/*.php`)
  -> middleware web i de rol
  -> controlador o component Livewire
  -> servei d'aplicació/domini quan existeix
  -> entitats Eloquent i serveis transversals
  -> vista Blade o resposta JSON/PDF
```

Els controladors antics encara poden accedir directament a Eloquent. El codi
nou o extret ha de preferir `app/Application`, `app/Domain`, `app/Finders` i
`app/Services` abans de duplicar lògica.

## Rutes i control d'accés

`app/Providers/RouteServiceProvider.php` carrega les rutes per perfil. El
middleware `RoleMiddleware` resol l'usuari autenticat, comprova el rol requerit
i permet sempre l'accés a administració.

| Fitxer | Prefix | Accés principal |
|---|---|---|
| `routes/public.php` | — | Sense autenticar |
| `routes/todos.php` | — | Qualsevol usuari autenticat |
| `routes/profesor.php` | — | Professorat i perfils docents compostos |
| `routes/jefeDpto.php` | `/depto` | Reservat per a caps de departament; actualment no declara rutes pròpies |
| `routes/direccion.php` | `/direccion` | Direcció |
| `routes/administrador.php` | — | Administració |
| `routes/alumno.php` | `/alumno` | Alumnat |
| `routes/conserge.php` | — | Consergeria |
| `routes/mantenimiento.php` | `/mantenimiento` | Manteniment |
| `routes/api.php` | `/api` | API; autenticació segons l'endpoint |

Els rols es defineixen com a factors primers en `config/roles.php`. Un usuari
pot acumular-ne diversos en un únic valor; `userIsAllow()`, `rolesUser()` i
`esRol()` comproven la divisibilitat del valor compost. Per això no s'ha de
comparar directament el camp `rol` amb un únic número.

## Menú i ajuda contextual

El menú lateral és dinàmic:

1. `MenuService::make('general')` consulta els registres actius de la taula
   `menus`.
2. Els elements pare es filtren pels rols de l'usuari.
3. Els elements es tradueixen amb les claus `messages.menu.*`.
4. El resultat es guarda en memòria cau per menú i DNI durant un dia.
5. Els layouts renderitzen l'arbre resultant en la barra lateral.

Una ruta pot existir i no aparéixer en el menú. Per documentar la interfície
real cal contrastar sempre la taula `menus` de l'entorn desplegat amb
`php artisan route:list`.

El camp `ajuda` de cada registre de menú enllaça una pantalla amb un manual.
`existsHelp()` busca l'ajuda associada a l'URL actual i els layouts obrin:

```text
https://cipfpbatoi.github.io/intranetBatoi/manuals/{ajuda}
```

Per tant, si es crea o es mou un manual, també s'ha de revisar el valor
`menus.ajuda` corresponent.

## Capes del backend

| Capa | Responsabilitat |
|---|---|
| `app/Entities` | Models Eloquent, relacions, accessors i compatibilitat llegada |
| `app/Domain` | Contractes i fronteres dels dominis extrets |
| `app/Application` | Casos d'ús i orquestració de cada domini |
| `app/Http/Controllers` | Entrada HTTP, validació, autorització i resposta |
| `app/Livewire` | Estat i accions dels panells reactius |
| `app/Finders` | Consultes reutilitzables |
| `app/Services` | Capacitats transversals: autenticació, documents, correu, notificacions, escola, signatures i UI |
| `app/UI` | Panells, pestanyes i botons de les pantalles operatives |
| `app/Policies` | Autorització sobre entitats |
| `app/Sao` | Integració i automatització amb SAO |

## Interfície web

Hi ha dos patrons principals:

- `Core/BaseController` i `UI/Panels/Panel` per a CRUD i graelles operatives;
- components Livewire i vistes `resources/views/livewire` per als panells
  reactius.

Els layouts conviuen en `resources/views/layouts` i
`resources/views/components/layouts`. Les taules compartides estan en
`resources/views/components/grid`. Les regles específiques de graelles i
DataTables estan documentades en
[`docs/agents/ui/grid-datatables.md`](../agents/ui/grid-datatables.md).

## API i autenticació

Les rutes API usen el prefix `/api` i controladors de
`Intranet\Http\Controllers\API`. L'aplicació usa Sanctum 4 i manté punts de
compatibilitat amb tokens llegats. Qualsevol canvi d'autenticació ha de
comprovar tant els clients web com els consumidors de l'API.

## Assets

Les fonts viuen en `resources/assets/js` i `resources/assets/sass`.
`vite.config.mjs` declara els punts d'entrada i Vite genera els bundles. No
s'han d'editar manualment els assets compilats de `public/` quan existeix una
font equivalent en `resources/assets`.

## Documentació relacionada

- [Mapa de pantalles del professorat](mapa-pantalles-professorat.md)
- [Esquema de base de dades](bbdd-esquema.md)
- [Swagger/API](swagger-api.md)
- [Importació de dades](import-flow.md)
- [Setup local](../operations/setup.md)
- [Tests i Docker](../agents/testing-docker.md)
- [Manuals per al professorat](../manuals/README.md)

## Manteniment

Quan canvie una pantalla s'ha de revisar, com a mínim:

1. el manual funcional i la captura associada;
2. el mapa tècnic de pantalles;
3. la documentació de domini i la spec, si canvia el comportament;
4. el valor `menus.ajuda`, si canvia el fitxer o l'àncora de l'ajuda;
5. els tests del flux afectat.
