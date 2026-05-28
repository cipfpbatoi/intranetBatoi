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
