# Sprint 4 - Primer tall BS5

Data: 2026-03-17
Issue relacionada: #78

## Objectiu
Deixar preparat un primer tall executable per començar la migració de Bootstrap 4 a Bootstrap 5 sense dispersar l'esforç.

## Fitxers prioritaris

### 1. Infraestructura
- `package.json`
- `resources/assets/js/ppIntranet.js`
- `resources/assets/js/custom.js`

### 2. Components compartits
- `resources/views/components/modal.blade.php`
- `resources/views/components/ui/tabs.blade.php`
- `resources/views/components/user-tabs.blade.php`
- `resources/views/components/layouts/topnav.blade.php`
- `resources/views/layouts/partials/titlecontent.blade.php`

### 3. Pantalles crítiques de validació
- `resources/views/fct/show.blade.php`
- `resources/views/empresa/show.blade.php`
- `resources/views/livewire/falta-direccion-panel.blade.php`
- `resources/views/livewire/comision-direccion-panel.blade.php`
- `resources/views/livewire/actividad-direccion-panel.blade.php`
- `resources/views/livewire/expediente-direccion-panel.blade.php`

## Patrons BS4 localitzats

### Markup i atributs
- `data-toggle`
- `data-target`
- `data-parent`
- `panel-heading`
- `close`
- `pull-right`
- `pull-left`
- `x_panel`
- `x_title`
- `x_content`

### Dependències d'infra
- `bootstrap ^4.6.2` en `package.json`
- `datatables.net-*-bs4` en `resources/assets/js/ppIntranet.js`
- tooltips, popovers i modals jQuery en `resources/assets/js/custom.js`

## Quick wins
- adaptar tabs i modals compartits abans d'entrar a pantalles concretes
- encapsular qualsevol compatibilitat temporal en helpers compartits
- concentrar el canvi de DataTables en `resources/assets/js/ppIntranet.js`

## Risc alt
- trencar tooltips/popovers globals per dependència del plugin jQuery de Bootstrap 4
- regressions visuals en layouts Gentelella
- comportaments diferents en collapse, dropdowns i tabs
- impacte lateral en pantalles que encara no s'han revisat manualment

## Ordre recomanat
1. Actualitzar la base d'infra Bootstrap/DataTables.
2. Migrar components compartits de modal i tabs.
3. Validar FCT, Empresa i Direcció.
4. Atacar layouts i classes Gentelella residuals.
5. Executar QA visual desktop/mòbil.
