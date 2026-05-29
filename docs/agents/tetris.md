# Filosofia Tetris (IA) — mapa i guia d'ús

Document d'entrada. Explica **com està organitzat este repo per a treballar amb agents d'IA** i **com usar-lo al dia a dia**. Si véns de nou (humà o IA), llig açò primer.

- Tens pressa i només vols saber què fer? → [Com s'usa al dia a dia](#com-susa-al-dia-a-dia).
- Vols entendre el perquè? → continua per [Regla Zero](#regla-zero-programació-predictible) i [Les 4 peces](#les-4-peces).

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

## Com s'usa al dia a dia

### Per on començar (arbre de decisió)

| Tens... | Fes... | Peça / eina |
|---|---|---|
| Una **funcionalitat nova** o un **canvi de comportament** | Flux OpenSpec: `propose → apply → archive` | [Recepta 1](#recepta-1-funcionalitat-nova-o-canvi-de-comportament) |
| Una **tasca que repeteixes** (nou controlador, entitat, test, vista…) | Copia una plantilla de `prompts/` | [Recepta 2](#recepta-2-una-tasca-que-repeteixes) |
| L'**IA s'equivoca** sempre en un domini | Refina el doc de `docs/agents/` (no el prompt) | [Recepta 3](#recepta-3-lia-sequivoca-en-un-domini) |
| Codi **generat per IA** a punt de revisar | Revisió creuada amb `ia-review` | [Recepta 4](#recepta-4-revisar-codi-generat-per-ia) |
| Un **bug menut i clar**, sense canvi de comportament | Arregla'l directe + test de regressió. No cal spec nova. | `tests/` |

> **Abans de res, sempre**: completa el [Pre-flight Checklist](../../AGENTS.md#pre-flight-checklist) d'`AGENTS.md` (llig AGENTS.md, identifica el domini i el seu doc, confirma el bounded context, llig la spec si existeix). Si falta una peça, l'agent ha de retornar `Status: need_input`, no suposar.

### Recepta 1: Funcionalitat nova o canvi de comportament

Usa el flux **OpenSpec** (font canònica: [`openspec.md`](openspec.md)). Tres passos amb **aprovació humana** entre cadascun:

1. **propose** — l'agent analitza i proposa una spec BDD (escenaris Given/When/Then, regles, fitxers afectats, riscos). **No escriu codi.** Acaba en `Status: awaiting_approval`.
2. *(tu revises i aproves la spec)*
3. **apply** — l'agent implementa **únicament** la spec, amb tests. Acaba en `Status: ready_for_review`.
4. *(tu revises el codi; opcionalment el passes per la [Recepta 4](#recepta-4-revisar-codi-generat-per-ia))*
5. **archive** — l'agent valida tests, marca els escenaris `✅` a la spec i suggereix el commit. **No fa el commit.** Acaba en `Status: ready_to_commit`.
6. *(tu fas el commit referenciant la issue)*

Invocació segons el motor (vegeu la [taula d'eines](#quina-eina-per-a-quin-motor)).

### Recepta 2: Una tasca que repeteixes

Si una tasca apareix >3 vegades, ja té (o ha de tindre) una plantilla a [`prompts/`](../../prompts/). Copia-la, ompli els buits `{{...}}` i envia-la:

| Vull… | Plantilla |
|---|---|
| Un controlador nou (CRUD o personalitzat) | `prompts/nou-controlador.md` |
| Una entitat Eloquent + Policy + migració | `prompts/nova-entitat.md` |
| Un test Feature PHPUnit | `prompts/nou-test-feature.md` |
| Una vista Blade amb layout i parcials | `prompts/nova-vista-blade.md` |
| Extraure lògica d'un controlador a un Service | `prompts/refactor-a-service.md` |
| Una revisió de codi (vegeu Recepta 4) | `prompts/review-checklist.md` |

Si la tasca encara no té plantilla i la repetiràs, **crea-la** (regla de les 3 vegades).

### Recepta 3: L'IA s'equivoca en un domini

No pegues pedaços al prompt cada vegada. **Refina el doc de domini** a `docs/agents/` perquè qualsevol agent encerte a la primera:

- FCT → [`fct/fct-map.md`](fct/fct-map.md), [`fct/signatures.md`](fct/signatures.md), [`fct/sao-selenium.md`](fct/sao-selenium.md)
- Activitats → [`activitats/activitats-map.md`](activitats/activitats-map.md)
- Convencions transversals → [`conventions.md`](conventions.md)

Si la norma és global (apareix en >3 prompts de qualsevol domini), va a `AGENTS.md`.

### Recepta 4: Revisar codi generat per IA

**Qui escriu no revisa.** Usa un motor diferent com a revisor independent (font canònica: [`ia-review-pipeline.md`](ia-review-pipeline.md)):

- Amb Claude Code: `/ia-review [domini]`.
- Sense segon motor a mà: copia [`prompts/review-checklist.md`](../../prompts/review-checklist.md).

El revisor només **reporta** (correcció, camps llegats, autorització, abast, tests, convencions, seguretat); no reescriu.

### Exemple complet (end-to-end)

Requeriment: *«Afegir exportació CSV a la llista d'empreses FCT.»*

```
# 1. PROPOSE  (Claude: /opsx-propose  ·  Codex: skill openspec)
/opsx-propose Afegir exportació CSV a la llista d'empreses FCT

→ L'agent llig AGENTS.md + docs/agents/fct/fct-map.md, proposa:
  Escenari 1: Given un professor FCT, When prem «Exporta CSV», Then es descarrega…
  Regles, fitxers afectats (EmpresaController, ruta, vista), riscos.
  Status: awaiting_approval
                                   ← [tu aproves o ajustes la spec]

# 2. APPLY
/opsx-apply
→ Implementa NOMÉS la spec + test a tests/Feature/. Sense extres.
  Status: ready_for_review
                                   ← [tu revises; opcional: /ia-review fct amb un altre motor]

# 3. ARCHIVE
/opsx-archive
→ Executa tests, marca escenaris ✅ a specs/fct.md, suggereix:
  [ADD] Exportació CSV d'empreses FCT #<issue>
  Status: ready_to_commit
                                   ← [tu fas el commit referenciant la issue]
```

### Quina eina per a quin motor

El **contingut** (passos, criteris) és el mateix per a tots; només canvia com l'invoques.

| Pas / acció | Claude Code | Codex | Manual (qualsevol agent) |
|---|---|---|---|
| OpenSpec | `/opsx-propose`, `/opsx-apply`, `/opsx-archive` | skill `openspec` | seguir [`openspec.md`](openspec.md) |
| Revisió creuada | `/ia-review [domini]` | demanar revisió segons criteris | [`prompts/review-checklist.md`](../../prompts/review-checklist.md) |
| Coneixement de domini | llegir `docs/agents/**` | skills `intranet-batoi-*` | llegir `docs/agents/**` |

Els fitxers de cada motor (`.claude/commands/`, `.codex/skills/`) són **adaptadors prims**: només afigen el seu *glue* i apunten a la font canònica (vegeu [regla de l'adaptador prim](#configuracions-dia-regla-de-ladaptador-prim)).

### Antipatrons (què NO fer)

- ❌ Escriure codi en el pas **propose** (encara no hi ha spec aprovada).
- ❌ «Millorar» coses no demanades durant **apply** (trenca la Regla Zero d'abast).
- ❌ Que l'agent **endevine** quan falta context: ha de retornar `Status: need_input`.
- ❌ Repetir la mateixa instrucció en cada prompt: si passa >3 vegades, va a `docs/agents/` o `AGENTS.md`.
- ❌ Revisar amb **el mateix motor** que ha generat el codi.
- ❌ Tocar **camps llegats** (`complementaria`, `extraescolar`, `sendTo`, `signed`…) sense migració explícita.
- ❌ Fer el **commit** des de l'agent en `archive`: el commit el fa (i el firma) un humà.

## Configuracions d'IA: regla de l'adaptador prim

Cada motor (Claude Code, Codex, Cursor…) té el seu mecanisme propi per a invocar instruccions: slash commands a `.claude/commands/`, skills a `.codex/skills/`, etc. Eixos mecanismes són **inevitablement específics del motor**, però el seu **contingut no ha de ser-ho**.

> **Regla**: el *contingut* (passos d'un flux, criteris de revisió, coneixement de domini) viu **una sola vegada** en una font canònica agnòstica (`docs/`, `specs/` o `prompts/`). El fitxer de cada motor és un **adaptador prim**: només afig el *glue* específic del motor i apunta a la font canònica.

| Mecanisme (específic del motor) | Glue que pot contindre | Font canònica (agnòstica) |
|---|---|---|
| `.claude/commands/opsx-*.md` | trigger slash, `$ARGUMENTS`, contracte d'eixida `Status:` | [`openspec.md`](openspec.md) |
| `.codex/skills/openspec/` | `name`/`description` (YAML), `agents/openai.yaml` | [`openspec.md`](openspec.md) |
| `.claude/commands/ia-review.md` | trigger slash, `$ARGUMENTS` | [`ia-review-pipeline.md`](ia-review-pipeline.md) |
| `.codex/skills/intranet-batoi-*` | metadades d'activació de la skill | `docs/agents/**` (conventions, fct, activitats) |

Així, afegir un motor nou (p. ex. Cursor) és escriure un adaptador prim nou; **mai** reescriure el flux. I corregir un flux és editar un sol fitxer canònic, no N còpies.

## Fluxos que segueixen este model

- **OpenSpec** (`propose → apply → archive`, amb aprovació humana entre passos): font canònica [`openspec.md`](openspec.md).
- **IA revisora ≠ IA generadora** (qui escriu no revisa): font canònica [`ia-review-pipeline.md`](ia-review-pipeline.md).

## Responsabilitat

L'IA ajuda i actua, però **tu eres el responsable**. Cada commit porta la teua firma, no la de l'agent (per això no s'afig atribució d'IA als commits).
