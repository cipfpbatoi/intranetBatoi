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

### Escenari 11: Mostrar la ubicació en el llistat de Direcció ✅

**Given** una activitat visible al panell de Direcció
**When** es renderitza el llistat d'activitats
**Then** apareix una columna `Ubicació` amb el valor corresponent

### Escenari 12: Mostrar la ubicació en el detall de Direcció ✅

**Given** que Direcció selecciona l'opció de veure una activitat
**When** s'obri el modal de detall
**Then** es mostra el camp `Ubicació`

### Escenari 13: Representar ubicació i transport en Direcció ✅

**Given** els valors guardats en `fueraCentro` i `transport`
**When** es prepara l'activitat per al panell de Direcció
**Then**
- Es mostra `Dins del centre` si `fueraCentro = 0`
- Es mostra `Fora del centre amb transport` si `fueraCentro = 1` i `transport = 1`
- Es mostra `Fora del centre sense transport` si `fueraCentro = 1` i `transport = 0`

### Escenari 14: Mostrar la classificació completa abans d'autoritzar en Direcció ✅

**Given** una activitat visible al panell de Direcció
**When** es renderitza el llistat o el detall de l'activitat
**Then**
- El llistat mostra les columnes `Tipus` i `Ubicació`
- Es mostra `Complementària` si `complementaria = 1`
- Es mostra `Extraescolar` si `complementaria = 0`
- La ubicació diferencia `Dins del centre`, `Fora del centre amb transport` i `Fora del centre sense transport`
- No s'usa l'etiqueta ambigua `No complementària`

## Llistat per a ITACA3

Proposta aprovada per l'usuari: botó de llistat per a ITACA i data d'impressió
guardada en cada activitat. El pas a ITACA indica generació del llistat de treball,
no una transferència automàtica de dades al sistema extern.

### Escenari 15: Guardar la data del llistat d'autorització ✅

**Given** activitats autoritzades pendents d'impressió
**When** es genera correctament el PDF del llistat d'autoritzades
**Then** les activitats incloses passen a Impressa i guarden la mateixa data
en `fecha_impresion`. Si es tornen a autoritzar i imprimir, s'actualitza la data.
Si falla la generació del PDF, no es registra la impressió ni es canvia l'estat.

### Escenari 16: Disponibilitat i selecció del llistat ITACA ✅

**Given** el panell d'activitats de Direcció
**When** es calcula la disponibilitat d'«Imprimir Llistat Valorades per Itaca», situat a la dreta de la barra d'accions
**Then** el botó només està actiu si existeixen activitats del flux d'extraescolars
(`extraescolar = 1`) amb estat Valorada (`4`) i finalització (`hasta`) anterior
a l'instant actual. El lot és global, independent dels filtres del panell.

### Escenari 17: Contingut complet per a ITACA3 ✅

**Given** activitats seleccionades per al llistat ITACA
**When** Direcció genera el PDF
**Then** inclou número, activitat, descripció, objectius, inici, finalització,
ubicació/transport, comentaris, professorat i grups del llistat existent, més:
- Categoria de `tipo_actividad.vliteral`, relacionada per `tipo_actividad_id`.
- Nom complet del coordinador real (`actividad_profesor.coordinador = 1`).
- Data d'impressió de l'autorització guardada en `fecha_impresion`.

Sense categoria o coordinador es mostra «Sense assignar». Les activitats antigues
sense data mostren «Sense data», sense assignar-los una data fictícia.

### Escenari 18: Marcar únicament el lot generat com a ITACA ✅

**Given** activitats valorades i ja finalitzades
**When** es genera correctament el PDF per a ITACA
**Then** únicament les incloses passen a ITACA (`5`), conservant la data
d'impressió de l'autorització i el document d'autorització vinculat.
No apareixen en una nova generació. Si falla el PDF, no canvia cap estat.
Si no hi ha activitats disponibles, no es genera cap PDF ni es modifica cap registre.

### Escenari 19: Recuperar la data històrica d'impressió ✅

**Given** una activitat antiga amb `fecha_impresion` buida i un document vinculat
per `idDocumento` amb `created_at` informat
**When** s'aplica la migració de recuperació de dates
**Then** es guarda en `fecha_impresion` la part de data de `documentos.created_at`,
també si l'activitat ja ha passat a Valorada o ITACA. No es modifica l'estat
ni la data d'última actualització de l'activitat.

### Escenari 20: Respectar dates existents i dades desconegudes ✅

**Given** activitats amb una data d'impressió guardada o sense document vàlid i datat
**When** s'aplica la recuperació, inclosa una nova execució
**Then** es conserven les dates existents i les desconegudes continuen buides.
No s'usa `updated_at` ni la data actual com a substitut de la data d'impressió.

## Regles de negoci invariants

- `complementaria` i `extraescolar` (llegat) no són el mateix camp; no intercanviar sense migració.
- `transport = 1` és incoherent si `fueraCentro = 0`; normalitzar o rebutjar.
- Les activitats complementàries mostren RA; les extraescolars, no.
- La justificació RA és `tipo_actividad.justificacio`; `actividades.descripcion` és descripció general.
- El coordinador sempre es determina per `actividad_profesor.coordinador = 1`, mai per posició.
- El panell de Direcció mostra la ubicació a partir dels camps llegats `fueraCentro` i `transport`.
- El panell de Direcció mostra el tipus a partir de `complementaria` amb les etiquetes `Complementària` i `Extraescolar`.
- La data d'impressió de l'autorització es conserva en passar a Valorada o ITACA.
- La generació del llistat ITACA requerix rol de Direcció i una petició POST.
