# Sprint 21 - Separació funcional entre colaboracion i FCT

Issue remot:
- `#123` https://github.com/cipfpbatoi/intranetBatoi/issues/123

## Objectiu

Separar clarament el domini de `colaboracion` del domini de `FCT` perquè:

- `colaboracion` prepare
- `FCT` execute

## Problema actual

Ara mateix la frontera està borrosa:

- `colaboracion` governa estat de contacte, instructors, reserves i pont cap a FCT
- `FCT` continua depenent fortament de `Colaboracion`, `Centro` i `Empresa`
- hi ha fluxos web, API i JS que travessen els dos dominis sense una frontera clara

## Principi de disseny

- `colaboracion` representa la relació centre-cicle i la seua preparació operativa
- `FCT` representa la pràctica real amb alumnat, hores, seguiment i avaluació

## Tall A. Contracte de frontera

- definir quines operacions són exclusives de `colaboracion`
- definir quines operacions són exclusives de `FCT`
- marcar casos mixts que necessiten punt de pas explícit

## Tall B. Punts de dependència actuals

- revisar dependències de `FCT` respecte a:
  - `Colaboracion`
  - `Centro`
  - `Empresa`
- revisar punts on `colaboracion` encara governa massa negoci de pràctica real

## Tall C. Extracció operativa

- moure a `FCT` el que ja siga pràctica real:
  - seguiment real
  - hores
  - alumnat assignat
  - avaluació
- mantindre en `colaboracion`:
  - estat de contacte
  - instructors
  - disponibilitat
  - reserves prèvies

## Tall D. Impacte tècnic

- revisar:
  - controllers
  - serveis
  - API
  - vistes Blade
  - JS legacy
- prioritzar punts on la vista encara pressuposa cadenes profundes de relacions

## Resultat esperat

- frontera clara entre preparació i execució
- menys acoblament entre panells de col·laboració i fluxos FCT
- millor base per a casos futurs:
  - reserves
  - multi-centre
  - hores parcials
