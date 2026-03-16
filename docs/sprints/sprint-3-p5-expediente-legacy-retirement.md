# Sprint 3 P5 - Pla de retirada progressiva de legacy en `direccion/expediente`

## Estat actual

El panell principal de Direcció d'expedients ja és:

- `/direccion/expediente`

I la ruta antiga de prova queda com a redirecció de compatibilitat:

- `/direccion/expediente-livewire` -> `/direccion/expediente`

El panell nou ja cobrix el nucli del flux de Direcció, però encara reutilitza diverses peces del mòdul legacy. Per tant, **encara no convé retirar tot el legacy** sense desacoblar abans les dependències que queden.

## Peces implicades

- `routes/direccion.php`
  - `GET /direccion/expediente` -> vista `resources/views/expediente/livewire-panel.blade.php`
  - `GET /direccion/expediente-livewire` -> redirecció de compatibilitat
  - component `app/Livewire/ExpedienteDireccionPanel.php`

### Controller legacy encara reutilitzat

- `app/Http/Controllers/ExpedienteController.php`

## Què ja substituïx el panell Livewire

El panell nou de `ExpedienteDireccionPanel` ja resol:

- llistat d'expedients de Direcció
- filtre textual per:
  - alumne
  - professor
  - tipus
  - mòdul
- filtre per estat
- paginació
- autoritzar expedient pendent
- tornar expedient autoritzat a pendent
- rebutjar expedient pendent amb motiu
- mostrar detall en modal
- mostrar comptadors en botons globals
- mostrar accés a document/PDF des del modal quan toca
- ser la ruta principal de Direcció

## Què encara depén del legacy

### 1. Bulk autoritzar expedients pendents

Ja desacoblat del controller legacy:

- `routes/direccion.php`
  - `GET /direccion/expediente/autorizar`
- `app/Http/Controllers/Direccion/Expediente/AuthorizeController.php`

### 2. Imprimir expedients autoritzats

Ja desacoblat del controller legacy:

- `routes/direccion.php`
  - `GET /direccion/expediente/pdf`
- `app/Http/Controllers/Direccion/Expediente/PrintController.php`

### 3. Gestor documental

Ja desacoblat del flux legacy de professorat:

- `routes/direccion.php`
  - `GET /direccion/expediente/{expediente}/gestor`
- `app/Http/Controllers/Direccion/Expediente/GestorController.php`

### 4. PDF individual i show complet

El modal del pilot ja no reutilitza el PDF individual del legacy:

- `routes/direccion.php`
  - `GET /direccion/expediente/{expediente}/pdf`
- `app/Http/Controllers/Direccion/Expediente/PdfController.php`

La vista completa `show` ja no és necessària per a Direcció.

En professorat continua existint:

- `routes/profesor.php`
  - `GET /expediente/{expediente}/show`
- `app/Http/Controllers/ExpedienteController.php::show()`

### 5. Formulari, edició i esborrat

El pilot actual encara **no** substituïx:

- crear expedient
- editar expedient
- esborrar expedient

Per tant, el mòdul legacy continua sent l'únic camí complet per a estes operacions.

## Peces legacy que no s'han de tocar encara

No convé eliminar encara:

- `ExpedienteController::show()`
- `ExpedienteController::store()`
- `ExpedienteController::update()`
- `ExpedienteController::destroy()`
- rutes legacy associades

Motiu:

- continuen tenint rutes actives
- el panell nou encara depén del mòdul legacy per al CRUD de professorat
- algunes cobrixen casos que el pilot encara no ha reemplaçat

## Peces candidates a bridge

En `ExpedienteController` hi ha una barreja de:

- CRUD clàssic
- show complet
- CRUD i inicialització del flux de professorat

El pas correcte no és esborrar-lo ara, sinó reduir-lo a **bridge temporal**.

## Ordre recomanat de retirada

### Fase 1. Consolidar el pilot de Direcció

Objectiu:

- validar que el llistat i les accions bàsiques cobrixen el flux real de Direcció

Accions:

- prova manual del panell nou
- completar, si cal, el que falte del flux real

### Fase 2. Desacoblar accions globals

Objectiu:

- que el pilot nou deixe de dependre del legacy per a les operacions massives

Accions:

- traure a servici:
  - bulk autoritzar
  - impressió col·lectiva
- substituir en Livewire els enllaços directes al controller legacy

### Fase 3. Decidir abast del detall

Objectiu:

- decidir si el modal actual és suficient o si Direcció necessita més informació/accions

Accions:

- si el modal és suficient, el `show` complet pot quedar com a flux residual temporal
- si no és suficient, ampliar el panell nou només amb el que Direcció utilitze de veritat

### Fase 4. Formulari i operacions CRUD

Objectiu:

- evitar que Direcció haja de tornar al legacy per a crear/editar/esborrar

Accions:

- decidir si el formulari va a Livewire
- o si es manté un bridge temporal cap al controller legacy

### Fase 5. Retirada visible del legacy

Només quan el pilot cobrisca el flux principal:

- amagar l'accés a `/direccion/expediente`
- deixar el legacy només per compatibilitat temporal
- simplificar `ExpedienteController`

## Criteri de retirada segura

Una peça legacy d'`expediente` només s'hauria d'eliminar si es complixen les tres:

1. el panell nou ja no la crida
2. el flux equivalent està cobert funcionalment
3. no queda cap altra ruta rellevant depenent d'eixa peça

## Recomanació immediata

El següent treball amb millor retorn és:

1. decidir després si Direcció necessita CRUD complet dins del pilot nou
2. revisar si queda algun accés visible al panell antic de Direcció
3. simplificar `ExpedienteController` una vegada Direcció deixe d'usar-ne més peces

## Decisió pràctica

No convé llevar el legacy d'`expediente` ara.

Sí convé:

- identificar-lo
- desacoblar primer les accions globals
- revisar la coherència de les rutes encara compartides

En este mòdul, el primer objectiu bo és **deixar de dependre del controller legacy per a autoritzar i imprimir en bloc**, i només després valorar la migració del formulari.
