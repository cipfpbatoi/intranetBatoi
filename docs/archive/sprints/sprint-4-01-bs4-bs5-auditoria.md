# Sprint 4 - S4-01 Auditoria BS4 -> BS5

Data d'actualització: 2026-03-18  
Branca objectiu: `sprint-4-js-migration`  
Issue relacionada: #78

## Objectiu
Inventariar els patrons Bootstrap 4 encara presents al projecte, identificar incompatibilitats amb Bootstrap 5 i prioritzar els punts amb més risc visual o funcional.

## Resum executiu
- L'auditoria confirma que el projecte encara depén de Bootstrap 4 en infraestructura, JS legacy i markup Blade/Livewire.
- Els tres fronts crítics són:
  - dependències `bootstrap` i `datatables.net-*-bs4` en `package.json` i `resources/assets/js/ppIntranet.js`
  - ús directe dels plugins jQuery de Bootstrap en `resources/assets/js/custom.js`
  - components compartits i pantalles crítiques amb atributs/classes BS4 (`data-toggle`, `data-dismiss`, `panel-*`, `pull-*`, `close`, `btn-default`)
- La migració recomanada comença per infraestructura i components compartits abans d'entrar a pantalles específiques.

## Estat després de l'execució de S4-02
- La infraestructura principal ja ha sigut migrada a Bootstrap 5:
  - `package.json`
  - `resources/assets/js/ppIntranet.js`
  - `resources/assets/js/bootstrap.js`
- S'ha afegit una capa de compatibilitat temporal per a:
  - plugins jQuery legacy de Bootstrap
  - tabs amb CSS/markup antic (`active`, `show`, `in`)
  - modals sobre CSS legacy BS3 (`show`, `in`, `modal-open`, `backdrop`)
- S'han migrat els components i pantalles prioritàries identificades en esta auditoria:
  - modals, tabs, alerts i dropdowns compartits
  - `FCT`, `Empresa`, `Reunió`, `Extraescolars`
  - panells Livewire de Direcció
  - gestió de Bústia i formularis d'autenticació
- La cerca final sobre `resources/views` i `resources/assets/js/components` ja no retorna patrons objectiu com:
  - `data-toggle`
  - `data-dismiss`
  - `btn-block`
  - `pull-right` / `pull-left`
  - `label label-*`
  - `class="close"`
  - `has-feedback`
  - `has-error`

## Recompte ràpid de patrons detectats
- `data-toggle=`: **37** ocurrències en `resources/views` i `resources/assets/js`
- `data-dismiss=`: **25** ocurrències
- `data-target=`: **6** ocurrències
- `panel-heading`: **15** ocurrències
- `pull-right`: **11** ocurrències
- `x_panel`: **16** ocurrències
- referències `bs4` en `package.json` i `resources/assets/js/ppIntranet.js`: **18**
- referències `tooltip` en infraestructura/layouts revisats: **32**
- crides `.modal(...)`: **13**
- referències `popover` en `resources/assets/js/custom.js`: **10**

## Incompatibilitats principals localitzades

### 1. Infraestructura JS i dependències
Risc: Alt

- `package.json`
  - continua amb `bootstrap ^4.6.2`
  - continua amb `popper.js ^1.12`
  - manté paquets `datatables.net-*-bs4`
- `resources/assets/js/ppIntranet.js`
  - importa DataTables BS4 (`datatables.net-bs4`, `buttons-bs4`, `responsive-bs4`, `keytable-bs4`, `scroller-bs4`, `fixedheader-bs4`)
  - importa també els CSS Bootstrap 4 equivalents
- Impacte esperat en BS5:
  - classes i wrappers de DataTables no alineats amb BS5
  - possible desquadre visual en taules, botons i responsive helpers
  - acoblament fort a la capa de compatibilitat actual

### 2. Comportament jQuery/Bootstrap legacy
Risc: Alt

- `resources/assets/js/custom.js`
  - inicialitza `tooltip()` amb selectors BS4 (`[data-toggle="tooltip"]`)
  - usa `popover()` i patch intern sobre `$.fn.popover.Constructor`
  - manté crides directes a `dropdown('toggle')`
  - manté crides directes a `$('#getCroppedCanvasModal').modal()`
- Impacte esperat en BS5:
  - Bootstrap 5 ja no exposa plugins jQuery per defecte
  - tooltips, popovers, modals i dropdowns poden deixar de funcionar encara que el markup compile
  - `custom.js` és el principal bloquejador tècnic de la migració real

### 3. Components compartits
Risc: Alt

- `resources/views/components/modal.blade.php`
  - usa `class="close"`, `data-dismiss="modal"`, `btn-default`
  - estructura de títol amb `<h4 class="modal-title">`
- `resources/views/components/ui/tabs.blade.php`
  - usa `data-toggle="tab"`
  - usa `active in` i `tab-pane fade ... in`
- `resources/views/components/user-tabs.blade.php`
  - mateix patró de tabs BS4
- `resources/views/components/layouts/topnav.blade.php`
  - dropdown de notificacions amb `data-toggle="dropdown"`
- `resources/views/layouts/partials/titlecontent.blade.php`
  - menú d'eines amb `data-toggle="dropdown"`
- `resources/views/components/layouts/topmenu.blade.php`
  - `data-toggle="dropdown"` i `pull-right`
- Impacte esperat en BS5:
  - tabs i dropdowns no funcionaran sense adaptar atributs i estats actius
  - botons de tancar modal i estils per defecte no quedaran bé

### 4. Layouts i tema Gentelella
Risc: Alt

- `resources/views/layouts/partials/panel.blade.php`
  - usa `x_panel`, `x_content`, `bar_tabs`, `data-toggle="tab"` i `active in`
- `resources/views/empresa/show.blade.php`
  - ús intensiu de `x_panel`, `x_title`, `panel_toolbox`, `data-toggle="modal"`
- `resources/views/components/layouts/page.blade.php`, `resources/views/components/layouts/panel.blade.php` i altres layouts mantenen patrons Gentelella
- Impacte esperat en BS5:
  - regressions visuals en contenidors, espaiats i jerarquia
  - comportaments de collapse i tabs dependents de JS legacy

### 5. Pantalles crítiques revisades
Risc: Mitjà-Alt / Alt

- `resources/views/fct/show.blade.php`
  - tabs BS4 (`data-toggle="tab"`, `active in`, `bar_tabs`)
  - és flux crític de negoci
- `resources/views/empresa/show.blade.php`
  - modals i panells Gentelella
  - toolbar amb accions de gestió
- `resources/views/livewire/comision-direccion-panel.blade.php`
  - `panel panel-default`, `panel-heading`, `collapse in`
  - modals amb `close`, `data-dismiss`, `btn-default`
- `resources/views/livewire/actividad-direccion-panel.blade.php`
  - `panel panel-danger`, `panel-heading`, `btn-default`
  - modal BS4 de detall
- `resources/views/livewire/falta-direccion-panel.blade.php`
  - entra en el mateix grup de panells/modals legacy per estructura del sprint
- `resources/views/livewire/expediente-direccion-panel.blade.php`
  - mateix risc funcional/visual per ús de patrons semblants en Direcció

### 6. Paquets i themes interns
Risc: Mitjà

- `packages/html/themes/bootstrap4/fields/default.blade.php`
  - encara usa tema `bootstrap4`
  - inclou `badge badge-info`
- Impacte esperat en BS5:
  - inconsciència visual en formularis generats
  - pot deixar residus BS4 encara que es migre la resta del projecte

## Llista prioritzada de risc

### Prioritat 1
- `package.json`
- `resources/assets/js/ppIntranet.js`
- `resources/assets/js/custom.js`
- `resources/views/components/modal.blade.php`
- `resources/views/components/ui/tabs.blade.php`
- `resources/views/components/user-tabs.blade.php`
- `resources/views/components/layouts/topnav.blade.php`
- `resources/views/components/layouts/topmenu.blade.php`
- `resources/views/layouts/partials/titlecontent.blade.php`
- `resources/views/layouts/partials/panel.blade.php`

### Prioritat 2
- `resources/views/fct/show.blade.php`
- `resources/views/empresa/show.blade.php`
- `resources/views/empresa/partials/centros.blade.php`
- `resources/views/fct/partials/colaboradores.blade.php`
- `resources/views/livewire/comision-direccion-panel.blade.php`
- `resources/views/livewire/actividad-direccion-panel.blade.php`
- `resources/views/livewire/falta-direccion-panel.blade.php`
- `resources/views/livewire/expediente-direccion-panel.blade.php`

### Prioritat 3
- `packages/html/themes/bootstrap4/*`
- pantalles d'autenticació amb `btn-block`, `pull-right`, `has-feedback`, `has-error`
- modals i alerts legacy dispersos fora dels components compartits

## Proposta d'ordre d'execució
1. Migrar dependències BS4 a BS5 i la capa DataTables en `package.json` i `resources/assets/js/ppIntranet.js`.
2. Separar a `resources/assets/js/custom.js` la part que depén de plugins jQuery de Bootstrap i encapsular la compatibilitat temporal.
3. Adaptar modals, tabs i dropdowns compartits.
4. Validar `FCT`, `Empresa` i panells Livewire de Direcció.
5. Revisar residus Gentelella i theme intern `bootstrap4`.

## Quick wins confirmats
- substituir `data-toggle`, `data-target` i `data-dismiss` pels equivalents BS5 en components compartits
- eliminar `btn-default`, `close`, `pull-right` i estats `in` dels tabs/modals comuns
- concentrar la migració DataTables en una única capa (`package.json` + `resources/assets/js/ppIntranet.js`)

## Bloquejadors tècnics
- `resources/assets/js/custom.js` continua assumint plugins jQuery de Bootstrap 4
- la capa visual Gentelella (`x_panel`, `x_title`, `x_content`, `panel_toolbox`) pot requerir compatibilitat CSS addicional
- els panells Livewire de Direcció encara barregen patrons BS3/BS4 i comportaments BS4

## Tancament de l'auditoria
- `S4-01` queda coberta com a auditoria inicial a data `2026-03-18`.
- Les prioritats detectades en esta auditoria s'han executat durant `S4-02` i han quedat resoltes a nivell funcional en el front prioritari.
- El risc principal que queda ja no és de markup BS4/BS5 sinó de deute tècnic adjacent:
  - warnings de Sass per `@import` legacy en `resources/assets/sass/app.scss`
  - grandària del bundle `ppIntranet`
  - revisió visual final de layouts/tema (`S4-03` i `S4-04`)
