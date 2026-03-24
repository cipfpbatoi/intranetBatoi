# Sprint 17 - Auditoria i migració de frontend legacy Vue 2

## Objectiu

Inventariar què queda realment en `Vue 2` i decidir per cada peça si convé migrar-la a `Vue 3`, a `Livewire` o eliminar-la com a residual.

## Context actual

La branca actual encara manté dependència directa de `Vue 2` en [`package.json`](/Users/igomis/Code/intranetBatoi/package.json):

- `vue`
- `vue-template-compiler`
- `vue-loader`
- `@vitejs/plugin-vue2`

L'entrypoint principal continua sent [`resources/assets/js/app.js`](/Users/igomis/Code/intranetBatoi/resources/assets/js/app.js), on es registren quatre components globals:

- `control-semana-view`
- `control-guardia-view`
- `reservas-view`
- `control-resumen-rango-view`

## Inventari real detectat

### 1. Nucli Vue 2 encara viu en producció

Components `.vue` detectats en [`resources/assets/js/components`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components):

- `fichar`
  - [`ControlSemanaView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/fichar/ControlSemanaView.vue)
  - [`ControlResumenRangoView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/fichar/ControlResumenRangoView.vue)
- `guardias`
  - [`ControlGuardiaView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/guardias/ControlGuardiaView.vue)
  - [`ControlGuardiaItem.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/guardias/ControlGuardiaItem.vue)
- `reservas`
  - [`ReservasView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/ReservasView.vue)
  - [`RecursosSelect.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/RecursosSelect.vue)
  - [`HorasTable.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/HorasTable.vue)
  - [`ProfesSelect.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/ProfesSelect.vue)
  - [`HorasSelect.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/HorasSelect.vue)
- `utils`
  - [`AppMsg.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/utils/AppMsg.vue)
  - [`ControlNav.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/utils/ControlNav.vue)
  - [`FechaPicker.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/utils/FechaPicker.vue)

### 2. Punts de muntatge detectats

S'han trobat usos directes dels components globals en:

- [`resources/views/fichar/control.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/fichar/control.blade.php)
- [`resources/views/fichar/resumen-rango.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/fichar/resumen-rango.blade.php)

En canvi, a l'inventari inicial no ha aparegut encara un ús tan visible de:

- `control-guardia-view`
- `reservas-view`

Per tant, cal una segona passada per localitzar el muntatge exacte d'estes dos peces o verificar si hi ha parts semiorfes.

### 2.b Resultat de la segona passada

La segona passada confirma dos coses importants:

1. no s'ha trobat encara un ús directe clar de les etiquetes:
   - `control-guardia-view`
   - `reservas-view`

2. però sí s'ha confirmat que [`public/js/components/app.js`](/Users/igomis/Code/intranetBatoi/public/js/components/app.js) continua registrant estos components globals, junt amb:
   - `control-semana-view`
   - `control-resumen-rango-view`

Per tant, el bundle compilat legacy continua viu i encara és part del problema, encara que alguns punts de muntatge no siguen evidents en la cerca superficial.

A més, s'ha detectat que:

- [`resources/views/guardias/control.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/guardias/control.blade.php) ja usa `Livewire`

Això suggereix que `guardias` pot estar en una fase híbrida:

- una ruta o pantalla nova basada en `Livewire`
- i una resta Vue 2 que podria ser secundària o residual

### 3. Resta més antiga que Vue 2

Hi ha almenys una resta clarament anterior i fora del pipeline modern:

- [`resources/views/intranet/partials/profile/comisionvue.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/comisionvue.php)

Este fitxer:

- carrega `Vue 1.0.24` per CDN
- carrega `vue-resource`
- crea un `new Vue(...)` inline

Això no és només `Vue 2` legacy: és un tros molt més antic que convé considerar **residual** fins que es demostre el contrari.

### 4. Mode híbrid de càrrega actual

Les plantilles base mantenen convivència entre:

- `legacy-app.js`
- `app.js`
- scripts compilats antics en `public/js/components/app.js`

Es veu en:

- [`resources/views/layouts/intranet.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/layouts/intranet.blade.php)
- [`resources/views/components/layouts/app.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/components/layouts/app.blade.php)

Això confirma que la migració no és només de components, sinó també de **pipeline i mode de càrrega**.

## Classificació inicial recomanada

### A. Candidats a migrar a Vue 3

Peces amb estat client clar i interacció rica:

- [`resources/assets/js/components/fichar/ControlSemanaView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/fichar/ControlSemanaView.vue)
- [`resources/assets/js/components/fichar/ControlResumenRangoView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/fichar/ControlResumenRangoView.vue)

Raó:

- són components de visualització interactiva
- consumeixen API JSON
- tenen estat client local
- no semblen CRUDs típics de formulari Laravel

### B. Candidats a reavaluar entre Vue 3 i Livewire

- [`resources/assets/js/components/guardias/ControlGuardiaView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/guardias/ControlGuardiaView.vue)
- [`resources/assets/js/components/guardias/ControlGuardiaItem.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/guardias/ControlGuardiaItem.vue)
- [`resources/assets/js/components/reservas/ReservasView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/ReservasView.vue)
- components auxiliars de `reservas`

Raó:

- és una UI amb interacció notable
- però també està molt acoblada a formulari, permisos i flux backend
- `guardias` ja mostra com a mínim una via `Livewire` activa
- podria acabar en `Vue 3` si es vol mantindre la interactivitat client
- o en `Livewire` si es busca acostar més la lògica al servidor

### C. Candidats a retirada o reescriptura simple

- [`resources/views/intranet/partials/profile/comisionvue.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/comisionvue.php)
- possibles restes de `guardias` si es confirma que la via bona ja és la de `Livewire`

Raó:

- usa `Vue 1`
- està fora del pipeline actual
- és un fort indicador de residu històric

## Recomanació tècnica

No fer una migració massiva d'entrada.

La seqüència recomanada seria:

1. inventari complet de punts de muntatge
2. decidir el destí de cada bloc
3. fer un pilot sobre un únic grup funcional

El millor pilot probable és:

- `fichar`

Perquè:

- està ben delimitat
- ja té muntatge visible en vistes
- i el valor funcional és clar

## Talls proposats

### Tall A. Completar inventari

- localitzar on es munten `control-guardia-view` i `reservas-view`
- verificar si hi ha més restes `new Vue(...)` o CDN vells
- identificar si [`public/js/components/app.js`](/Users/igomis/Code/intranetBatoi/public/js/components/app.js) continua sent necessari o si només sosté restes residuals

### Tall B. Decisió de destí

- `fichar` -> `Vue 3`
- `guardias` -> decidir si la ruta bona ja és `Livewire` i, si és així, retirar la branca Vue 2
- `reservas` -> decidir entre `Vue 3` o `Livewire`
- `comisionvue.php` -> retirada o reescriptura simple

### Tall C. Pilot

- migrar el bundle principal de `Vue 2` a `Vue 3` sobre el bloc triat
- mantindre la resta en convivència temporal mentre no es migre

## Criteri d'acceptació

- hi ha inventari real dels components i punts de muntatge
- cada bloc queda classificat a `Vue 3`, `Livewire` o retirada
- hi ha un primer pilot recomanat
- queda clar que la migració afecta també el mode de càrrega híbrid actual
