---
name: openspec
description: Flux OpenSpec de tres passos per a implementar funcionalitats amb aprovació humana. Usa quan l'usuari demana un nou feature, modificació de comportament existent, o quan cal escriure una spec BDD (Given/When/Then) abans d'implementar. Els tres passos són propose (analitza i proposa spec), apply (implementa la spec aprovada) i archive (valida tests i tanca).
---

# OpenSpec — adaptador per a Codex

> **Adaptador prim.** Esta skill només és el punt d'entrada de Codex al flux. Les instruccions completes i autoritzades viuen a la **font canònica agnòstica**: [`docs/agents/openspec.md`](../../../docs/agents/openspec.md). Llig-la i segueix-la.

Tres passos que obliguen a parar i esperar confirmació humana abans del següent:

1. **propose** (`opsx-propose <requeriment>`) — analitza, genera escenaris BDD + regles + fitxers + riscos, escriu `Status: awaiting_approval` i s'atura. **No escriu codi.**
2. **apply** (`opsx-apply`) — implementa **únicament** la spec aprovada amb tests, escriu `Status: ready_for_review` i s'atura.
3. **archive** (`opsx-archive`) — valida tests, marca escenaris `✅`, suggereix el commit (`#issue`), escriu `Status: ready_to_commit`. **No fa el commit.**

El detall de cada pas, els formats d'eixida i les regles del flux estan a [`docs/agents/openspec.md`](../../../docs/agents/openspec.md). Specs de domini: [`specs/`](../../../specs/). Plantilles de prompt: [`prompts/`](../../../prompts/).
