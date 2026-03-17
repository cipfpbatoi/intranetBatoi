# Sprint 3 - Pendents de retirada jQuery

Data d'actualització: 2026-03-17
Branca: `sprint-3-livewire-vue`

## Objectiu
Deixar constància explícita de quins punts encara depenen de jQuery i per què, després de la migració incremental de Sprint 3.

## Pendents per dependència tècnica
- Continuen existint dependències jQuery residuals en codi propi.
- Recompte actual:
  - fitxers amb ús directe de jQuery/ajax/modal: **4**
  - fitxers amb `.modal(...)`: **4**
  - fitxers amb DataTables: **20**

Fitxers principals que continuen pendents:

- Base compartida:
  - `resources/assets/js/custom.js`
  - `resources/assets/js/ppIntranet.js`
  - `public/js/app.js`
  - `public/js/common/ui-helpers.js`

Infraestructura de compatibilitat introduïda:

- `public/js/common/data-table.js`
  - Centralitza la compatibilitat DataTables v2 / jQuery DataTables.

Reducció efectiva en esta passada:

- `public/js/Comision/grid.js` ja no depén directament de jQuery per inicialitzar DataTables.
- `public/js/Fct/show.js` ja no manté fallback local de modal; usa el helper compartit.
- `public/js/Lote/index.js` ja no manté fallback local de modal ni inicialitza DataTables amb `$("#datatable")`.
- `public/js/grid.js`, `public/js/Empresa/index.js`, `public/js/Comision/grid.js` i `public/js/Lote/index.js` deleguen ara la compatibilitat DataTables en `public/js/common/data-table.js`.

## Fallbacks jQuery intencionals (compatibilitat modal)
- Patró temporal centralitzat:
  - Fallback de modal mantingut únicament en `public/js/common/ui-helpers.js`.
  - Els fitxers funcionals migrats del sprint ja no inclouen fallback local a `window.jQuery(...).modal(...)`.
- Helper compartit introduït:
  - `public/js/common/ui-helpers.js`
  - Carregat des de `resources/views/js/js.blade.php` i `resources/views/js/modaljs.blade.php`.
  - Ús inicial en `public/js/delete.js` per reduir duplicació.

## Fitxers marcats com a deprecated
- `public/js/Comision/comisionShort.js`
  - Sense ús actiu al flux actual de Comissió.
- `public/js/Fctdual/modal.js`
  - Flux legacy mantingut temporalment; prioritat funcional actual en línia FCT/FCTCAP migrada.
- `public/js/Fctdual/index.js`
  - Flux legacy mantingut per convivència temporal amb mòdul FCTCAP (marcat deprecat).
- Bloc funcional DUAL/FCTDUAL (JS, vistes Blade, CSS i classes PHP específiques)
  - Marcat com a legacy/deprecated per evitar nous desenvolupaments sobre eixe camí.

Inventari localitzat de punts deprecated:

- JS:
  - `public/js/Fctdual/index.js`
  - `public/js/Fctdual/modal.js`
  - `public/js/Dual/create.js`
- Vistes:
  - `resources/views/fctdual/index.blade.php`
  - `resources/views/fctdual/partials/grid.blade.php`
  - `resources/views/dual/*.blade.php`
- Backend:
  - `app/Http/Controllers/Deprecated/DualController.php`
  - `app/Http/Controllers/API/DualController.php`
  - `app/Http/Controllers/PanelDualController.php`
  - `app/Http/Controllers/PanelPGDualController.php`
  - `app/Http/Controllers/CicloDualController.php`
  - `app/Entities/Dual.php`
  - `app/Http/Resources/DualResource.php`
  - `app/Http/Requests/DualRequest.php`
  - `app/Http/Requests/CicloDualRequest.php`

Nota d'abast:

- Este inventari es considera explícitament fora de l'abast de rematada funcional d'Sprint 3.

## Criteri per tancar S3-07
- Mantindre només compatibilitat centralitzada en `public/js/common/ui-helpers.js` mentre convisquen pantalles BS4/BS5.
- Eliminar fallback `.modal(...)` quan totes les pantalles del sprint funcionen només amb Bootstrap 5.
- Reauditar i deixar el recompte de fitxers amb jQuery en mínim residual justificat.

## Relació amb S3-01
- `S3-01` queda tancada com a auditoria i mapa de risc.
- `S3-07` continua oberta perquè la retirada efectiva de jQuery encara no està completada.
