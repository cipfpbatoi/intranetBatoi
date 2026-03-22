# Sprint 21 - Separació funcional entre colaboracion i FCT

Issue remot:
- `#123` https://github.com/cipfpbatoi/intranetBatoi/issues/123

## Objectiu

Separar clarament el domini de `colaboracion` del domini de `FCT` perquè:

- `colaboracion` prepare
- `FCT` execute

## Problema actual

Ara mateix la frontera està borrosa:

- `colaboracion` governa estat de contacte, instructors, reserves i pont cap a FCT
- `FCT` continua depenent fortament de `Colaboracion`, `Centro` i `Empresa`
- hi ha fluxos web, API i JS que travessen els dos dominis sense una frontera clara

## Principi de disseny

- `colaboracion` representa la relació centre-cicle i la seua preparació operativa
- `FCT` representa la pràctica real amb alumnat, hores, seguiment i avaluació

## Tall A. Contracte de frontera

- definir quines operacions són exclusives de `colaboracion`
- definir quines operacions són exclusives de `FCT`
- marcar casos mixts que necessiten punt de pas explícit

## Tall B. Punts de dependència actuals

- revisar dependències de `FCT` respecte a:
  - `Colaboracion`
  - `Centro`
  - `Empresa`
- revisar punts on `colaboracion` encara governa massa negoci de pràctica real

## Tall C. Extracció operativa

- moure a `FCT` el que ja siga pràctica real:
  - seguiment real
  - hores
  - alumnat assignat
  - avaluació
- mantindre en `colaboracion`:
  - estat de contacte
  - instructors
  - disponibilitat
  - reserves prèvies

## Tall D. Impacte tècnic

- revisar:
  - controllers
  - serveis
  - API
  - vistes Blade
  - JS legacy
- prioritzar punts on la vista encara pressuposa cadenes profundes de relacions

## Resultat esperat

- frontera clara entre preparació i execució
- menys acoblament entre panells de col·laboració i fluxos FCT
- millor base per a casos futurs:
  - reserves
  - multi-centre
  - hores parcials

## Resultat executat

El tall funcional del sprint ha quedat completat en els punts principals de frontera.

- `Colaboracion` governa explícitament la capacitat i les preassignacions provisionals
- `Fct` concentra el context operatiu cap a `Centro`, `Empresa` i `Ciclo`
- el panell de `colaboracion` separa millor accions de preparació i accions pont
- s'han llevat fluxos morts de creació d'FCT i de `Dual`
- les operacions de seguiment real ja no pengen d'`API/ColaboracionController`
- bona part de recursos de lectura, SAO i PDFs d'FCT ja passen per helpers de context d'`Fct`

## Canvis principals

### Preparació en `Colaboracion`

- s'han afegit helpers de capacitat i reserves provisionals en [`app/Entities/Colaboracion.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Colaboracion.php)
- [`app/Application/Colaboracion/ColaboracionPreasignacionService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Colaboracion/ColaboracionPreasignacionService.php) reutilitza ara eixe contracte
- [`resources/views/intranet/partials/profile/partials/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/partials/colaboracion.blade.php) ha deixat de calcular la capacitat a mà

### Context operatiu en `Fct`

- [`app/Entities/Fct.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Fct.php) exposa `relatedColaboracion()`, `relatedCenter()`, `relatedCompany()`, `relatedCycle()`, `cycleTutors()` i `hasOperationalContext()`
- estos helpers s'han passat a usar en models, recursos, repositoris, serveis i vistes

### API i fluxos morts

- `telefonico` s'ha mogut de [`app/Http/Controllers/API/ColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ColaboracionController.php) a [`app/Http/Controllers/API/FctController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/FctController.php)
- s'han eliminat els endpoints `colaboracion/instructores`
- s'han eliminat fluxos morts de creació FCT i del mòdul `Dual`
- s'han eliminat:
  - [`public/js/Dual/create.js`](/Users/igomis/Code/intranetBatoi/public/js/Dual/create.js)
  - [`public/js/Fct/create.js`](/Users/igomis/Code/intranetBatoi/public/js/Fct/create.js)
  - [`resources/views/intranet/partials/modal/afegirFct.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/modal/afegirFct.blade.php)
  - el wrapper `nouFctAlumno()` en [`app/Http/Controllers/FctController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/FctController.php)

### Lectures i documentació

- s'ha reduït navegació profunda en:
  - [`app/Http/Resources/AlumnoFctControlResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/AlumnoFctControlResource.php)
  - [`app/Http/Resources/SelectFctResource.php`](/Users/igomis/Code/intranetBatoi/app/Http/Resources/SelectFctResource.php)
  - [`app/Entities/AlumnoFct.php`](/Users/igomis/Code/intranetBatoi/app/Entities/AlumnoFct.php)
  - [`app/Entities/Grupo.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Grupo.php)
  - [`app/Entities/Signatura.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Signatura.php)
  - [`app/Entities/FctDay.php`](/Users/igomis/Code/intranetBatoi/app/Entities/FctDay.php)
  - [`app/Sao/*`](/Users/igomis/Code/intranetBatoi/app/Sao)
  - diversos recursos i PDFs d'FCT

## Fora d'abast conscient

No s'ha forçat la mateixa conversió en peces on el model principal continua sent `Colaboracion`, no `Fct`. En concret:

- [`resources/views/pdf/fct/seguimentInstructor.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/pdf/fct/seguimentInstructor.blade.php)
- [`resources/views/pdf/fct/certificatColaborador.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/pdf/fct/certificatColaborador.blade.php)

En estos casos, el dubte ja no és de cadena profunda sinó de responsabilitat de domini: si continuen sent documents de col·laboració o si han de passar a ser documents d'execució FCT.

## Validació

Durant el sprint s'han anat afegint i executant proves de regressió sobre:

- capacitat i preassignacions de `Colaboracion`
- context operatiu de `Fct`
- recursos de lectura d'FCT
- mètriques de `Grupo`
- accessors d'`AlumnoFct`, `Signatura` i `FctDay`
- repositori d'FCT
- `InstructorWorkflowService`
- endpoints API de `FCT`

Bateries representatives passades en l'última part del sprint:

- `php artisan test tests/Feature/ApiColaboracionControllerFeatureTest.php tests/Feature/ApiFctControllerFeatureTest.php`
- `php artisan test tests/Unit/Entities/FctContextTest.php tests/Unit/Entities/AlumnoFctTest.php tests/Unit/Entities/ColaboracionTest.php tests/Unit/Entities/GrupoTest.php tests/Unit/Entities/SignaturaTest.php tests/Unit/Entities/FctDayTest.php`
- `php artisan test tests/Unit/Application/Colaboracion/ColaboracionPreasignacionServiceTest.php tests/Unit/Application/Instructor/InstructorWorkflowServiceTest.php`
- `php artisan test tests/Unit/Http/Resources/AlumnoFctControlResourceTest.php tests/Unit/Http/Resources/SelectFctResourceTest.php tests/Unit/FctRepositoryTest.php`

## Conclusió

El sprint deixa una frontera molt més clara:

- `Colaboracion` prepara
- `FCT` executa

I, sobretot, deixa el codi base en millor posició per afrontar el següent tall gran sense continuar acumulant dependència implícita entre els dos dominis.
