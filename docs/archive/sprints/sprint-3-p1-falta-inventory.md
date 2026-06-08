# Sprint 3 P1 - Inventari funcional `direccion/falta`

Data: 2026-03-12  
Branca: `sprint-3-livewire-vue`

## Àmbit
Pantalla de Direcció de faltes:
- URL principal: `/direccion/falta`
- Rutes d'acció:
  - `GET /direccion/falta/{falta}/resolve`
  - `POST /direccion/falta/{falta}/refuse`
  - `GET /direccion/falta/{falta}/alta`
  - `GET /direccion/falta/{falta}/show`

## Components actuals implicats
- `app/Http/Controllers/PanelFaltaController.php`
- `app/Http/Controllers/FaltaController.php`
- `resources/views/intranet/partials/profile/falta.blade.php`
- `resources/views/intranet/partials/modal/explicacion.blade.php`
- `public/js/Falta/index.js`

## Comportament observable actual
- Llistat en targetes (`profile_view`) agrupat per `desde`.
- Botons segons estat (configuració en `PanelFaltaController::iniBotones`):
  - `resolve` (`.authorize`) quan `estado > 0 && estado < 3`
  - `refuse` (`.refuse`) quan `estado > 0 && estado < 4`
  - `alta` (`.alta`) quan `estado == 5`
  - grid: `delete/edit` quan `estado < 4`
- Rebuig:
  - obri modal `#dialogo`
  - envia `POST` a `.../refuse` amb camp `explicacion`.

## Riscos de migració
- Dependència de transicions d'estat implícites en controlador/servei.
- Diferències de permisos segons rol (Direcció/Admin vs altres).
- Rebuig amb modal: el flux ha de mantindre motiu obligatori/esperat.
- Convivència amb vista legacy mentre el pilot estiga en validació.

## Paritat MVP (acceptació funcional mínima)
- Visualitzar llistat per data amb dades essencials de cada falta.
- Executar `acceptar/rebutjar/alta` amb feedback a usuari.
- Mantindre filtres/condicions de visibilitat de botons per `estado`.
- Mantindre compatibilitat de permisos actuals.
- Evitar dependència de JS legacy per al flux principal del pilot.

## Casos QA mínims del pilot
1. Direcció pot rebutjar una falta i el motiu queda registrat.
2. Direcció pot acceptar una falta pendent i canvia estat visual.
3. Estat no vàlid no mostra botons no aplicables.
4. Usuari sense permisos no pot executar accions protegides.
5. Ruta legacy continua funcionant durant la convivència.
