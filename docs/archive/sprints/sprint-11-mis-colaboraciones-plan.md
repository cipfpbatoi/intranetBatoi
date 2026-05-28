# Sprint 11: Reestructuració de `misColaboraciones`

## Objectiu

Fer `misColaboraciones` més útil, més escanejable i més mantenible sense rehacer el mòdul a cegues ni obrir un tall gran de Livewire abans de temps.

## Diagnòstic

L'estat actual té quatre problemes principals:

1. La vista barreja estructura, consultes i presentació.
   - [`resources/views/intranet/partials/profile/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/colaboracion.blade.php) consulta `Activity` dins de Blade.
2. La targeta té massa densitat informativa.
   - [`resources/views/intranet/partials/profile/partials/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/partials/colaboracion.blade.php) mescla resum, historial, relacionades i accions.
3. Hi ha doble agrupació.
   - [`app/Http/Controllers/PanelColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelColaboracionController.php) agrupa per `situation`, però després Blade torna a separar en `meues` i `altres`.
4. El JS del domini és massa monolític.
   - [`public/js/Colaboracion/grid.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/grid.js) concentra grid, colors, accions i comportament del panell.

## Decisió de disseny

Per ara es mantenen les pestanyes com a organització principal. No es fa una migració a Livewire en este sprint.

Raons:

- la informació ja és massa densa i primer cal simplificar el model visual
- Livewire ara mateix reproduiria el mateix acoblament en una altra tecnologia
- les pestanyes per estat continuen tenint sentit com a eix del panell

Livewire queda com a fase futura, només després de simplificar dades i targeta.

## Model objectiu

### Estructura del panell

- pestanyes per estat
- dins de cada pestanya, una sola llista
- desaparix la separació en dos blocs grans `meues` / `altres`
- `meua` o `d'altre tutor` passa a ser un badge o marca visual dins de la targeta

### Estructura de la targeta

Cada targeta ha de tindre tres zones:

1. Capçalera
   - centre / empresa
   - cicle
   - tutor responsable
   - badge d'estat
2. Cos curt
   - últim contacte
   - pròxima acció
   - contacte principal
3. Peu d'accions
   - `avisar`
   - `telefonico`
   - `book`
   - `resolve`
   - `refuse`
   - `switch`

### Informació plegable

Només queda desplegada quan l'usuari la demana:

- relacionades
- historial complet
- notes/comentaris llargs

## Fases d'implementació

### Tall A: Preparació de dades

- traure les consultes d'`Activity` fora de Blade
- preparar en backend:
  - `contactos`
  - `ultima_actividad`
  - `proxima_accion`
  - `relacionadas`
- garantir càrrega eficient i evitar N+1

Fitxers probables:

- [`app/Http/Controllers/PanelColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelColaboracionController.php)
- [`app/Application/Colaboracion/ColaboracionService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Colaboracion/ColaboracionService.php)

### Tall B: Simplificació de la vista principal

- eliminar la doble separació `meues` / `altres`
- renderitzar una sola llista per pestanya
- afegir un badge de propietat

Fitxers probables:

- [`resources/views/intranet/partials/profile/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/colaboracion.blade.php)

### Tall C: Redisseny de la targeta

- reduir pes visual
- prioritzar resum i pròxima acció
- passar relacionades i historial a blocs plegables

Fitxers probables:

- [`resources/views/intranet/partials/profile/partials/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/partials/colaboracion.blade.php)
- CSS associat si cal

### Tall D: Desacoblament del JS

- separar el que és grid/tables del que és accions de col·laboració
- preparar el camí per a una futura migració del panell

Fitxers probables:

- [`public/js/Colaboracion/grid.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/grid.js)

### Tall E: Valoració de Livewire

Només si els talls anteriors ja estan resolts.

Livewire tindria sentit per a:

- filtres en viu
- cerca
- comptadors per estat
- expandir detall sense recàrrega

No és objectiu del primer tall del sprint.

## Criteris d'acceptació

- `misColaboraciones` continua mostrant les mateixes col·laboracions útils
- la vista no consulta `Activity` dins de Blade
- cada pestanya mostra una única llista consistent
- una targeta és escanejable sense llegir tot el detall
- relacionades i historial no saturen la vista inicial
- les accions actuals continuen funcionant

## Riscos

- trencar accions JS si es refà la targeta massa prompte
- perdre informació rellevant en simplificar massa la vista
- moure dades a backend sense cobrir tots els casos d'ús del tutor

## Recomanació

Començar per Tall A + Tall B en la primera passada.

És el punt amb millor retorn:

- millora rendiment
- baixa l'acoblament
- deixa el panell preparat per a una segona passada visual

## Estat actual

- Tall A executat:
  - `contactos` del propi element ja es preparen en backend
  - Blade ja no consulta `Activity`
- Tall B iniciat:
  - la vista principal ja renderitza una sola llista per pestanya
  - `meua` / `altre tutor` passa a marcador visual dins de la targeta
- Tall C iniciat:
  - la targeta mostra primer resum curt, estat i últim contacte
  - el detall llarg i les relacionades passen a blocs plegables
