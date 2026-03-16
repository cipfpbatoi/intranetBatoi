# Sprint 3 - Pendents de retirada jQuery

Data d'actualització: 2026-03-16
Branca: `sprint-3-livewire-vue`

## Objectiu
Deixar constància explícita de quins punts encara depenen de jQuery i per què, després de la migració incremental de Sprint 3.

## Pendents per dependència tècnica
- Continuen existint dependències jQuery residuals en codi propi.
- Recompte actual:
  - fitxers amb jQuery/ajax: **24**
  - fitxers amb `.modal(...)`: **4**
  - fitxers amb DataTables: **19**

Fitxers principals que continuen pendents:

- Base compartida:
  - `resources/assets/js/custom.js`
  - `resources/assets/js/ppIntranet.js`
  - `public/js/app.js`
  - `public/js/common/ui-helpers.js`
- Fluxos FCT i administració:
  - `public/js/Fct/grid.js`
  - `public/js/Empresa/index.js`
  - `public/js/Comision/grid.js`
  - `public/js/Menu/grid.js`
  - `public/js/TipoActividad/grid.js`
  - `public/js/Lote/index.js`

## Fallbacks jQuery intencionals (compatibilitat modal)
- Patró temporal centralitzat:
  - Fallback de modal mantingut únicament en `public/js/common/ui-helpers.js`.
  - Els fitxers funcionals migrats ja no inclouen fallback local a `window.jQuery(...).modal(...)`.
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

## Criteri per tancar S3-07
- Mantindre només compatibilitat centralitzada en `public/js/common/ui-helpers.js` mentre convisquen pantalles BS4/BS5.
- Eliminar fallback `.modal(...)` quan totes les pantalles del sprint funcionen només amb Bootstrap 5.
- Reauditar i deixar el recompte de fitxers amb jQuery en mínim residual justificat.

## Relació amb S3-01
- `S3-01` queda tancada com a auditoria i mapa de risc.
- `S3-07` continua oberta perquè la retirada efectiva de jQuery encara no està completada.
