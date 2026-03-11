# Sprint 3 - Pendents de retirada jQuery

Data d'actualització: 2026-03-11  
Branca: `sprint-3-js-migration`

## Objectiu
Deixar constància explícita de quins punts encara depenen de jQuery i per què, després de la migració incremental de Sprint 3.

## Pendents per dependència tècnica
- `public/js/tabledit.js`
  - Motiu: plugin/flux d'edició en taula fortament acoblat a jQuery.

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
- Substituir/retirar el plugin jQuery residual (`tabledit`) o encapsular-lo en un adaptador.
- Eliminar fallback `.modal(...)` quan totes les pantalles del sprint funcionen només amb Bootstrap 5.
- Reauditar i deixar el recompte de fitxers amb jQuery en mínim residual justificat.
