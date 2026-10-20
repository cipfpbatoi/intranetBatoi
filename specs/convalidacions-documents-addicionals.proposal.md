# Proposta: afegir documentació a una sol·licitud existent

- Estat: ready_for_review
- Issue relacionada: #323
- Domini: convalidacions de l'alumnat
- Punt d'entrada actual: `AlumnoConvalidacioController::correctAttachment()` / `correct()`, les rutes d'alumnat i `ConvalidacioService`
- Docs/specs llegits: `AGENTS.md`, `docs/agents/conventions.md`, `docs/agents/openspec.md`, `specs/convalidacions.md`
- Abast: permetre afegir adjunts nous a una sol·licitud que està sent esmenada sense reemplaçar els adjunts existents. No inclou OCR, IA, canvis en les regles de convalidació ni validació automàtica del contingut documental.

## Context

Ara l'alumnat pot corregir un adjunt substituint-lo. Si Direcció demana documentació addicional —per exemple, un certificat acadèmic i després el certificat de prevenció de riscos laborals—, pujar el segon fitxer pot reemplaçar el primer. Cal separar les accions «substituir un adjunt» i «afegir un adjunt».

## Regles de negoci

1. L'alumnat pot afegir adjunts només mentre la sol·licitud estiga en `Enviada` o `Revisar documentació`, sempre que siga la seua sol·licitud.
2. Afegir un adjunt no elimina, substitueix ni modifica cap adjunt existent. La substitució individual d'un adjunt continua disponible com a acció separada.
3. Es poden conservar com a màxim tres documents per sol·licitud. El límit compta tant els adjunts nous com els documents antics guardats en camps llegats (`document_path` i `document_prl_path`); s'ha de validar al servidor encara que la interfície també desactive l'acció en arribar al màxim.
4. Cada nou fitxer ha d'incloure una descripció i ha de complir les mateixes restriccions de tipus, mida i emmagatzematge privat que els adjunts de la sol·licitud inicial.
5. Si s'afegeix documentació mentre l'estat és `Revisar documentació`, l'estat del mòdul torna a `En procés` després de guardar correctament. Si és `Enviada`, es manté `Enviada`. Una pujada fallida no canvia l'estat ni la documentació existent.
6. Els adjunts nous han d'aparéixer junt amb tots els documents previs, inclosos els documents llegats, i s'han de poder consultar/descargar segons les autoritzacions vigents.
7. La declaració responsable existent continua aplicant-se a tots els documents adjunts. Per a documentació externa, no es pot afegir un fitxer sense que la declaració estiga acceptada; una declaració ja acceptada cobreix també els adjunts posteriors.
8. La validesa i suficiència dels documents continuen sent revisades manualment per Direcció. No s'introdueix cap comprovació amb IA ni OCR.

## Escenaris BDD

### Escenari 1: afegir un document sol·licitat sense perdre els anteriors

Given una sol·licitud pròpia en estat `Revisar documentació` amb un o més adjunts existents
When l'alumne afegeix un fitxer vàlid amb la seua descripció
Then el nou fitxer queda afegit a la sol·licitud sense substituir ni esborrar els adjunts anteriors
And tots els documents continuen visibles per a l'alumne i Direcció
And l'estat del mòdul passa a `En procés`

### Escenari 2: afegir documentació a una sol·licitud encara `Enviada`

Given una sol·licitud pròpia en estat `Enviada` i amb menys de tres documents
When l'alumne afegeix un fitxer vàlid amb la seua descripció
Then el fitxer s'afig i es conserven els adjunts previs
And l'estat continua sent `Enviada`

### Escenari 3: respectar el màxim de tres documents

Given una sol·licitud amb tres documents, comptant els adjunts relacionals i els documents llegats
When l'alumne intenta afegir-ne un altre
Then el servidor rebutja l'operació amb un missatge comprensible
And no s'elimina ni es modifica cap document existent

### Escenari 4: protegir adjunts i sol·licituds

Given una sol·licitud d'un altre alumne o en un estat diferent d'`Enviada` i `Revisar documentació`
When l'usuari intenta afegir un document, incloent-hi una petició manipulada directament
Then l'operació és denegada al servidor
And no es guarda el fitxer ni es modifica la sol·licitud

### Escenari 5: revisió documental manual

Given una sol·licitud amb diversos documents adjunts
When Direcció revisa la documentació
Then pot consultar cada fitxer i la descripció aportada
And el sistema no declara automàticament vàlid cap document mitjançant IA o OCR

## Criteris d'acceptació

- Afegir un adjunt i substituir un adjunt existent són accions distintes.
- El màxim de tres s'aplica també a peticions antigues que tenen documents en camps llegats.
- Cap error de validació, autorització o emmagatzematge pot esborrar els adjunts previs.
- Hi ha proves de regressió per a l'afegit, la conservació dels adjunts antics, els límits, els estats i l'autorització.

## Àrees previsibles d'implementació

- Ruta i acció del controlador d'alumnat per a afegir un adjunt.
- `ConvalidacioService`, reutilitzant les validacions i l'emmagatzematge privat existents sense confondre afegir amb reemplaçar.
- Vista d'alumnat de detall/correcció, amb llistat dels adjunts existents i un formulari separat per a afegir documentació.
- Proves de funcionalitat per als escenaris anteriors.
- En arxivar la proposta, actualitzar `specs/convalidacions.md` i retirar qualsevol afirmació incompatible amb la nova possibilitat d'afegir documents.
