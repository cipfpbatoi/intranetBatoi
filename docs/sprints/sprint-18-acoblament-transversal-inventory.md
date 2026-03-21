# Sprint 18 - Inventari inicial d'acoblament transversal

Issue remot:
- `#117` https://github.com/cipfpbatoi/intranetBatoi/issues/117

Document de treball:
- [`docs/sprints/sprint-18-acoblament-transversal-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-18-acoblament-transversal-plan.md)

## Tall A

Este tall deixa un mapa inicial dels tres dominis prioritaris:

- `FCT`
- `colaboraciones`
- `documentos`

L'objectiu no és encara refactorar, sinó identificar on està realment repartida la responsabilitat.

## 1. FCT

### Rutes principals

- [`routes/profesor.php`](/Users/igomis/Code/intranetBatoi/routes/profesor.php)
  - `/fct`
  - `/fct/{id}/alFct`
  - `/fct/{id}/pdf`
  - `/fct/{id}/colaboradorPdf`
  - `/alumnofct/*`
  - `/avalFct`
  - múltiples accions d'acta, projecte, importació, SAO i qualitat
- [`routes/api.php`](/Users/igomis/Code/intranetBatoi/routes/api.php)
  - `alumnofct`
  - `fct/{id}/alFct`
  - `documentacionFCT/*`
  - `signatura*`

### Controllers i serveis

- [`FctController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/FctController.php)
- [`FctAlumnoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/FctAlumnoController.php)
- [`PanelFctController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelFctController.php)
- [`PanelFctAvalController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelFctAvalController.php)
- [`API/FctController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/FctController.php)
- [`API/AlumnoFctController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/AlumnoFctController.php)
- [`FctService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Fct/FctService.php)
- [`AlumnoFctService.php`](/Users/igomis/Code/intranetBatoi/app/Application/AlumnoFct/AlumnoFctService.php)
- [`AlumnoFctAvalService.php`](/Users/igomis/Code/intranetBatoi/app/Application/AlumnoFct/AlumnoFctAvalService.php)
- [`FctCertificateService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Fct/FctCertificateService.php)

### Vistes i JS

- [`resources/views/fct/show.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/fct/show.blade.php)
- [`resources/views/intranet/partials/profile/fct.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/fct.blade.php)
- [`resources/views/fct/partials/*.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/fct)
- [`public/js/Fct/show.js`](/Users/igomis/Code/intranetBatoi/public/js/Fct/show.js)
- [`public/js/Fct/grid.js`](/Users/igomis/Code/intranetBatoi/public/js/Fct/grid.js)
- [`public/js/Fctcap/index.js`](/Users/igomis/Code/intranetBatoi/public/js/Fctcap/index.js)
- [`public/js/Fctdual/index.js`](/Users/igomis/Code/intranetBatoi/public/js/Fctdual/index.js)

### Models i dependències fortes

- `Fct`
- `AlumnoFct`
- `Colaboracion`
- `Centro`
- `Empresa`
- `Instructor`
- `Documento`
- `Adjunto`
- `Grupo`

### Acoblaments detectats

- `fort`: [`resources/views/fct/show.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/fct/show.blade.php) pressuposa cadenes profundes com `Fct -> Colaboracion -> Centro -> Empresa`.
- `fort`: [`PanelFctAvalController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelFctAvalController.php) barreja avaluació, estadística, acta, qualitat, SAO i gestió documental.
- `fràgil`: hi ha fluxos duplicats web/API per a alumnat FCT i seguiment (`/fct/{id}/alFct` apareix en web i API).
- `lateral`: FCT depén de `documentacionFCT`, `signatura`, `projecte`, `qualitat` i `colaboraciones` com si foren el mateix subdomini.
- `legacy`: molt JS específic de panell continua governant parts del domini (`Fct/show.js`, `Fct/grid.js`, `Fctcap/index.js`).

## 2. Colaboraciones

### Rutes principals

- [`routes/profesor.php`](/Users/igomis/Code/intranetBatoi/routes/profesor.php)
  - `/colaboracion`
  - `/misColaboraciones`
  - `/liveColaboraciones`
  - `/colaboracion/{id}/show`
  - `/colaboracion/{id}/edit`
  - `/colaboracion/preasignacion/*`
- [`routes/api.php`](/Users/igomis/Code/intranetBatoi/routes/api.php)
  - `colaboracion`
  - `colaboracion/instructores/{id}`
  - `colaboracion/{id}/resolve`
  - `colaboracion/{id}/refuse`
  - `colaboracion/{id}/unauthorize`
  - `colaboracion/{id}/switch`
  - `colaboracion/{id}/telefonico`
  - `colaboracion/{id}/book`

### Controllers i serveis

- [`ColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/ColaboracionController.php)
- [`PanelColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelColaboracionController.php)
- [`API/ColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ColaboracionController.php)
- [`ColaboracionService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Colaboracion/ColaboracionService.php)
- [`ColaboracionQueryService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Colaboracion/ColaboracionQueryService.php)
- [`ColaboracionPreasignacionService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Colaboracion/ColaboracionPreasignacionService.php)

### Vistes i JS

- [`resources/views/intranet/partials/profile/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/colaboracion.blade.php)
- [`resources/views/intranet/partials/profile/partials/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/partials/colaboracion.blade.php)
- [`resources/views/intranet/partials/colaboracion/departamento.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/colaboracion/departamento.blade.php)
- [`resources/views/colaboracion/show.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/colaboracion/show.blade.php)
- [`public/js/Colaboracion/grid.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/grid.js)
- [`public/js/Colaboracion/modal.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/modal.js)

### Models i dependències fortes

- `Colaboracion`
- `Centro`
- `Empresa`
- `Ciclo`
- `Instructor`
- `Activity`
- `Fct`
- `Alumno`
- `ColaboracionPreasignacion`

### Acoblaments detectats

- `fort`: el domini governa alhora estat de contacte, reserves d'alumnat, instructors i pont cap a FCT.
- `fort`: [`public/js/Colaboracion/grid.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/grid.js) concentra DataTables, color, estats, drag/drop, reserves, telèfon i accions d'agenda.
- `fràgil`: el mateix concepte es veu des de `empresa`, `departament`, `misColaboraciones` i `FCT create`, cadascun amb UI diferent.
- `lateral`: `colaboracion` és prerequisit funcional de moltes FCT, però també es tracta com a CRM lleuger de contactes.
- `legacy`: conviuen pantalles antigues tipus panell/modal amb serveis nous d'aplicació.

## 3. Documentos

### Rutes principals

- [`routes/todos.php`](/Users/igomis/Code/intranetBatoi/routes/todos.php)
  - `/documento/*`
  - `/documento/{grupo}/grupo`
  - `/documento/{grupo}/acta`
  - `/proyecto`
- [`routes/profesor.php`](/Users/igomis/Code/intranetBatoi/routes/profesor.php)
  - `/documento`
  - `/fct/{fct}/proyecto`
  - `/fct/upload`
  - `/qualitat/documento`
- [`routes/direccion.php`](/Users/igomis/Code/intranetBatoi/routes/direccion.php)
  - `/documento`
- [`routes/api.php`](/Users/igomis/Code/intranetBatoi/routes/api.php)
  - `documento`
  - `documentacionFCT/*`

### Controllers i serveis

- [`DocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/DocumentoController.php)
- [`PanelDocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelDocumentoController.php)
- [`PanelDocAgrupadosController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelDocAgrupadosController.php)
- [`PanelProyectoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelProyectoController.php)
- [`PanelProjecteController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelProjecteController.php)
- [`API/DocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/DocumentoController.php)
- [`API/DocumentacionFCTController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/DocumentacionFCTController.php)
- [`DocumentoFormService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Documento/DocumentoFormService.php)
- [`DocumentoLifecycleService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Documento/DocumentoLifecycleService.php)
- [`TipoDocumentoService.php`](/Users/igomis/Code/intranetBatoi/app/Services/Document/TipoDocumentoService.php)
- `CreateOrUpdateDocumentAction`

### Vistes i JS

- [`resources/views/intranet/partials/profile/documento.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/documento.blade.php)
- [`resources/views/livewire/documento-table.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/livewire/documento-table.blade.php)
- [`resources/views/documento/livewire.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/documento/livewire.blade.php)
- múltiples vistes PDF i email sota [`resources/views/pdf`](/Users/igomis/Code/intranetBatoi/resources/views/pdf) i [`resources/views/email`](/Users/igomis/Code/intranetBatoi/resources/views/email)

### Models i dependències fortes

- `Documento`
- `Adjunto`
- `AlumnoFct`
- `Grupo`
- `Profesor`
- `TipoDocumentoService`

### Acoblaments detectats

- `fort`: [`DocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/DocumentoController.php) governa documents genèrics, projecte, qualitat i notes FCT.
- `fort`: el mateix model `Documento` suporta documents generals, FCT, projecte i qualitat, amb filtrat per `rol`, `tipoDocumento`, `curso`, `propietario`.
- `fràgil`: hi ha tres UIs sobre el mateix domini: panell legacy, tickets agrupats i Livewire.
- `lateral`: molt de comportament documental viu fora del domini `Documento`, en PDF, mails, FCT i signatures.
- `legacy`: [`PanelDocAgrupadosController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelDocAgrupadosController.php) continua filtrant directament sobre el model i `TipoDocumentoService`, mentre la taula nova Livewire té un altre camí.

## Punts de fricció compartits

- `fort`: relacions profundes assumides en vistes (`Fct -> Colaboracion -> Centro -> Empresa`).
- `fort`: convivència de web clàssic, API interna i JS legacy sobre els mateixos fluxos.
- `fràgil`: duplicació de responsabilitat entre panells, modals i endpoints API.
- `legacy`: massa JS per domini continua sent orquestració de negoci i no només presentació.

## Fronteres que ara no estan clares

- `FCT` versus `colaboracion`:
  - on acaba la relació amb centre
  - on comença la pràctica real
- `documentos` versus `FCT`:
  - quins documents són realment subdomini FCT
  - quins haurien de ser només adjunts genèrics
- `documentos` versus `projecte`:
  - hi ha fluxos específics, però continuen governats des de `DocumentoController`

## Següent pas recomanat

Tall B:

- classificar estes dependències per severitat
- marcar quines es poden considerar:
  - `fort`
  - `fràgil`
  - `lateral`
  - `legacy`
- i deixar una proposta de frontera per domini

## Tall B

## Classificació per severitat

### FCT

- `fort`
  - [`PanelFctAvalController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelFctAvalController.php)
    centralitza massa subfluxos: avaluació, actes, qualitat, SAO, projecte, estadística i documents.
  - [`resources/views/fct/show.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/fct/show.blade.php)
    depén directament de `Colaboracion`, `Centro`, `Empresa`, `Instructor` i `Cotutor`.
- `fràgil`
  - doble via web/API per a seguiment i alumnat FCT (`/fct/{id}/alFct`, `alumnofct`, imports, emails).
  - `FctAlumnoController` continua fent de controlador de gestió, impressió, mail i documents.
- `lateral`
  - dependència contínua de `Documento`, `Adjunto`, `Signatura`, `Projecte`, `Qualitat`.
- `legacy`
  - JS específic repartit en `Fct/show.js`, `Fct/grid.js`, `Fctcap/index.js`, `Fctdual/index.js`.

### Colaboraciones

- `fort`
  - [`public/js/Colaboracion/grid.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/grid.js)
    és el punt d'orquestració real de massa operacions.
  - el domini suporta simultàniament:
    - agenda de contacte
    - estat comercial
    - instructors
    - reserves
    - pont a creació de FCT
- `fràgil`
  - la mateixa informació es representa des de departament, tutor, empresa i FCT create.
  - hi ha coexistència de vistes compactes, modals, targetes i grid antic.
- `lateral`
  - lligam fort amb `Activity`, `Fct`, `Alumno` i `Centro`.
- `legacy`
  - gran part del comportament depén encara de JS procedural i accions GET mutadores.

### Documentos

- `fort`
  - [`DocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/DocumentoController.php)
    governa massa variants de document.
  - el model `Documento` està assumint subdominis molt diferents sota un mateix contracte.
- `fràgil`
  - tres interfícies distintes per al mateix domini:
    - panell legacy
    - perfil/tickets
    - Livewire
  - filtres i criteris d'accés repartits entre controllers, serveis i Blade.
- `lateral`
  - el domini documental està escampat per PDFs, emails, FCT, projecte i signatures.
- `legacy`
  - controllers com [`PanelDocAgrupadosController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelDocAgrupadosController.php)
    continuen operant directament sobre model + `TipoDocumentoService`.

## Fronteres recomanades

### FCT

Hauria de quedar dins:

- pràctica real
- alumnat assignat
- instructors i cotutoria
- seguiment real
- calendari/hores
- avaluació final

Hauria d'eixir fora:

- relació comercial inicial amb empresa
- captació o classificació de centres
- gestió genèrica de documents
- signatures com a infraestructura transversal

Capa que hauria de governar:

- servei d'aplicació `Fct`
- no la vista ni el JS del panell

### Colaboraciones

Hauria de quedar dins:

- relació centre-cicle
- disponibilitat de llocs
- estat de contacte amb el centre
- instructors i dades operatives del centre
- reserves prèvies a FCT

Hauria d'eixir fora:

- seguiment FCT real
- documentació FCT
- decisions d'avaluació
- calendari d'alumnat

Capa que hauria de governar:

- serveis `ColaboracionService` i `ColaboracionQueryService`
- no `grid.js` com a orquestrador principal

### Documentos

Hauria de quedar dins:

- metadades comunes
- adjunt i fitxer
- permisos i visibilitat
- llistat, filtrat i cicle de vida bàsic

Hauria d'eixir fora:

- lògica específica de projecte
- lògica específica de qualitat FCT
- efectes sobre notes o estat acadèmic
- generació/negoci de signatures

Capa que hauria de governar:

- serveis de document i accions específiques per subdomini
- no un únic `DocumentoController` per a tots els casos

## Priorització de refactors futurs

1. Separar millor `colaboracion` de `FCT`.
   Ara és el punt on més es nota la frontera borrosa.
2. Tallar responsabilitats de [`DocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/DocumentoController.php).
   És el nucli més transversal del tercer domini.
3. Reduir el pes orquestrador del JS legacy.
   Sobretot en [`public/js/Colaboracion/grid.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/grid.js) i el bloc FCT.
4. Replantejar `PanelFctAvalController`.
   És probablement el controlador amb més acoblament funcional.
