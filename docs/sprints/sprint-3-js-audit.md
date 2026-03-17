# Sprint 3 - Audit JS legacy (S3-01)

Data d'actualització: 2026-03-16
Branca: `sprint-3-livewire-vue`

## Resum executiu
- Dependència jQuery encara alta en runtime web.
- Cal migració incremental per verticals funcionals (no big bang).
- Prioritat immediata: `Signatura` + fluxos base compartits (`indexModal`, `selecciona`, `selDoc`).

## Mètriques ràpides
- Fitxers JS escanejats (`public/js` + `resources/assets/js`): **100**
- Fitxers amb ús jQuery/ajax (`$(`, `jQuery`, `.ajax(`): **24**
- Fitxers amb DataTables: **19**
- Fitxers amb ús de modals jQuery (`.modal(`): **4**

Detall de recompte actual:

- jQuery/ajax:
  - `public/js/Alumno/index.bak.js`
  - `public/js/Colaboracion/index.js`
  - `public/js/Comision/grid.js`
  - `public/js/Documento/create.js`
  - `public/js/Empresa/edit.js`
  - `public/js/Empresa/index.js`
  - `public/js/Falta/edit.js`
  - `public/js/Falta/itaca.js`
  - `public/js/Fct/grid.js`
  - `public/js/Lote/index.js`
  - `public/js/Menu/grid.js`
  - `public/js/MyMail/block.js`
  - `public/js/MyMail/create.js`
  - `public/js/TipoActividad/grid.js`
  - `public/js/Tutoria/index.js`
  - `public/js/app.js`
  - `public/js/common/ui-helpers.js`
  - `public/js/datepicker.js`
  - `public/js/grid.js`
  - `public/js/list.js`
  - `public/js/taulaCheck.js`
  - `resources/assets/js/bootstrap.js`
  - `resources/assets/js/custom.js`
  - `resources/assets/js/ppIntranet.js`

- modals jQuery:
  - `public/js/Lote/index.js`
  - `public/js/app.js`
  - `public/js/common/ui-helpers.js`
  - `resources/assets/js/custom.js`

## Mapa de risc per mòdul

### Risc alt
Impacte funcional directe en fluxos crítics o codi base compartit que continua viu.

- `public/js/Fct/grid.js`
- `resources/assets/js/custom.js`
- `resources/assets/js/ppIntranet.js`
- `public/js/app.js`
- `public/js/common/ui-helpers.js`

### Risc mitjà
Mòduls funcionals importants, però menys transversals o més acotats.

- `public/js/Empresa/index.js`
- `public/js/Material/index.js`
- `public/js/Inventario/index.js`
- `public/js/Lote/index.js`
- `public/js/Comision/grid.js`
- `public/js/TipoActividad/grid.js`
- `public/js/Menu/grid.js`

### Risc baix
Àrees puntuals o administratives, menys crítiques per Sprint 3.

- `public/js/Notification/index.js`
- `public/js/Documento/create.js`
- `public/js/Falta/edit.js`
- `public/js/Falta/itaca.js`
- `public/js/Tutoria/index.js`
- `public/js/MyMail/block.js`
- `public/js/MyMail/create.js`
- `public/js/datepicker.js`
- `public/js/list.js`

## Primera onada de migració (Sprint 3)
Objectiu: estabilitzar flux crític i construir base reutilitzable.

1. `public/js/Fct/grid.js`
2. `resources/assets/js/custom.js`
3. `resources/assets/js/ppIntranet.js`
4. `public/js/app.js`
5. `public/js/common/ui-helpers.js`

## Dependències a substituir/encapsular
- `$.ajax` -> `fetch` via `apiClient` compartit
- `$(...).on(...)` -> `addEventListener` + delegació manual
- `$(...).modal(...)` -> API Bootstrap 5 (`new bootstrap.Modal(...)`) o helper propi
- DataTables: mantindre temporalment, encapsulat en adaptador

## Criteris d'acceptació S3-01
- Mapa `fitxer -> risc -> prioritat` definit.
- Primera onada de fitxers tancada.
- Pla de substitució tècnica base (`fetch`, events, modal) validat.

## Estat de tancament
- `S3-01` es pot considerar tancada com a auditoria.
- Açò no implica retirada de jQuery; eixa part continua oberta en `S3-07`.
- La documentació de pendents vius queda en [sprint-3-jquery-pendents.md](./sprint-3-jquery-pendents.md).

## Notes
- Es recomana no eliminar jQuery global fins completar `S3-03`, `S3-04` i `S3-07`.
- Mantindre compatibilitat temporal en mòduls no migrats.
