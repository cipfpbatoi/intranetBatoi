# Convalidacions — control d'obertura de noves sol·licituds

Estat: archived — consolidated into specs/convalidacions.md

## Traça mínima

- Domini: Convalidacions.
- Punt d'entrada: `ConvalidacioAccessService`, middleware `convalidacions.alumne.access`, `AlumnoConvalidacioController` i la configuració de Direcció.
- Docs/specs llegits: `AGENTS.md`, `docs/agents/tetris.md`, `docs/agents/openspec.md`, `docs/agents/conventions.md`, `docs/agents/fct/fct-map.md` i `specs/convalidacions.md`.
- Abast: permetre a Direcció controlar la presentació de **noves** sol·licituds per a tot l'alumnat i, separadament, per a l'alumnat amb matrícula de juliol, mostrant-los un avís quan no puguen iniciar-ne una.

## Decisió de vocabulari

Per evitar l'ambigüitat entre «activar» i «desactivar», la interfície usa estos controls:

- `Permetre noves sol·licituds`: actiu permet presentar-ne a tot l'alumnat; inactiu les bloqueja globalment.
- `Bloquejar noves sol·licituds per a matrícules de juliol`: actiu bloqueja només qui tinga `alumnos.fecha_matricula` en juliol.

El primer control preval sobre el segon: si les noves sol·licituds no estan permeses globalment, ningú no en pot presentar, inclòs l'alumnat matriculat en juliol.

## Escenaris

### Escenari 1: Direcció configura els dos controls d'obertura

**Given** que una persona amb rol Direcció consulta la pestanya `/direccion/convalidacions/configuracio`  
**When** activa o desactiva `Permetre noves sol·licituds` i/o `Bloquejar noves sol·licituds per a matrícules de juliol`  
**Then** cada valor queda guardat de manera persistent i independent  
**And** els canvis són efectius immediatament per a l'alumnat  
**And** Secretaria no pot consultar ni modificar estos controls.

### Escenari 2: Bloqueig global de noves sol·licituds

**Given** que `Permetre noves sol·licituds` està inactiu  
**When** qualsevol alumne intenta obrir o enviar una nova sol·licitud  
**Then** el servidor no crea cap `SollicitudConvalidacio` ni cap petició de mòdul  
**And** l'alumne veu l'avís «Les noves sol·licituds de convalidació no estan obertes en este moment.»  
**And** pot continuar consultant les seues sol·licituds existents i fer les accions de correcció documental que els seus estats permeten.

### Escenari 3: Bloqueig exclusiu per matrícula de juliol

**Given** que `Permetre noves sol·licituds` està actiu  
**And** `Bloquejar noves sol·licituds per a matrícules de juliol` està actiu  
**And** un alumne té `fecha_matricula` en un mes diferent de juliol  
**When** obri i envia una nova sol·licitud  
**Then** pot completar el flux ordinari de convalidacions.

### Escenari 4: Alumne matriculat en juliol

**Given** que `Permetre noves sol·licituds` està actiu  
**And** `Bloquejar noves sol·licituds per a matrícules de juliol` està actiu  
**And** l'alumne té `alumnos.fecha_matricula` amb mes `07`  
**When** intenta obrir o enviar una nova sol·licitud  
**Then** el servidor no crea cap `SollicitudConvalidacio` ni cap petició de mòdul  
**And** l'alumne veu l'avís «No pots presentar una nova sol·licitud de convalidació perquè la teua matrícula és de juliol.»  
**And** conserva l'accés a les seues sol·licituds existents i a les seues correccions documentals permeses.

### Escenari 5: Prioritat del bloqueig global

**Given** que `Permetre noves sol·licituds` està inactiu  
**And** l'alumne té una matrícula de juliol, independentment de l'estat del bloqueig de juliol  
**When** intenta iniciar una nova sol·licitud  
**Then** s'aplica el bloqueig global  
**And** veu només l'avís de tancament general.

## Regles de negoci

- El bloqueig afecta exclusivament crear i presentar sol·licituds noves; no revoca l'accés a dades, documents ni correccions d'una sol·licitud ja presentada.
- La font de decisió per al bloqueig de juliol és únicament `alumnos.fecha_matricula`, un camp `DATE` obligatori; es considera juliol quan el seu mes és `07`.
- La comprovació és obligatòria al servidor tant en mostrar el formulari com en tramitar el `POST`; no pot dependre només de l'ocultació d'un botó o de la notificació visual.
- Els dos controls persistixen en la configuració existent de convalidacions i conserven el comportament actual per defecte: noves sol·licituds permeses i bloqueig específic de juliol inactiu.
- Direcció és l'únic perfil que administra la configuració; no es modifica la capacitat operativa de Secretaria sobre sol·licituds existents.

## Fitxers previsiblement afectats en apply

- `app/Application/Convalidacio/ConvalidacioAccessService.php` — estat persistent i decisió única d'elegibilitat per a una nova sol·licitud.
- `app/Http/Middleware/ConvalidacioAlumnoAccessMiddleware.php` o un middleware específic — mantindre la frontera HTTP de creació sense bloquejar les accions sobre sol·licituds existents.
- `app/Http/Controllers/AlumnoConvalidacioController.php` — revalidar el permís en `create()` i `store()` i retornar l'avís funcional.
- `app/Http/Controllers/DireccionConvalidacioController.php` — guardar els dos controls, restringits a Direcció.
- `routes/alumno.php` i `routes/direccion.php` — aplicar la protecció a les rutes de nova sol·licitud i exposar l'acció de configuració necessària.
- `resources/views/intranet/convalidacions/alumno/index.blade.php` i/o `create.blade.php` — mostrar la notificació i ocultar/inhabilitar l'acció de nova sol·licitud quan pertoque.
- `resources/views/intranet/convalidacions/direccion/configuracio.blade.php` — oferir els dos controls d'obertura a Direcció exclusivament en la pestanya `/direccion/convalidacions/configuracio`, no en el panell de sol·licituds.
- `tests/Feature/ConvalidacioFlowTest.php` — cobrir configuració, bloqueig global, matrícula de juliol, prioritat i preservació de les sol·licituds existents.
- `specs/convalidacions.md` — incorporar els escenaris aprovats en la fase `archive`.

## Riscos detectats

- El middleware actual de convalidacions bloqueja totes les rutes d'alumnat amb una contrasenya de proves. La implementació ha de separar el bloqueig de **nova sol·licitud** de les rutes de consulta, descàrrega i correcció, per no contradir este abast.
- Els estats persistents es guarden actualment en `settings`; els noms de claus i el valor predeterminat han d'evitar que un desplegament tanque involuntàriament el període.
- La data s'ha de llegir com a data de matrícula de l'alumne autenticat; no s'ha d'inferir a partir de grup, curs o data actual.
- L'avís visual no és una barrera de seguretat: cal protegir també les peticions HTTP directes i evitar persistència parcial.

Status: ready_to_commit
