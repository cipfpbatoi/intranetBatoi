# Pas 5 - Inventari Legacy API (abans de deprecació)

Data: 2026-02-21

## Objectiu
Identificar rutes API amb contracte legacy (sobretot `show` amb filtres en path tipus `camp=valor&...`) i qui les està consumint, per poder deprecar-les per fases sense trencar front ni integracions externes.

## Rutes legacy detectades

1. `GET /api/reserva/{cadena}`
Contracte legacy: `idEspacio=...&dia=...` en el path.
Exemple: `/api/reserva/idEspacio=12&dia=2026-02-21`.
Estat: mantinguda per compatibilitat.

2. `GET /api/horario/{cadena}`
Contracte legacy: filtres en path (`idProfesor=...`, comparadors `] [ ! > <`, etc.).
Exemple: `/api/horario/idProfesor=021652470V`.
Estat: mantinguda per compatibilitat.

3. `GET /api/faltaProfesor/{cadena}`
Contracte legacy: filtres en path (p. ex. `dia=2026-02-22`).
Estat: mantinguda per compatibilitat.

4. `GET /api/guardia/{cadena}`
Contracte legacy específic de rang: `dia]YYYY-MM-DD&dia[YYYY-MM-DD`.
Exemple: `/api/guardia/dia]2026-02-10&dia[2026-02-15`.
Estat: mantinguda per compatibilitat.

5. `GET /api/alumnogrupo/{id}`
Contracte custom no REST:
- si `id` longitud 8: cerca per alumne.
- si no: interpreta tutor i retorna llistat.
Estat: mantinguda per compatibilitat.

## Consumidors interns detectats

1. `resources/assets/js/components/reservas/ReservasView.vue`
Consumix `/api/reserva/idEspacio=...&dia=...` (legacy).

2. `resources/assets/js/components/guardias/ControlGuardiaView.vue`
Consumix `/api/guardia/range` (modern).

3. `resources/assets/js/components/fichar/ControlSemanaView.vue`
Consumix `/api/presencia/resumen-rango` (modern).

4. Tests (no producció) que validen legacy:
- `tests/Feature/ApiGuardiaControllerFeatureTest.php`
- `tests/Feature/ApiHorarioControllerFeatureTest.php`
- `tests/Feature/ApiFaltaProfesorControllerFeatureTest.php`
- `tests/Feature/ApiReservaControllerFeatureTest.php`

Nota: no s'han trobat crides internes clares a `/api/horario/idProfesor=...` ni `/api/faltaProfesor/{cadena}` en JS/Blade actuals. Poden existir consumidors externs (apps, scripts, integracions).

## Proposta de deprecació per fases

1. Fase 1 (segura, immediata)
- Mantindre endpoints legacy.
- Afegir `Deprecation` + `Sunset` headers quan entra via patró legacy.
- Registrar en log (`api_legacy`) ruta, IP, user-agent, usuari i query legacy.

Estat actual:
- Implementat en `ApiResourceController::markLegacyUsage()` (headers + `Log::info`).
- Aplicat en:
  - `FaltaProfesorController::show` (mode legacy)
  - `GuardiaController::show` (rang legacy + filtre legacy)
  - `ReservaController::show` (filtre legacy)
  - `HorarioController::show` (filtre legacy)

2. Fase 2 (migració front intern)
- Substituir en `ReservasView.vue` la crida legacy per endpoint modern:
  - proposta: `GET /api/reserva?idEspacio=...&dia=...`
  - o endpoint específic `GET /api/reserva/search?...`.
- Mantindre fallback legacy.

Estat actual:
- Implementat en `resources/assets/js/components/reservas/ReservasView.vue`.
- Crida principal: `GET /api/reserva?idEspacio=...&dia=...`.
- Fallback temporal: `GET /api/reserva/idEspacio=...&dia=...`.

3. Fase 3 (validació d'ús real)
- Revisar logs 2-4 setmanes.
- Si no hi ha consum extern legacy, anunciar retirada amb data.

4. Fase 4 (retirada)
- Eliminar parser legacy de controladors.
- Deixar només contracte modern i tests de contracte final.

## Criteri de bloqueig abans d'eliminar legacy

No eliminar cap endpoint legacy sense:
1. Evidència de no-ús en logs.
2. Front intern migrat.
3. Tests de regressió en endpoints moderns equivalents.

## Evidència de codi (verificada)

Data de verificació: 2026-02-21

1. Rutes API:
- `routes/api.php`: `Route::resource('reserva'...)`, `Route::resource('horario'...)`, `Route::resource('faltaProfesor'...)`, `Route::resource('guardia'...)`, `Route::resource('alumnogrupo'...)`.
- `routes/api.php`: ruta moderna específica `GET /api/guardia/range`.

2. Marcat de deprecació:
- `app/Http/Controllers/API/ApiResourceController.php`: mètode `markLegacyUsage()` afegix headers `Deprecation: true`, `Sunset: Wed, 31 Dec 2026 23:59:59 GMT`, `X-API-Replacement` i `Log::info`.

3. Controladors amb parser legacy en `show`:
- `app/Http/Controllers/API/ReservaController.php`
- `app/Http/Controllers/API/HorarioController.php`
- `app/Http/Controllers/API/FaltaProfesorController.php`
- `app/Http/Controllers/API/GuardiaController.php`

4. Consumidor intern legacy confirmat:
- `resources/assets/js/components/reservas/ReservasView.vue`: `axios.get('/api/reserva/idEspacio=...&dia=...')`.

## Matriu legacy -> contracte objectiu

1. `GET /api/reserva/{cadena}`
- Legacy actual: sí.
- Contracte objectiu: `GET /api/reserva?idEspacio=...&dia=...`.
- Estat real: implementat en `ReservaController::index`.

2. `GET /api/horario/{cadena}`
- Legacy actual: sí.
- Contracte objectiu: `GET /api/horario?idProfesor=...`.
- Estat real: implementat en `HorarioController::index` (inclou lògica de substitució).

3. `GET /api/faltaProfesor/{cadena}`
- Legacy actual: sí.
- Contracte objectiu: `GET /api/faltaProfesor?dia=...`.
- Estat real: implementat en `FaltaProfesorController::index`.

4. `GET /api/guardia/{cadena}`
- Legacy actual: sí.
- Contracte objectiu: `GET /api/guardia/range?desde=...&hasta=...`.
- Estat real: implementat i en ús intern.

5. `GET /api/alumnogrupo/{id}`
- Contracte custom actual (8 caràcters = alumne; altre = tutor): sí.
- Contracte objectiu: separar endpoints explícits (`/api/alumnogrupo/alumno/{dni}` i `/api/alumnogrupo/tutor/{dni}` o equivalent).
- Estat real: pendent de disseny i migració.

## Tasques de continuació (Pas 6)

1. Implementar filtrat per query-string en `index` o en endpoint `search` per:
- `ReservaController` (fet)
- `HorarioController` (fet)
- `FaltaProfesorController` (fet)

2. Migrar front intern de reserves:
- Fitxer: `resources/assets/js/components/reservas/ReservasView.vue`
- Canvi: usar endpoint modern i deixar fallback temporal legacy amb control d'error. (fet)

3. Instrumentació de seguiment:
- Canalitzar logs legacy a canal dedicat (`api_legacy`) per facilitar explotació.
- Definir consulta periòdica (setmanal) de volum per contracte legacy i `user_agent`.

4. Preparar retirada controlada:
- Publicar data objectiu de tall en documentació interna.
- Mantindre `Sunset` coherent amb la data anunciada.

## Riscos oberts

1. Els contractes moderns nous estan implementats per a filtres d'igualtat (`camp=valor`), però no cobrixen encara comparadors legacy (`] [ ! > <`) en query-string.
2. Poden existir clients externs no inventariats (scripts o apps) consumint legacy.
3. `alumnogrupo/{id}` és un contracte amb semàntica implícita i alt risc de trencament si es lleva sense endpoint equivalent.
