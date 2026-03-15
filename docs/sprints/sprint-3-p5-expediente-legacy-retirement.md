# Sprint 3 P5 - Pla de retirada progressiva de legacy en `direccion/expediente`

## Estat actual

Convivixen dos camins per al panell de Direcció d'expedients:

- Legacy: `/direccion/expediente`
- Pilot Livewire: `/direccion/expediente-livewire`

El pilot nou ja cobrix el nucli del flux de Direcció, però encara reutilitza diverses peces del mòdul legacy. Per tant, **encara no convé retirar el legacy** sense desacoblar abans les dependències que queden.

## Peces implicades

### Ruta i entrada legacy

- `routes/direccion.php`
  - `GET /direccion/expediente` -> `PanelExpedienteController@index`

### Ruta i entrada nova

- `routes/direccion.php`
  - `GET /direccion/expediente-livewire` -> vista `resources/views/expediente/livewire-panel.blade.php`
  - component `app/Livewire/ExpedienteDireccionPanel.php`

### Controller legacy encara reutilitzat

- `app/Http/Controllers/ExpedienteController.php`

### Panell legacy encara operatiu

- `app/Http/Controllers/PanelExpedienteController.php`

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
- mostrar accés a document/PDF/vista completa des del modal quan toca

## Què encara depén del legacy

### 1. Bulk autoritzar expedients pendents

En la vista Livewire:

- `resources/views/livewire/expediente-direccion-panel.blade.php`
  - botó amb `href="/direccion/expediente/autorizar"`

En backend:

- `routes/direccion.php`
  - `GET /direccion/expediente/autorizar`
- `app/Http/Controllers/ExpedienteController.php::autorizar()`

### 2. Imprimir expedients autoritzats

En la vista Livewire:

- `resources/views/livewire/expediente-direccion-panel.blade.php`
  - botó amb `href="/direccion/expediente/pdf"`

En backend:

- `routes/direccion.php`
  - `GET /direccion/expediente/pdf`
- `app/Http/Controllers/ExpedienteController.php::imprimir()`

### 3. Gestor documental

El botó de document del modal usa una ruta clàssica:

- `routes/profesor.php`
  - `GET /expediente/{actividad}/gestor`
- `app/Http/Controllers/Core/IntranetController.php::gestor()`

Nota:

- la ruta està declarada amb el paràmetre `{actividad}`, però funcionalment apunta a expedients
- és un detall legacy que convé revisar abans de consolidar el mòdul

### 4. PDF individual i show complet

El modal del pilot reutilitza:

- `routes/profesor.php`
  - `GET /expediente/{expediente}/pdf`
  - `GET /expediente/{expediente}/show`
- `app/Http/Controllers/ExpedienteController.php::pdf()`
- `app/Http/Controllers/ExpedienteController.php::show()`

### 5. Formulari, edició i esborrat

El pilot actual encara **no** substituïx:

- crear expedient
- editar expedient
- esborrar expedient

Per tant, el mòdul legacy continua sent l'únic camí complet per a estes operacions.

## Peces legacy que no s'han de tocar encara

No convé eliminar encara:

- `ExpedienteController::autorizar()`
- `ExpedienteController::imprimir()`
- `ExpedienteController::pdf()`
- `ExpedienteController::show()`
- `ExpedienteController::store()`
- `ExpedienteController::update()`
- `ExpedienteController::destroy()`
- rutes legacy associades

Motiu:

- continuen tenint rutes actives
- el panell nou encara les reutilitza parcialment
- algunes cobrixen casos que el pilot encara no ha reemplaçat

## Peces candidates a bridge

En `ExpedienteController` hi ha una barreja de:

- CRUD clàssic
- autorització d'estats
- impressió col·lectiva
- PDF individual
- show complet
- integració amb gestor documental

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
- simplificar `PanelExpedienteController`
- simplificar `ExpedienteController`

## Criteri de retirada segura

Una peça legacy d'`expediente` només s'hauria d'eliminar si es complixen les tres:

1. el panell nou ja no la crida
2. el flux equivalent està cobert funcionalment
3. no queda cap altra ruta rellevant depenent d'eixa peça

## Recomanació immediata

El següent treball amb millor retorn és:

1. traure `autorizar()` i `imprimir()` del controller legacy cap a servici/bridge específic
2. revisar la ruta `expediente.gestor`, perquè la firma actual és confusa
3. decidir després si Direcció necessita CRUD complet dins del pilot nou

## Decisió pràctica

No convé llevar el legacy d'`expediente` ara.

Sí convé:

- identificar-lo
- desacoblar primer les accions globals
- revisar la coherència de les rutes encara compartides

En este mòdul, el primer objectiu bo és **deixar de dependre del controller legacy per a autoritzar i imprimir en bloc**, i només després valorar la migració del formulari.
