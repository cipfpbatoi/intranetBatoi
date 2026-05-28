# Sprint 29 - Tancament de la convivència temporal de contactes

## Estat

Sprint executat parcialment i deixat en estat operatiu.

No s'ha eliminat encara `activities`, però sí s'ha deixat la convivència temporal funcional per als dominis que estàveu usant en els fluxos reals:

- `Colaboracion`
- `Fct`
- `AlumnoFct` dins de la fitxa de FCT

## Què s'ha entregat

- taula nova [`seguimientos`](/Users/igomis/Code/intranetBatoi/database/migrations/2026_03_29_120000_create_seguimientos_table.php)
- model [`Seguimiento`](/Users/igomis/Code/intranetBatoi/app/Entities/Seguimiento.php)
- servei [`SeguimientoService`](/Users/igomis/Code/intranetBatoi/app/Application/Seguimiento/SeguimientoService.php)
- escriptura duplicada `activities` + `seguimientos`
- lectura combinada amb deduplicació per `activity_id`
- adaptació del modal de contactes en:
  - `MisColaboraciones`
  - panell `Fct`
- moviment d'evidències sincronitzant també el mirall en `seguimientos`

## Cobertura funcional aconseguida

- els contactes nous de col·laboració creen ja registre estructurat
- els contactes nous de FCT creen ja registre estructurat
- el seguiment d'alumnat en FCT crea ja registre estructurat
- les lectures visibles en panell i fitxa no depenen només de `activities`
- les còpies/moviments d'evidències creen també el mirall nou

## Què no es dona per tancat encara

- eliminació de la taula `activities`
- migració completa de tots els punts d'escriptura legacy
- substitució global de totes les lectures directes a `Activity`
- neteja final de rutes/controladors/components que encara assumixen el model antic

## Decisió

Este sprint es dona per bo com a sprint de convivència temporal.

El següent sprint ja no hauria de continuar afegint compatibilitat, sinó:

- reduir dependències pendents amb `Activity`
- inventariar els punts que encara escriuen allí
- i preparar la retirada real del model legacy per al domini de contactes
