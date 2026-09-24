# Spec: FCT (Formació en Centres de Treball)

Especificació del comportament esperat per al domini FCT. Tecnologia-agnòstica: cada escenari ha de ser verificable independentment del framework.

## Signatures i enviament d'annexos

### Escenari 1: Enviar un annex individual a l'instructor

**Given** que existeix una `Signatura` amb `sendTo < 2`, `Fct` associat, `Instructor` amb `email` i `nombre` vàlids  
**When** el professor crida `POST /signatura/{id}/send`  
**Then**
- S'envia un correu a l'email de l'instructor
- El camp `sendTo` de la `Signatura` s'incrementa o es marca com a tramés
- Si tots els documents associats a l'instructor són Annex V → s'usa la plantilla `email.fct.a5`
- Si hi ha qualsevol altre annex → s'usa la plantilla `email.fct.anexes`

### Escenari 2: Enviar annexos múltiples a instructors

**Given** que el professor selecciona un o més IDs de `Signatura` i fa `POST /signatura/All/send`  
**When** es processa l'enviament múltiple  
**Then**
- Cada instructor afectat rep un sol correu (agrupació per instructor)
- `EmailPostSendService::handleAnnexeIndividual()` actualitza el camp `sendTo` per a cada signatura enviada
- Les signatures sense instructor vàlid (sense email) s'ometen i es reporta l'error

### Escenari 3: Enviar Annex III a alumnat

**Given** que el professor selecciona `Signatura` de tipus Annex III i fa `POST /signatura/A3/send`  
**When** es processa l'enviament  
**Then**
- Cada alumne afectat rep un correu des de `email.signaturaA3`
- El camp `sendTo` de les signatures es marca com a enviat

### Escenari 4: Pujar document signat

**Given** que existeix una `Signatura` i el professor puja un fitxer via `POST /signatura/{id}/upload`  
**When** el fitxer és vàlid  
**Then**
- El document queda guardat
- El camp `sendTo` es reinicia (a 0 o al valor inicial definit per la lògica de reinici)
- El camp `signed` s'actualitza per a reflectir el progrés

### Escenari 5: Accés no autoritzat a signatures

**Given** que un usuari sense rol `profesor` o `direccion` intenta accedir a `/signatura`  
**When** fa la petició  
**Then** rep un `403` o és redirigit al login

## Documentació FCT

### Escenari 6: Generar PDF d'una signatura

**Given** que existeix una `Signatura` vàlida i l'usuari autenticat pot accedir-hi  
**When** fa `GET /signatura/{id}/pdf`  
**Then** el servidor retorna un PDF vàlid (Content-Type `application/pdf`)

## Enquestes FE tutors

### Escenari 7: ✅ Exportar resultats per grup amb preguntes d'empresa agregades

**Given** que una enquesta FE de tutors té respostes numèriques sobre FCT/empresa vinculades a alumnat d'un grup  
**When** s'exporta la pestanya **Grups** a Excel  
**Then**
- Es mostren les preguntes numèriques de valoració d'empresa com a columnes
- Cada columna de pregunta mostra la mitjana aritmètica de les respostes rebudes en eixe grup
- Les mitjanes s'exporten com a valors numèrics d'Excel i amb format de dos decimals

### Escenari 8: ✅ Mostrar empreses i valoracions totals per grup

**Given** que un grup té diverses empreses/FCT avaluables i només una part han rebut valoració  
**When** s'exporta la pestanya **Grups** a Excel  
**Then**
- La columna `Empreses del grup` compta les FCT/empreses avaluables del grup
- La columna `Valoracions totals` compta una valoració per cada parella FCT/respondedor
- Una resposta de cotutor sobre la mateixa FCT incrementa les valoracions totals però no duplica les empreses del grup

## Dades de l'empresa en les col·laboracions

### Escenari 9: ✅ Separar el NIF llegat del nom del gerent

**Given** que una empresa té el camp `gerente` en format `NIF nom i cognoms`
**When** s'executa la migració de dades del gerent
**Then**
- El DNI, NIE o NIF inicial es guarda en `nif_gerente`, normalitzat en majúscules
- La resta del text es conserva en `gerente` com a nom complet

### Escenari 10: ✅ Conservar dades de gerent amb format ambigu

**Given** que el valor de `gerente` no comença per un DNI, NIE o NIF recognoscible
**When** s'executa la migració de dades del gerent
**Then**
- El contingut original de `gerente` no es modifica
- `nif_gerente` queda buit perquè l'empresa es revise manualment

### Escenari 11: ✅ Marcar una empresa sense NIF del gerent

**Given** que una col·laboració està vinculada a una empresa sense `nif_gerente`
**When** el tutor consulta el panell de col·laboracions
**Then**
- La fitxa mostra l'avís visible `Falta NIF del gerent`
- La col·laboració queda identificada com a fitxa incompleta
- L'avís enllaça directament amb l'edició de l'empresa

### Escenari 12: ✅ No marcar una empresa amb NIF del gerent

**Given** que una empresa té `nif_gerente` informat
**When** es mostra una col·laboració vinculada a l'empresa
**Then** no apareix l'avís `Falta NIF del gerent`

### Escenari 13: ✅ Actualitzar el NIF compartit del gerent

**Given** que diverses col·laboracions estan vinculades a la mateixa empresa
**When** un usuari autoritzat guarda el nom i el NIF del gerent des de l'edició de l'empresa
**Then**
- El NIF es normalitza en majúscules i sense espais externs
- La dada queda guardada en la fitxa compartida de l'empresa
- L'avís desapareix de totes les col·laboracions vinculades
- Els permisos d'edició continuen regits per `EmpresaPolicy`

## Confirmació pública de dades de l'empresa

### Escenari 14: ✅ Enviar una sol·licitud a una empresa assignada

**Given** que un tutor té almenys una col·laboració assignada en una empresa
**When** selecciona l'empresa i envia la sol·licitud
**Then**
- L'empresa rep un únic correu amb un enllaç públic temporal
- No cal que la col·laboració estiga marcada prèviament com a acceptada
- L'empresa queda desmarcada per defecte en enviaments posteriors

### Escenari 15: ✅ Accedir al formulari mitjançant un token temporal

**Given** que l'empresa disposa d'un token vigent i no utilitzat
**When** obri l'enllaç sense iniciar sessió
**Then** només veu les dades de la seua empresa i pot confirmar-les una vegada

### Escenari 16: ✅ Confirmar empresa, gerent i centres amb formacions

**Given** que la intranet ja disposa de dades de l'empresa
**When** l'empresa revisa el formulari
**Then**
- Les dades apareixen preemplenades
- Només es mostren els centres que tenen almenys una col·laboració amb un cicle
- Cada centre mostra tots els cicles amb què col·labora, encara que siguen d'altres tutors
- Els centres ocults no es modifiquen

### Escenari 17: ✅ Organitzar instructors per centre i designar coordinador

**Given** que els instructors estan vinculats als centres de treball
**When** l'empresa revisa les dades
**Then**
- Els instructors apareixen ordenats i agrupats per centre visible
- Les dades personals compartides s'editen una sola vegada
- Cal conservar almenys un instructor i designar exactament un coordinador de l'empresa
- Els instructors vinculats exclusivament a centres sense cicles no apareixen

### Escenari 18: ✅ Desvincular un instructor d'un centre

**Given** que un instructor ja no treballa en un centre
**When** l'empresa marca que ja no hi està vinculat i confirma
**Then**
- Només s'elimina la vinculació amb eixe centre
- Es conserven les vinculacions amb altres centres i l'històric d'FCT
- No es pot eliminar el coordinador seleccionat sense designar-ne un altre

### Escenari 19: ✅ Enviar el correu en nom del tutor i en dos idiomes

**Given** que el tutor té un correu electrònic vàlid
**When** envia la sol·licitud
**Then**
- El tutor figura com a remitent i adreça de resposta
- El missatge agraïx la col·laboració en valencià i castellà
- Explica el canvi de l'aplicació de pràctiques i el posterior accés del coordinador a la plataforma

### Escenari 20: ✅ Avisar el tutor després de la confirmació

**Given** que l'empresa confirma correctament les dades
**When** finalitza la transacció
**Then** el tutor rep un correu amb l'empresa i la data de confirmació

### Escenari 21: ✅ Conservar la confirmació si falla l'avís al tutor

**Given** que les dades ja s'han guardat correctament
**When** falla l'enviament del correu al tutor
**Then** la confirmació continua registrada i l'error queda anotat al log

## Regles de negoci invariants

- `sendTo` i `signed` no es poden modificar directament des de cap controlador sense passar per `SignaturaStatusService` o `EmailPostSendService`.
- Abans d'enviar a instructor: verificar existència de `Fct`, `Instructor`, `email`, `nombre`.
- Canvis a `sendTo`/`signed` requereixen revisar: `A1Finder`, `A2Finder`, `A3Finder`, `MailFinders/*`, `SignaturaStatusService`, `EmailPostSendService`.
- En la pestanya **Grups** de l'Excel FE tutors, les mitjanes s'han d'exportar com a números, no com a text.
- `gerente` conserva el nom complet i `nif_gerente` conserva l'identificador fiscal separat.
- La migració només separa identificadors recognoscibles situats al principi de `gerente`; els valors ambigus no es modifiquen.
- La falta de `nif_gerente` és un avís de fitxa incompleta, però no bloqueja el treball amb la col·laboració.
- Els tokens públics són d'un sol ús, caduquen als quinze dies i només exposen una empresa.
- Els centres sense col·laboracions i els seus instructors exclusius no formen part del formulari públic.
- La fallada del correu posterior al tutor no pot revertir una confirmació ja guardada.
