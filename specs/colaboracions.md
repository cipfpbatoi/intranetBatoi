# Col·laboracions

Especificació del bounded context que presenta i filtra les col·laboracions amb empreses del tutor i del departament.

## Regles de negoci

- `MisColaboraciones` permet filtrar cada pestanya per poble i per nom d'empresa.
- Els filtres per poble, empresa i estat ràpid es combinen: una targeta ha de complir-los tots.
- La cerca per empresa és parcial, ignora accents i majúscules i conserva els números.
- En buidar el filtre d'empresa es mantenen actius els altres filtres.
- El panell departamental mostra totes les col·laboracions dels cicles del departament i destaca els cicles que tutoritza el professor actual.
- Cada fitxa representa una col·laboració entre una empresa i un cicle, i s'ordena en una graella responsive de tres columnes des de 992 px, dos en tauleta i una en mòbil.
- El professor pot classificar la informació per poble o per cicle i filtrar-la per contactades, col·laboradores, documentació antiga o pendent i absència d'activitat recent.
- El filtre independent per poble es combina amb qualsevol classificació activa del panell departamental.
- L'abreviatura de cada cicle conserva un color estable i diferenciat.
- Les fitxes d'una mateixa empresa compartixen un fons molt degradat i una vora clara estable, sense interferir amb el color del cicle.
- Una col·laboració és contactada quan `estado != 0` i col·labora quan `estado = 2`.
- La documentació antiga o pendent reutilitza l'indicador `conveniPendent` del domini.
- Una col·laboració està sense activitat recent quan no té cap FCT en el curs actual ni en l'anterior, segons la data màxima `alumno_fcts.hasta`; les que no tenen cap FCT també s'hi inclouen.
- Cada fitxa mostra empresa, poble, abreviatura del cicle, professor amb nom i primer cognom o una icona accessible de professor ratllat, estat, documentació i última FCT.
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

### ✅ Distribuir les col·laboracions del departament en columnes

**Given** col·laboracions de diverses empreses i cicles del departament

**When** el professor consulta les col·laboracions del departament

**Then** cada parella empresa-cicle apareix en una fitxa diferenciada dins d'una graella de tres columnes des de 992 px, dos en tauleta i una en mòbil.

### ✅ Classificar per poble i per cicle

**Given** col·laboracions de diferents pobles i cicles

**When** el professor selecciona la classificació per poble o per cicle

**Then** les fitxes s'agrupen segons el criteri seleccionat i cada grup mostra el seu recompte.

### ✅ Filtrar el panell departamental per poble

**Given** col·laboracions de diferents pobles i qualsevol classificació activa

**When** el professor escriu o selecciona un poble en el filtre independent

**Then** només es mostren les fitxes coincidents amb el poble que també complixen la classificació activa.

### ✅ Identificar visualment els cicles

**Given** col·laboracions de diferents cicles

**When** es mostren les seues abreviatures

**Then** cada cicle conserva un color estable i diferenciat en totes les seues fitxes.

### ✅ Diferenciar visualment les empreses

**Given** diverses empreses dins d'un mateix grup

**When** es mostren les seues fitxes

**Then** les fitxes d'una mateixa empresa compartixen un fons molt degradat i una vora clara estable, mentre el color identificatiu queda reservat al cicle.

### ✅ Filtrar les col·laboracions contactades i les que col·laboren

**Given** col·laboracions amb diferents estats

**When** el professor selecciona `Contactades` o `Col·laboren`

**Then** `Contactades` mostra les que tenen `estado != 0` i `Col·laboren` les que tenen `estado = 2`.

### ✅ Identificar els cicles propis del tutor

**Given** que el professor tutoritza un o més cicles del departament

**When** consulta el panell departamental

**Then** les fitxes d'eixos cicles mostren un distintiu que les diferencia de la resta.

### ✅ Identificar la documentació antiga o pendent

**Given** col·laboracions amb documentació vigent i amb conveni pendent o anterior a l'1 de gener de 2024

**When** el professor selecciona `Documentació antiga o pendent`

**Then** només es mostren les fitxes marcades pel domini amb `conveniPendent`.

### ✅ Identificar les col·laboracions sense activitat recent

**Given** col·laboracions amb FCT recents, amb l'última FCT anterior al curs acadèmic precedent i sense cap FCT

**When** el professor selecciona `Sense activitat recent`

**Then** es mostren les que no tenen cap FCT en el curs actual ni en l'anterior, incloses les que no en tenen cap registrada.

### ✅ Mostrar la informació operativa de cada col·laboració

**Given** col·laboracions amb professor assignat i sense assignar, diferents estats i historial FCT

**When** es renderitza el panell departamental

**Then** cada fitxa mostra empresa, poble, abreviatura del cicle, estat, documentació, última FCT i el professor amb nom i primer cognom o la icona accessible `Sense assignar`.
