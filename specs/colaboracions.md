# Col·laboracions

Especificació del bounded context que presenta i filtra les col·laboracions amb empreses del tutor i del departament.

## Regles de negoci

- `MisColaboraciones` permet filtrar cada pestanya per poble i per nom d'empresa.
- Els filtres per poble, empresa i estat ràpid es combinen: una targeta ha de complir-los tots.
- La cerca per empresa és parcial, ignora accents i majúscules i conserva els números.
- En buidar el filtre d'empresa es mantenen actius els altres filtres.
- El panell departamental agrupa primer per poble i després per empresa.
- Les empreses es distribuïxen en una graella responsive i cada fitxa mostra només el nom en la capçalera, sense etiqueta ni recompte.
- Les col·laboracions d'una empresa es representen amb les abreviatures dels cicles en una fila flexible i cada cicle enllaça amb el seu detall.
- El professor assignat es mostra amb nom i primer cognom; l'absència d'assignació es representa amb una icona accessible de professor ratllat.
- L'estat `No contactada` no es mostra en la fitxa compacta; `Contactada` i `No col·labora` es mantenen visibles.
- Els filtres i la presentació no modifiquen dades, accions ni permisos.

## Escenaris

### ✅ Filtrar `MisColaboraciones` per empresa

**Given** una pestanya amb col·laboracions de diverses empreses

**When** el professor escriu una part del nom en el filtre d'empresa

**Then** només es mostren les targetes coincidents, ignorant accents i majúscules i conservant els números del nom.

### ✅ Combinar els filtres de poble, empresa i estat

**Given** filtres per poble, empresa i estat ràpid

**When** el professor n'activa diversos

**Then** només es mostren les targetes que complixen tots els criteris i els pobles sense resultats queden ocults.

### ✅ Netejar el filtre d'empresa

**Given** un filtre d'empresa actiu junt amb altres filtres

**When** el professor buida el camp d'empresa

**Then** es recuperen les targetes admeses pels altres filtres actius.

### ✅ Distribuir les empreses del departament en columnes

**Given** un poble amb col·laboracions de diverses empreses

**When** el professor consulta les col·laboracions del departament

**Then** cada empresa apareix en una fitxa diferenciada dins d'una graella responsive i la capçalera mostra només el nom de l'empresa, sense etiqueta ni recompte.

### ✅ Mostrar els cicles de cada empresa de manera compacta

**Given** diverses col·laboracions de la mateixa empresa per a diferents cicles

**When** es renderitza el panell departamental

**Then** les abreviatures dels cicles apareixen en una fila flexible dins de la mateixa fitxa i cadascuna enllaça amb el detall de la seua col·laboració.

### ✅ Mostrar l'assignació amb el nom curt del professor

**Given** col·laboracions amb professor assignat i sense professor assignat

**When** es renderitzen els seus cicles

**Then** el professor assignat apareix amb nom i primer cognom i la col·laboració sense assignació mostra una icona de professor ratllat amb l'ajuda accessible `Sense assignar`.

### ✅ Ometre l'estat no contactada en la fitxa compacta

**Given** col·laboracions amb els estats `No contactada`, `Contactada` i `No col·labora`

**When** es renderitza la fitxa de l'empresa

**Then** no es mostra el text `No contactada` i es mantenen visibles els estats `Contactada` i `No col·labora`.
