# Proposta: registrar documentació rebuda per Direcció o Secretaria

- Estat: `ready_to_commit`
- Domini: convalidacions, documentació i traçabilitat
- Punt d'entrada: detall de la sol·licitud, `ConvalidacioPolicy` i `ConvalidacioService`

## Context

L'alumnat pot aportar o corregir documentació, però a vegades porta la documentació a Secretaria o Direcció perquè la incorporen a l'expedient. Les peticions tenen documents privats associats i l'estat `Revisar documentació`, que indica que s'ha sol·licitat documentació addicional. Les aportacions del personal no han de substituir ni esborrar documents anteriors ni fer-se passar per una aportació de l'alumne.

## Escenaris

### ✅ Escenari 1: personal autoritzat registra documentació pendent

**Given** una petició està en estat `Revisar documentació`
**And** actua una persona amb rol Secretaria o permisos operatius de Direcció
**When** incorpora un document, un comentari o tots dos a la petició
**Then** el sistema guarda una aportació vinculada a eixa petició
**And** el fitxer, si n'hi ha, queda en l'emmagatzematge privat amb les validacions de tipus i mida vigents
**And** es conserven tots els documents ja existents i la declaració responsable de l'alumne no es modifica
**And** es registra qui ha fet l'aportació, el rol operatiu amb què ha actuat i la data i hora.

### ✅ Escenari 2: l'aportació només està disponible quan s'ha demanat documentació

**Given** una persona de Secretaria o Direcció obri una petició en qualsevol estat diferent de `Revisar documentació`
**When** consulta o intenta enviar directament una aportació
**Then** no se li mostra l'acció i el servidor rebutja igualment la petició manipulada
**And** no es crea cap document ni comentari.

### ✅ Escenari 3: es reprén la revisió quan s'incorpora un fitxer

**Given** una petició està en `Revisar documentació` i el personal autoritzat incorpora almenys un fitxer rebut
**When** l'aportació es guarda correctament
**Then** la petició passa a `En procés`, com quan l'alumne aporta documentació
**And** l'acció queda registrada amb la persona i el rol que l'ha feta
**But** si només es guarda un comentari sense fitxer, es conserva `Revisar documentació` perquè no s'ha incorporat documentació sol·licitada.

### ✅ Escenari 4: consultar la traçabilitat de les aportacions

**Given** una persona autoritzada consulta el detall d'una petició amb aportacions del personal
**When** revisa els documents i comentaris
**Then** pot distingir cada aportació del personal de les aportacions de l'alumne
**And** veu la identitat, el rol operatiu i la data de cada aportació
**And** només pot descarregar els fitxers mitjançant la ruta privada autoritzada.

## Regles de negoci

- Només Secretaria i personal amb permisos operatius de Direcció poden registrar aportacions del personal.
- L'acció només està disponible quan l'estat del mòdul és `Revisar documentació`; no es concedix una capacitat general d'edició de l'expedient.
- Una aportació pot contindre fitxer, comentari o tots dos; no pot estar buida.
- Els fitxers són privats, passen les validacions existents i no substituïxen ni eliminen documents previs.
- La declaració responsable de l'alumne no s'activa ni es modifica quan el personal incorpora un fitxer.
- Cada aportació conserva la identitat de qui l'ha registrada, el rol operatiu utilitzat i la marca temporal.
- Quan s'incorpora un fitxer, `Revisar documentació` torna a `En procés`; un comentari sense fitxer no canvia l'estat.
- Es manté el límit actual de tres documents per petició, comptant els adjunts existents i els llegats.

## Fitxers previsibles

- `app/Policies/ConvalidacioPolicy.php` — autoritzar l'acció de personal només per a Secretaria/Direcció i només en `Revisar documentació`.
- `app/Application/Convalidacio/ConvalidacioService.php` — guardar l'aportació de manera segura i transaccional, conservar fitxers anteriors i aplicar el canvi d'estat definit.
- `app/Entities/DocumentConvalidacio.php` i migracions — persistir comentari, actor, rol i traçabilitat sense perdre documents existents.
- `app/Http/Controllers/DireccionConvalidacioController.php` i `routes/convalidacions-sollicituds.php` — validar peticions i exposar l'acció protegida.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` — formulari contextual i llistat de les aportacions.
- `tests/Feature/ConvalidacioFlowTest.php` i `tests/Feature/RouteNameContractTest.php` — casos per rol, estat, càrrega privada, preservació de documents i traçabilitat.

## Riscos i punts a validar

- El límit vigent és de tres documents per petició; si el personal ha de poder excedir-lo quan l'alumne ja n'ha aportat tres, caldrà canviar explícitament esta regla.
- El comentari de l'aportació es manté separat de l'observació de Direcció associada a la resolució, per no sobreescriure-la ni generar notificacions pròpies d'un canvi d'estat.
- Si una persona té alhora Secretaria i Direcció, s'ha de guardar el rol operatiu triat o bé una instantània dels rols actius en el moment de l'aportació; no s'ha d'inferir a posteriori del rol actual.

Status: ready_to_commit
