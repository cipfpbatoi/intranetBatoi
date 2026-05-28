# /opsx-propose — Analitza i proposa una especificació

**Pas 1 del flux OpenSpec.** Analitza el requeriment, genera un esborrany de spec i s'atura per a aprovació humana. **No escriu cap línia de codi.**

## Instruccions per a l'agent

1. Llig `AGENTS.md` i el doc de domini corresponent de `docs/agents/`.
2. Identifica el bounded context afectat.
3. Si ja existeix `specs/<domini>.md`, llegeix-lo per a no duplicar escenaris.
4. Analitza el requeriment $ARGUMENTS i genera:
   - **Llistat d'escenaris** BDD en format Given/When/Then (mínim 3, màxim 8).
   - **Regles de negoci invariants** que l'escenari introdueix o modifica.
   - **Fitxers afectats** (controladors, entitats, vistes, tests) sense tocar-los.
   - **Riscos detectats** (efectes col·laterals, camps llegats, dependències).
5. Mostra el resultat i escriu `Status: awaiting_approval`.
6. **Atura't.** No implementes fins que l'usuari aprove la spec.

## Format de resposta

```
## Spec proposada: <títol>

### Escenaris
**Escenari N: <títol>**
Given ...
When ...
Then ...

### Regles de negoci
- ...

### Fitxers afectats
- `ruta/fitxer.php` — motiu

### Riscos
- ...

Status: awaiting_approval
```
