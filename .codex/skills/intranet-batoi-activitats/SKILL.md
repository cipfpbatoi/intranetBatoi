---
name: intranet-batoi-activitats
description: Guia per treballar en activitats complementàries i extraescolars de la intranet Batoi. Use when Codex is asked to modify, debug, test, review, or explain activity flows including ActividadController, ActividadDireccionPanel, complementaria/extraescolar, fueraCentro, transport, participants, coordinador, autorització de direcció, valoració, ITACA/Gestib, or activity PDFs.
---

# Intranet Batoi Activitats

## Workflow

1. Read `AGENTS.md` and use `intranet-batoi-general` for repository conventions.
2. Start from routes:
   - Professor activity flow: `routes/profesor.php`.
   - Direcció activity flow: `routes/direccion.php`.
   - API edit/resource flow: `routes/api.php`.
3. Trace these core files before editing:
   - `app/Http/Controllers/ActividadController.php`.
   - `app/Livewire/ActividadDireccionPanel.php` for the direcció panel.
   - `app/Entities/Actividad.php` and `app/Entities/ActividadProfesor.php`.
   - `app/Presentation/Crud/ActividadCrudSchema.php`.
   - `app/Http/Requests/ActividadRequest.php`.
4. Preserve the legacy stored fields unless a migration is explicitly required:
   - `complementaria`: separates complementària from extraescolar in the activity form.
   - `extraescolar`: legacy/module flag used to include activities in the extraescolars flow; do not assume it is the same as the user-facing complementària/extraescolar choice.
   - `fueraCentro`: whether the activity happens outside the school.
   - `transport`: only meaningful when `fueraCentro = 1`.
5. For UI copy, use Valencià and keep concepts separate:
   - Type: `Complementària` / `Extraescolar`.
   - Location: `Dins del centre` / `Fora del centre`.
   - Transport: only applies outside the centre.
6. For participants and coordinador:
   - The real coordinator is the row in `actividad_profesor` with `coordinador = 1`.
   - Do not use the first participant as coordinator.
   - Use `Actividad::Creador()` or pivot `coordinador` where appropriate.
7. For PDFs, check the relevant Blade:
   - `resources/views/pdf/extraescolars.blade.php`.
   - `resources/views/pdf/valoracionActividad.blade.php`.
   - `resources/views/extraescolares/showValue.blade.php`.
8. Add focused tests for state mapping, coordinador selection, or rendered output when behaviour changes.

## Domain Notes

- Complementàries have RA (Resultats d'Aprenentatge); extraescolars do not.
- If a field represents RA justification, show it only for complementàries.
- The combination `fueraCentro = 0, transport = 1` is incoherent and should be avoided or normalized.
