# Sprint 3 P2 - Pla de retirada progressiva de legacy en `direccion/comision`

## Estat actual

Convivixen dos camins per al panell de Direcció de comissions:

- Legacy: `/direccion/comision`
- Pilot Livewire: `/direccion/comision-livewire`

El pilot nou ja resol millor el llistat, els filtres, la paginació, el modal de detall i part de les accions, però encara reutilitza peces del flux legacy. Això implica que **encara no es pot eliminar el legacy** sense abans desacoblar dependències.

## Peces implicades

### Ruta i entrada legacy

- `routes/direccion.php`
  - `GET /direccion/comision` -> `PanelComisionController@index`

### Ruta i entrada nova

- `routes/direccion.php`
  - `GET /direccion/comision-livewire` -> vista `resources/views/comision/livewire-panel.blade.php`
  - component `app/Livewire/ComisionDireccionPanel.php`

### Controller legacy que encara continua viu

- `app/Http/Controllers/ComisionController.php`

### Vista legacy encara existent

- `resources/views/comision/detalle.blade.php`

## Què ja substituïx el panell Livewire

El panell nou de `ComisionDireccionPanel` ja cobrix:

- llistat de comissions de Direcció
- filtre per professor
- filtre per estat
- paginació
- autoritzar una comissió individual
- tornar una comissió autoritzada a pendent
- rebutjar una comissió pendent
- mostrar el detall en modal
- editar des de modal
- esborrar mentre no estiga cobrada
- selecció de pagaments pendents per professor

## Què encara depén del legacy

El panell Livewire de Direcció ja no depén del controller generalista de
`ComisionController` per a les seues accions principals. El que queda pendent
de retirada està més relacionat amb la convivència del mòdul legacy complet que
amb dependències tècniques directes del pilot.

### 1. Bulk autoritzar comissions pendents

Ja desacoblat del controller legacy:

- `app/Livewire/ComisionDireccionPanel.php::autoritzarPendents()`
- `app/Application/Comision/ComisionService.php::authorizeAllPending()`

El panell nou ja no usa:

- `GET /direccion/comision/autorizar`
- `app/Http/Controllers/ComisionController.php::autorizar()`

### 2. Imprimir comissions autoritzades

Ja desacoblat del controller legacy generalista:

- `app/Livewire/ComisionDireccionPanel.php::imprimirAutoritzades()`
- `GET /direccion/comision-livewire/pdf`
- `app/Http/Controllers/ComisionDireccionPrintController.php`

El panell nou ja no usa:

- `GET /direccion/comision/pdf`
- `app/Http/Controllers/ComisionController.php::printAutoritzats()`

### 3. Imprimir pagaments

Ja desacoblat del controller legacy generalista:

- `app/Livewire/ComisionDireccionPanel.php::imprimirPagamentsSeleccionats()`
- `GET /direccion/comision-livewire/paid`
- `app/Http/Controllers/ComisionDireccionPaymentPrintController.php`

El panell nou ja no usa:

- `GET /direccion/comision/paid`
- `app/Http/Controllers/ComisionController.php::payment()`

### 4. Actualització des del modal

Ja desacoblat del controller legacy i de la ruta d'update del pilot:

- `app/Livewire/ComisionDireccionPanel.php::guardarEdicio()`
- modal propi en `resources/views/livewire/comision-direccion-panel.blade.php`

El `FormBuilder` ja no participa en l'edició del panell de Direcció.

### 5. Gestor documental

Ja desacoblat del controller legacy generalista:

- `GET /direccion/comision/{comision}/gestor`
- `app/Http/Controllers/ComisionDireccionGestorController.php`

## Peces legacy que no s'han de tocar encara

Les següents no s'han d'eliminar mentre no es tanque completament el flux nou:

- `ComisionController::printAutoritzats()`
- `ComisionController::payment()`
- `ComisionController::autorizar()`

Motiu:

- encara tenen rutes actives
- encara poden ser usades pel flux legacy
- formen part d'un flux funcional validat per Direcció

## Peces candidates a bridge

El següent pas correcte no és esborrar el controller, sinó aprimar-lo.

En `ComisionController` hi ha una barreja de:

- lògica CRUD modal
- lògica d'autorització
- fluxos d'impressió
- gestió FCT
- notificacions/correus

La part de Direcció hauria d'anar quedant en serveis reutilitzables, deixant el controller com a façana prima temporal.

## Ordre recomanat de retirada

### Fase 1. Fer explícita la convivència

Objectiu:

- mantindre els dos panells operatius
- deixar clar què és pilot nou i què és compatibilitat

Accions:

- documentar dependències actuals
- marcar en comentaris i phpDoc els punts bridge

### Fase 2. Traure del controller les accions bulk de Direcció

Objectiu:

- que el panell Livewire deixe de dependre de rutes legacy per a operacions massives

Accions:

- extraure a servei:
  - bulk autoritzar
  - imprimir autoritzades
  - preparar pagaments
- fer que Livewire cride servicis o rutes específiques noves de Direcció

### Fase 3. Tancar l'edició i el document sense controller legacy generalista

Objectiu:

- deixar de dependre de `ModalController` i del patró antic per a l'edició des del panell nou

Accions:

- decidir si el formulari de comissió continua com a bridge temporal
- o si es passa a un formulari propi Livewire

### Fase 4. Retirada visible del legacy

Només quan el pilot cobrisca el 100% del flux de Direcció:

- amagar l'enllaç a `/direccion/comision`
- deixar la ruta legacy només per compatibilitat interna temporal
- eliminar vistes i JS antics no referenciats
- simplificar o segmentar `ComisionController`

## Criteri de retirada segura

Una peça legacy de `comision` només s'hauria d'eliminar si es complixen les tres:

1. no queda cap ruta del panell nou apuntant a ella
2. hi ha cobertura de prova o validació manual equivalent
3. el flux funcional ja està tancat en el panell nou

## Recomanació immediata

El següent treball amb millor retorn és:

1. revisar si `autorizar()`, `printAutoritzats()` i `payment()` continuen sent necessaris només per a la ruta legacy
2. deixar `ComisionController` com a bridge mínim del camí antic
3. revisar després si `resources/views/comision/detalle.blade.php` continua sent necessari per al flux de Direcció o queda només per FCT/professorat
4. decidir quan s'amaga l'enllaç a `/direccion/comision`

## Decisió pràctica

No convé "llevar el legacy" ara.

Sí convé:

- identificar-lo
- aprimar-lo
- marcar-lo
- retirar-lo per peces

En este mòdul, el primer objectiu ja no és "fer que funcione", perquè això ja ho fa. Ara el bon objectiu és **reduir dependències del panell nou respecte al controller legacy**.
