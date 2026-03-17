# Sprint 4 - Upgrade visual i de framework UI (Bootstrap 5)

Data: 2026-03-17  
Branca objectiu: `sprint-4-js-migration`

## Objectiu
Planificar i executar la migració visual de Bootstrap 4 a Bootstrap 5 amb impacte controlat.

Issue relacionada: #78

## Abast
- Inventari de patrons/classes BS4 utilitzades al projecte.
- Adaptar DataTables i components UI dependents de BS4.
- Validació visual responsive de fluxos crítics.

## Backlog prioritzat

### S4-01 Auditoria BS4 -> BS5
Prioritat: Alta

Tasques:
- Inventariar patrons i classes Bootstrap 4 encara presents.
- Identificar incompatibilitats amb Bootstrap 5 en components propis i de tercers.
- Classificar riscos per pantalles crítiques i infraestructura compartida.

Criteris d'acceptació:
- Informe de diferències BS4 -> BS5 aplicades al projecte.
- Llista prioritzada de pantalles i components amb més risc visual o funcional.

### S4-02 Migració de components comuns
Prioritat: Alta

Tasques:
- Migrar formularis, taules, modals i navegació als patrons compatibles amb Bootstrap 5.
- Adaptar wrappers, helpers i inicialitzacions que encara assumixen markup o comportaments de Bootstrap 4.
- Verificar compatibilitat amb DataTables i altres components UI dependents.

Criteris d'acceptació:
- Components comuns funcionals amb Bootstrap 5 en pantalles prioritàries.
- No hi ha regressions visuals greus en els patrons reutilitzats.

### S4-03 Revisió de layouts i tema
Prioritat: Mitjana-Alta

Tasques:
- Revisar layouts compartits, menús i contenidors generals.
- Validar l'impacte sobre Gentelella i altres peces visuals heretades.
- Ajustar espaiats, jerarquia visual i consistència entre pantalles.

Criteris d'acceptació:
- Layouts principals visualment consistents en Bootstrap 5.
- El tema base no introdueix regressions greus de navegació o llegibilitat.

### S4-04 Validació visual responsive
Prioritat: Alta

Tasques:
- Preparar checklist QA visual per desktop i mòbil.
- Revisar fluxos crítics amb especial atenció a modals, taules, formularis i navegació.
- Registrar incidències visuals i prioritzar correccions.

Criteris d'acceptació:
- Checklist QA visual desktop/mòbil executada.
- Fluxos crítics sense regressions visuals greus.

## Tall inicial recomanat

Fase 1 d'execució pràctica:

- Infraestructura Bootstrap/DataTables:
  - `package.json`
  - `resources/assets/js/ppIntranet.js`
  - `resources/assets/js/custom.js`
- Components compartits:
  - `resources/views/components/modal.blade.php`
  - `resources/views/components/ui/tabs.blade.php`
  - `resources/views/components/user-tabs.blade.php`
  - `resources/views/components/layouts/topnav.blade.php`
  - `resources/views/layouts/partials/titlecontent.blade.php`
- Pantalles crítiques a validar en el primer tall:
  - `resources/views/fct/show.blade.php`
  - `resources/views/empresa/show.blade.php`
  - panells Livewire de Direcció

Quick wins identificats:

- substituir `data-toggle` i `data-target` pels equivalents Bootstrap 5 en components compartits
- revisar tabs i modals comuns abans d'entrar a pantalles específiques
- preparar compatibilitat DataTables Bootstrap 5 en una sola capa d'infra

Risc alt identificat:

- `resources/assets/js/custom.js` per dependència de tooltips, popovers, modals jQuery i plugins legacy
- `resources/assets/js/ppIntranet.js` per dependència de `datatables.net-*-bs4`
- layouts i peces Gentelella amb `x_panel`, `x_title`, `x_content`, `panel-heading`, `pull-right`

Checklist de fase 1:

- infraestructura:
  - revisar substitució de `datatables.net-*-bs4` en `resources/assets/js/ppIntranet.js`
  - separar en `resources/assets/js/custom.js` el bloc BS4 directe del bloc de plugins no relacionats amb Bootstrap
- components compartits:
  - `resources/views/components/modal.blade.php`
  - `resources/views/components/ui/tabs.blade.php`
  - `resources/views/components/user-tabs.blade.php`
  - `resources/views/components/layouts/topnav.blade.php`
  - `resources/views/layouts/partials/titlecontent.blade.php`
  - `resources/views/layouts/partials/panel.blade.php`
- pantalles prioritàries per validar després del primer tall:
  - `resources/views/empresa/show.blade.php`
  - `resources/views/empresa/partials/centros.blade.php`
  - `resources/views/fct/show.blade.php`
  - `resources/views/fct/partials/colaboradores.blade.php`
  - `resources/views/livewire/falta-direccion-panel.blade.php`
  - `resources/views/livewire/comision-direccion-panel.blade.php`
  - `resources/views/livewire/actividad-direccion-panel.blade.php`
  - `resources/views/livewire/expediente-direccion-panel.blade.php`

## Criteris de tancament
- Fluxos crítics sense regressions visuals greus.
- DataTables i components UI compatibles amb Bootstrap 5 en pantalles prioritàries.
- Documentació d'estils i patrons actualitzada.

## Risc/impacte
Risc alt (visual), impacte alt en consistència i vida útil de la UI.

## Definició de fet (DoD) Sprint 4
- Components comuns validats en Bootstrap 5.
- QA visual executada en els fluxos prioritaris.
- Incidències visuals crítiques resoltes o documentades amb pla clar.
