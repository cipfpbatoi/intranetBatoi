# Sprint 3 P2 - Pla de retirada progressiva de legacy en `direccion/comision`

## Estat actual

El panell de Direcció de comissions ja entra per Livewire en:

- `/direccion/comision`

La ruta antiga:

- `/direccion/comision-livewire`

queda només com a redirecció de compatibilitat.

## Peces implicades

### Ruta i entrada actual

- `routes/direccion.php`
  - `GET /direccion/comision` -> vista `resources/views/comision/livewire-panel.blade.php`
  - `GET /direccion/comision-livewire` -> redirecció de compatibilitat
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

- `ComisionController`
- `resources/views/comision/detalle.blade.php`
- rutes de `routes/profesor.php`

Motiu:

- encara donen servei al flux de professorat
- hi ha funcionalitat FCT i detall que no hem retirat del mòdul antic

## Peces candidates a bridge

El treball pendent ja no és el panell de Direcció, sinó el mòdul antic que
continua donant servei a professorat/FCT.

En `ComisionController` hi ha una barreja de:

- lògica CRUD modal
- lògica d'autorització
- fluxos d'impressió
- gestió FCT
- notificacions/correus

La part de Direcció ja s'ha tret del controller principal. Ara la decisió és si
convé segmentar el que queda entre flux general de professorat i flux FCT.

## Ordre recomanat de retirada

### Fase 1. Fer explícita la convivència

Objectiu:

- mantindre compatibilitat sense mantindre dos pantalles actives

Accions:

- documentar dependències actuals
- fer que `/direccion/comision` ja siga el panell nou

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

- passar el formulari de comissió a Livewire
- traure gestor documental a una ruta específica de Direcció

### Fase 4. Retirada visible del legacy

Només quan el pilot cobrisca el 100% del flux de Direcció:

- deixar la ruta antiga només com a redirecció temporal
- eliminar vistes i JS antics no referenciats
- simplificar o segmentar `ComisionController`

## Criteri de retirada segura

Una peça legacy de `comision` només s'hauria d'eliminar si es complixen les tres:

1. no queda cap ruta del panell nou apuntant a ella
2. hi ha cobertura de prova o validació manual equivalent
3. el flux funcional ja està tancat en el panell nou

## Recomanació immediata

El següent treball amb millor retorn és:

1. revisar si `resources/views/comision/detalle.blade.php` continua sent necessari per al flux de Direcció o queda només per FCT/professorat
2. decidir si `ComisionController` es pot segmentar entre flux de professorat i flux FCT
3. fer la mateixa retirada segura en `falta`, `actividad` i `expediente`

## Estat de tancament del panell de Direcció

Des del punt de vista de Direcció, el mòdul de comissions queda pràcticament
tancat en este sprint:

- ruta principal migrada a Livewire
- accions bulk desacoblades
- edició desacoblada
- gestor documental desacoblat
- codi mort exclusiu de Direcció eliminat

El que queda en `ComisionController` respon ja al flux viu de professorat/FCT.

## Decisió pràctica

En este mòdul, el panell de Direcció ja ha quedat desacoblat. El que queda ara és
retirada del codi antic que encara siga exclusiu de Direcció i simplificació del
camí legacy que continua viu per professorat/FCT.
