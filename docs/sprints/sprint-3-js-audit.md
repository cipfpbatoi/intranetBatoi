# Sprint 3 - Audit JS legacy (S3-01)

Data: 2026-03-11
Branca: `sprint-3-js-migration`

## Resum executiu
- Dependència jQuery encara alta en runtime web.
- Cal migració incremental per verticals funcionals (no big bang).
- Prioritat immediata: `Signatura` + fluxos base compartits (`indexModal`, `selecciona`, `selDoc`).

## Mètriques ràpides
- Fitxers JS escanejats (`public/js` + `resources/assets/js`): **111**
- Fitxers amb ús jQuery/ajax (`$(`, `jQuery`, `.ajax(`): **54**
- Fitxers amb DataTables: **19**
- Fitxers amb ús de modals jQuery (`.modal(`): **16**
- Fitxers amb plugins addicionals (iCheck/select2/datepicker): **8**

## Mapa de risc per mòdul

### Risc alt
Impacte funcional directe en fluxos crítics o codi base compartit.

- `public/js/Signatura/index.js`
- `public/js/selDoc.js`
- `public/js/indexModal.js`
- `public/js/selecciona.js`
- `public/js/Fct/grid.js`
- `public/js/Fct/show.js`
- `resources/assets/js/custom.js`
- `resources/assets/js/ppIntranet.js`

### Risc mitjà
Mòduls funcionals importants, però menys transversals.

- `public/js/Fctcap/index.js`
- `public/js/Fctcap/modal.js`
- `public/js/Fctdual/index.js`
- `public/js/Fctdual/modal.js`
- `public/js/Empresa/index.js`
- `public/js/Material/index.js`
- `public/js/Inventario/index.js`
- `public/js/Lote/index.js`

### Risc baix
Àrees puntuals o administratives, menys crítiques per Sprint 3.

- `public/js/Notification/index.js`
- `public/js/Notification/tindex.js`
- `public/js/Actividad/img.js`
- `public/js/barcode.js`
- `public/js/MaterialBaja/index.js`
- `public/js/Instructor/create.js`

## Primera onada de migració (Sprint 3)
Objectiu: estabilitzar flux crític i construir base reutilitzable.

1. `public/js/Signatura/index.js`
2. `public/js/selDoc.js`
3. `public/js/indexModal.js`
4. `public/js/selecciona.js`
5. `public/js/Fct/grid.js` (fase 1: accions principals)

## Dependències a substituir/encapsular
- `$.ajax` -> `fetch` via `apiClient` compartit
- `$(...).on(...)` -> `addEventListener` + delegació manual
- `$(...).modal(...)` -> API Bootstrap 5 (`new bootstrap.Modal(...)`) o helper propi
- DataTables: mantindre temporalment, encapsulat en adaptador

## Criteris d'acceptació S3-01
- Mapa `fitxer -> risc -> prioritat` definit.
- Primera onada de fitxers tancada.
- Pla de substitució tècnica base (`fetch`, events, modal) validat.

## Notes
- Es recomana no eliminar jQuery global fins completar S3-03/S3-04.
- Mantindre compatibilitat temporal en mòduls no migrats.
