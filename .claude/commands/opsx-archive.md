# /opsx-archive — Tanca i arxiva la implementació

**Pas 3 del flux OpenSpec.** Valida que tot és correcte, actualitza la spec permanent i prepara el commit.

## Instruccions per a l'agent

1. Comprova que els tests passen:
   - `php artisan test --filter=<NomTest>` (o comanda Docker equivalent).
   - Si fallen: reporta quins i atura't (`Status: tests_failing`).
2. Actualitza `specs/<domini>.md` amb els escenaris nous/modificats (si no hi eren ja).
3. Marca els escenaris implementats amb `✅` al fitxer de spec.
4. Comprova que `AGENTS.md` i `docs/agents/` no necessiten actualització per als canvis introduïts.
5. Llista tots els fitxers modificats i escriu `Status: ready_to_commit`.
6. **No fa el commit.** L'usuari revisa i confirma.

## Checklist de tancament

- [ ] Tests en verd (PHPUnit + Dusk si aplica)
- [ ] `specs/<domini>.md` actualitzat
- [ ] Cap fitxer no relacionat modificat
- [ ] Missatge de commit preparat amb prefix `[ADD]`/`[MOD]`/`[FIX]` i referència `#issue`

## Format de resposta

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
