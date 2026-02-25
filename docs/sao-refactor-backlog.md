# Backlog Refactor SAO

## Estat actual

- Fase 0 iniciada i parcialment completada.
- Fixes de tancament de sessio Selenium aplicats en:
  - `RedirectAfterAuthenticationController`
  - `Sao\A2`
  - `Sao\Signatura`
  - `Sao\Importa`
  - `Sao\Compara`
  - `SeleniumService`
- Tests de caracteritzacio creats:
  - `tests/Unit/Controllers/RedirectAfterAuthenticationControllerTest.php`
  - `tests/Unit/Sao/A2LifecycleTest.php`
  - `tests/Unit/Sao/SyncLifecycleTest.php`
  - `tests/Unit/Sao/AnnexesLifecycleTest.php`
  - `tests/Unit/Sao/SignaturaLifecycleTest.php`

## Objectiu d'arquitectura

Separar l'actual `Sao\A2` per responsabilitats i per document (`A2`, `A3`, `A5`), amb un punt d'entrada generic `SAOAction`.

### Estructura proposada

- `app/Sao/Actions/`
  - `SAOAction.php` (orquestrador principal)
- `app/Sao/Documents/`
  - `A1DocumentService.php`
  - `A2DocumentService.php`
  - `A3DocumentService.php`
  - `A5DocumentService.php`
- `app/Sao/Support/`
  - `SaoRunner.php` (lifecycle driver)
  - `SaoNavigator.php` (navegacio i waits)
  - `SaoDownloadManager.php` (descàrrega, espera fitxer, neteja)
  - `SaoContext.php` (dades compartides de proces)

## Tasques pendents (prioritat)

### P0 - Tancar Fase 0 (caracteritzacio)

- [ ] Afegir test de cicle de vida per `Sao\Importa` (quit en exit/error).
- [ ] Afegir test de cicle de vida per `Sao\Compara` (quit en exit/error).
- [ ] Afegir test de robustesa per `Sao\A2` quan falla signatura (`annexe23/annexe5`).
- [ ] Crear test d'integracio lleuger del flux `/externalAuth` amb accio SAO dummy.

### P1 - Fase 1 (lifecycle centralitzat)

- [ ] Implementar `SaoRunner`:
  - login SAO
  - execucio de l'accio
  - `quit()` en `finally`
- [ ] Fer que `RedirectAfterAuthenticationController` delegue en `SaoRunner`.
- [ ] Llevar `quit()` dispersos on ja no calga.

### P2 - Fase 2 (contracte d'accions)

- [ ] Definir interfície comuna (`SaoActionInterface`).
- [ ] Crear `SAOAction` com a entrypoint unificat.
- [ ] Adaptar `Importa`, `Compara`, `Signatura`, `Sync`, `Annexes` al contracte nou.

### P3 - Fase 3 (split de documents en A2)

- [ ] Extraure `annexe1` a `A1DocumentService`.
- [ ] Extraure annex 2 a `A2DocumentService`.
- [ ] Extraure annex 3 a `A3DocumentService`.
- [ ] Extraure `annexe5` a `A5DocumentService`.
- [ ] Deixar `SAOAction` com orquestrador: seleccio de documents + notificacions.

### P4 - Fase 4 (infra comuna SAO)

- [ ] Implementar `SaoNavigator` (URLs, esperes, retries).
- [ ] Implementar `SaoDownloadManager` (tmp files, `waitForFile`, neteja).
- [ ] Substituir `sleep()` directes per esperes explicites on siga possible.

### P5 - Fase 5 (configuracio)

- [ ] Crear `config/sao.php` per URLs SAO, timeouts i directoris temporals.
- [ ] Eliminar literals hardcoded de paths/urls dins classes SAO.

### P6 - Fase 6 (errors i observabilitat)

- [ ] Definir excepcions propies (`SaoLoginException`, `SaoDownloadException`, etc.).
- [ ] Logs estructurats amb context (`accio`, `document`, `idSao`, `usuari`).
- [ ] Missatgeria d'usuari uniforme per errors recuperables/no recuperables.

### P7 - Fase 7 (neteja final)

- [ ] Revisar tipus de retorn i `strict_types` on toque.
- [ ] Unificar nomenclatura de metodes (`index`, `execute`, etc.).
- [ ] Eliminar codi mort i comentaris antics.

## Ordre recomanat d'execucio

1. Tancar P0.
2. Fer P1 + P2 (`SaoRunner` + `SAOAction`).
3. Fer P3 (split per documents: `A2`, `A3`, `A5`, i `A1`).
4. Fer P4 i P5 (infra i configuracio).
5. Tancar amb P6 i P7.
