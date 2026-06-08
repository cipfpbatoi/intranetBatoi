---
name: intranet-batoi-general
description: Orientació general per treballar en el projecte intranetBatoi. Use when the agent is asked to modify, debug, test, review, document, commit, or explain code in this Laravel intranet, especially tasks involving project structure, routes by role, alerts, Blade views, local conventions, PHPUnit/Dusk, Docker, commits, or Valencià language expectations.
---

# Intranet Batoi General

## Workflow

1. Llig `AGENTS.md` primer i segueix les seues regles de repositori (és la font autoritzada compartida per a tots els agents).
2. Inspecciona el fitxer de ruta rellevant abans de canviar un controlador o vista.
3. Prefereix serveis de domini, finders, policies, presenters, helpers UI i partials Blade existents per damunt d'abstraccions noves.
4. Mantén el text d'usuari en Valencià llevat que la plantilla existent ja siga bilingüe.
5. Usa `Intranet\Services\UI\AppAlert as Alert` per a alertes; no reintroduïsques `Styde\Html\Facades\Alert`.
6. Afig o actualitza PHPDoc en classes PHP modificades/noves i mètodes/propietats rellevants.
7. Si toques rutes, comprova noms duplicats i `route:cache`; si toques graelles, revisa la skill `intranet-batoi-ui`.
8. Executa les comprovacions més acotades que tinguen sentit. Si PHP no està disponible localment, indica-ho clarament.

## Referències compartides (`docs/agents/`)

> Aquests fitxers viuen a `docs/agents/` i són la font de veritat per a qualsevol agent (Codex, Claude, etc.). Aquesta skill només és un punt d'entrada amb metadades per a Codex.

- Estructura de repo, alertes, llenguatge i convencions de commit: [`docs/agents/conventions.md`](../../../docs/agents/conventions.md).
- Graelles i DataTables: [`docs/agents/ui/grid-datatables.md`](../../../docs/agents/ui/grid-datatables.md).
- Tests i notes Docker/Selenium: [`docs/agents/testing-docker.md`](../../../docs/agents/testing-docker.md).
