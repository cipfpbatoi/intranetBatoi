# Flux OpenSpec

**Font canònica i agnòstica** del flux de tres passos que obliga l'agent a demanar aprovació humana abans d'implementar. Garanteix disseny iteratiu i control de qualitat.

Qualsevol motor (Claude Code, Codex, Cursor…) segueix **estes** instruccions. Els fitxers específics de motor (`.claude/commands/opsx-*.md`, `.codex/skills/openspec/`) són adaptadors prims que només afigen el seu *glue* i apunten ací (vegeu [`tetris.md`](tetris.md) § adaptador prim).

```
Requeriment inicial
       │
       ▼
 propose                ← Agent analitza, genera spec, s'atura
       │
  [Revisió humana]      ← Tu aproves, rebutges o ajustes la spec
       │
       ▼
 apply                  ← Agent implementa estrictament la spec aprovada
       │
  [Revisió humana]      ← Tu revises el codi generat
       │
       ▼
 archive                ← Agent valida tests, actualitza spec, suggereix commit
       │
  [Commit humà]         ← Tu fas el commit i el push
```

## Pas 1 — propose (analitza i proposa una spec)

Analitza el requeriment, genera un esborrany de spec i s'atura per a aprovació humana. **No escriu cap línia de codi.**

1. Llig `AGENTS.md` i el doc de domini corresponent de `docs/agents/`.
2. Identifica el bounded context afectat.
3. Si ja existeix `specs/<domini>.md`, llig-lo per a no duplicar escenaris.
4. Analitza el requeriment i genera:
   - **Escenaris** BDD Given/When/Then (mínim 3, màxim 8).
   - **Regles de negoci invariants** que l'escenari introdueix o modifica.
   - **Fitxers afectats** (controladors, entitats, vistes, tests) sense tocar-los.
   - **Riscos detectats** (efectes col·laterals, camps llegats, dependències).
5. Mostra el resultat i escriu `Status: awaiting_approval`.
6. **Atura't.** No implementes fins que l'usuari aprove la spec.

Format d'eixida:

```
## Spec proposada: <títol>

### Escenaris
**Escenari N: <títol>**
Given ...
When ...
Then ...

### Regles de negoci
- ...

### Fitxers afectats
- `ruta/fitxer.php` — motiu

### Riscos
- ...

Status: awaiting_approval
```

## Pas 2 — apply (implementa la spec aprovada)

Implementa **estrictament** la spec aprovada. No afig res fora del que descriu.

1. Llig `AGENTS.md` complet.
2. Llig `specs/<domini>.md` (o l'esborrany aprovat per l'usuari).
3. Verifica que la spec té `Status: approved` (o confirmació explícita de l'usuari). Si no: retorna `Status: need_input — falta spec aprovada`.
4. Implementa **únicament** el que descriu la spec:
   - Crea/modifica els fitxers afectats llistats.
   - Segueix `docs/agents/conventions.md`; afig PHPDoc a classes/mètodes nous; text visible en Valencià.
5. Crea o actualitza els tests que verifiquen els escenaris (`tests/Feature/` o `tests/Browser/`).
6. **No refactoritzes** res fora de l'abast de la spec.
7. Llista els fitxers modificats i escriu `Status: ready_for_review`.

Verificació prèvia (abans d'escriure codi):

- [ ] Spec aprovada per l'usuari
- [ ] Bounded context identificat
- [ ] Cap camp llegat modificat sense migració explícita
- [ ] Tests nous cobreixen tots els escenaris de la spec

## Pas 3 — archive (valida i tanca)

Valida que tot és correcte, actualitza la spec permanent i prepara el commit.

1. Comprova que els tests passen (`php artisan test --filter=<NomTest>` o equivalent Docker). Si fallen: reporta quins i atura't amb `Status: tests_failing`.
2. Actualitza `specs/<domini>.md` amb els escenaris nous/modificats i marca'ls amb `✅`.
3. Comprova que `AGENTS.md` i `docs/agents/` no necessiten actualització pels canvis.
4. Llista tots els fitxers modificats i escriu `Status: ready_to_commit`.
5. **No fa el commit.** L'usuari revisa i confirma.

Checklist de tancament:

- [ ] Tests en verd (PHPUnit + Dusk si aplica)
- [ ] `specs/<domini>.md` actualitzat (escenaris marcats `✅`)
- [ ] Cap fitxer no relacionat modificat
- [ ] Missatge de commit preparat amb prefix `[ADD]`/`[MOD]`/`[FIX]` i referència `#issue`

Format d'eixida:

```
## Resum de la implementació

### Fitxers modificats
- `ruta/fitxer.php` — descripció del canvi

### Tests
- `tests/Feature/NomTest.php::test_nom` ✅

### Spec actualitzada
- `specs/<domini>.md` — escenaris N, M marcats ✅

### Missatge de commit suggerit
[ADD] <descripció> #<issue>

Status: ready_to_commit
```

## Regles del flux

- **propose** no escriu mai codi. Si l'agent escriu codi sense aprovació, el pas és incorrecte.
- **apply** no afig res fora de la spec. Si l'agent "millora" coses no sol·licitades, cal revertir-ho.
- **archive** no fa el commit. El commit el fa sempre un humà.
- Si els tests fallen en `archive`, el flux torna a `apply` (no s'arxiva codi trencat).

## Com invocar-lo per motor (adaptadors prims)

- **Claude Code (CLI)**: `/opsx-propose <requeriment>` → `/opsx-apply` → `/opsx-archive` (`.claude/commands/opsx-*.md`).
- **Codex**: activa la skill `openspec` i demana cada pas (`.codex/skills/openspec/`).
- **Qualsevol altre agent (manual)**: copia el pas corresponent **d'este document** i envia'l com a prompt. No cal cap fitxer específic de motor.

## On viuen les specs

Les especificacions aprovades i implementades es guarden a [`specs/`](../../specs/):

- [`specs/fct.md`](../../specs/fct.md)
- [`specs/activitats.md`](../../specs/activitats.md)
- [`specs/comisions.md`](../../specs/comisions.md)
- [`specs/guardies.md`](../../specs/guardies.md)
- [`specs/horaris.md`](../../specs/horaris.md)

Quan `archive` completa un escenari, el marca amb `✅` al fitxer de spec corresponent.
