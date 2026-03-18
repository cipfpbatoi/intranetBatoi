# Sprint 4 - Proposta de tancament

Data d'actualització: 2026-03-18  
Branca objectiu: `sprint-4-js-migration`  
Issue relacionada: #78

## Decisió proposada
Tancar el `Sprint 4` com a **complet** dins de l'abast definit.

## Què queda completat
- Auditoria BS4 -> BS5 documentada i executada.
- Migració principal de Bootstrap/DataTables a BS5 completada.
- Components compartits corregits:
  - modals
  - tabs
  - dropdowns
  - alerts
  - formularis comuns
- Fluxos crítics verificats funcionalment:
  - `FCT`
  - `Empresa`
  - Direcció
  - Bústia
  - autenticació
- Correccions aplicades durant QA tècnica:
  - modals que bloquejaven la pantalla
  - tabs sense contingut visible
  - botó `btn-close` desalineat/invisible
  - scripts legacy fallant massa prompte (`datepicker.js`, `grid.js`)
  - consistència visual de paginació DataTables
  - botons de login inconsistents

## Residuals tècnics acceptats
1. `resources/assets/sass/app.scss`
   - warnings de Sass per `@import` legacy.
2. `resources/assets/js/ppIntranet.js`
   - chunk gran en compilació Vite.
3. Compatibilitat temporal en `resources/assets/js/bootstrap.js`
   - necessària per conviure amb CSS/markup legacy mentre es completa la neteja futura.

## Criteri de tancament recomanat
Es recomana donar el sprint per tancat si s'accepta que:
- el nucli BS4 -> BS5 que trencava funcionalitat ja està resolt
- la passada manual desktop/mòbil dels fluxos prioritaris ja s'ha completat
- els residuals restants són deute tècnic no bloquejant

## Següent pas recomanat
Obrir un pendent específic post-sprint per a:
- deute tècnic de Sass i chunking
