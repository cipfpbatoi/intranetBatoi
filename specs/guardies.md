# Spec: Guàrdies

Especificació del comportament esperat per al domini Guàrdies. Tecnologia-agnòstica.

## Presència del professor de guàrdia

### Escenari 1: Professor fixa la seua presència

**Given** que un professor autenticat accedeix a `/guardia` des d'una IP reconeguda del centre  
**When** la pàgina carrega  
**Then**
- Es calcula la sessió actual (`sesion(hora())`) i el dia d'avui
- El sistema comprova si el professor ja té una `Guardia` registrada per a (`dia`, `hora`) actuals
- Si hi és (`estoy = true`): es mostra la llista de totes les guàrdies actives en eixe moment
- Si no hi és (`estoy = false`): es mostra la pantalla per a fitxar

### Escenari 2: Accés des de fora del centre (sense IP reconeguda)

**Given** que un professor accedeix a `/guardia` des d'una IP no reconeguda  
**When** la pàgina carrega  
**Then** no es mostra el panell de guàrdies (la IP no és vàlida)

## Panell de gestió de guàrdies (direcció / cap d'estudis)

### Escenari 3: Consultar qui hauria d'estar ara al centre

**Given** que un usuari amb permís `manageAttendance` accedeix al panell de guàrdies  
**When** fa la consulta (`search`)  
**Then** per a cada entrada de l'horari lectiu actual, el sistema assigna un estat `donde`:
- `Al centre` — professor ha fitxat entrada i no ha eixit
- `En comisión de servicio` — té una `Comision` activa que coincideix amb la sessió actual
- `Comunica Ausència` — té una `Falta` registrada per al dia/hora actual
- `Extraescolar Profesor` — participa en una activitat extraescolar fora del centre amb horari coincident
- `Extraescolar Grup` — el seu grup té una activitat extraescolar fora del centre
- `No ha fitxat` — cap de les condicions anteriors es compleix

### Escenari 4: Activitat extraescolar fora del centre coincident

**Given** que existeix una `Actividad` amb `fueraCentro = 1` programada per avui  
**When** el panell de guàrdies calcula l'estat dels professors  
**Then**
- Els grups de l'activitat marquen els seus horaris com `Extraescolar Grup`
- Els professors de l'activitat marquen els seus horaris com `Extraescolar Profesor`
- Només si les hores de l'activitat coincideixen amb la sessió actual (via `coincideHorario`)

### Escenari 5: Comissió coincident amb la sessió actual

**Given** que un professor té una `Comision` activa avui  
**When** el panell comprova el seu estat  
**Then**
- Si `desde` i `hasta` són el mateix dia: la comissió coincideix si `hora_ini`/`hora_fin` cobreixen la sessió actual
- Si `desde` i `hasta` son dies diferents (o `dia_completo = 1`): la comissió cobreix tota la jornada

### Escenari 6: Accés no autoritzat al panell

**Given** que un professor sense permís `manageAttendance` intenta accedir al panell  
**When** fa la petició  
**Then** rep `403` (Gate `manageAttendance` en `Profesor::class`)

## Regles de negoci invariants

- La sessió actual es calcula amb `sesion(hora())` i el dia amb `Hoy()`.
- `coincideHorario` retorna `true` si l'element té `dia_completo = 1` o si `desde != hasta` (dies diferents).
- La prioritat d'estat és: Extraescolar > Comissió > Falta > No fitxat (es comprova en eixe ordre).
- El model `Guardia` usa els scopes `Profesor($dni)` i `DiaHora($dia, $hora)` per a les consultes.
- `realizada` i `observaciones` es poden actualitzar sobre una `Guardia` existent.
