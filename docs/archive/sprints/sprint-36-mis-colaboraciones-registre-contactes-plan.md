# Sprint 36 - Registre ràpid i estructurat de contactes

## Objectiu

Fer que registrar un contacte en `MisColaboraciones` siga prou ràpid i útil com per desplaçar notes externes, correus fora del sistema o seguiments no registrats.

## Problema a resoldre

Si registrar un contacte costa massa:

- no es farà
- es farà tard
- o es farà fora del sistema

La primera barrera d'adopció és la fricció.

## Abast

- botons ràpids de contacte
- modal curt de registre
- tipus i resultat de contacte estructurats
- autor i data automàtics
- reutilització del flux actual sense bloquejar el treball

## Tall A. Tipus de contacte

Normalitzar almenys:

- telefonada
- correu
- visita
- reunió
- seguiment

## Tall B. Resultat mínim

Cada registre hauria de poder deixar:

- resultat
- observacions
- pròxim pas
- data prevista

## Tall C. Flux curt

El formulari ha de permetre completar un contacte en molt pocs segons.

No convé forçar camps llargs ni massa context si ja és deduïble:

- empresa
- tutor
- moment
- origen de l'acció

## Tall D. Integració visual

Des de la targeta:

- telefonada
- correu
- visita
- seguiment

Amb retorn immediat:

- actualització d'últim contacte
- nova entrada en historial
- possible actualització de pròxima acció

## Fitxers candidats

- [`resources/views/intranet/partials/profile/partials/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/partials/colaboracion.blade.php)
- [`public/js/Colaboracion/grid.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/grid.js)
- [`public/js/Colaboracion/modal.js`](/Users/igomis/Code/intranetBatoi/public/js/Colaboracion/modal.js)
- punts actuals d'escriptura en `API/ColaboracionController` i `API/FctController`

## Criteris d'acceptació

- registrar una telefonada o seguiment és clar i ràpid
- cada contacte queda amb tipus i resultat
- el tutor no ha d'escriure manualment dades que el sistema ja sap
- l'historial de l'empresa reflectix el contacte acabat de crear

## No objectius

- no es migra encara necessàriament tot fora d'`activities`
- no es bloqueja encara cap procés per falta de contacte registrat
