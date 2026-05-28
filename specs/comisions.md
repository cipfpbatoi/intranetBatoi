# Spec: Comissions de Servei

Especificació del comportament esperat per al domini Comissions. Tecnologia-agnòstica.

## Cicle de vida d'una comissió

Els estats possibles (`estado`) són: `0=Creada`, `1=Pendent` (enviada a direcció), `2=Autoritzada`, `3=Impresa`, `4=No pagada`.

### Escenari 1: Crear una comissió

**Given** que un professor autenticat accedeix al formulari de nova comissió  
**When** envia el formulari amb `desde`, `hasta` i `servicio` vàlids  
**Then**
- Es crea un registre `Comision` amb `estado = 0` i `idProfesor` del professor autenticat
- Si el professor té FCTs actives, el camp `fct` s'inicialitza a `1`
- Si la comissió té `fct = 1`, es redirigeix al detall (`comision.detalle`) per associar FCTs
- Si `fct = 0`, es redirigeix a `comision.confirm` per enviar a direcció

### Escenari 2: Iniciar una comissió (enviar correus)

**Given** que una `Comision` té `estado = 0` i `desde` és una data futura  
**When** el professor fa `GET /comision/{id}/init`  
**Then**
- Per cada FCT associada amb `aviso = 1`, s'envia un correu a l'instructor amb fitxer `.ics` adjunt
- Es registra una `Activity` de tipus `visita` per a cada FCT
- L'`estado` de la comissió avança (via `StateService`)

### Escenari 3: Direcció autoritza una comissió

**Given** que una `Comision` té `estado >= 1` i `estado < 4`  
**When** un usuari amb rol `direccion` fa `GET /comision/{id}/authorize`  
**Then** l'`estado` passa a `2` (Autoritzada)

### Escenari 4: Direcció rebutja una comissió

**Given** que una `Comision` té `estado >= 1`  
**When** un usuari amb rol `direccion` fa `POST /comision/{id}/refuse`  
**Then** la comissió queda en estat rebutjat (o torna a `0`) i el professor n'és notificat

### Escenari 5: Cancel·lar una comissió

**Given** que una `Comision` té `estado >= 2` i `estado < 4`  
**When** es fa `GET /comision/{id}/cancel`  
**Then** l'`estado` baixa o s'anul·la i la comissió ja no apareix com a activa

### Escenari 6: Marcar com a no pagada

**Given** que una `Comision` té `estado = 3` i `total > 0`  
**When** es fa `GET /comision/{id}/unpaid`  
**Then** l'`estado` passa a `4` (No pagada)

### Escenari 7: Generar PDF d'una comissió

**Given** que existeix una `Comision` vàlida i l'usuari pot accedir-hi  
**When** fa `GET /comision/{id}/pdf`  
**Then** el servidor retorna un PDF vàlid (`Content-Type: application/pdf`)

## Gestió de FCTs associades

### Escenari 8: Associar una FCT a una comissió

**Given** que una `Comision` té `fct = 1` i el professor accedeix al detall  
**When** envia `POST /comision/{id}/createFct` amb `idFct` i `hora_ini` vàlids  
**Then**
- La FCT queda associada via taula pivot amb `hora_ini` i `aviso`
- L'usuari és redirigit al detall de la comissió

### Escenari 9: Desassociar una FCT

**Given** que una FCT està associada a una comissió  
**When** es fa `GET /comision/{comisionId}/deleteFct/{fctId}`  
**Then** la FCT queda desvinculada i la vista del detall s'actualitza

## Autorització

### Escenari 10: Accés sense permís

**Given** que un professor intenta editar o esborrar una comissió que no és seua  
**When** fa la petició (ruta amb middleware `owner:Comision`)  
**Then** rep `403`

## Regles de negoci invariants

- El professor autenticat (`authUser()->dni`) s'assigna automàticament com a `idProfesor` si no s'especifica.
- Botons `edit`/`delete` només visibles quan `estado >= 0` i `estado < 2`.
- Botó `cancel` només visible quan `estado >= 2` i `estado < 4`.
- Botó `unpaid` només visible quan `estado == 3` i `total > 0`.
- Botó `init` només visible quan `estado == 0` i `desde` és una data futura.
- Estats: `0=Creada`, `1=Pendent`, `2=Autoritzada`, `3=Impresa`, `4=No pagada`.
