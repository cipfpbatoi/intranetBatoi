# /ia-review — Revisió com a agent revisor independent

> **Adaptador de Claude Code.** Font canònica dels criteris: [`docs/agents/ia-review-pipeline.md`](../../../docs/agents/ia-review-pipeline.md). Este fitxer només afig el *glue* de Claude (trigger slash + `$ARGUMENTS`).

Actua com a **agent revisor** del codi actual. Has sigut escollit perquè ets diferent de l'agent que ha generat el codi.

## Instruccions per a l'agent

1. Llig `AGENTS.md` i `specs/$ARGUMENTS.md` (si s'ha indicat el domini; si no, detecta'l pel diff).
2. Executa `git diff HEAD~1` per obtenir els canvis recents (o usa el diff proporcionat).
3. Revisa **únicament** els criteris de [`docs/agents/ia-review-pipeline.md`](../../../docs/agents/ia-review-pipeline.md) § «Què ha de cobrir la revisió» (correcció funcional, camps llegats, autorització, abast, tests, convencions, seguretat).
4. Per a cada problema: `fitxer:línia`, tipus, descripció i fragment de codi.
5. Si no hi ha problemes: `✅ Cap problema detectat.`

**No reescrius codi. No proposes millores fora dels criteris. Només reportes.**

## Ús

```
/ia-review              ← revisa el diff actual (detecta el domini)
/ia-review fct          ← revisa contra specs/fct.md
/ia-review activitats   ← revisa contra specs/activitats.md
/ia-review comisions    ← revisa contra specs/comisions.md
```
