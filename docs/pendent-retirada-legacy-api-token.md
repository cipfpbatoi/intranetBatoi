# Pendent de retirada legacy `api_token`

## Estat actual

La branca està en fase de coexistència:

1. frontend majoritàriament adaptat a Bearer,
2. backend encara admet legacy per compatibilitat.

## Objectiu final

Deixar l'autenticació API només en Sanctum (Bearer), sense dependència de `api_token` legacy.

## Fase A. Estabilització (abans de tallar)

1. Executar la bateria manual (`docs/validacio-manual-api-bearer-coexistencia.md`).
2. Corregir incidències funcionals detectades.
3. Fer monitoratge de logs:
   - incidències 401/500,
   - usos legacy residuals.

## Fase B. Inventari de legacy residual

1. Revisar backend i confirmar punts que encara depenen de `api_token`.
2. Revisar clients externs (si n'hi ha) que envien `api_token`.
3. Marcar endpoints “sunset candidates” amb data de tall.

### Estat Fase B (2026-03-01)

En curs, amb inventari tècnic inicial completat.

#### 1) Backend: dependències `api_token` confirmades

1. **Middleware de coexistència (global API)**  
   `app/Http/Middleware/ApiTokenToBearer.php`  
   `app/Http/Middleware/LegacyApiTokenDeprecation.php`
2. **Rutes protegides en mode mixt**  
   `routes/api.php` usa `auth:api,sanctum` en:
   - `/api/auth/me`
   - `/api/auth/logout`
   - grup principal `/api/*`
3. **Controladors amb fallback o validació legacy explícita**
   - `app/Http/Controllers/API/MaterialController.php` (`resolveApiUser()`)
   - `app/Http/Controllers/API/FicharController.php` (`dni + api_token`)
   - `app/Http/Controllers/API/ReservaController.php` (`unsecure`, fallback legacy)
   - `app/Support/Helpers/MyHelpers.php` (`apiAuthUser()`)
4. **Intercanvi legacy -> Bearer (previst)**
   - `app/Http/Controllers/API/AuthTokenController.php` (`/api/auth/exchange`)

#### 2) Clients/punts d'entrada que encara exposen o consumeixen legacy

1. **UI/layout**
   - `resources/views/components/layouts/meta.blade.php` (`meta user-token`)
   - `resources/views/components/layouts/leftside.blade.php` (`#_token` hidden)
2. **Dropzone**
   - `resources/views/dropzone/partials/value.blade.php` (input hidden `api_token`)
3. **Fluxos externs login per token**
   - `app/Http/Controllers/Auth/ExternLoginController.php`
   - `app/Http/Controllers/Auth/Social/SocialController.php`
4. **Correus amb enllaços legacy**
   - `resources/views/email/apitoken.blade.php`

#### 3) Sunset candidates (proposta)

1. **Candidat A (baix risc):** endpoints de lectura/escriptura estàndard del grup API general.
   - Canvi objectiu: `auth:api,sanctum` -> `auth:sanctum`.
   - Prerequisit: 0 peticions reals amb `api_token` en logs per eixos endpoints.
2. **Candidat B (mig risc):** `MaterialController`, `ColaboracionController`, `DropZone`.
   - Prerequisit: validar que frontend intern usa `Authorization` en tots els fluxos.
3. **Candidat C (alt risc):** `FicharController` i `ReservaController@unsecure`.
   - Mantindre temporalment per dependència històrica de `dni + api_token`.
4. **Candidat D (flux de transició):** `/api/auth/exchange`.
   - Mantindre fins final de migració; retirar només quan no quede cap client legacy.

#### 4) Pendent per tancar Fase B

1. Confirmar amb logs (producció/preproducció) quins endpoints reben `api_token`.
2. Identificar clients externs (scripts/apps) que no són frontend web.
3. Fixar data de tall per cada candidat (A/B/C/D) abans d'entrar en Fase C.

#### 5) Telemetria observada en logs (entorn no producció)

Mostra actual de `storage/logs/laravel.log` (event `Legacy api_token usage detected`):

1. `api/auth/exchange` -> 33
2. `api/reserva` -> 10
3. `api/colaboracion/20/switch` -> 6
4. `api/auth/me` -> 5
5. `api/reserva/idEspacio=A-217&dia=2026-03-01` -> 3
6. `api/attachFile` -> 3
7. `api/reserva/idEspacio=A-217&dia=2026-03-02` -> 2
8. `api/removeAttached/alumnofctaval/2434/A56%20extern` -> 2
9. `api/getAttached/profesor/021652470V` -> 2
10. `api/getAttached/alumnofctaval/2434` -> 2
11. `api/actividad/1334/edit` -> 2
12. `api/reserva/idEspacio=A-217&dia=2026-03-03` -> 1
13. `api/falta/4418/edit` -> 1
14. `api/expediente/2186` -> 1
15. `api/comision/3647/edit` -> 1

Important:
1. Aquest llistat és parcial perquè no és producció.
2. Pot faltar trànsit real d'usuaris i clients externs que encara no han passat per aquests fluxos.

#### 6) Quick wins que podem arreglar ja

1. Deixar d'exposar `api_token` en layout intern:
   - `resources/views/components/layouts/meta.blade.php` (`user-token`)
   - `resources/views/components/layouts/leftside.blade.php` (`#_token`)
2. Migrar Dropzone intern a Bearer pur:
   - `resources/views/dropzone/partials/value.blade.php`
3. Revisar crides de frontend detectades en logs:
   - `attachFile`, `getAttached`, `removeAttached`, `colaboracion/switch`, `actividad/*/edit`
4. Mantindre temporalment com a excepció controlada:
   - `/api/auth/exchange`
   - fluxos crítics de `ReservaController@unsecure` i `FicharController`

#### 7) Validació local després dels quick wins (2026-03-01, vesprada)

1. Proves manuals executades:
   - adjunts (attach/list/remove),
   - reserves (consulta i operacions habituals).
2. Resultat funcional:
   - fluxos provats funcionen correctament.
3. Resultat en logs (`storage/logs/laravel.log`):
   - no s'han observat entrades noves de `Legacy api_token usage detected` després de les últimes proves locals,
   - no s'han observat errors nous 401/500 associats al canvi.
4. Nota:
   - validació limitada a entorn no producció; encara pot quedar trànsit legacy no exercitat.

## Fase C. Tall progressiu backend

1. Rutes:
   - passar de `auth:api,sanctum` a `auth:sanctum` en endpoints no legacy.
2. Controladors:
   - eliminar fallback `api_token` on ja no calga.
3. Middleware:
   - retirar `ApiTokenToBearer` quan no hi haja clients legacy.
   - retirar `LegacyApiTokenDeprecation` quan no hi haja trànsit legacy.

## Fase D. Neteja codi i dades

1. Eliminar consultes/repositoris que busquen per `api_token`.
2. Eliminar exposició de token legacy en vistes/layout.
3. Revisar si `api_token` de `profesores` queda només per un cas d'ús explícit (fitxatge) o es pot retirar.

## Fase E. Tancament operatiu

1. Comunicar data de sunset.
2. Tancar compatibilitat legacy.
3. Validació final completa en preproducció i producció.

## Checklist de “ready to remove legacy”

1. 0 requests frontend amb `api_token`.
2. 0 clients externs usant `api_token`.
3. 0 fallback legacy en controladors crítics.
4. Rutes API protegides només amb `auth:sanctum`.
5. Monitoratge 24-72h sense regressions.

## Riscos i mitigació

1. Risc: client ocult encara usa `api_token`.
   - Mitigació: període de telemetria i alerta abans del tall.
2. Risc: flux crític trencat en mòduls antics.
   - Mitigació: retirada per fases i checklist manual per mòdul.
3. Risc: regressió després de desplegament.
   - Mitigació: desplegament gradual + rollback plan.
