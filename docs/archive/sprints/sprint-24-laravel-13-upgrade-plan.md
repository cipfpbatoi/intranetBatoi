# Sprint 24 - Upgrade a Laravel 13

Issue remota:
- `#131` https://github.com/cipfpbatoi/intranetBatoi/issues/131

Data d'arranc:
- `2026-03-23`

Branca de treball:
- `sprint-24-laravel-13-upgrade`

## Objectiu

Preparar i executar la migració del projecte de `Laravel 12.53.0` a `Laravel 13` dins d'una branca aïllada, reduint el risc sobre `Laravel12` i deixant traçat quins paquets i quins punts del codi condicionen el canvi.

## Estat actual

- el projecte corre ara mateix sobre `Laravel 12.53.0`
- no hi havia encara document local específic d'Sprint 24
- la migració es farà en una branca nova per poder provar `composer`, codi i regressions sense bloquejar la línia principal

## Restriccions reals detectades

No és prudent fer un `composer update` a cegues sobre `laravel/framework:^13` perquè el `lock` actual ja mostra dependències que encara no declaren compatibilitat amb `Laravel 13`.

Els punts més sensibles detectats en el `composer.lock` i en els `path repositories` locals són:

- `barryvdh/laravel-dompdf v3.1.1`
  - declara `illuminate/support: ^9|^10|^11|^12`
- `barryvdh/laravel-snappy v1.0.4`
  - declara `illuminate/filesystem` i `illuminate/support` fins a `^12`
- `darkaonline/l5-swagger 9.0.1`
  - declara `laravel/framework: ^11.0 || ^12.0`
- `laravel/ui v4.6.1`
  - declara components `illuminate/*` fins a `^12`
- `laravelcollective/html 6.4.0`
  - és un `path repository` local en [`packages/laravelcollective/html/composer.json`](/Users/igomis/Code/intranetBatoi/packages/laravelcollective/html/composer.json)
  - declara `illuminate/*` fins a `^12`
- `livewire/livewire v3.7.3`
  - declara `illuminate/*` per a `^10|^11|^12`
- `maatwebsite/excel 3.1.67`
  - declara `illuminate/support` fins a `^12`

## Paquets que no semblen bloquejar d'entrada

- `laravel/sanctum v4.3.1`
  - ja declara compatibilitat amb `^13`
- `laravel/socialite v5.24.3`
  - ja declara compatibilitat amb `^13`
- `laravel/browser-kit-testing v7.2.7`
  - ja declara compatibilitat amb `^13`
- `laravel/dusk v8.3.6`
  - ja declara compatibilitat amb `^13`
- `nunomaduro/collision v8.9.1`
  - no entra en conflicte amb `Laravel 13`

## Impacte funcional esperable

Els riscos no estan només en `composer.json`. El projecte depén fortament de peces que solen notar els upgrades:

- autenticació híbrida `auth:api,sanctum` en [`routes/api.php`](/Users/igomis/Code/intranetBatoi/routes/api.php)
- frontend legacy amb `laravel/ui`, `Vue 2`, `Livewire 3` i jQuery convivint
- generació documental i PDF amb `dompdf`, `snappy` i fluxos documentals propis
- formularis i helpers HTML via `laravelcollective/html` i el paquet local `igomis/laravel-html`
- documentació OpenAPI amb `l5-swagger`

## Hipòtesi de treball

La migració és viable, però abans cal resoldre un d'estos escenaris per als paquets bloquejants:

1. actualitzar a versions ja compatibles amb `Laravel 13`
2. ampliar temporalment constraints en paquets locals o forkejats quan el codi siga realment compatible
3. substituir paquets si no tenen línia clara de compatibilitat

El cas més delicat és `laravelcollective/html`, perquè ací no és només una dependència externa: hi ha un repositori local que el sobreescriu i un segon paquet local (`igomis/laravel-html`) que en depén directament.

## Tall A. Preparació de dependències

- revisar si existeixen versions compatibles amb `Laravel 13` per a:
  - `livewire/livewire`
  - `maatwebsite/excel`
  - `barryvdh/laravel-dompdf`
  - `barryvdh/laravel-snappy`
  - `darkaonline/l5-swagger`
  - `laravel/ui`
- decidir si `laravel/ui` continua sent necessari o si només manté scaffolding ja fossilitzat
- preparar ajust del paquet local [`packages/laravelcollective/html/composer.json`](/Users/igomis/Code/intranetBatoi/packages/laravelcollective/html/composer.json) si el codi és compatible i només bloqueja per constraints

## Tall B. Prova controlada d'upgrade

- canviar `laravel/framework` a `^13.0`
- mantindre `php` en `^8.2` si `Laravel 13` ho continua admetent
- executar primer un `composer update` focalitzat en la branca nova
- capturar exactament quins conflictes persistixen després dels primers ajustos de constraints

## Tall C. Verificació mínima després de resoldre Composer

- `php artisan --version`
- `php artisan test --filter=ApiEditResourceFeatureTest`
- `php artisan test --filter=ApiIncidenciaEditResourceFeatureTest`
- `php artisan test --filter=ApiAlumnoFctControllerFeatureTest`
- `php artisan test --filter=AuthProfesorLoginControllerFeatureTest`
- una passada curta de rutes API autenticades amb `Sanctum`

## Tall D. Àrees a vigilar quan compile

- providers i autodiscovery de paquets
- middlewares d'autenticació híbrida
- `Livewire` i components de Direcció
- generació PDF
- documentació `OpenAPI`
- helpers/form builders de `Collective` i `Styde`

## Resultat esperat del primer tall

- una branca pròpia d'Sprint 24
- un inventari explícit de paquets bloquejants
- una primera iteració d'ajust de `composer.json`
- conflictes residuals reduïts a una llista curta i accionable

## Resultat executat

En este primer tall ja s'ha pogut executar l'upgrade de dependències fins a `Laravel 13.1.1` dins de la branca [`sprint-24-laravel-13-upgrade`](/Users/igomis/Code/intranetBatoi).

Canvis de constraints aplicats:

- `laravel/framework` -> `^13.0`
- `darkaonline/l5-swagger` -> `^11.0`
- `fedeisas/laravel-mail-css-inliner` -> `^6.0`
- `livewire/livewire` -> `^3.7.11`
- `barryvdh/laravel-debugbar` -> `^4.1`
- `laravel/tinker` -> `^3.0`
- ampliació de compatibilitat a `^13.0` en el paquet local [`packages/laravelcollective/html/composer.json`](/Users/igomis/Code/intranetBatoi/packages/laravelcollective/html/composer.json)

Resultat de l'update real de Composer:

- `laravel/framework` actualitzat a `13.1.1`
- `l5-swagger` actualitzat a `11.0.0`
- `livewire` actualitzat a `3.7.11`
- `maatwebsite/excel` actualitzat a `3.1.68`
- `barryvdh/laravel-dompdf` actualitzat a `3.1.2`
- `barryvdh/laravel-snappy` actualitzat a `1.0.5`
- `barryvdh/laravel-debugbar` actualitzat a `4.1.3`
- `fedeisas/laravel-mail-css-inliner` actualitzat a `6.0.0`
- `laravel/tinker` actualitzat a `3.0.0`
- `laravel/ui` actualitzat a `4.6.3`
- `doctrine/annotations` eliminat per l'entrada de `swagger-php 6`
- publicació d'assets de `Livewire` refeta via `vendor:publish --tag=livewire:assets`

## Validació actual

- `php artisan --version`
  - resultat: `Laravel Framework 13.1.1`
- `php artisan test --filter='ApiEditResourceFeatureTest|ApiIncidenciaEditResourceFeatureTest|ApiAlumnoFctControllerFeatureTest|AuthProfesorLoginControllerFeatureTest'`
  - resultat: `16` proves passades
  - resultat: `100` assertions
- `php artisan test --filter='ApiAuthTokenExchangeFeatureTest|ApiColaboracionControllerFeatureTest|ApiDropZoneControllerFeatureTest|LegacyApiTokenDeprecationMiddlewareTest|ApiResourceControllerEditFallbackFeatureTest|ApiCursoEditResourceFeatureTest|ApiLegacyCatalogEditResourceFeatureTest|ApiTaskEditResourceFeatureTest|AuthProfesorAccessFeatureTest|ApiMaterialControllerFeatureTest'`
  - resultat: `28` proves passades
  - resultat: `126` assertions
- `php artisan test --filter='ComisionDireccionPanelTest|ActividadDireccionPanelTest|ExpedienteDireccionPanelTest|FaltaDireccionPanelTest'`
  - resultat: `24` proves passades
  - resultat: `123` assertions
- `php artisan l5-swagger:generate`
  - resultat: generació correcta de documentació `OpenAPI`

## Validació descartada com a criteri de regressió

S'ha executat també una passada de `Dusk`, però **no es considera criteri fiable de regressió per a este sprint**.

Motiu:

- la suite feia temps que no es corria de manera regular
- una part important depén fortament d'entorn Docker/Selenium/MySQL
- en host local ni tan sols era executable de forma vàlida per la configuració de `.env.dusk.local`
- dins del contenidor sí que arranca, però les fallades observades no es poden atribuir automàticament a `Laravel 13` perquè no hi ha una línia base recent verda prèvia

Per tant, per al tancament tècnic d'Sprint 24:

- les `Feature` passades i la generació `OpenAPI` sí compten com a validació fiable
- la suite `Dusk` queda explícitament fora del tall de validació fiable fins que es pose al dia en un sprint específic

## Estat de Dusk en este tall

Observacions útils deixades pel tall:

- `Dusk` en host local fallava per entorn (`DB_HOST=mysql` fora del contenidor)
- `Dusk` dins de `docker compose exec laravel.test ...` sí arranca
- `Selenium` respon dins del contenidor
- hi ha fallades reals en suites com [`ApiAuthCoexistenceTest.php`](/Users/igomis/Code/intranetBatoi/tests/Browser/ApiAuthCoexistenceTest.php) i [`ApiPendingAuthFlowTest.php`](/Users/igomis/Code/intranetBatoi/tests/Browser/ApiPendingAuthFlowTest.php), però es tracten com a deute de manteniment de la suite, no com a prova concloent de regressió del framework

## Següent tall natural

- executar alguna passada addicional de proves `Feature` transversals i algun tall de `Dusk` si és viable
- revisar si hi ha deprecacions o canvis menors de framework fora de la capa de dependències
- validar especialment:
  - autenticació híbrida `api` + `sanctum`
  - `Livewire` de Direcció
  - generació PDF
  - `OpenAPI` amb `l5-swagger`
  - fluxos legacy que continuen depenent de `laravel/ui`

## Criteri pràctic de prioritat

Amb l'Sprint 25 pendent de producció i l'Sprint 26 orientat sobretot a disseny, este és ara mateix el sprint tècnicament més executable i amb millor retorn per avançar en paral·lel sense tocar operativa viva.
