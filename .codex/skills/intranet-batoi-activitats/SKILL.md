---
name: intranet-batoi-activitats
description: Guia per treballar en activitats complementàries i extraescolars de la intranet Batoi. Use when the agent is asked to modify, debug, test, review, or explain activity flows including ActividadController, ActividadDireccionPanel, complementaria/extraescolar, fueraCentro, transport, participants, coordinador, autorització de direcció, valoració, ITACA/Gestib, or activity PDFs.
---

# Intranet Batoi Activitats

## Workflow

1. Llig `AGENTS.md` i usa `intranet-batoi-general` (o `docs/agents/conventions.md`) per a convencions de repo.
2. Comença per les rutes:
   - Flux professor: `routes/profesor.php`.
   - Flux direcció: `routes/direccion.php`.
   - Edició/recursos API: `routes/api.php`.
3. Consulta el mapa de domini abans d'editar: [`docs/agents/activitats/activitats-map.md`](../../../docs/agents/activitats/activitats-map.md).
4. Preserva els camps llegats (`complementaria`, `extraescolar`, `fueraCentro`, `transport`) llevat que es demane una migració explícita.
5. Mantén el copy en Valencià amb els conceptes separats (tipus / ubicació / transport).
6. Per al coordinador, usa la fila d'`actividad_profesor` amb `coordinador = 1` (no el primer participant).
7. Afig tests acotats quan canvia el mapping d'estats, la selecció de coordinador o la sortida renderitzada.

## Referències compartides

- Mapa complet d'activitats (rutes, fitxers clau, camps, copy UI, PDFs, notes de domini): [`docs/agents/activitats/activitats-map.md`](../../../docs/agents/activitats/activitats-map.md).
