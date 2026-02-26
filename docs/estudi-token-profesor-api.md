# Estudi: token únic de professor per fitxatge i autenticació API

## Context actual (observat en codi)

- El mateix `api_token` de `profesores` s’usa per a:
  - fitxatge (`/api/doficha`)
  - autenticació de gran part de l’API (`auth:api` amb guard `token`)
  - login extern per URL (`/login/{token}`)
  - consum des de frontend via DOM ocult (`id="_token"`) i query string `?api_token=...`
- Referències clau:
  - `config/auth.php` (guard `api` amb driver `token`)
  - `resources/views/components/layouts/leftside.blade.php` (`<div id="_token">...`)
  - `resources/views/layouts/partials/topnav.blade.php` (enllaç amb `api_token` en URL)
  - `app/Infrastructure/Persistence/Eloquent/Profesor/EloquentProfesorRepository.php` (lookup per `api_token` en clar)

## Risc real del model actual

1. **Token estàtic i reutilitzat per a tot**
- Si es filtra una vegada, compromet fitxatge + API + login extern.

2. **Token en URL (query string)**
- Pot acabar en logs, historial, referers i monitoratge.

3. **Token injectat en HTML**
- Qualsevol XSS o extensió del navegador pot capturar-lo.

4. **Absència de separació de privilegis**
- El token de “fitxar” té efectivament poder de “session/API key”.

## Recomanació (sense trencar funcionalitat docent)

## Principi
Mantindre el **codi fix de professor** per a l’ús humà (fitxar, etc.), però **deixar d’usar-lo com a credencial API general**.

## Model objectiu

1. **Codi fitxatge (estable, humà)**
- Es manté.
- Únicament autoritza operacions de fitxatge i fluxos explícits de fitxatge.

2. **Token d’accés API curt (rotatiu)**
- Per a API general: bearer curt (ex. 15-60 min) + refresh.
- Amb scopes/permisos (`fichaje:write`, `reservas:read`, etc.).

3. **Canal web amb sessió**
- Per a pàgines web autenticades, usar sessió + CSRF.
- Evitar passar `api_token` al client.

## Com migrar sense trencar (pla realista)

## Fase 1. Contenció immediata (1-2 setmanes)
- Mantindre compatibilitat legacy.
- Afegir middleware de telemetria:
  - quins endpoints encara entren amb `api_token` query.
- Marcar respostes legacy amb headers de deprecació (ja hi ha base en `ApiResourceController`).
- Tallar nous usos: regla de codi “prohibit nou `?api_token=`”.

## Fase 2. Separació funcional (2-4 setmanes)
- Crear endpoint d’intercanvi:
  - `POST /api/auth/exchange`
  - input: codi fitxatge (i opcional PIN/2FA si és viable)
  - output: access token curt amb scopes mínims.
- Adaptar frontend nou perquè use `Authorization: Bearer ...` en lloc de `api_token` query.
- Limitar `api_token` legacy a una whitelist estricta (ex. només `doficha` temporalment).

## Fase 3. Enduriment i retirada legacy (2-6 setmanes)
- Eliminar `api_token` en query dels endpoints generals.
- Retirar login per `/login/{token}` o convertir-lo a link d’un sol ús amb caducitat curta.
- Rotació forçada i revocació centralitzada.

## Decisions tècniques concretes

1. **No reutilitzar token**
- `codi_fitxatge` != `api_access_token`.

2. **No token en URL**
- Només headers (`Authorization`) o cookie HttpOnly per SPAs controlades.

3. **Token al client**
- No exposar en `<div hidden>` ni meta si no és imprescindible.

4. **Persistència**
- Si necessiteu validació de codi estable:
  - guardar hash del codi (si no cal recuperar-lo),
  - o xifrat reversible només si realment cal mostrar-lo (millor evitar mostrar-lo; millor regenerar).

5. **Rate limiting i controls**
- `throttle` per IP+usuari en fitxatge i auth exchange.
- Auditoria d’ús i alertes d’anomalies.

## Què faria jo en este projecte concret

1. Mantindre “codi de fitxatge” com a identificador d’usuari per al cas d’ús de fitxar.
2. Introduir auth API moderna (Sanctum o JWT curt) per a tota la resta.
3. Migrar primers components Vue que ara lligen `#_token` (`fichar`, `guardias`, `reservas`) a bearer curt.
4. Deixar `api_token` legacy només en endpoints imprescindibles i amb data de sunset.

## Estat d'implementació (branca `api-seguretat`)

- Fase 1 iniciada:
  - coexistència `auth:api,sanctum` en rutes API protegides,
  - endpoint `POST /api/auth/exchange` (legacy `api_token` -> bearer Sanctum),
  - endpoint `GET /api/auth/me` (usuari autenticat),
  - migració frontend inicial a header `Authorization: Bearer` en components de fitxatge/guardies/reserves.
- Correcció de model de dades Sanctum per a `Profesor::dni` (PK string):
  - `tokenable_id` de `personal_access_tokens` passat a `string`.

## Impacte funcional esperat

- Professorat: quasi transparent si el flux de fitxar es conserva.
- Seguretat: millora molt alta (exposició i abast del token es redueixen dràsticament).
- Manteniment: autenticació més clara per domini/cas d’ús.
