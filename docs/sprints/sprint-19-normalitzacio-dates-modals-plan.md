# Sprint 19: Normalització de dates en modals d'edició

Issue remot:
- `#122` https://github.com/cipfpbatoi/intranetBatoi/issues/122

## Objectiu

Reduir la lògica de conversió de dates en el frontend fent que els endpoints `edit`
tornen el format exacte que espera cada camp del formulari del modal.

## Problema actual

Ara mateix [`public/js/indexModal.js`](/Users/igomis/Code/intranetBatoi/public/js/indexModal.js)
fa de capa de compatibilitat entre:

- camps HTML natius `date`
- camps HTML natius `datetime-local`
- camps HTML natius `time`
- camps legacy amb `datetimepicker`
- payloads que arriben en formats diferents segons el controlador

Açò provoca:

- massa heurístiques al frontend
- regressions difícils de detectar
- contracte implícit i no uniforme entre backend i modal

## Principi de disseny

El contracte correcte és:

- el backend coneix el tipus del camp
- l'endpoint `edit` torna el valor en el format canònic que espera eixe camp
- el frontend només assigna el valor i inicialitza el widget, sense deduir formats

## Tall A. Inventari

- identificar formularis de modal que usen:
  - `date`
  - `datetime-local`
  - `time`
  - pickers legacy via classe `date`, `datetime`, `time`
- localitzar els endpoints `edit` que omplin eixos modals

## Tall B. Contracte de formats

- definir format canònic per tipus:
  - `date` -> `YYYY-MM-DD`
  - `datetime-local` -> `YYYY-MM-DDTHH:mm`
  - `time` -> `HH:mm`
  - picker legacy -> format pactat únic del projecte
- documentar excepcions legacy que no es puguen tocar encara

## Tall C. Refactor backend

- ajustar els `edit()` perquè retornen dates en format canònic
- prioritzar dominis amb més incidències:
  - `actividad`
  - `comision`
  - `expediente`
  - `alumnofct`

## Tall D. Simplificació frontend

- reduir la normalització interna de [`public/js/indexModal.js`](/Users/igomis/Code/intranetBatoi/public/js/indexModal.js)
- mantindre només compatibilitat transitòria on siga imprescindible
- evitar cerca global de camps fora del formulari actiu

## Tall E. Regressió

- afegir proves on tinga sentit
- validar manualment:
  - alta/edició en `actividad`
  - alta/edició en `comision`
  - alta/edició en `expediente`
  - alta/edició en `alumnofct`

## Resultat esperat

- menys lògica fràgil en `indexModal.js`
- contracte clar entre backend i modal
- menys regressions en la càrrega de dates

## Tancament

### Canvis aplicats

- els endpoints `edit()` API prioritaris ja retornen dates en format canònic per a modal:
  - `Actividad`
  - `Comision`
  - `Expediente`
  - `AlumnoFct`
- [`app/Http/Controllers/API/ApiResourceController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ApiResourceController.php) construeix el payload d'edició a partir de camps editables del model i aplica normalització només als tipus que ho necessiten
- [`app/Http/Controllers/API/AlumnoFctController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/AlumnoFctController.php) s'ha alineat amb el mateix contracte
- [`public/js/indexModal.js`](/Users/igomis/Code/intranetBatoi/public/js/indexModal.js) ha perdut gran part de la compatibilitat heurística i ara només adapta formats canònics
- [`public/js/datepicker.js`](/Users/igomis/Code/intranetBatoi/public/js/datepicker.js) s'ha convertit en inicialitzador compartit i [`public/js/Colaboracion/grid.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/grid.js) reutilitza eixa mateixa lògica
- el modal ja no força sempre `text` per a camps de data i hora:
  - [`resources/views/themes/bootstrap/formodal.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/themes/bootstrap/formodal.blade.php)
  - [`app/Services/UI/FormBuilder.php`](/Users/igomis/Code/intranetBatoi/app/Services/UI/FormBuilder.php)
- [`resources/views/themes/bootstrap/fields/partials/especial.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/themes/bootstrap/fields/partials/especial.blade.php) ha deixat de reconstruir inputs a mà
- s'han alineat valors inicials de formularis que encara injectaven dates en format visual:
  - [`resources/views/intranet/partials/modal/entreFechas.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/modal/entreFechas.blade.php)
  - [`resources/views/intranet/partials/modal/fecha.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/modal/fecha.blade.php)
- en [`resources/views/comision/detalle.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/comision/detalle.blade.php) el camp d'hora s'ha deixat com a `input[type=time]` natiu

### Regressions trobades durant el sprint

- en una passada intermèdia els camps `text` del modal no es recarregaven perquè el payload d'`edit()` es basava massa en `inputTypes`
- el problema s'ha corregit fent que el payload d'edició incloga també els `fillable` del model
- el camp d'hora de `comision/{id}/detalle` ha canviat de comportament visual segons navegador en passar a `type="time"`

### Estat final

- el contracte backend -> modal és ara molt més explícit
- el frontend de modal ja no deduïx múltiples formats antics
- el sistema continua tolerant restes legacy puntuals, però ja no depén d'elles com a mecanisme principal
- queda pendent un disseny més net per a serialització API: `JsonResource`

### Proves

- s'han afegit o ampliat proves de contracte per a:
  - `Actividad`
  - `Comision`
  - `Expediente`
  - `AlumnoFct`
- també hi ha cobertura unitària sobre el nou comportament de `FormBuilder`
- la bateria curta verda al tancament ha sigut:
  - `tests/Feature/ApiEditDateNormalizationFeatureTest.php`
  - `tests/Feature/ApiAlumnoFctControllerFeatureTest.php`
  - `tests/Feature/ApiComisionControllerFeatureTest.php`
  - `tests/Unit/FormBuilderTest.php`
  - `tests/Unit/ModalControllerTest.php`

### Continuació recomanada

- migrar la serialització d'API a recursos explícits en:
  - [`docs/sprints/sprint-23-json-resources-api-contract-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-23-json-resources-api-contract-plan.md)
- preparar pujada de framework en:
  - [`docs/sprints/sprint-24-laravel-13-upgrade-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-24-laravel-13-upgrade-plan.md)
