# Plantilla: Nova vista Blade

> Copia, substitueix `{{...}}` i envia.

---

## Context i Rol

Ets un desenvolupador PHP/Laravel expert en el projecte intranetBatoi.
Stack de vistes: Blade, Bootstrap 4, Gentelella admin theme, Livewire 3.
Llegeix `AGENTS.md` i `docs/agents/conventions.md` abans de generar res.

## Tasca

Crea la vista Blade per a **{{nom de la pantalla}}** (rol: `{{rol}}`).

## Especificacions

- Ruta de la vista: `resources/views/{{ruta/nom}}.blade.php`
- Layout base: `resources/views/layouts/app.blade.php` *(o `{{altre layout}}`)*
- Dades rebudes des del controlador: `{{variable: tipus, …}}`
- Components/parcials que ha d'incloure: `{{@include o @component}}`
- Formulari: `{{sí — mètode: POST/PUT, ruta: nom-ruta | no}}`
- Validació en client: `{{sí / no}}`
- Component Livewire: `{{sí — nom: NomComponent | no}}`

## Criteris de qualitat

- Tot el text visible en Valencià.
- Usar `AppAlert::render()` per a missatges d'èxit/error.
- Classes CSS alineades amb el tema Gentelella (no afegir CSS inline).
- Si hi ha formulari: token CSRF `@csrf` i mètode `@method` quan siga necessari.

## Format de resposta

1. Fitxer `resources/views/{{ruta/nom}}.blade.php` complet.
2. Si cal un parcial nou: `resources/views/intranet/partials/{{nom}}.blade.php`.
3. Si cal un component Livewire nou: `app/Livewire/{{Nom}}.php` i `resources/views/livewire/{{nom}}.blade.php`.
