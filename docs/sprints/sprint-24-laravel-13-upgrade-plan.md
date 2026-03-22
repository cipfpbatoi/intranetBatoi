# Sprint 24 - Preparació de migració a Laravel 13

Issue remot:
- pendent de crear

## Objectiu

Preparar el projecte per a una migració controlada de Laravel 12 a Laravel 13, reduint risc de bloqueig per dependències, canvis de framework i regressions transversals.

## Estat actual

- Laravel actual: `12.53.0`
- PHP requerit en projecte: `^8.2`
- plataforma Composer fixada a PHP `8.3.1`
- stack frontend amb Vite, Bootstrap 5, Livewire 3 i peces legacy amb jQuery

## Problema actual

Una pujada directa de versió pot fallar per diverses vies alhora:

- incompatibilitats de paquets Composer
- canvis de contracte en framework, auth, consola i testing
- acoblament amb paquets locals:
  - `packages/html`
  - `packages/laravelcollective/html`
- coexistència de frontend modern i legacy
- cobertura desigual segons domini

## Principi de disseny

- no fer la migració com un únic canvi “gran”
- separar:
  - preparació
  - compatibilitat de dependències
  - upgrade del framework
  - remat de regressions
- pujar amb contractes explícits i proves en les zones més sensibles

## Tall A. Inventari de compatibilitat Composer

- revisar dependències que poden bloquejar Laravel 13:
  - `laravel/framework`
  - `laravel/sanctum`
  - `laravel/ui`
  - `livewire/livewire`
  - `laravelcollective/html`
  - `darkaonline/l5-swagger`
  - `laravel/dusk`
  - `laravel/browser-kit-testing`
  - `nunomaduro/collision`
- revisar també paquets Symfony ja fixats a `^7.0`
- identificar si algun paquet local depén d'APIs internes de Laravel 12

## Tall B. Paquets locals i extensió de formularis

- auditar:
  - [`packages/html`](/Users/igomis/Code/intranetBatoi/packages/html)
  - [`packages/laravelcollective/html`](/Users/igomis/Code/intranetBatoi/packages/laravelcollective/html)
- verificar:
  - service providers
  - facades
  - helpers de formulari
  - rendering custom de camps
- confirmar si necessiten:
  - adaptació de namespaces
  - canvis en signatures
  - canvis en tests interns

## Tall C. Superfícies de risc dins de l'aplicació

- prioritzar revisió de:
  - autenticació web + API
  - Sanctum
  - Livewire 3
  - middleware personalitzats
  - controllers API base
  - renderitzat de formularis
  - cues, notificacions i mail
  - documentació PDF i exportacions

## Tall D. Cobertura abans d'upgrade

- reforçar proves sobre zones amb més impacte:
  - login professor
  - endpoints API principals
  - modals CRUD
  - fluxos FCT / colaboració
  - comissió
  - expedient
- deixar almenys una smoke suite curta per validar després del `composer update`

## Tall E. Upgrade tècnic

- pujar primer constraints de Composer
- executar resolució de dependències
- adaptar fitxers base del framework si canvien:
  - bootstrap
  - excepcions
  - config
  - kernel / middleware
  - providers
- revisar deprecacions resoltes en Laravel 12 que passen a ser ruptura en 13

## Tall F. Regressió funcional

- executar:
  - proves unitàries
  - proves feature prioritàries
  - smoke manual de CRUD modal
  - smoke d'autenticació
  - smoke Livewire
- validar especialment:
  - sessions
  - CSRF
  - validació
  - serialització JSON
  - renderitzat Blade/components

## Tall G. Tancament

- documentar:
  - paquets actualitzats
  - trencaments trobats
  - hacks temporals si n'hi hagueren
  - backlog residual post-upgrade

## Ordre recomanat

1. inventari de compatibilitat Composer
2. auditoria de paquets locals
3. reforç mínim de proves
4. pujada de constraints i resolució d'errors de framework
5. regressió funcional
6. documentació final i backlog residual

## Resultat esperat

- camí clar i acotat per a migrar a Laravel 13
- menys sorpreses en dependències i paquets locals
- millor capacitat de validar el sistema després de l'upgrade
- base més segura per a futurs upgrades menors de l'ecosistema
