# Filosofia Tetris (IA) — mapa del repositori

Document d'entrada. Explica **com està organitzat este repo per a treballar amb agents d'IA** i a quin artefacte correspon cada peça. Si véns de nou (humà o IA), llig açò primer i després el fitxer concret que necessites.

## Regla Zero: programació predictible

El codi i la documentació han de ser **avorrits i predictibles**: cada peça encaixa sense buits. **Zero buits = zero suposicions.** Quan un agent ha de suposar, és que falta una peça (una spec, una convenció, un camp documentat). La resposta correcta no és que l'agent endevine, sinó **omplir el buit** al fitxer que toca.

Conseqüència pràctica (a `AGENTS.md` § Execution Rules):

- Si l'stack o el requeriment és ambigu → **una sola pregunta concreta**, no suposes.
- **Menys és més**: dona a l'agent la informació mínima vital, no tot el context.
- **Res fora d'abast**: sense refactors ni funcionalitats no sol·licitades.

## Les 4 peces

Cada peça té una **font canònica única** i un lloc físic al repo. Quan l'agent es comporta malament, **refines la peça**, no el prompt puntual.

| Peça | Què és | On viu | Quan la refines |
|---|---|---|---|
| **1. Prompt (SISO)** | Entrada estructurada → eixida estructurada. Plantilles reutilitzables amb buits `{{...}}`. | [`prompts/`](../../prompts/) | Quan repeteixes el mateix encàrrec >3 vegades. |
| **2. Router (`AGENTS.md`)** | Guia única i autoritzada per a **qualsevol** agent. Pre-flight, regles d'execució i índex cap a la resta. | [`AGENTS.md`](../../AGENTS.md) | Quan una norma apareix en >3 prompts (regla de les 3 vegades). |
| **3. Docs vives** | Coneixement de domini amb alta cohesió i baix acoblament. | [`docs/agents/`](README.md) | Quan l'IA s'equivoca en un domini concret. |
| **4. Specs + Tests** | Veritat immutable del **què** (no del com), en BDD Given/When/Then, agnòstica de tecnologia. | [`specs/`](../../specs/) + `tests/` | Quan canvia el comportament esperat. |

```
        Prompt (SISO)
            │  entrada estructurada
            ▼
   AGENTS.md (router) ──► docs/agents/ (com es fa ací)
            │
            ▼
   specs/ + tests/ (què ha de passar = veritat)
```

## Configuracions d'IA: regla de l'adaptador prim

Cada motor (Claude Code, Codex, Cursor…) té el seu mecanisme propi per a invocar instruccions: slash commands a `.claude/commands/`, skills a `.codex/skills/`, etc. Eixos mecanismes són **inevitablement específics del motor**, però el seu **contingut no ha de ser-ho**.

> **Regla**: el *contingut* (passos d'un flux, criteris de revisió, coneixement de domini) viu **una sola vegada** en una font canònica agnòstica (`docs/`, `specs/` o `prompts/`). El fitxer de cada motor és un **adaptador prim**: només afig el *glue* específic del motor i apunta a la font canònica.

| Mecanisme (específic del motor) | Glue que pot contindre | Font canònica (agnòstica) |
|---|---|---|
| `.claude/commands/opsx-*.md` | trigger slash, `$ARGUMENTS`, contracte d'eixida `Status:` | [`docs/agents/openspec.md`](openspec.md) |
| `.codex/skills/openspec/` | `name`/`description` (YAML), `agents/openai.yaml` | [`docs/agents/openspec.md`](openspec.md) |
| `.claude/commands/ia-review.md` | trigger slash, `$ARGUMENTS` | [`docs/agents/ia-review-pipeline.md`](ia-review-pipeline.md) |
| `.codex/skills/intranet-batoi-*` | metadades d'activació de la skill | `docs/agents/**` (conventions, fct, activitats) |

Així, afegir un motor nou (p. ex. Cursor) és escriure un adaptador prim nou; **mai** reescriure el flux. I corregir un flux és editar un sol fitxer canònic, no N còpies.

## Fluxos que segueixen este model

- **OpenSpec** (`propose → apply → archive`, amb aprovació humana entre passos): font canònica [`openspec.md`](openspec.md).
- **IA revisora ≠ IA generadora** (qui escriu no revisa): font canònica [`ia-review-pipeline.md`](ia-review-pipeline.md).

## Responsabilitat

L'IA ajuda i actua, però **tu eres el responsable**. Cada commit porta la teua firma, no la de l'agent (per això no s'afig atribució d'IA als commits).
