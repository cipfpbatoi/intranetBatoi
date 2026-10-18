# Col·laboracions

Especificació del bounded context que presenta i filtra les col·laboracions amb empreses del tutor i del departament.

## Regles de negoci

- `MisColaboraciones` permet filtrar cada pestanya per poble i per nom d'empresa.
- Els filtres per poble, empresa i estat ràpid es combinen: una targeta ha de complir-los tots.
- La cerca per empresa és parcial, ignora accents i majúscules i conserva els números.
- En buidar el filtre d'empresa es mantenen actius els altres filtres.
- El panell departamental mostra totes les col·laboracions dels cicles del departament sense afegir un distintiu al cicle tutoritzat pel professor actual.
- El professor pot filtrar el panell departamental per població, cicle, estat de contacte, conveni actualitzat i assignació.
- Els filtres departamentals són acumulatius: una mateixa col·laboració ha de complir tots els criteris actius.
- `Sense contacte` i `Contactada` depenen respectivament de l'absència o existència d'almenys un contacte registrat, no del valor d'`estado`.
- Les marques d'estat respecten el domini: `No contactada` (`estado = 0`), `No sé` (`estado = 1`), `Col·labora` (`estado = 2`) i `No col·labora` (`estado = 3`).
- Una col·laboració està assignada quan té professor tutor i té el conveni actualitzat quan `conveniPendent` és fals.
- Les poblacions es filtren amb una clau que ignora majúscules, accents, apòstrofs i puntuació, sense modificar `centros.localidad`.
- Els àlies configurats compartixen un únic nom canònic; inicialment s'unifiquen les variants d'Alcoi, Muro d'Alcoi, València, Xàtiva, Denia, Ontinyent, Alacant, Banyeres, Xixona i Sant Vicent del Raspeig.
- Les opcions del filtre de població es mostren en majúscules i conserven els accents del nom canònic; el text de les fitxes no es modifica.
- El panell departamental exclou abans d'agrupar i comptar les col·laboracions vinculades a empreses Erasmus (`empresas.europa = 1`).
- Primer es filtren les col·laboracions i després s'agrupen per `idCentro`, de manera que cada empresa o centre apareix una sola vegada i conté els cicles coincidents.
- Cada cicle visible dins de la fitxa permet accedir al detall de la seua col·laboració.
- La graella és responsive: tres columnes des de 992 px, dos en tauleta i una en mòbil.
- L'abreviatura de cada cicle conserva un color estable i diferenciat.
- Cada empresa o centre conserva un fons degradat i una vora clara estables, sense interferir amb el color del cicle.
- El panell no classifica ni etiqueta les col·laboracions segons l'historial d'FCT, perquè les dades es renoven cada curs.
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

### ✅ Combinar els filtres del panell departamental

**Given** col·laboracions de diferents poblacions, cicles, estats, situacions documentals i assignacions

**When** el professor activa diversos filtres alhora

**Then** només es mostren les col·laboracions que complixen tots els criteris actius sobre el mateix registre.

### ✅ Filtrar per estat

**Given** col·laboracions amb i sense contactes registrats i amb `estado` 0, 1, 2 i 3

**When** selecciona `Sense contacte`, `Contactada` o `Col·labora`

**Then** es mostren respectivament les que no tenen contacte, les que en tenen almenys un o les d'estat 2, i l'estat 1 s'etiqueta com `No sé`.

### ✅ Filtrar per assignació

**Given** col·laboracions amb professor tutor i sense professor tutor

**When** selecciona `Assignada` o `Sense assignar`

**Then** només es mostren les col·laboracions que tenen o no tenen professor tutor, respectivament.

### ✅ Filtrar per conveni actualitzat

**Given** col·laboracions amb `conveniPendent` verdader i fals

**When** el professor activa `Conveni actualitzat`

**Then** desapareixen les col·laboracions amb conveni pendent i només es mostren les que tenen `conveniPendent` fals.

### ✅ Agrupar una empresa amb diversos cicles

**Given** diverses col·laboracions que compartixen `idCentro` però pertanyen a cicles diferents

**When** el professor consulta o filtra el panell departamental

**Then** l'empresa apareix en una única fitxa que conté només els cicles coincidents i cada cicle enllaça amb el detall de la seua col·laboració.

### ✅ Presentar la graella i els colors de manera estable

**Given** empreses i cicles diferents en el panell departamental

**When** es renderitza la pantalla en escriptori, tauleta o mòbil

**Then** la graella mostra tres, dos o una columna respectivament, cada empresa conserva el seu degradat i vora i cada cicle conserva el seu color.

### ✅ Ometre el distintiu del cicle propi

**Given** que el professor tutoritza un o més cicles del departament

**When** consulta el panell departamental

**Then** el cicle conserva la mateixa presentació compacta que la resta i no mostra l'etiqueta `El meu cicle`.

### ✅ Normalitzar les poblacions del filtre

**Given** poblacions escrites amb diferències de caixa, accents, puntuació o àlies castellans i valencians configurats

**When** es construïx el filtre departamental per població

**Then** cada municipi apareix una sola vegada amb el nom valencià canònic i totes les seues variants responen a la mateixa opció.

### ✅ Mostrar les poblacions del filtre en majúscules

**Given** noms canònics amb i sense accents en el filtre departamental de població

**When** es mostren les opcions del selector

**Then** els noms apareixen en majúscules, conserven els accents i mantenen intactes les claus de filtratge i el text de les fitxes.

### ✅ Excloure les empreses Erasmus del panell departamental

**Given** col·laboracions d'empreses ordinàries (`europa = 0`) i Erasmus (`europa = 1`)

**When** el professor consulta o filtra les col·laboracions del departament

**Then** només es carreguen, agrupen i compten les col·laboracions de les empreses ordinàries, sense modificar el panell específic d'Erasmus.

### ✅ Netejar els filtres departamentals

**Given** diversos filtres departamentals actius i un recompte de resultats filtrat

**When** prem `Neteja els filtres`

**Then** es restablixen tots els controls, tornen a aparéixer totes les col·laboracions i s'actualitza el recompte.

### ✅ Ometre classificacions basades en l'historial FCT

**Given** que les dades d'FCT es renoven cada curs

**When** consulta les col·laboracions del departament

**Then** no hi ha filtres ni etiquetes de falta d'activitat recent o d'antiguitat calculats a partir de l'historial FCT.
