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
- guardar directament des del component sense passar per rutes de Direcció de `store/update`

## Què encara depén del legacy

### 1. Formulari de crear/editar

El modal del panell nou ja és un formulari Livewire real. No reutilitza `FormBuilder`.

Peces implicades:

- `resources/views/livewire/falta-direccion-panel.blade.php`
- `app/Livewire/FaltaDireccionPanel.php`
- `app/Application/Falta/FaltaService.php`
- `app/Http/Requests/FaltaRequest.php` com a referència de regles

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

### 4. Flux legacy de store/update fora del panell de Direcció

El panell de Direcció ja no entra per rutes pròpies de `store/update`. El que continua viu és el flux legacy/professorat:

- `POST /profesor/falta/create`
- `PUT /profesor/falta/{falta}/edit`

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

- continuen tenint rutes actives fora del panell de Direcció
- encara donen servei al flux legacy/professorat

## Peces candidates a bridge

En `FaltaController` hi ha encara responsabilitats de:

- persistència
- compatibilitat amb professorat

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

- completat en Direcció

### Fase 3. Traure del controller les utilitats de suport

Objectiu:

- deixar `FaltaController` només amb les parts que encara siguen necessàries fora del panell nou de Direcció

Accions:

- `show` i `document` ja estan desacoblats en bridges de Direcció
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
- crear/editar tampoc passen ja per rutes de Direcció del controller legacy

Però encara queda un bridge clar:

- `FaltaController::store()` i `update()` per al flux legacy/professorat

Per tant, en `falta` el primer objectiu no és bulk actions ni PDF, sinó **substituir el formulari legacy**.

## Recomanació immediata

El següent treball amb millor retorn en `falta` és:

1. segmentar millor `FaltaController` entre flux de professorat i flux comú
2. revisar si `store/update` legacy continuen sent només una via de professorat
3. simplificar la documentació i els bridges de Direcció que ja han quedat estabilitzats

## Decisió pràctica

No convé llevar el legacy de `falta` ara.

Sí convé:

- considerar `FaltaController` com a bridge temporal
- assumir que Direcció ja no depén del formulari legacy
- retirar després les rutes/controladors de suport que realment hagen quedat morts

Actualització:

- `PanelFaltaController` ja s'ha eliminat del camí de Direcció
