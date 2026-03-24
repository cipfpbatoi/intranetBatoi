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

### Resultat de la primera passada

S'han localitzat ja estos punts clars:

#### 1. `Colaboracion` via API

En [`app/Http/Controllers/API/ColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ColaboracionController.php):

- `telefon($id, Request $request)`
  - escriu en `activities`
  - `action = phone`
  - el model real associat és `Fct::find($id)`
  - `document = Seguiment telefònic`
- `book($id, Request $request)`
  - escriu en `activities`
  - `action = book`
  - el model real associat és `Colaboracion::find($id)`
  - `document = Contacte previ`
- `alumnat($id, Request $request)`
  - escriu en `activities`
  - `action = review`
  - el model real associat és `Fct::find($id)`
  - `document = Seguiment Alumnat`

És important remarcar que ací ja hi ha **barreja de dominis**:

- unes activitats pengen de `Colaboracion`
- altres pengen de `Fct`
- però totes es tracten com a “contactes” o “seguiments”

#### 2. `FCT` via API

En [`app/Http/Controllers/API/FctController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/FctController.php):

- `seguimiento($id, Request $request)`
  - escriu manualment en `activities`
  - `action = review`
  - `model_class = Intranet\\Entities\\AlumnoFct`
  - `document = Seguimiento Alumno`

Açò introdueix un tercer nivell de context:

- no sols `Colaboracion`
- no sols `Fct`
- també `AlumnoFct`

#### 3. Lectura de contactes en `Colaboracion`

En [`app/Entities/Colaboracion.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Colaboracion.php):

- `getAnotacioAttribute()`
  - reconstruïx l'anotació a partir de:
    - `model_class = Colaboracion`
    - `action = book`

En [`app/Application/Colaboracion/ColaboracionQueryService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Colaboracion/ColaboracionQueryService.php):

- es recuperen activitats de `Colaboracion`
- `notUpdate()`
- agrupades per `model_id`
- després es pengen com a `contactos` i `ultimaActividad`

En [`app/Http/Controllers/ColaboracionController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/ColaboracionController.php):

- `show()` llig l'últim contacte amb:
  - `modelo('Colaboracion')`
  - `notUpdate()`

#### 4. Lectura de contactes en `Fct`

En [`app/Entities/Fct.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Fct.php):

- `Contactos()`
  - recupera `Activity`
  - filtrat per `mail()`
  - `model_class = Intranet\\Entities\\Fct`

Això mostra que per a `Fct` hi ha almenys dos usos diferents d'`Activity`:

- contactes tipus `mail/phone/visita/review`
- seguiments específics sobre alumnat o sobre la pròpia FCT

### Conclusió del tall A

L'inventari confirma que `activities` està fent de calaix de sastre per a tres nivells distints:

- `Colaboracion`
- `Fct`
- `AlumnoFct`

I que la semàntica real del contacte no està modelada en camps de domini forts, sinó repartida entre:

- `action`
- `document`
- `model_class`
- `model_id`

Açò reforça que el següent pas no hauria de ser només “moure una taula”, sinó primer decidir:

1. quin és l'agregat arrel del contacte
2. si `AlumnoFct` és un tipus de seguiment propi o un detall d'una `Fct`
3. si `Colaboracion` i `Fct` necessiten taules separades o una taula comuna amb `domain_type`

## Tall B. Model de dades

- decidir si cal una taula nova tipus `contactes`, `seguiments` o semblant
- definir si la clau de context ha de ser:
  - només `fct_id` / `colaboracion_id`
  - o també `grupo`
- decidir si `grupo` ha de viure en `FCT` o només en el registre de contacte

### Proposta inicial de model

Després del tall A, la proposta més defensable no és fer dues taules separades de primeres, sinó una taula comuna de seguiments amb context explícit de domini.

Nom provisional:

- `seguimientos`

O, si es vol un terme més neutre:

- `contactos_dominio`

### Camps mínims proposats

- `id`
- `domain_type`
  - `colaboracion`
  - `fct`
  - `alumno_fct`
- `domain_id`
- `contact_type`
  - `phone`
  - `book`
  - `review`
  - `mail`
  - `visit`
  - o catàleg equivalent
- `title`
  - equivalent funcional de l'actual `document`
- `comment`
- `author_id`
- `contacted_at`
- `meta`
  - opcional per a càrrega flexible de dades de transició
- `created_at`
- `updated_at`

### Per què una taula comuna i no dos taules separades

Perquè l'inventari real mostra que el problema principal no és només “FCT versus Colaboracion”, sinó que el sistema actual barreja almenys tres nivells:

- `Colaboracion`
- `Fct`
- `AlumnoFct`

Fer ara dos models separats deixaria probablement fora el tercer cas o obligaria a doblar regles de manera artificial.

Una taula comuna amb `domain_type + domain_id`:

- reflectix millor l'estat real
- facilita migració incremental
- evita duplicar infraestructura de consultes i escriptura

### Decisió sobre `grupo`, `tutor` i visibilitat

La lectura actual és esta:

- `Colaboracion` manté un únic `tutor`
- eixe tutor és el responsable funcional principal de les `Fct` associades
- però els contactes no els ha de veure només el tutor de la col·laboració
- també els ha de poder veure el tutor acadèmic de l'alumne

A més, hi ha un matís operatiu nou:

- alumnat de primer també fa pràctiques
- pot interessar no barrejar les `Fct` de primer amb les de segon

### Què no convé fer ara

Amb este context, no sembla bona idea:

- afegir dos tutors fixes a `colaboraciones`
- ni usar `grupo` com a substitut d'un model de visibilitat

Les dos opcions acoblarien massa el model a una casuística concreta.

### Criteri actual recomanat

1. `Colaboracion` manté un únic `tutor`
   És el responsable funcional de la col·laboració.

2. La visibilitat dels contactes no s'ha de resoldre només per `Colaboracion`
   També ha d'entrar el tutor acadèmic de l'alumne o de la `Fct`.

3. La separació primer/segon, si realment fa falta, no s'ha de resoldre amb “dos tutors en `Colaboracion`”
   El lloc natural per a eixe context seria més prompte:
   - `Fct`
   - o una capa d'assignació/responsabilitat associada a la `Fct`

### Estat de la decisió sobre `grupo`

En [`app/Entities/AlumnoFct.php`](/Users/igomis/Code/intranetBatoi/app/Entities/AlumnoFct.php), `getGrupAttribute()` ja pot derivar el grup creuant:

- alumne
- grups de l'alumne
- cicle de la `Colaboracion` associada a la `Fct`

Per tant, la decisió prudent ara és:

- **no afegir encara `grupo` com a camp obligatori ni a `fcts` ni a la nova taula**
- deixar oberta la possibilitat d'afegir context acadèmic a `Fct` si després es confirma que primer i segon s'han de separar operativament

En altres paraules:

- `grupo` és de moment context acadèmic potencial
- però el problema principal identificat és de **responsabilitat i visibilitat**

### Capa de compatibilitat

La migració més sana no seria substituir de colp `Activity`, sinó introduir una capa nova i fer convivència temporal:

1. nova escriptura en taula pròpia per als casos nous
2. lectura híbrida on siga necessari
3. retirada progressiva dels punts que ara depenen de:
   - `action`
   - `document`
   - `model_class`
   - `model_id`

### API de domini recomanada

En lloc d'escampar `Activity::record()` o instàncies manuals, convindria tindre una API de domini explícita, per exemple:

- `SeguimientoService::recordForColaboracion(...)`
- `SeguimientoService::recordForFct(...)`
- `SeguimientoService::recordForAlumnoFct(...)`

I que siga este servei qui resolga:

- persistència
- normalització del tipus de contacte
- convivència temporal amb l'històric

### Conclusió del tall B

La primera proposta de model queda així:

1. una taula comuna de seguiments/contactes
2. context explícit per `domain_type + domain_id`
3. la visibilitat no es resol dins del contacte, sinó en la capa de domini
4. el context acadèmic de `Fct` queda pendent de decidir si realment cal explicitar-lo
5. servei de domini propi per a escriure i llegir
6. convivència temporal amb `activities`

## Tall C. Compatibilitat amb l'històric

- decidir què fer amb els contactes ja guardats en `activities`
- opcions:
  - no migrar i deixar lectura híbrida
  - migració parcial
  - migració completa si és viable

### Lectura actual del problema

La convivència amb `activities` no és opcional a curt termini.

El que s'ha vist és:

- [`app/Application/Activity/ActivityService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Activity/ActivityService.php) continua sent el punt genèric de registre
- [`app/Http/Controllers/API/ActivityController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ActivityController.php) encara opera directament sobre `Activity`
- [`app/Finders/Finder.php`](/Users/igomis/Code/intranetBatoi/app/Finders/Finder.php) usa `Activity` per a comprovacions de duplicat documental
- les proves actuals de [`ApiColaboracionControllerFeatureTest.php`](/Users/igomis/Code/intranetBatoi/tests/Feature/ApiColaboracionControllerFeatureTest.php) i [`ApiFctControllerFeatureTest.php`](/Users/igomis/Code/intranetBatoi/tests/Feature/ApiFctControllerFeatureTest.php) encara validen persistència sobre `activities`

Per tant, qualsevol migració “big bang” tindria massa superfície de regressió.

### Estratègia recomanada

La proposta més segura és:

1. **no migrar l'històric de primeres**
   Deixar els registres antics en `activities`.

2. **introduir nova escriptura només per als casos nous del domini de seguiments**
   Els punts nous o refactoritzats de `Colaboracion`, `Fct` i `AlumnoFct` escriurien en la taula nova.

3. **mantindre lectura híbrida**
   Les consultes de contactes i seguiments resoldrien:
   - nova taula
   - i, si cal, també `activities`

4. **aïllar la compatibilitat en una capa de servei**
   No repartir la doble lectura per controladors i models, sinó concentrar-la en:
   - `SeguimientoQueryService`
   - `SeguimientoService`
   - o noms equivalents

### Fases de convivència proposades

#### Fase 1. Introducció sense migració de dades

- nova taula creada
- nous serveis de lectura i escriptura
- cap migració massiva d'històric
- adaptadors perquè la UI continue veient també dades antigues

#### Fase 2. Canvi de punts d'escriptura

- `API/ColaboracionController`
- `API/FctController`
- i els punts web equivalents

passen a escriure en la nova taula, no en `activities`.

#### Fase 3. Lectura principal per la nova taula

- els panells i consultes passen a usar primer la nova taula
- `activities` queda com a històric legacy llegible

#### Fase 4. Decisió sobre migració històrica

Només quan la capa nova estiga estable, decidir si val la pena:

- no migrar res
- migrar un subconjunt
- o migrar-ho tot

### Recomanació sobre l'històric

La recomanació actual és:

- **no fer migració completa d'entrada**
- i, si es vol netejar històric, començar només per:
  - `book`
  - `phone`
  - `review`

que són les accions que ja hem identificat com a semàntica pròpia de seguiment/contacte.

### Contracte funcional de la convivència

Perquè la migració siga defensable, la capa híbrida hauria de garantir:

- que el tutor de la col·laboració continue veient l'històric
- que el tutor acadèmic continue veient el seguiment rellevant
- que els contactes nous no es barregen de forma opaca amb `activities`
- que el comportament visible no empitjore mentre conviuen els dos models

### Conclusió del tall C

La decisió més sòlida ara mateix és:

1. taula nova per a seguiments nous
2. `activities` com a històric legacy
3. lectura híbrida temporal
4. sense migració massiva al primer tall

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
