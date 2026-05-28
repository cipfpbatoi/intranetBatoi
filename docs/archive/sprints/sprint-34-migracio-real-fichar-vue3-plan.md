# Sprint 34 - Migració real de fichar a Vue 3

## Nota d'estat

Document històric completat. `fichar` ja funciona sobre `Vue 3` i el projecte ja no manté `Vue 2` en codi executable.

## Objectiu

Completar la migració real de `fichar` a `Vue 3`, aprofitant que el bloc ja està separat en un entrypoint propi.

## Punt de partida

Després dels sprints anteriors:

- `guardias` ja no depén de Vue
- `reservas` ja no depén de Vue
- `fichar` és l'únic bloc Vue viu
- però en aquell moment encara funcionava amb runtime i toolchain de `Vue 2`

La preparació ja està feta en:

- [`resources/assets/js/fichar-app.js`](/Users/igomis/Code/intranetBatoi/resources/assets/js/fichar-app.js)
- [`resources/assets/js/components/fichar/ControlSemanaView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/fichar/ControlSemanaView.vue)
- [`resources/assets/js/components/fichar/ControlResumenRangoView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/fichar/ControlResumenRangoView.vue)

## Abast

- canviar el runtime de `fichar` a `Vue 3`
- adaptar els components de `fichar` i auxiliars si cal
- mantindre el comportament actual de:
  - `/fichar/control`
  - `/fichar/resumen-rango`
- reduir dependència del pipeline `Vue 2`

## Fora d'abast

- retirar encara totes les dependències globals de `Vue 2` del projecte
- reescriure `fichar` en `Livewire`
- refactor visual profund de les pantalles

## Talls proposats

### Tall A. Runtime

- preparar convivència temporal o substitució directa de `fichar-app.js`
- revisar `vite.config.mjs` i dependències necessàries

### Tall B. Components

- adaptar `ControlSemanaView`
- adaptar `ControlResumenRangoView`
- revisar `ControlNav` i auxiliars

### Tall C. Validació

- smoke test manual de les dos rutes
- comprovació de build
- comprovació de contracte amb APIs existents

## Criteri d'acceptació

- `fichar` funciona sobre `Vue 3`
- `/fichar/control` i `/fichar/resumen-rango` mantenen funcionalitat
- queda clar quin és el següent pas per retirar el toolchain `Vue 2`
