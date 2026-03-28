# Sprint 38 - Documentació integrada en la fitxa de col·laboració

## Objectiu

Fer que la fitxa de `MisColaboraciones` servisca també per entendre si l'empresa està preparada documentalment per a treballar amb FCT.

## Problema a resoldre

Ara el tutor pot haver de saltar entre:

- col·laboració
- FCT
- instructor
- correus
- conveni
- annexos o documentació pendent

Això afavoreix que la informació es gestione fora del sistema.

## Abast

- resum documental en la fitxa
- enllaç clar entre col·laboració i FCT
- estat de preparació d'empresa
- detecció de buits documentals

## Tall A. Estat documental

Mostrar en la targeta o detall:

- conveni
- instructor
- email de contacte
- telèfon
- places
- FCT associades
- documentació pendent

## Tall B. Estat de preparació

Definir un indicador funcional com:

- no preparada
- parcialment preparada
- preparada

Este estat hauria de dependre de dades bàsiques i documentació mínima.

## Tall C. Navegació integrada

Enllaços directes:

- de col·laboració a FCT
- de FCT a col·laboració
- de fitxa a documentació relacionada

## Tall D. Accions documentals traçables

Quan es genere o envie documentació des del sistema:

- que quede visible en l'historial o resum d'estat

## Fitxers candidats

- [`app/Entities/Colaboracion.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Colaboracion.php)
- [`app/Entities/Fct.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Fct.php)
- [`resources/views/intranet/partials/profile/partials/colaboracion.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/partials/colaboracion.blade.php)
- pantalles de `Fct` i documentació associada

## Criteris d'acceptació

- el tutor pot saber des de la fitxa si l'empresa està preparada
- es reduïx la necessitat d'anar a altres pantalles per entendre l'estat
- els buits documentals importants són visibles

## No objectius

- no remodelar completament tots els fluxos de documentació
- no convertir encara la fitxa en expedient exhaustiu de cada empresa
