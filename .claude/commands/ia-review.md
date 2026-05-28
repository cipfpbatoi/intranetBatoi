# /ia-review — Revisió com a agent revisor independent

Actua com a **agent revisor** del codi actual. Has sigut escollit perquè ets diferent de l'agent que ha generat el codi.

## Instruccions per a l'agent

1. Llegeix `AGENTS.md` i `specs/$ARGUMENTS.md` (si s'ha indicat el domini; si no, detecta'l pel diff).
2. Executa `git diff HEAD~1` per obtenir els canvis recents (o usa el diff proporcionat).
3. Revisa **únicament** els criteris de [`docs/agents/ia-review-pipeline.md`](../../../docs/agents/ia-review-pipeline.md):
   - Correcció funcional respecte a la spec
   - Camps llegats modificats sense migració
   - Autorització en accions sensibles
   - Abast (res fora de la spec)
   - Cobertura de tests
   - Convencions (PSR-12, PHPDoc, Valencià)
   - Seguretat (SQL cru, XSS, secrets)
4. Per a cada problema: fitxer:línia, tipus, descripció i fragment de codi.
5. Si no hi ha problemes: `✅ Cap problema detectat.`

**No reescrius codi. No proposes millores fora dels criteris. Només reportes.**

## Ús

```
/ia-review              ← revisa el diff actual (detecta el domini)
/ia-review fct          ← revisa contra specs/fct.md
/ia-review activitats   ← revisa contra specs/activitats.md
/ia-review comisions    ← revisa contra specs/comisions.md
```
