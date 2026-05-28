# CLAUDE.md

> **Aquest fitxer és intencionadament curt.** La guia completa (workflow, estil, tests, commits, PRs, arquitectura, seguretat, llenguatge) viu a [`AGENTS.md`](AGENTS.md), que és la font autoritzada compartida per a tots els agents (Codex, Claude, Cursor, etc.). Llig-lo en complet abans de treballar.

## Què fer com a Claude Code

1. **Llig [`AGENTS.md`](AGENTS.md) primer** — conté regles, arquitectura del projecte i índex de coneixement de domini.
2. **Consulta el coneixement de domini a [`docs/agents/`](docs/agents/)** quan la tasca toque una àrea concreta. Els fitxers més habituals:
   - Convencions: [`docs/agents/conventions.md`](docs/agents/conventions.md)
   - Tests/Docker/Selenium: [`docs/agents/testing-docker.md`](docs/agents/testing-docker.md)
   - FCT: [`docs/agents/fct/`](docs/agents/fct/) (`fct-map.md`, `signatures.md`, `sao-selenium.md`)
   - Activitats: [`docs/agents/activitats/activitats-map.md`](docs/agents/activitats/activitats-map.md)
3. **Respecta les regles de commit i PR d'AGENTS.md**, incloent prefixos `[MOD]`/`[ADD]`/`[DEL]`/`[FIX]` i no afegir atribució d'IA.
4. **Llenguatge**: Valencià per a tot text d'usuari, comentaris i vistes.
