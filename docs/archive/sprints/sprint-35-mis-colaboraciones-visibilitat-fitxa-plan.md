# Sprint 35 - Visibilitat de fitxa i qualitat de dades en `MisColaboraciones`

## Objectiu

Fer que cada targeta de `MisColaboraciones` deixe clar, d'un colp d'ull:

- l'estat de la col·laboració
- si les dades estan completes
- quan es va fer l'últim contacte
- i què és el més urgent de revisar

## Problema a resoldre

Ara mateix el panell mostra molta informació, però no priorita prou bé:

- què està incomplet
- què està desactualitzat
- quines empreses fa massa temps que no es revisen

Sense eixa capa visual, el tutor no té incentiu clar per mantindre la fitxa viva.

## Abast

- indicador d'últim contacte
- indicador d'informació bàsica incompleta
- estat de documentació mínima visible
- filtres ràpids de qualitat de fitxa
- ordenació per desactualització i prioritat

## Tall A. Estat de fitxa

Afegir en cada targeta:

- `últim contacte`
- `dies sense contacte`
- `email principal`
- `telèfon principal`
- `contacte principal`
- `estat funcional`

## Tall B. Badges de qualitat

Definir i mostrar badges com:

- `sense email`
- `sense telèfon`
- `sense contacte`
- `sense instructor`
- `conveni pendent`
- `documentació pendent`

## Tall C. Filtres i ordenació

Afegir filtres:

- sense contacte este curs
- sense email
- sense telèfon
- sense instructor
- documentació incompleta
- col·laboracions més antigues sense moviment

## Tall D. Preparació de dades en backend

Evitar calcular en Blade o dispersar lògica en JS.

Preparar en backend:

- `ultima_actividad`
- `dies_sense_contacte`
- `fitxa_incompleta`
- `indicadors_documentacio`

## Fitxers candidats

- [`app/Http/Controllers/PanelColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelColaboracionController.php)
- [`app/Application/Colaboracion/ColaboracionQueryService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Colaboracion/ColaboracionQueryService.php)
- [`resources/views/intranet/partials/profile/partials/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/partials/colaboracion.blade.php)
- [`public/js/Colaboracion/grid.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/grid.js)

## Criteris d'acceptació

- una targeta permet detectar ràpidament si està incompleta
- és possible filtrar empreses sense contacte o sense dades clau
- l'últim contacte és visible sense obrir detall
- el panell ajuda a prioritzar revisió, no només a consultar dades

## No objectius

- no es canvia encara el model de persistència de contactes
- no es força encara cap registre obligatori
- no es redissenya completament el flux documental
