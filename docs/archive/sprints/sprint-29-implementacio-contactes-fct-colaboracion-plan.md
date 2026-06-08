# Sprint 29 - Implementació de contactes FCT i col·laboració

## Estat actual

Este sprint s'ha acabat executant i la convivència temporal ha quedat operativa.

El tancament funcional s'ha documentat en:

- [`docs/sprints/sprint-29-contactes-convivencia-closeout.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-29-contactes-convivencia-closeout.md)

## Objectiu

Executar, si finalment es decidix fer-ho, la substitució parcial del model actual basat en `activities` per un model de seguiments/contactes propi de domini.

Este sprint **no implica que el canvi s'haja d'executar ja**. Es deixa preparat per a poder decidir més avant si convé abordar-lo.

## Punt de partida

El disseny i l'inventari previs s'han deixat en:

- [`docs/sprints/sprint-26-contactes-fct-colaboracion-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-26-contactes-fct-colaboracion-plan.md)

La conclusió d'eixe sprint és:

- `activities` està fent de calaix de sastre
- el domini real barreja `Colaboracion`, `Fct` i `AlumnoFct`
- la visibilitat dels contactes no es resol només pel grup
- i el cost real de la implementació està en desacoblar la lectura de `activities`

## Decisió pendent abans d'arrancar

Abans de començar la implementació real, cal decidir explícitament:

1. si el canvi es fa ara o es deixa per més avant
2. si es vol convivència temporal amb `activities`
3. si es vol només nova escriptura o també nova lectura

## Abast de la implementació

Si s'executa, este sprint hauria de cobrir:

- creació de la nova taula de seguiments/contactes
- model i servei de domini per a escriure i llegir
- adaptació dels punts d'escriptura actuals:
  - `API/ColaboracionController`
  - `API/FctController`
  - i equivalents web si toca
- desacoblament progressiu de lectures que ara depenen de `activities`

## Tall A. Nova persistència

- migració de base de dades
- model Eloquent nou
- servei d'aplicació per a registrar seguiments
- normalització de tipus de contacte

## Tall B. Nova lectura

- substituir lectures directes d'`Activity` en:
  - [`app/Entities/Colaboracion.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Colaboracion.php)
  - [`app/Application/Colaboracion/ColaboracionQueryService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Colaboracion/ColaboracionQueryService.php)
  - [`app/Http/Controllers/ColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/ColaboracionController.php)
  - [`app/Entities/Fct.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Fct.php)

## Tall C. Regles de visibilitat

- definir clarament qui pot veure cada seguiment
- mínim:
  - tutor de la col·laboració
  - tutor acadèmic de l'alumne o de la FCT
- evitar que la visibilitat depenga només de `grupo`

## Tall D. Compatibilitat

Si s'executa ara, caldrà decidir una d'estes dues rutes:

- convivència temporal amb `activities`
- o migració més agressiva amb desacoblament immediat

La recomanació inicial és:

- fer convivència temporal només si l'execució és imminent
- si no hi ha urgència, no tocar encara la capa legacy

## Tall E. Validació

- proves `Feature` per a escriptura
- proves de lectura o query service
- comprovació de visibilitat per rols funcionals
- verificació manual dels panells afectats

## Risc principal

El risc gran no és crear la taula nova, sinó:

- trencar lectures actuals
- perdre històric visible
- o empitjorar la visibilitat dels tutors implicats

## Criteri de decisió

Si no hi ha necessitat funcional urgent, este sprint pot quedar **preparat però no executat**.

La seua utilitat ara és separar clarament:

- el sprint de disseny
- del sprint d'implementació real
