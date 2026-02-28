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

