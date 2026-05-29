# /opsx-apply — Implementa la spec aprovada

> **Adaptador de Claude Code.** Font canònica del flux: [`docs/agents/openspec.md`](../../../docs/agents/openspec.md) § **Pas 2 — apply**. Este fitxer només afig el *glue* de Claude.

**Pas 2 del flux OpenSpec.** Segueix les instruccions de `docs/agents/openspec.md` § Pas 2.

Recordatori del contracte: verifica que hi ha una spec aprovada (si no, `Status: need_input — falta spec aprovada`); implementa **únicament** el que descriu la spec, amb tests per a cada escenari; **no refactoritzes** res fora d'abast; acabes amb `Status: ready_for_review`.
