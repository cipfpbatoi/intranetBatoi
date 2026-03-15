# Sprint 3 P3 - Pla de retirada progressiva de legacy en `direccion/falta`

## Estat actual

El panell principal de Direcció de faltes ja és:

- `/direccion/falta`

I la ruta antiga de prova queda com a redirecció de compatibilitat:

- `/direccion/falta-livewire` -> `/direccion/falta`

El panell nou ja cobrix el llistat principal i les accions habituals de Direcció, però encara reutilitza parts del flux legacy. Açò implica que **encara no convé retirar tot el legacy** sense abans desacoblar les dependències que queden.

## Peces implicades

- `routes/direccion.php`
  - `GET /direccion/falta` -> vista `resources/views/falta/livewire-panel.blade.php`
  - `GET /direccion/falta-livewire` -> redirecció de compatibilitat
  - component `app/Livewire/FaltaDireccionPanel.php`

### Controller legacy encara reutilitzat

- `app/Http/Controllers/FaltaController.php`

## Què ja substituïx el panell Livewire

El panell nou de `FaltaDireccionPanel` ja resol:

- llistat de faltes de Direcció
- filtre per professor escrivint
- filtre per estat
- acceptar una falta pendent
- rebutjar una falta pendent amb motiu
- donar una baixa per alta
- esborrar faltes no autoritzades
- crear una falta des de modal
- editar una falta des de modal

## Què encara depén del legacy

### 1. Formulari de crear/editar

El modal del panell nou encara reutilitza el formulari antic basat en `FormBuilder` i l'esquema de CRUD.

Peces implicades:

- `resources/views/livewire/falta-direccion-panel.blade.php`
- `app/Presentation/Crud/FaltaCrudSchema.php`
- `app/Http/Controllers/FaltaController.php::store()`
- `app/Http/Controllers/FaltaController.php::update()`

### 2. Document adjunt

El panell nou ja usa un bridge específic de Direcció per a obrir el document de la falta.

Peces implicades:

- `routes/direccion.php`
  - `GET /direccion/falta/{falta}/document`
- `app/Http/Controllers/Direccion/Falta/DocumentController.php`

### 3. Show legacy

La ruta de detall continua existint, però ja entra per un bridge específic de Direcció:

- `routes/direccion.php`
  - `GET /direccion/falta/{falta}/show`
- `app/Http/Controllers/Direccion/Falta/ShowController.php`

Ara mateix el pilot nou no necessita redirigir ací per al flux principal, però la ruta continua viva.

### 4. Flux de store/update encara lligat al controller

Encara que el panell és Livewire, el guardat no és un formulari Livewire pur. Les operacions continuen entrant per:

- `POST /direccion/falta`
- `PUT /direccion/falta/{falta}/edit`

Gestionades per:

- `FaltaController::store()`
- `FaltaController::update()`

## Què el pilot nou ja no necessita del legacy per al flux principal

El panell nou ja no depén del controller legacy per a:

- acceptar
- rebutjar
- alta
- esborrar

Estes accions ja es resolen en `FaltaDireccionPanel` amb:

- `AutorizacionStateService`
- `FaltaService`
- accés directe a model en el cas d'esborrar

## Peces legacy que no s'han de tocar encara

No convé eliminar encara:

- `FaltaController::store()`
- `FaltaController::update()`

Motiu:

- continuen tenint rutes actives
- el modal del pilot encara les reutilitza
- el formulari nou encara no és independent

## Peces candidates a bridge

En `FaltaController` hi ha encara responsabilitats de:

- formulari CRUD modal
- persistència
- compatibilitat amb Direcció i Professorat
- show/document de suport

En este mòdul el controller ja pot començar a considerar-se un **bridge temporal** per al pilot Livewire, sobretot en:

- `store`
- `update`

## Ordre recomanat de retirada

### Fase 1. Mantindre convivència explícita

Objectiu:

- que el pilot continue funcionant sense perdre el flux històric

Accions:

- documentar dependències actuals
- no eliminar rutes ni vistes encara usades

### Fase 2. Desacoblar el formulari

Objectiu:

- que crear i editar una falta no depenguen de `FormBuilder` ni del CRUD modal antic

Accions:

- portar el formulari a Livewire real
- reutilitzar validació de `FaltaRequest` o extraure regles compartides
- evitar `edit-data` com a endpoint auxiliar

### Fase 3. Traure del controller les utilitats de suport

Objectiu:

- deixar `FaltaController` només amb les parts que encara siguen necessàries fora del panell nou

Accions:

- revisar si `show` i `document` poden continuar com a bridges separats
- revisar si Direcció continua necessitant estes rutes o si només són útils per al flux legacy/professorat

### Fase 4. Retirada visible del legacy

Només quan el pilot cobrisca el 100% del flux de Direcció:

- amagar l'accés a `/direccion/falta`
- retirar les dependències del panell nou cap a `FaltaController`
- simplificar o segmentar el controller

## Criteri de retirada segura

Una peça legacy de `falta` només s'hauria d'eliminar si es complixen les tres:

1. el panell nou ja no la crida
2. el flux equivalent està cobert funcionalment
3. no queda cap altra ruta activa depenent d'eixa peça

## Comparació amb `comision`

`falta` està un poc més avançat que `comision` en desacoblament de les accions de Direcció, perquè:

- acceptar/rebutjar/alta/esborrar ja no passen pel controller per al flux principal del panell nou

Però està més lligat al legacy en una altra peça clau:

- el formulari de crear/editar

Per tant, en `falta` el primer objectiu no és bulk actions ni PDF, sinó **substituir el formulari legacy**.

## Recomanació immediata

El següent treball amb millor retorn en `falta` és:

1. convertir el modal de crear/editar en formulari Livewire real
2. ja no dependre de `editData`
3. revisar després si `show` i `document` continuen sent necessaris per a Direcció

## Decisió pràctica

No convé llevar el legacy de `falta` ara.

Sí convé:

- considerar `FaltaController` com a bridge temporal
- reduir dependència del formulari respecte al CRUD antic
- retirar després les rutes de suport una vegada el modal siga realment Livewire
