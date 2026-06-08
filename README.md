# Intranet CIP FP Batoi

Aplicació de gestió interna del centre (Laravel, namespace `Intranet\`; Blade + Bootstrap 4/Gentelella + Vue 2 + Livewire 3). Rutes separades per rol, capes `app/Domain` i `app/Application`, tests amb PHPUnit i Dusk.

## Com treballem amb IA: Filosofia Tetris

Este repo està preparat perquè els agents d'IA (Claude Code, Codex, Cursor…) treballen de forma **predictible**: cada peça encaixa sense buits, i quan falta context **s'omple la peça** en lloc de suposar.

> 📐 **Comença ací:** [`docs/agents/tetris.md`](docs/agents/tetris.md) — mapa de les 4 peces i **guia d'ús al dia a dia** (receptes, exemple complet del flux OpenSpec, antipatrons).

| Vull… | Vés a… |
|---|---|
| Entendre com treballar i quina eina usar | [`docs/agents/tetris.md`](docs/agents/tetris.md) |
| La guia autoritzada per a agents (router) | [`AGENTS.md`](AGENTS.md) |
| Implementar amb aprovació humana (propose → apply → archive) | [`docs/agents/openspec.md`](docs/agents/openspec.md) |
| Plantilles de prompt reutilitzables | [`prompts/`](prompts/) |
| Especificacions de comportament (BDD) | [`specs/`](specs/) |
| Coneixement de domini (FCT, Activitats…) | [`docs/agents/`](docs/agents/README.md) |

## Documentació

Índex complet de la documentació (arquitectura, operacions, manuals, governança): [`docs/README.md`](docs/README.md).

- Setup i instal·lació: [`docs/operations/setup.md`](docs/operations/setup.md)
- Tests, Docker i Dusk: [`docs/agents/testing-docker.md`](docs/agents/testing-docker.md)
- Esquema de la BD: [`docs/architecture/bbdd-esquema.md`](docs/architecture/bbdd-esquema.md)

## Contribuir

- Llig `AGENTS.md` abans de tocar codi i completa el seu **Pre-flight Checklist**.
- Commits amb prefix `[ADD]`/`[MOD]`/`[DEL]`/`[FIX]` i referència a la issue (`#NNN`); sense atribució d'IA.
- Text d'usuari, comentaris i vistes en **Valencià**.
