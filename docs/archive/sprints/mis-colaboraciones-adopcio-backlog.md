# Backlog - Adopció de `MisColaboraciones` com a sistema principal

## Objectiu

Fer que `MisColaboraciones` siga el punt únic i natural de treball amb empreses, de manera que:

- els contactes queden registrats en el sistema
- la documentació de les empreses es mantinga actualitzada
- es reduïsca l'ús de canals externs o registres dispersos
- el tutor tinga un seguiment real, no només una fitxa informativa

## Punt de partida

Este backlog se recolza en treball anterior ja documentat:

- [`docs/sprints/sprint-11-mis-colaboraciones-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-11-mis-colaboraciones-plan.md)
- [`docs/sprints/sprint-26-contactes-fct-colaboracion-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-26-contactes-fct-colaboracion-plan.md)
- [`docs/sprints/sprint-29-implementacio-contactes-fct-colaboracion-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-29-implementacio-contactes-fct-colaboracion-plan.md)
- [`docs/manual-fct-meues-colaboracions.md`](/Users/igomis/Code/intranetBatoi/docs/manual-fct-meues-colaboracions.md)

## Diagnòstic funcional

Ara mateix `MisColaboraciones`:

- sí és útil per a classificar empreses i centralitzar part del seguiment
- però encara no és clarament millor que mecanismes externs
- no força prou el registre estructurat de contactes
- no premia prou al tutor que treballa dins del sistema
- no exposa prou bé els pendents, la documentació incompleta ni la pròxima acció

El canvi desitjat no és només tècnic. És sobretot d'adopció:

- reduir fricció
- augmentar valor immediat
- fer visible el treball pendent
- i convertir la fitxa de col·laboració en context compartit real

## Principis de disseny

### 1. Més útil que qualsevol alternativa externa

Registrar un contacte ha de ser més ràpid que apuntar-lo fora.

### 2. El contacte ha de generar context reutilitzable

No val només guardar una anotació. Cal saber:

- què s'ha fet
- quin resultat ha tingut
- què toca després
- quan toca

### 3. La documentació i el seguiment han d'estar units

La col·laboració no hauria de tindre:

- d'una banda contactes
- i d'una altra documentació dispersa

El tutor ha de poder entendre l'estat real d'una empresa des d'una sola fitxa.

### 4. El sistema ha d'ajudar a prioritzar

Cal fer visibles:

- empreses sense contacte
- empreses sense resposta
- empreses amb documentació incompleta
- empreses amb accions vençudes

### 5. Adopció progressiva, no big bang

Primer s'ha de guanyar confiança i valor operatiu.

No convé començar bloquejant processos si abans el sistema encara no és el camí més còmode.

## Roadmap recomanat

### Sprint 35

Visibilitat de fitxa i qualitat de dades.

Document:

- [`docs/sprints/sprint-35-mis-colaboraciones-visibilitat-fitxa-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-35-mis-colaboraciones-visibilitat-fitxa-plan.md)

### Sprint 36

Registre ràpid i estructurat de contactes.

Document:

- [`docs/sprints/sprint-36-mis-colaboraciones-registre-contactes-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-36-mis-colaboraciones-registre-contactes-plan.md)

### Sprint 37

Seguiment real amb pendents, pròxima acció i venciments.

Document:

- [`docs/sprints/sprint-37-mis-colaboraciones-seguiment-pendents-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-37-mis-colaboraciones-seguiment-pendents-plan.md)

### Sprint 38

Integració de documentació, FCT i estat de preparació d'empresa.

Document:

- [`docs/sprints/sprint-38-mis-colaboraciones-documentacio-integrada-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-38-mis-colaboraciones-documentacio-integrada-plan.md)

### Sprint 39

Adopció operativa i reducció d'ús de canals externs.

Document:

- [`docs/sprints/sprint-39-mis-colaboraciones-adopcio-operativa-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-39-mis-colaboraciones-adopcio-operativa-plan.md)

### Sprint 40

Quadres de comandament, coordinació i explotació transversal.

Document:

- [`docs/sprints/sprint-40-mis-colaboraciones-dashboard-coordinacio-plan.md`](/Users/igomis/Code/intranetBatoi/docs/sprints/sprint-40-mis-colaboraciones-dashboard-coordinacio-plan.md)

## Línies de backlog transversal

### UX i producte

- reduir clics per registrar contactes
- mostrar sempre últim contacte i pròxima acció
- marcar clarament fitxes incompletes
- reforçar la navegació entre col·laboració, FCT i documentació

### Model de dades

- decidir si el seguiment continua sobre `activities` o passa a model propi
- normalitzar tipus de contacte i resultat
- decidir on viu `proxima_accion`
- evitar duplicació entre anotació lliure i seguiment estructurat

### Integració funcional

- correus enviats des del sistema
- seguiments telefònics
- seguiments d'alumnat i FCT
- instructor i documentació
- relació amb preassignacions o reserves futures

### Analítica i adopció

- empreses sense contacte este curs
- empreses amb documentació incompleta
- tutors amb pendents oberts
- mesura d'activitat registrada dins del sistema

## Dependències i ordre recomanat

L'ordre recomanat és:

1. Sprint 35
2. Sprint 36
3. Sprint 37
4. Sprint 38
5. Sprint 39
6. Sprint 40

Raó:

- primer cal fer visible el problema
- després fer fàcil registrar
- després donar seguiment real
- i només després usar el sistema com a eina de coordinació i govern

## MVP realista

Si calguera fer un primer tall curt amb alt retorn, la combinació mínima seria:

- estat de fitxa visible
- filtres per fitxa incompleta o sense contacte
- modal curt de contacte
- últim contacte
- pròxima acció

Si això funciona i s'usa, la resta del roadmap té sentit.
