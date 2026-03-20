# Sprint 16 - Protegir endpoints API sense autenticació

## Objectiu

Auditar i protegir endpoints de l'API que ara mateix queden oberts sense autenticació o amb protecció inconsistent.

## Diagnòstic inicial

En [`routes/api.php`](/Users/igomis/Code/intranetBatoi/routes/api.php) hi ha un patró mixt:

- molts endpoints correctament protegits amb `auth:api,sanctum`
- alguns endpoints públics que poden ser legítims
- alguns punts sensibles que, com a mínim, necessiten revisió

Exemples visibles fora del grup autenticat:

- `Route::resource('projecte', 'ProjecteController', ['except' => [ 'create']]);`
- `Route::get('/convenio', 'EmpresaController@indexConvenio');`
- `Route::get('actividad/{actividad}/getFiles', 'ActividadController@getFiles');`
- `Route::get('server-time', 'GuardiaController@getServerTime');`
- endpoints de porta/cotxe:
  - `porta/obrir`
  - `porta/obrir-automatica`
  - `eventPortaSortida`
  - `eventPorta`
- `presencia/resumen-rango`
- `auth/exchange`

No tots són necessàriament vulnerables, però sí són candidats a revisió.

## Criteri

Només poden quedar públics els endpoints que complisquen alguna d'estes condicions:

- són estrictament d'infraestructura o salut
- el seu ús públic és deliberat i documentat
- tenen una altra capa de validació forta equivalent a autenticació

La resta s'ha de protegir.

## Fases

### Tall A - Inventari real

- classificar cada endpoint API en:
  - públic justificat
  - protegit correcte
  - obert dubtós
  - obert incorrecte
- documentar per què és públic o per què s'ha de tancar

### Tall B - Enduriment mínim

- afegir `auth:api,sanctum` als endpoints sensibles que ara estan oberts
- revisar especialment:
  - `projecte`
  - `actividad/{actividad}/getFiles`
  - `presencia/resumen-rango`
  - endpoints de porta/cotxe si no tenen protecció externa prou clara

### Tall C - Coherència d'autenticació

- revisar si cal usar:
  - `auth:api,sanctum`
  - `auth:sanctum`
  - o middleware propi
- evitar barreges arbitràries
- deixar clar el contracte d'autenticació de cada família d'endpoints

### Tall D - Proves de regressió

- afegir proves que fallen si un endpoint sensible queda accessible sense auth
- deixar explícites les excepcions públiques

## Criteris d'acceptació

- no queden endpoints sensibles accessibles sense autenticació
- els endpoints públics queden justificats i documentats
- les proves de regressió cobrixen almenys els punts més crítics

## Referència

- issue remot: `#115`
