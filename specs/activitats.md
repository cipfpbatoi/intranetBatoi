# Spec: Activitats complementàries i extraescolars

Especificació del comportament esperat per al domini Activitats. Tecnologia-agnòstica.

## Creació d'activitats

### Escenari 1: Crear activitat complementària

**Given** que un professor autenticat omple el formulari d'activitat  
**When** selecciona tipus `Complementària` i envia el formulari  
**Then**
- Es crea un registre `Actividad` amb `complementaria = 1`
- El formulari mostra el camp de **Resultats d'Aprenentatge (RA)**
- El camp `extraescolar` (llegat) es gestiona internament; no és la mateixa cosa que el tipus d'usuari

### Escenari 2: Crear activitat extraescolar

**Given** que un professor autenticat omple el formulari d'activitat  
**When** selecciona tipus `Extraescolar` i envia el formulari  
**Then**
- Es crea un registre `Actividad` amb `complementaria = 0`
- El camp de RA **no** apareix al formulari ni es desa
- El camp `extraescolar` (llegat) es gestiona internament

### Escenari 3: Activitat fora del centre amb transport

**Given** que l'usuari selecciona ubicació `Fora del centre` (`fueraCentro = 1`)  
**When** omple el formulari  
**Then** el camp `transport` és visible i opcional

### Escenari 4: Activitat dins del centre — transport ocult

**Given** que l'usuari selecciona ubicació `Dins del centre` (`fueraCentro = 0`)  
**When** omple el formulari  
**Then**
- El camp `transport` **no** és visible
- Si es rep `transport = 1` per API amb `fueraCentro = 0`, el sistema normalitza `transport = 0`

## Coordinador

### Escenari 5: Identificar coordinador real

**Given** que una activitat té múltiples professors participants  
**When** es consulta el coordinador de l'activitat  
**Then**
- El coordinador és la fila d'`actividad_profesor` amb `coordinador = 1`
- No s'usa el primer registre de participants com a coordinador
- Si no hi ha cap fila amb `coordinador = 1`, el sistema retorna null (no infereix)

## Accés i autorització

### Escenari 6: Professor accedeix a les seues activitats

**Given** que un professor autenticat accedeix al llistat  
**When** fa `GET /actividades` (o la ruta de professor)  
**Then** veu únicament les activitats on és participant o coordinador

### Escenari 7: Direcció accedeix a totes les activitats

**Given** que un usuari amb rol `direccion` accedeix al panell  
**When** fa la petició  
**Then** veu totes les activitats del centre (no filtrades per professor)

## PDF i vistes

### Escenari 8: Generar PDF d'activitat extraescolar

**Given** que l'activitat és extraescolar i l'usuari té permís  
**When** sol·licita el PDF  
**Then** es genera un PDF usant `resources/views/pdf/extraescolars.blade.php` amb les dades correctes

### Escenari 9: Mostrar justificació RA només en complementàries ✅

**Given** que una activitat té un tipus amb `tipo_actividad.justificacio`
**When** es mostra el detall o la valoració de l'activitat
**Then**
- La justificació RA prové de `tipo_actividad.justificacio`
- Les activitats complementàries poden mostrar `Justificació RA`
- Les activitats extraescolars no mostren `Justificació RA`

### Escenari 10: Evitar etiquetes ambigües en PDFs d'activitats ✅

**Given** que direcció genera el PDF/listat d'activitats extraescolars
**When** es renderitza la columna de descripció
**Then** la columna es mostra com `Descripció` i no com `Descripció/Justificació`

## Regles de negoci invariants

- `complementaria` i `extraescolar` (llegat) no són el mateix camp; no intercanviar sense migració.
- `transport = 1` és incoherent si `fueraCentro = 0`; normalitzar o rebutjar.
- Les activitats complementàries mostren RA; les extraescolars, no.
- La justificació RA és `tipo_actividad.justificacio`; `actividades.descripcion` és descripció general.
- El coordinador sempre es determina per `actividad_profesor.coordinador = 1`, mai per posició.
