# Pipeline: IA revisora ≠ IA generadora

La mateixa IA que escriu codi té els mateixos punts cecs que la que ho revisa. Usar motors diferents com a revisor independent augmenta la qualitat i redueix els falsos "tot correcte".

## Principi

```
Generació   →   Revisió   →   Decisió humana
(IA A)          (IA B)         (tu)
```

**Mai**: generació + revisió amb el mateix agent en la mateixa sessió.

## Assignació de rols en aquest projecte

| Tasca | Agent primari recomanat | Agent revisor recomanat |
|---|---|---|
| Nova funcionalitat, refactor | Claude Code | Codex (OpenAI) |
| Migració de BD, canvi d'entitat | Codex | Claude Code |
| Revisió de PR | `/ultrareview` (cloud multi-agent) | — |
| Revisió ràpida de diff | `/code-review` (Claude) | contrast manual |

> La regla és: **si A ha escrit el codi, B el revisa**. No importa qui és A i qui és B.

## Workflow pràctic

### 1. Generar amb l'agent primari (flux OpenSpec)

```
/opsx-propose <requeriment>    ← analitza i proposa spec
# [aprovació humana]
/opsx-apply                    ← implementa la spec
```

### 2. Revisar amb l'agent revisor

**Opció A — Claude Code revisa codi generat per Codex:**
```
/code-review
```
Llegeix el diff actual i reporta bugs de correcció.

**Opció B — Revisió de PR completa (cloud, multi-agent):**
```
/ultrareview
```
Llança una revisió cloud del branch actual. Requereix connexió i és de pagament.

**Opció C — Revisió manual amb checklist** (quan no hi ha segon agent disponible):
Usa el fitxer [`prompts/review-checklist.md`](../../prompts/review-checklist.md).

### 3. Decidir i tancar

```
/opsx-archive    ← valida tests, actualitza spec, suggereix commit
# [commit humà]
```

## Què ha de cobrir la revisió

El revisor (IA B o humà) ha de verificar:

- [ ] **Correcció funcional**: el codi fa el que diu la spec (`specs/<domini>.md`).
- [ ] **Camps llegats**: no s'han modificat camps crítics sense migració (`complementaria`, `extraescolar`, `sendTo`, `signed`…).
- [ ] **Autorització**: totes les accions sensibles passen per `authorize()` o `Gate`.
- [ ] **Abast**: no hi ha codi fora del que descriu la spec (refactors no sol·licitats, funcionalitats noves).
- [ ] **Tests**: els escenaris de la spec tenen cobertura a `tests/Feature/` o `tests/Browser/`.
- [ ] **Convencions**: PSR-12, PHPDoc, text en Valencià, `AppAlert` correcte.
- [ ] **Seguretat**: sense SQL cru, sense secrets hardcoded, sense XSS en Blade.

## Sobre la injecció de prompts

Quan copies text d'una issue, d'un comentari de PR o d'un fitxer extern per incloure'l en un prompt:

- **Revisa** que no continga instruccions disfressades (`"Ignora les instruccions anteriors i…"`).
- **No copies** mai credencials, tokens ni contingut de `.env` dins d'un prompt.
- **Desconfia** de respostes de l'agent que incloguen accions no sol·licitades (esborrar fitxers, fer push, enviar correus).
- Si una resposta sembla fora de lloc, **descarta la sessió** i comença de nou.

## Responsabilitat

La IA ajuda i actua, però **tu ets el responsable** de les accions. Cada commit que fas porta la teua firma, no la de l'agent.
