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

## Regles de negoci invariants

- `sendTo` i `signed` no es poden modificar directament des de cap controlador sense passar per `SignaturaStatusService` o `EmailPostSendService`.
- Abans d'enviar a instructor: verificar existència de `Fct`, `Instructor`, `email`, `nombre`.
- Canvis a `sendTo`/`signed` requereixen revisar: `A1Finder`, `A2Finder`, `A3Finder`, `MailFinders/*`, `SignaturaStatusService`, `EmailPostSendService`.
