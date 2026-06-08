# Sprint 41 - Eliminar dependència de `Activity` en contactes

## Objectiu

Tancar la migració iniciada en el Sprint 29 i deixar el domini de contactes/seguiments funcionant sense dependre de `activities` com a font principal ni com a punt habitual d'escriptura.

## Punt de partida

Després del Sprint 29 ja tenim:

- taula pròpia de [`seguimientos`](/Users/igomis/Code/intranetBatoi/database/migrations/2026_03_29_120000_create_seguimientos_table.php)
- escriptura duplicada en diversos fluxos
- lectures combinades en `Colaboracion`, `Fct` i `AlumnoFct` dins de `fct/show`
- compatibilitat temporal per a modal i moviments d'evidències

El problema ja no és crear el model nou, sinó acabar de retirar el pes funcional de `Activity`.

## Abast

- inventariar els punts que encara escriuen contactes o seguiments en `activities`
- decidir quins continuen existint i quins s'eliminen
- migrar les escriptures pendents a `seguimientos`
- substituir lectures directes que encara depenen de `Activity`
- deixar `activities` fora del camí crític del domini de contactes

## Tall A. Inventari final d'ús de `Activity`

- localitzar totes les escriptures sobre `Activity::record(...)`
- separar:
  - contactes de `Colaboracion`
  - contactes de `Fct`
  - seguiment d'`AlumnoFct`
  - altres usos no relacionats amb este domini
- decidir quins usos pertanyen realment a contactes i quins no

## Tall B. Escriptura

- migrar a `seguimientos` els punts que encara escriuen només en `activities`
- revisar especialment:
  - moviments/còpies
  - fluxos de formulari antics
  - qualsevol controlador web que encara no haja passat pel servei nou
- evitar seguir afegint contactes nous sense mirall estructurat

## Tall C. Lectura

- retirar lectures directes a `Activity` en pantalles de contactes
- deixar `seguimientos` com a font principal
- mantindre fallback temporal només on siga imprescindible
- avaluar si `components/activity` ha de continuar sent l'adaptador visual o si convé un component propi

## Tall D. UI i rutes legacy

- eliminar rutes redundants que només existien per al model antic
- reduir JS que encara assumix `Activity` com a backend natural
- revisar modals legacy de comentari lliure

## Tall E. Estratègia de retirada

- definir quan `activities` deixa de ser obligatori
- decidir si cal una migració de dades històriques completa o només convivència llarga
- deixar criteri clar per a:
  - lectures antigues
  - exportacions
  - evidències mogudes/copiades

## Riscos

- trencar històrics visibles en pantalles antigues
- deixar contactes ocults si alguna lectura continua en `Activity`
- confondre activitats generals del sistema amb seguiments de contacte

## Criteri de tancament

Este sprint es podrà donar per tancat quan:

- els contactes nous del domini ja no necessiten `Activity`
- les pantalles principals de consulta no depenguen de `Activity`
- i quede clar quins usos residuals d'`Activity` no formen part del domini de contactes
