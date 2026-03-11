# Sprint 3 - Pendents de retirada jQuery

Data d'actualització: 2026-03-11  
Branca: `sprint-3-js-migration`

## Objectiu
Deixar constància explícita de quins punts encara depenen de jQuery i per què, després de la migració incremental de Sprint 3.

## Pendents per dependència tècnica
- `public/js/Poll/create.js`
  - Motiu: depén de `ionRangeSlider` (plugin jQuery).
  - Estat: codi modernitzat, però es manté jQuery com a bridge del plugin.
- `public/js/tabledit.js`
  - Motiu: plugin/flux d'edició en taula fortament acoblat a jQuery.
- `public/js/grid.js`
  - Motiu: inicialització i extensions DataTables legacy en wrapper jQuery.
- `public/js/delete.js`
  - Motiu: delegació d'events i flux de modals legacy sobre `#datatable`.
- `public/js/barcode.js`
  - Motiu: pendent de migració en aquesta fase.

## Fallbacks jQuery intencionals (compatibilitat modal)
- Patró temporal en fitxers migrats:
  - Primer `bootstrap.Modal` (BS5).
  - Fallback a `window.jQuery(...).modal(...)` per pantalles encara BS4/legacy.
- Aquests fallback no impliquen lògica jQuery de negoci, només compatibilitat de runtime.

## Fitxers marcats com a deprecated
- `public/js/Comision/comisionShort.js`
  - Sense ús actiu al flux actual de Comissió.
- `public/js/Fctdual/modal.js`
  - Flux legacy mantingut temporalment; prioritat funcional actual en línia FCT/FCTCAP migrada.

## Criteri per tancar S3-07
- Substituir plugins jQuery sense equivalent (`ionRangeSlider`, table edit legacy) o encapsular-los en adaptadors.
- Eliminar fallback `.modal(...)` quan totes les pantalles del sprint funcionen només amb Bootstrap 5.
- Reauditar i deixar el recompte de fitxers amb jQuery en mínim residual justificat.
