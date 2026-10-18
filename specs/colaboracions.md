# Col·laboracions

Especificació del bounded context que presenta i filtra les col·laboracions amb empreses del tutor i del departament.

## Regles de negoci

- `MisColaboraciones` permet filtrar cada pestanya per poble i per nom d'empresa.
- Els filtres per poble, empresa i estat ràpid es combinen: una targeta ha de complir-los tots.
- La cerca per empresa és parcial, ignora accents i majúscules i conserva els números.
- En buidar el filtre d'empresa es mantenen actius els altres filtres.
- El panell departamental agrupa primer per poble i després per empresa.
- Cada empresa té una capçalera pròpia i les seues col·laboracions queden subordinades i diferenciades per cicle, estat i responsable.
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

### ✅ Diferenciar empreses en el panell departamental

**Given** un poble amb col·laboracions de diverses empreses

**When** el professor consulta les col·laboracions del departament

**Then** cada empresa apareix en un bloc visual propi amb nom destacat i recompte de col·laboracions.

### ✅ Agrupar les col·laboracions de la mateixa empresa

**Given** diverses col·laboracions de la mateixa empresa per a diferents cicles

**When** es renderitza el panell departamental

**Then** totes queden dins del mateix bloc d'empresa i continuen diferenciades per cicle, estat i responsable.
