# Sprint 22 - Extracció de negoci específic de DocumentoController

Issue remot:
- `#124` https://github.com/cipfpbatoi/intranetBatoi/issues/124

## Objectiu

Reduir el caràcter transversal de [`DocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/DocumentoController.php) i deixar `Documento` com a infraestructura comuna.

## Problema actual

El controlador genèric de documents continua governant massa subfluxos:

- documents generals
- projecte
- qualitat FCT
- parts del flux FCT
- efectes col·laterals sobre altres dominis

## Principi de disseny

- `Documento` ha de quedar com a infraestructura comuna:
  - metadades
  - fitxer/adjunt
  - permisos
  - visibilitat
  - cicle de vida bàsic
- el negoci específic ha d'eixir a serveis o controllers propis

## Tall A. Inventari d'accions candidates

- identificar dins de `DocumentoController`:
  - projecte
  - qualitat
  - operacions vinculades a FCT
  - operacions que toquen notes o estat acadèmic

## Tall B. Tall de frontera

- definir què queda en el controlador genèric
- definir què ha d'eixir a:
  - projecte
  - qualitat FCT
  - documentació FCT

## Tall C. Extracció progressiva

- crear accions o controllers específics per subflux
- mantindre `DocumentoController` només per al contracte documental comú

## Tall D. Impacte en UI

- revisar coherència entre:
  - panell legacy
  - tickets/perfil
  - taula Livewire
- evitar que cada UI torne a encapsular negoci propi

## Resultat esperat

- `DocumentoController` més menut i menys transversal
- millor separació entre infraestructura documental i negoci específic
- millor base per a modularització i simplificació del frontend

## Resultat executat

- la qualitat documental d'FCT ha eixit a:
  - [`FctQualitatDocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/FctQualitatDocumentoController.php)
  - [`FctQualitatUploadService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Documento/FctQualitatUploadService.php)
- la documentació FCT ha deixat de resoldre's dins del controlador i ara usa:
  - [`FctDocumentOptionsService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Fct/FctDocumentOptionsService.php)
  - [`FctDocumentRenderService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Fct/FctDocumentRenderService.php)
- la documentació específica de projectes ha eixit a:
  - [`ProjecteDocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/ProjecteDocumentoController.php)
  - [`ProjecteDocumentService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Projecte/ProjecteDocumentService.php)
- el formulari documental de projecte FCT ja no penja de [`DocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/DocumentoController.php), sinó de [`FctProjecteDocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/FctProjecteDocumentoController.php)
- la persistència comuna de documents s'ha mogut a [`DocumentoPersistenceService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Documento/DocumentoPersistenceService.php)
- els `POST` específics de:
  - `proyecto.create`
  - `qualitat.create`
  ja no entren per [`DocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/DocumentoController.php), sinó pels seus controladors de domini
- el servei mort [`FctMailService.php`](/Users/igomis/Code/intranetBatoi/app/Services/Mail/FctMailService.php) s'ha eliminat

## Què queda en DocumentoController

- `store()`
- `edit()`
- `show()`
- `destroy()`
- `readFile()`

Amb això, el controlador genèric queda centrat en persistència i cicle de vida documental comú, i ja no orquestra fluxos específics de qualitat, correu FCT o documentació de projecte.

El matís que queda és que `DocumentoController@store()` continua sent l'entrada del CRUD genèric de documents, però sense la regla específica de `nota` ni rutes de domini acoblades.

## Validació

- `php artisan test tests/Unit/Application/Documento/DocumentoFormServiceTest.php`
- `php artisan test tests/Unit/Application/Documento/DocumentoPersistenceServiceTest.php`
- `php artisan test tests/Unit/Application/Documento/FctQualitatUploadServiceTest.php`
- `php artisan test tests/Unit/Application/Fct/FctDocumentRenderServiceTest.php`
- `php artisan test tests/Unit/Application/Projecte/ProjecteDocumentServiceTest.php`
- `php artisan test tests/Unit/Entities/AlumnoFctAvalTest.php`

Resultat actual:
- `10` proves passades en la bateria curta del sprint

## Tancament

- sprint resolt funcionalment
- integrat directament sobre la branca [`Laravel12`](/Users/igomis/Code/intranetBatoi)
- commits finals:
  - `20f0b13f` `[MOD] Extrau fluxos documentals i repara dropzone #124`
  - `d7fca356` `[FIX] Repara formularis de projecte i nota FCT #124`

## Incidències resoltes durant el tancament

- reparació del flux Dropzone d'FCT i qualitat:
  - càrrega de la llibreria
  - inicialització manual
  - resolució d'adjunts per identificador intern
  - recuperació de la compatibilitat del trait [`DropZone.php`](/Users/igomis/Code/intranetBatoi/app/Http/Traits/Core/DropZone.php)
- correcció dels formularis específics que havien quedat amb signatures incompatibles després de l'extracció:
  - [`FctProjecteDocumentoController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/FctProjecteDocumentoController.php)
  - [`PanelFctAvalController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelFctAvalController.php)
- correcció de l'edició de `calProyecto` perquè no valide camps generals d'[`AlumnoFct`](/Users/igomis/Code/intranetBatoi/app/Entities/AlumnoFct.php) ni depenga d'una policy inexistent
