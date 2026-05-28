# Spec: Horaris

Especificació del comportament esperat per al domini Horaris. Tecnologia-agnòstica.

## Consulta d'horari

### Escenari 1: Professor consulta el seu horari setmanal

**Given** que un professor autenticat accedeix a la gestió d'horari  
**When** fa la petició  
**Then** es mostra el seu horari setmanal obtingut via `HorarioService::semanalByProfesor($id)`

## Canvi temporal d'horari

El flux de canvi temporal es gestiona via fitxers JSON a `storage/horarios/{dni}/{id}.json`. Els estats possibles del JSON són: `Pendiente` → `Aceptado` → `Guardado` (o `Rebutjat`).

### Escenari 2: Professor envia una proposta de canvi

**Given** que un professor autenticat vol canviar temporalment el seu horari  
**When** envia una proposta amb els canvis (`de` → `a`) i el rang de dates  
**Then**
- Es crea un fitxer JSON a `storage/horarios/{dni}/{id}.json` amb `estado = Pendiente`
- La proposta és visible al panell de propostes de direcció

### Escenari 3: Direcció accepta una proposta

**Given** que existeix una proposta amb `estado = Pendiente` a `storage/horarios/{dni}/{id}.json`  
**When** un usuari de direcció fa `GET /horario/propuesta/{dni}/{id}/aceptar`  
**Then**
- L'`estado` del JSON passa a `Aceptado`
- S'envia una notificació al professor via `NotificationService`
- S'envia un correu de confirmació al professor amb el detall dels canvis i dates

### Escenari 4: Direcció rebutja una proposta

**Given** que existeix una proposta amb `estado = Pendiente`  
**When** un usuari de direcció fa `GET /horario/propuesta/{dni}/{id}/rebutjar` amb un `motiu` no buit  
**Then**
- L'`estado` del JSON passa a `Rebutjat`
- El camp `motiu_rebuig` queda desat al JSON
- S'envia una notificació al professor amb el motiu
- Si `motiu` és buit: es retorna un avís d'error i **no** es modifica l'estat

### Escenari 5: Aplicar un canvi acceptat

**Given** que una proposta té `estado = Aceptado`  
**When** s'executa `changeTable($dni)`  
**Then**
- Es guarda una còpia de seguretat a `storage/horarios/horariosCambiados/{dni}.json` (si no n'existia)
- Per a cada canvi `{de, a}`: l'entrada `Horario` corresponent actualitza `sesion_orden` i `dia_semana`
- L'`estado` del JSON passa a `Guardado` i `cambios` s'esborra
- Si un canvi referencia una cel·la inexistent: s'informa via `Alert::info` però es continua

### Escenari 6: Aplicar tots els canvis acceptats (bulk)

**Given** que existeixen diverses propostes amb `estado = Aceptado`  
**When** s'executa `changeTableAll()`  
**Then**
- `changeTable($dni)` s'executa per a cada professor actiu
- Es mostra el nombre total de canvis aplicats correctament

### Escenari 7: Intent d'aplicar un horari ja guardat

**Given** que una proposta té `estado = Guardado`  
**When** s'executa `changeTable($dni)`  
**Then** s'informa via `Alert::info` que ja estava guardat i **no** es fa cap canvi

### Escenari 8: Esborrar una proposta

**Given** que existeix una proposta (en qualsevol estat)  
**When** un usuari de direcció fa `GET /horario/propuesta/{dni}/{id}/esborrar`  
**Then** el fitxer JSON és eliminat permanentment

## Modificació directa de l'horari (administrador)

### Escenari 9: Modificar una entrada d'horari individual

**Given** que un administrador accedeix a l'edició d'horari  
**When** envia `PUT` amb les dades validades via `HorarioUpdateRequest`  
**Then** l'entrada `Horario` queda actualitzada via `persist`

## Regles de negoci invariants

- Les propostes es guarden com a fitxers JSON, no a la base de dades.
- `changeTable` no aplica canvis si `estado != Aceptado`; informa però no llança error.
- La còpia de seguretat (`horariosCambiados/`) es crea **una sola vegada**: si ja existeix no se sobreescriu.
- El camp `id` del canvi JSON és opcional: si s'inclou, es busca per ID; si no, per cel·la (`sesion_orden-dia_semana`).
- Un canvi amb `de` o `a` buits o mal formats s'omet silenciosament.
- El llistat de propostes es pot filtrar per `estado`; per defecte mostra `Pendiente`. `Todos` mostra tots els estats.
