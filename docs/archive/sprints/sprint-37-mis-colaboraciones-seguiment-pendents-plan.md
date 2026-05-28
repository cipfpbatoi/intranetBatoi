# Sprint 37 - Seguiment real amb pendents i pròxima acció

## Objectiu

Convertir `MisColaboraciones` d'un registre d'anotacions en una eina de seguiment real.

## Problema a resoldre

Sense `pròxima acció` ni pendents explícits:

- l'historial només mira cap arrere
- no ajuda a treballar el que toca fer
- i no evita que una empresa quede oblidada

## Abast

- camp de pròxima acció
- data prevista
- pendents vençuts
- filtres de seguiment
- resum de treball pendent per tutor

## Tall A. Model funcional

Cada col·laboració ha de poder tindre:

- pròxima acció
- data prevista
- estat de seguiment

Estats mínims recomanats:

- pendent
- en curs
- sense resposta
- tancat

## Tall B. Venciments

Marcar visualment:

- accions vençudes
- accions d'esta setmana
- empreses sense moviment durant massa temps

## Tall C. Vista de pendents

Afegir una vista o filtre per:

- pendents de resposta
- pròxima acció vençuda
- sense contacte recent

## Tall D. Resum personal

Panell resum del tutor amb:

- pendents oberts
- col·laboracions desactualitzades
- pròximes accions d'esta setmana

## Fitxers candidats

- [`app/Http/Controllers/PanelColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelColaboracionController.php)
- [`app/Application/Colaboracion/ColaboracionQueryService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Colaboracion/ColaboracionQueryService.php)
- [`resources/views/intranet/partials/profile/partials/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/partials/colaboracion.blade.php)

## Criteris d'acceptació

- després de cada contacte es pot deixar una acció futura clara
- el tutor veu fàcilment què té vençut
- el panell ajuda a treballar en ordre de prioritat

## No objectius

- no fer encara dashboard transversal per direcció
- no introduir analítica avançada
