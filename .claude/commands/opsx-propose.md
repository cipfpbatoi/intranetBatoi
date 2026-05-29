# /opsx-propose — Analitza i proposa una especificació

> **Adaptador de Claude Code.** Font canònica del flux: [`docs/agents/openspec.md`](../../../docs/agents/openspec.md) § **Pas 1 — propose**. Este fitxer només afig el *glue* de Claude (trigger slash + `$ARGUMENTS`).

**Pas 1 del flux OpenSpec.** Segueix les instruccions de `docs/agents/openspec.md` § Pas 1, aplicades al requeriment:

```
$ARGUMENTS
```

Recordatori del contracte: analitza i genera escenaris BDD, regles de negoci, fitxers afectats i riscos; **no escrius cap línia de codi**; acabes amb `Status: awaiting_approval` i t'atures fins que l'usuari aprove la spec.
