# 🏫 Intranet CIP FP Batoi

[![PHP Tests](https://github.com/cipfpbatoi/intranetBatoi/actions/workflows/php-tests.yml/badge.svg?branch=main)](https://github.com/cipfpbatoi/intranetBatoi/actions/workflows/php-tests.yml)
[![Docker Build & Push](https://github.com/cipfpbatoi/intranetBatoi/actions/workflows/docker-build.yml/badge.svg?branch=main)](https://github.com/cipfpbatoi/intranetBatoi/actions/workflows/docker-build.yml)
[![Lang Audit](https://github.com/cipfpbatoi/intranetBatoi/actions/workflows/lang-audit.yml/badge.svg?branch=main)](https://github.com/cipfpbatoi/intranetBatoi/actions/workflows/lang-audit.yml)
![Laravel](https://img.shields.io/badge/Laravel-FF2D20?logo=laravel&logoColor=white)
![Vue 3](https://img.shields.io/badge/Vue_3-4FC08D?logo=vuedotjs&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF?logo=vite&logoColor=white)
![Bootstrap 5](https://img.shields.io/badge/Bootstrap_5-7952B3?logo=bootstrap&logoColor=white)
![Livewire 3](https://img.shields.io/badge/Livewire_3-4E56A6?logo=livewire&logoColor=white)

Aplicació de gestió interna del centre (Laravel, namespace `Intranet\`; Blade + Bootstrap 5 + Vue 3 + Livewire 3, assets amb Vite). Rutes separades per rol, capes `app/Domain` i `app/Application`, tests amb PHPUnit i Dusk.

## 🤖 Com treballem amb IA: Filosofia Tetris

Este repo està preparat perquè els agents d'IA (Claude Code, Codex, Cursor…) treballen de forma **predictible**: cada peça encaixa sense buits, i quan falta context **s'omple la peça** en lloc de suposar.

> 📐 **Comença ací:** [`docs/agents/tetris.md`](docs/agents/tetris.md) — mapa de les 4 peces i **guia d'ús al dia a dia** (receptes, exemple complet del flux OpenSpec, antipatrons).

| Vull… | Vés a… |
|---|---|
| 🧭 Entendre com treballar i quina eina usar | [`docs/agents/tetris.md`](docs/agents/tetris.md) |
| 📖 La guia autoritzada per a agents (router) | [`AGENTS.md`](AGENTS.md) |
| ✅ Implementar amb aprovació humana (propose → apply → archive) | [`docs/agents/openspec.md`](docs/agents/openspec.md) |
| 📋 Plantilles de prompt reutilitzables | [`prompts/`](prompts/) |
| 🧪 Especificacions de comportament (BDD) | [`specs/`](specs/) |
| 🗺️ Coneixement de domini (FCT, Activitats…) | [`docs/agents/`](docs/agents/README.md) |

## 📚 Documentació

Índex complet (arquitectura, operacions, manuals, governança): [`docs/README.md`](docs/README.md).

| Necessitat | Document |
|---|---|
| 🚀 Setup i instal·lació | [`docs/operations/setup.md`](docs/operations/setup.md) |
| 🧪 Tests, Docker i Dusk | [`docs/agents/testing-docker.md`](docs/agents/testing-docker.md) |
| 🐳 Desplegament Docker | [`docs/operations/docker-prod.md`](docs/operations/docker-prod.md) |
| 🗄️ Esquema de la BD | [`docs/architecture/bbdd-esquema.md`](docs/architecture/bbdd-esquema.md) |
| ❓ Preguntes freqüents | [`docs/governance/faqs.md`](docs/governance/faqs.md) |
| 👥 Manuals d'usuari | [`docs/manuals/`](docs/manuals/) |

## 🤝 Contribuir

- Llig `AGENTS.md` abans de tocar codi i completa el seu **Pre-flight Checklist**.
- Commits amb prefix `[ADD]`/`[MOD]`/`[DEL]`/`[FIX]` i referència a la issue (`#NNN`); sense atribució d'IA.
- Text d'usuari, comentaris i vistes en **Valencià**.
