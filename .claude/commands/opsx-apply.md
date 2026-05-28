# /opsx-apply — Implementa la spec aprovada

**Pas 2 del flux OpenSpec.** Implementa estrictament la spec aprovada. No afegeix res fora del que descriu la spec.

## Instruccions per a l'agent

1. Llig `AGENTS.md` complet.
2. Llig `specs/<domini>.md` (o l'esborrany aprovat per l'usuari).
3. Verifica que la spec té `Status: approved` (o que l'usuari ha confirmat explícitament).
4. Si no hi ha spec aprovada: retorna `Status: need_input — falta spec aprovada`.
5. Implementa **únicament** el que descriu la spec:
   - Crea/modifica els fitxers afectats llistats.
   - Segueix les convencions de `docs/agents/conventions.md`.
   - Afig PHPDoc a classes i mètodes nous.
   - Text visible en Valencià.
6. Crea o actualitza els tests que verifiquen els escenaris de la spec (`tests/Feature/` o `tests/Browser/`).
7. **No refactoritzis** res fora de l'abast de la spec.
8. Al final, llista els fitxers modificats i escriu `Status: ready_for_review`.

## Verificació prèvia

Abans d'escriure codi comprova:
- [ ] Spec aprovada per l'usuari
- [ ] Bounded context identificat
- [ ] Cap camp llegat modificat sense migració explícita
- [ ] Tests nous cobreixen tots els escenaris de la spec
