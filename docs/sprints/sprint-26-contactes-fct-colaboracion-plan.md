# Sprint 26 - Remodelació de contactes FCT i col·laboració

## Objectiu

Separar els contactes i seguiments de `FCT` i `Colaboracion` d'una forma menys rudimentària que l'actual, evitant continuar depenent de [`Activity`](/Users/igomis/Code/intranetBatoi/app/Entities/Activity.php) com a magatzem genèric de notes i telefonades.

## Problema actual

Ara mateix, contactes com:

- seguiment telefònic de `FCT`
- contactes i moviments de `Colaboracion`
- altres anotacions operatives semblants

es guarden en `activities` amb un model molt genèric i amb poc context de domini.

Els problemes que això crea són:

- no hi ha una taula pròpia de contactes/seguiments
- costa distingir bé contactes d'`FCT` i de `Colaboracion`
- es depén de `action`, `document` i `model_class` com a convencions toves
- el model no està preparat per créixer en camps específics de seguiment
- la consulta i explotació de dades històriques és poc clara

## Matís funcional nou

Abans, les pràctiques estaven molt associades a alumnat de segon i el context era més simple.

Ara:

- també hi ha alumnat de primer
- es poden barrejar contactes de diferents `FCT`
- i apareix el dubte de si la `FCT` hauria d'incorporar una clau nova de `grupo`

Això pot resoldre's de dos maneres:

1. afegint més context directament a `FCT`
2. o bé separant millor els contactes en una taula pròpia, on el context del grup i del tipus de seguiment puga quedar registrat de forma explícita

## Restricció important

Este tema té una dificultat clara: enguany ja hi ha molts contactes guardats en `activities`.

Per tant, qualsevol redisseny ha de decidir entre:

- conviure temporalment amb l'històric actual
- migrar parcialment dades antigues
- o deixar el nou model només per a registres nous

## Hipòtesi de disseny

La direcció que sembla més sana és:

- crear una taula pròpia de contactes/seguiments
- separar explícitament el domini origen:
  - `fct`
  - `colaboracion`
- guardar camps estructurats de context
  - identificador del registre origen
  - tipus de contacte
  - autor
  - data/hora
  - comentari
  - potser grup/curs si realment és necessari

I deixar `activities` només per a l'activitat transversal que encara tinga sentit com a audit trail genèric.

## Tall A. Inventari real

- localitzar totes les escriptures a `Activity::record()` o equivalents per a seguiments de `FCT` i `Colaboracion`
- classificar:
  - contactes telefònics
  - seguiments manuals
  - moviments d'estat
  - altres anotacions

## Tall B. Model de dades

- decidir si cal una taula nova tipus `contactes`, `seguiments` o semblant
- definir si la clau de context ha de ser:
  - només `fct_id` / `colaboracion_id`
  - o també `grupo`
- decidir si `grupo` ha de viure en `FCT` o només en el registre de contacte

## Tall C. Compatibilitat amb l'històric

- decidir què fer amb els contactes ja guardats en `activities`
- opcions:
  - no migrar i deixar lectura híbrida
  - migració parcial
  - migració completa si és viable

## Tall D. Moment adequat

Ara mateix este sprint sembla més adequat per a:

- preparar-lo enguany
- però executar el canvi estructural fort de cara al curs vinent

La raó és que:

- el model actual ja té històric viu
- hi ha risc de barrejar dades a mig curs
- i convé no obrir una migració de domini delicada en plena operativa

## Resultat esperat

- un model de seguiment/contacte propi de domini
- menys dependència de `activities` com a calaix de sastre
- millor separació entre `FCT` i `Colaboracion`
- millor base per a distingir contactes per grup/curs quan el negoci ho requerisca

## Recomanació

No abordar encara l'execució completa en calent si no hi ha una necessitat urgent.

El millor ús d'este sprint és:

1. deixar clar el problema
2. preparar el disseny
3. decidir l'estratègia de convivència amb l'històric
4. atacar la implementació estructural a l'inici del curs vinent
