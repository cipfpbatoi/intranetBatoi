# Flux OpenSpec

Procediment de tres passos que obliga l'agent a demanar aprovació humana abans d'implementar. Garanteix disseny iteratiu i control de qualitat.

```
Requeriment inicial
       │
       ▼
 /opsx-propose          ← Agent analitza, genera spec, s'atura
       │
  [Revisió humana]      ← Tu aproves, rebutges o ajustes la spec
       │
       ▼
 /opsx-apply            ← Agent implementa estrictament la spec aprovada
       │
  [Revisió humana]      ← Tu revises el codi generat
       │
       ▼
 /opsx-archive          ← Agent valida tests, actualitza spec, suggereix commit
       │
  [Commit humà]         ← Tu fas el commit i el push
```

## Com usar-lo

### Amb Claude Code (CLI)
```
/opsx-propose Afegir exportació CSV a la llista d'empreses FCT
/opsx-apply
/opsx-archive
```

Els slash commands estan definits a `.claude/commands/`.

### Amb Codex
Activar la skill `openspec` i demanar cada pas explícitament.

### Manual (qualsevol agent)
Copia les instruccions del pas corresponent de `.claude/commands/opsx-*.md` i envia-les com a prompt.

## Regles del flux

- **propose** no escriu mai codi. Si l'agent escriu codi sense aprovació, el pas és incorrecte.
- **apply** no afegeix res fora de la spec. Si l'agent "millora" coses no sol·licitades, cal revertir-ho.
- **archive** no fa el commit. El commit el fa sempre un humà.
- Si els tests fallen en `archive`, el flux torna a `apply` (no s'arxiva codi trencat).

## On viuen les specs

Les especificacions aprovades i implementades es guarden a [`specs/`](../../specs/):
- [`specs/fct.md`](../../specs/fct.md)
- [`specs/activitats.md`](../../specs/activitats.md)

Quan `opsx-archive` completa un escenari, el marca amb `✅` al fitxer de spec corresponent.
