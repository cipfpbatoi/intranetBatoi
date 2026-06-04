---
name: intranet-batoi-ui
description: Guia per treballar en UI operativa, graelles, components Blade, Panel/Pestana, botons i DataTables de la intranet Batoi. Use when the agent is asked to modify, debug, test, review, or explain table listings, /tutoria grids, DataTables warnings, reusable Blade grid components, Panel UI, BotonIcon/BotonBasico, or operational screens.
---

# Intranet Batoi UI

## Workflow

1. Llig `AGENTS.md` i la skill `intranet-batoi-general`.
2. Identifica si la pantalla usa `BaseController`, `Panel`, components Blade de graella o inicialització JavaScript pròpia.
3. Per errors DataTables, comprova primer coherència de columnes i files generades, especialment l'estat buit.
4. Mantín les graelles com a interfícies operatives: compactes, escanejables i coherents amb Bootstrap/Gentelella.
5. Executa el test més acotat de component o Feature; per la taula base usa `GridTableComponentTest`.

## Referències Compartides

- Graelles, DataTables i estat buit: [`docs/agents/ui/grid-datatables.md`](../../../docs/agents/ui/grid-datatables.md).
- Convencions generals, rutes i validació: [`docs/agents/conventions.md`](../../../docs/agents/conventions.md).
