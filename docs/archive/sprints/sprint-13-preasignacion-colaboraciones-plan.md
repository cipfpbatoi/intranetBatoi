# Sprint 13 - Preassignació d'alumnat a col·laboracions

## Objectiu

Permetre reservar o preassignar un o més alumnes a una col·laboració abans de crear la FCT definitiva, sense embrutar la taula `fcts` ni barrejar reserves amb pràctiques ja formalitzades.

## Diagnòstic actual

- [`Colaboracion`](/Users/igomis/Code/intranetBatoi/app/Entities/Colaboracion.php) representa la relació `centre + cicle`.
- [`FctController@store`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/FctController.php) crea o reutilitza una `Fct` i després hi enganxa alumnat.
- [`FctService`](/Users/igomis/Code/intranetBatoi/app/Application/Fct/FctService.php) treballa sempre sobre FCT real, no sobre un estat previ.
- [`PanelColaboracionController`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelColaboracionController.php) i les vistes de `misColaboraciones` ja són el punt natural per a mostrar reserves, perquè és on el tutor gestiona l’estat de cada col·laboració.
- [`ColaboracionAlumnoController`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/ColaboracionAlumnoController.php) existeix, però és un panell legacy de consulta per a alumnat; no resol la necessitat de preassignació per al tutor.

## Decisió d'arquitectura

La preassignació ha de ser una capa nova i explícita.

No farem:
- esborranys en `fcts`
- reutilitzar `AlumnoFct` com a reserva
- guardar alumnat provisional dins de `colaboraciones`

Sí farem:
- una taula nova de reserves o preassignacions
- un model propi
- un flux separat de la FCT final

## Model proposat

Nom de treball recomanat:
- `ColaboracionPreasignacion`

Taula proposada:
- `colaboracion_preasignaciones`

Camps mínims:
- `id`
- `idColaboracion`
- `idAlumno`
- `idProfesor`
- `estado`
- `observaciones`
- `created_at`
- `updated_at`

## Estats inicials

Per al primer tall, millor pocs estats i clars:
- `proposta`
- `reservada`
- `descartada`
- `convertida`

`confirmada` es podria afegir més avant si realment apareix una diferència funcional entre “reservada” i “ja acordada amb empresa”.

## Regles funcionals

- una col·laboració pot tindre zero, una o més preassignacions
- no es pot duplicar el mateix alumne en la mateixa col·laboració
- cal advertir si el mateix alumne ja està reservat en una altra col·laboració del mateix cicle
- el sistema ha de respectar `puestos` com a límit tou
  - primer tall: avisar i bloquejar si se supera
- una preassignació `convertida` ja no s’edita com a reserva normal
- les col·laboracions han d'estar com a col·labora per poder ser seleccionades 

## Flux recomanat

### Tall A - Base de dades i domini

- crear migració `colaboracion_preasignaciones`
- crear model [`ColaboracionPreasignacion`](/Users/igomis/Code/intranetBatoi/app/Entities)
- afegir relació en [`Colaboracion`](/Users/igomis/Code/intranetBatoi/app/Entities/Colaboracion.php)
- afegir relació mínima des d’`Alumno` o entitat equivalent

### Tall B - Servei d’aplicació

- crear servei nou, per exemple:
  - `app/Application/Colaboracion/ColaboracionPreasignacionService.php`
- responsabilitats:
  - crear reserva
  - llistar reserves d’una col·laboració
  - descartar reserva
  - convertir reserva a FCT
  - validar conflictes per alumne/cicle

### Tall C - UI tutor en col·laboracions

- afegir botó `Preassignar alumnat` des de `misColaboraciones`
- modal simple amb:
  - selector d’alumne
  - observacions
  - estat inicial
- en el detall de la col·laboració, mostrar:
  - reserves actuals
  - estat
  - qui les ha fet

Estat actual del Tall C:
- el panell ja hidrata preassignacions i opcions d’alumnat des de backend
- `misColaboraciones` ja mostra el llistat de preassignacions per col·laboració
- el tutor ja pot crear i descartar preassignacions des del panell
- pendent encara un tall curt de poliment/validació funcional sobre la UI

### Tall D - Conversió a FCT

En standBy: de moment no cal 

- afegir acció `Convertir a FCT`
- reutilitzar el flux d’`FctController`/`FctService`
- quan es convertisca:
  - crear o reutilitzar la FCT segons la signatura actual
  - vincular alumnat
  - marcar la preassignació com a `convertida`

## Fora d’abast inicial

- assignació múltiple massiva
- Livewire nou per a tot el mòdul
- sincronització automàtica amb `puestos` lliures/ocupats en temps real
- dashboard d’estadístiques de reserves

## Riscos

- col·lisió conceptual amb el flux existent de creació de FCT
- ús ambigu del camp `tutor` de `Colaboracion`
- possibles duplicitats si un alumne ja té FCT real al mateix cicle

## Primer tall recomanat

Començar per `Tall A + Tall B`:
- domini
- migració
- model
- servei
- proves unitàries de regles

Després, quan això estiga estable, entrar a la UI del tutor.
