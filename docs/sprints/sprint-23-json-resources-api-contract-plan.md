# Sprint 23 - Contracte API amb JsonResource

Issue remot:
- `#125` https://github.com/cipfpbatoi/intranetBatoi/issues/125

## Objectiu

Passar dels payloads implícits construïts en controladors API a contractes d'eixida explícits amb `JsonResource`.

## Problema actual

Encara hi ha endpoints API que:

- retornen el model directament
- confien en accessors del model com a contracte d'eixida
- barregen negoci HTTP i serialització dins del controlador
- deixen `edit()` com un payload no documentat i difícil de mantindre

El cas més clar és [`ApiResourceController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ApiResourceController.php), que encara tenia `edit()` com a fallback genèric.

## Principi de disseny

- cada payload d'API que tinga contracte propi ha de tindre `JsonResource` propi
- `show()` i `edit()` no són necessàriament el mateix contracte
- `edit()` ha de respondre al formulari/modal que el consumeix, no al model Eloquent
- el controlador base només ha de coordinar resolució i resposta, no decidir el payload camp a camp

## Jerarquia de contractes

En els fluxos migrats, la responsabilitat queda separada així:

- `FORM_FIELDS`
  - contracte de formulari/modal
  - definix quins camps necessita la UI
- `EditResource`
  - contracte HTTP d'eixida per a `edit()`
  - definix quins camps ixen per API i amb quin nom
- `inputTypes`
  - capa legacy de normalització/persistència
  - continua sent útil per a `fillAll()` i per al tractament de `checkbox`, `date`, `datetime`, `time`, `select`
  - ja no es considera la font principal del contracte API

El criteri del sprint és no tornar a carregar `inputTypes` amb responsabilitats de serialització HTTP. Quan hi haja divergència entre `FORM_FIELDS` i `inputTypes`, el contracte prioritari per a modals passa a ser:

1. `FORM_FIELDS`
2. `EditResource`
3. `inputTypes` només com a suport transitori del model legacy

## Pilot inicial

El pilot del sprint se centra en quatre dominis on `edit()` té impacte directe en modals legacy:

- [`ActividadController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ActividadController.php)
- [`ComisionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ComisionController.php)
- [`ExpedienteController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ExpedienteController.php)
- [`AlumnoFctController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/AlumnoFctController.php)

Després del pilot inicial, s'ha ampliat també a:

- [`CursoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/CursoController.php)
- [`TipoActividadController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/TipoActividadController.php)
- [`TipoIncidenciaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/TipoIncidenciaController.php)
- [`TaskController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/TaskController.php)
- [`IncidenciaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/IncidenciaController.php)

## Tall A. Contracte explícit per a edit()

- afegir recursos específics:
  - [`ActividadEditResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/ActividadEditResource.php)
  - [`ComisionEditResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/ComisionEditResource.php)
  - [`ExpedienteEditResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/ExpedienteEditResource.php)
  - [`AlumnoFctEditResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/AlumnoFctEditResource.php)
  - [`CursoEditResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/CursoEditResource.php)
  - [`TipoActividadEditResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/TipoActividadEditResource.php)
  - [`TipoIncidenciaEditResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/TipoIncidenciaEditResource.php)
  - [`TaskEditResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/TaskEditResource.php)
  - [`IncidenciaEditResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/IncidenciaEditResource.php)
- fer explícit en cada controlador si usa `editResource`

## Tall B. Coordinació en el controlador base

- afegir suport a `editResource` en [`ApiResourceController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ApiResourceController.php)
- mantindre fallback genèric per als endpoints encara no migrats
- deixar el fallback genèric traçable en logs per facilitar la migració progressiva
- evitar una migració massiva en un sol tall

## Tall C. Cobertura

- afegir prova feature del pilot:
  - [`ApiEditResourceFeatureTest.php`](/Users/igomis/Code/intranetBatoi/tests/Feature/ApiEditResourceFeatureTest.php)
- conservar la prova d'[`AlumnoFct`](/Users/igomis/Code/intranetBatoi/tests/Feature/ApiAlumnoFctControllerFeatureTest.php) per garantir compatibilitat del modal actual

## Resultat executat

- `edit()` en el controlador base ja admet recurs específic
- el fallback genèric continua disponible, però ara queda explícit i traçable en log
- `Actividad`, `Comision` i `Expediente` ja exposen el payload d'edició via `JsonResource`
- `AlumnoFct` també usa recurs propi, però manté el contracte actual del modal
- `Curso` també usa recurs específic per al payload d'edició
- `TipoActividad` i `TipoIncidencia` també entren ja pel mateix patró
- `Task` també queda cobert pel mateix patró
- `Incidencia` també queda coberta i deixa d'entrar pel fallback genèric detectat en logs
- el fallback genèric continua existint per a la resta de controladors API que encara no s'han migrat

## Validació

- `php artisan test tests/Feature/ApiAlumnoFctControllerFeatureTest.php tests/Feature/ApiEditResourceFeatureTest.php`
- `php artisan test tests/Feature/ApiAlumnoFctControllerFeatureTest.php tests/Feature/ApiEditResourceFeatureTest.php tests/Feature/ApiResourceControllerEditFallbackFeatureTest.php`
- `php artisan test tests/Feature/ApiCursoEditResourceFeatureTest.php`
- `php artisan test tests/Feature/ApiLegacyCatalogEditResourceFeatureTest.php`
- `php artisan test tests/Feature/ApiTaskEditResourceFeatureTest.php`
- `php artisan test tests/Feature/ApiIncidenciaEditResourceFeatureTest.php`

Resultat actual:
- `15` proves passades
- `93` assertions

## Següent tall natural

- ampliar el patró a altres `edit()` actius del frontend
- decidir si el fallback genèric d'[`ApiResourceController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ApiResourceController.php) ha de quedar com a compatibilitat temporal o eliminar-se progressivament
- revisar, en els casos ja migrats, si `INPUT_TYPES` i `FORM_FIELDS` es poden alinear millor per reduir comportament implícit en `fillAll()`
