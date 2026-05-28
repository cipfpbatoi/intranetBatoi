---
name: openspec
description: Flux OpenSpec de tres passos per a implementar funcionalitats amb aprovació humana. Usa quan l'usuari demana un nou feature, modificació de comportament existent, o quan cal escriure una spec BDD (Given/When/Then) abans d'implementar. Els tres passos són: propose (analitza i proposa spec), apply (implementa la spec aprovada), archive (valida tests i tanca).
---

# OpenSpec — Flux d'implementació amb aprovació humana

Tres passos que obliguen l'agent a parar i esperar confirmació humana abans de passar al següent.

## Pas 1: propose

**Trigger**: `opsx-propose <descripció del requeriment>`

1. Llig `AGENTS.md` i el doc de domini de `docs/agents/`.
2. Analitza el requeriment.
3. Genera escenaris BDD (Given/When/Then), regles de negoci, fitxers afectats i riscos.
4. Escriu `Status: awaiting_approval` i s'atura.

**No escriu codi.**

## Pas 2: apply

**Trigger**: `opsx-apply` (després que l'usuari aprove la spec)

1. Verifica que hi ha una spec aprovada.
2. Implementa **únicament** el que descriu la spec.
3. Crea tests per a cada escenari de la spec.
4. Escriu `Status: ready_for_review` i s'atura.

## Pas 3: archive

**Trigger**: `opsx-archive` (després de revisar el codi)

1. Executa els tests; si fallen, para amb `Status: tests_failing`.
2. Actualitza `specs/<domini>.md` marcant escenaris amb `✅`.
3. Suggereix el missatge de commit (amb prefix i `#issue`).
4. Escriu `Status: ready_to_commit` i s'atura.

**No fa el commit.** L'usuari confirma.

## Referències

- Documentació completa: [`docs/agents/openspec.md`](../../../docs/agents/openspec.md)
- Specs de domini: [`specs/`](../../../specs/)
- Plantilles de prompt: [`prompts/`](../../../prompts/)
