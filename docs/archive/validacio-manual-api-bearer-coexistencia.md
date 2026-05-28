# Validació manual API seguretat (coexistència Bearer + legacy)

## Objectiu

Comprovar que l'aplicació funciona en mode coexistència:

- flux modern: `Authorization: Bearer <token_sanctum>`
- flux legacy: `api_token` com a fallback controlat

## Precondicions

1. Sessió iniciada com a professor.
2. Entorn local en marxa (`php artisan serve` o equivalent).
3. Navegador amb DevTools oberts (xarxa).
4. Cache neta:
   - `php artisan optimize:clear`
   - refresc forçat del navegador.

## Criteri d'acceptació global

1. No hi ha errors 401/500 inesperats en fluxos habituals.
2. Les crides API del frontend envien:
   - header `Authorization` quan hi ha `user-bearer-token`,
   - o fallback legacy quan no hi ha bearer.
3. No apareixen URLs amb `?api_token=...` en les crides del frontend.

## Proves transversals

1. Obrir una pàgina amb API (p.ex. `Comissió` o `Guardia`).
2. En DevTools, verificar:
   - existeix `Authorization: Bearer ...` en almenys una crida API,
   - no hi ha query param `api_token`.
3. Cridar `/api/auth/me`:
   - resposta `200`,
   - dades de l'usuari autenticat correctes.

## Bateria funcional per mòduls

## 1. Comissió

1. Marcar una o més files.
2. Prémer acció de pagament/prepagament.
3. Esperat:
   - operació correcta,
   - redirecció o refresc correcte,
   - sense errors de consola.

## 2. Fitxatge / Presència

1. Obrir vista de control dia/setmana.
2. Canviar dia/setmana.
3. Esperat:
   - dades carreguen correctament,
   - totals i horaris visibles,
   - sense 401.

## 3. Guardia

1. Obrir pantalla de guardies (normal i biblio si aplica).
2. Carregar franja i guardar canvi.
3. Esperat:
   - lectura de guardia existent correcta,
   - alta/modificació correcta,
   - missatge de confirmació.

## 4. Reserva espais

1. Seleccionar recurs + dia.
2. Fer reserva en un interval lliure.
3. Alliberar reserva.
4. Esperat:
   - reserva/alliberament correctes,
   - taula actualitzada en pantalla.

## 5. FCT / Dual / CAP

1. Llistar alumnes FCT.
2. Marcar/desmarcar checkboxes de documentació.
3. Obrir modal de missatge/evidència.
4. Esperat:
   - canvis persistixen,
   - modal funciona,
   - sense errors JS.

## 6. Lote / Material / Inventari

1. Obrir llistats amb DataTable.
2. Executar una acció de cada tipus:
   - veure,
   - editar,
   - acció d'estat/ubicació/inventari.
3. Esperat:
   - API respon bé,
   - taula reflecteix canvis.

## 7. Col·laboració / Empresa / Reunió

1. Obrir accions en grid (resolve/refuse/switch si aplica).
2. Obrir i guardar un modal de reunió o col·laboració.
3. Esperat:
   - accions aplicades,
   - no bloquejos en modal.

## 8. Dropzone adjunts

1. Obrir pantalla amb adjunts.
2. Llistar fitxers existents.
3. Pujar un fitxer nou.
4. Esborrar un fitxer.
5. Esperat:
   - llistat/alta/baixa correctes.

## Checklist tècnic final

1. `storage/logs/laravel.log` sense errors nous de seguretat API.
2. Consola navegador sense `ReferenceError: event is not defined`.
3. Cap request frontend amb `?api_token=...`.

## Incidències (plantilla)

1. Mòdul:
2. URL:
3. Request:
4. Codi HTTP:
5. Error:
6. Passos per reproduir:
7. Severitat:

