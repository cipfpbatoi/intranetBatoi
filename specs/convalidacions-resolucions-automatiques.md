# Convalidacions automàtiques — aplicació interna de regles

Issue: #323
Status: ready_for_review

## Traça i punt d'entrada

- Domini: Convalidacions.
- Punt d'entrada: panell de Direcció, controlador específic del catàleg, peticions ja presentades i registre intern de resolució.
- Documents/specs llegits: `AGENTS.md`, `docs/agents/openspec.md`, `docs/agents/conventions.md`, `docs/agents/fct/fct-map.md`, `specs/convalidacions.md`, `specs/convalidacions-context-matricula.md` i `specs/convalidacions-documents-i-fol-catalog.md`; comentari amb el YAML de la issue #323.
- Abast: importar i consultar el catàleg YAML de regles, detectar els casos automàtics verificables amb dades disponibles i aplicar-los en la intranet com a convalidacions resoltes, conservant una còpia de les dades i de la base normativa. No generar encara informes imprimibles ni aplicar canvis a ITACA.

## Escenaris

### Escenari 1: incorporar i validar el fitxer de regles

Given una persona de Direcció en el panell de convalidacions

When incorpora una nova versió del YAML de regles

Then el sistema valida l'estructura, la versió i els camps necessaris abans d'activar-la

And conserva el catàleg actiu si el fitxer nou no és vàlid

And el fitxer es guarda en emmagatzematge privat i no es pot descarregar públicament

And el panell mostra la versió i la data d'actualització del catàleg actiu

### Escenari 2: consultar les regles i la seua aplicabilitat

Given el catàleg YAML actiu i les avaluacions d'ITACA privades

When Direcció consulta les regles d'automatització

Then cada regla mostra el mòdul destí, l'origen, les condicions, el resultat i la base normativa

And cada regla es considera habilitada per defecte si no indica el contrari

And si la regla inclou un comentari, Direcció el pot consultar com a aclariment

And indica quants casos elegibles s'han detectat o per què una regla no es pot aplicar automàticament

And una regla deshabilitada, sense resultat definit, sense base normativa o amb alguna condició no verificable no s'aplica

And no s'infereixen equivalències per semblança de noms ni es processen certificats mitjançant OCR

### Escenari 3: detectar una petició verificable d'una sol·licitud presentada

Given un alumne ha presentat una sol·licitud amb una petició de convalidació pendent

And la petició conserva el mòdul destí, l'origen aprovat i el context de matrícula validats en tramitar-la

And hi ha una regla YAML habilitada amb resultat i base normativa

When el sistema avalua la regla

Then només considera la petició elegible si coincidixen el codi del mòdul destí, la identificació de l'origen guardada i totes les condicions comprovables

And les condicions de família comparen la família d'origen amb la família autoritativa del cicle de matrícula actual
And si el codi del mòdul d'origen és el codi base de la regla més l'abreviatura XML de la família d'origen, es considera coincidència només quan l'abreviatura està guardada per al codi XML d'eixa família

And una condició `minimum_weekly_hours: 5` només es complix quan la correspondència del cicle d'origen identifica el mòdul d'anglés amb almenys 5 hores setmanals en la Comunitat Valenciana

And la nota d'origen es conserva només si `grade.mode` és `preserve`, mentre que `none` no assigna nota numèrica

And les condicions basades en certificats, documents externs o dades que no estiguen disponibles no es donen per complides

And la previsualització usa les dades ja guardades en la petició i no torna a llegir els XML acadèmics

And no es consideren alumnes sense sol·licitud presentada ni peticions en estat terminal

### Escenari 4: aplicar les convalidacions automàtiques

Given una o més peticions elegibles dins de sol·licituds ja presentades segons les regles automàtiques del YAML actiu

When Direcció confirma «Aplicar convalidacions automàtiques»

Then cada petició elegible existent passa a l'estat terminal «Resolta»

And conserva la capçalera de sol·licitud, l'origen i els documents originals presentats per l'alumne

And el registre conserva també el resultat de la regla (`AA` o `CO`) i la qualificació si correspon, sense enviar-los encara a ITACA

And l'acció mostra quants casos s'han aplicat i quins no s'han pogut aplicar, amb el motiu

And si no hi ha cap cas elegible, no es creen registres i s'informa Direcció

### Escenari 5: conservar la traçabilitat i evitar duplicats

Given una convalidació automàtica aplicada

When el YAML canvia o Direcció torna a executar l'acció

Then cada registre conserva una còpia immutable de tots els camps de la regla aplicada, la base normativa, les dades d'origen i destí i el resultat

And conserva la versió o empremta del fitxer YAML i les dades acadèmiques que justificaren la coincidència

And no es torna a aplicar una regla al mateix alumne i mòdul destí si ja hi ha una convalidació no denegada

And diverses regles coincidents amb el mateix resultat i base normativa es tracten com una sola aplicació i totes queden en la instantània

And si les regles coincidents discrepen en resultat o base normativa, el cas queda pendent i no s'aplica

And el registre continua sent consultable encara que se substituïsca el YAML o s'elimine un XML

### Escenari 6: separació de l'informe i d'ITACA

Given una convalidació automàtica aplicada en la intranet

When Direcció o Secretaria la consulta

Then no es genera encara cap informe imprimible

And no s'escriu ni modifica cap dada d'ITACA

And la generació de l'informe i la comunicació posterior a ITACA són fases separades i fora d'este abast

## Regles de negoci

- El YAML és la font de les regles, condicions i resultats; no es codifiquen equivalències paral·leles en PHP.
- `enabled` és opcional i, si falta, la regla està habilitada. Una regla habilitada s'aplica quan coincidixen el destí, l'origen i totes les condicions definides, i disposa d'un resultat i una base normativa.
- `comment` és opcional i només aporta context llegible; no canvia la decisió de coincidència.
- No s'exigix un indicador `automatic`: l'acció de Direcció aplica totes les regles habilitades que queden validades per les dades. Les regles que no coincidixen o no es poden comprovar no generen cap convalidació.
- Les regles que no siguen de convalidació (`proposal.action` diferent de `convalidate`), les deshabilitades i les que no tenen dades suficients són visibles però no s'apliquen.
- Només s'avaluen peticions que formen part d'una sol·licitud presentada per l'alumne i que continuen pendents. El mòdul destí, l'origen aprovat i el context de matrícula s'han validat en tramitar la sol·licitud i es reutilitzen des de la còpia immutable guardada en la petició.
- La previsualització i l'aplicació no recorren els XML: contrasten el catàleg YAML amb les dades persistides de cada petició. Els XML només intervenen quan l'alumne selecciona i tramita estudis aprovats al propi centre.
- Els codis de mòdul són cadenes per preservar zeros inicials. Quan la regla no conté codi d'origen, no s'accepta una coincidència aproximada: només es podrà aplicar si hi ha un identificador de catàleg inequívoc i verificable.
- Quan una regla inclou codi d'origen, s'accepta el codi literal o exactament el codi base concatenat amb `departamentos.abreviatura_xml` del departament que té el mateix `codigo_xml` que la família professional d'origen guardada en la petició. No s'accepten prefixos parcials ni sufixos desconeguts. La resolució conserva tant el codi literal com el detall de la coincidència utilitzada.
- Les condicions `same_professional_family` i `minimum_weekly_hours` només es consideren si cada dada es pot obtindre d'una font fiable i documentada. La família del destí prové de la relació autoritativa cicle-departament de la intranet, no de la jerarquia XML.
- La condició `minimum_weekly_hours: 5` significa almenys 5 hores setmanals (`>= 5`). Per als mòduls d'anglés que poden donar accés a Aprofundiment d'anglés, el cicle d'origen es valida mitjançant una correspondència de cicles de la Comunitat Valenciana que proporcionarà l'usuari.
- Les regles d'Aprofundiment d'anglés que depenen d'esta correspondència queden pendents fins que s'incorpore; això no bloqueja altres regles d'anglés que no depenguen de les hores ni d'eixa correspondència.
- Els requisits de certificat PRL, EOI o altra documentació no es deduïxen d'un XML d'avaluacions ni del nom d'un mòdul; sense evidència comprovable queden fora de l'aplicació automàtica.
- `Resolta` és un nou estat terminal per a les convalidacions automàtiques; no canvia ni substituïx els estats del flux manual, inclòs `Realitzada`.
- Cada convalidació automàtica guarda una instantània completa de la regla YAML, metadades i versió del catàleg, base normativa, dades de matrícula/destí, dades acadèmiques d'origen, criteris aplicats, estat intern `Resolta`, resultat (`AA` o `CO`) i qualificació quan corresponga.
- L'aplicació de cada cas és transaccional i idempotent: un error no deixa registres parcials ni una reexecució crea duplicats.
- L'acció resol la petició existent dins de la sol·licitud presentada; no crea cap capçalera ni cap petició nova, no altera l'origen ni els documents de l'alumne i no modifica matrícula. Tampoc interactua amb ITACA.
- Les dades i instantànies són privades i només accessibles a Direcció; no s'exposen els XML originals.
- La generació de l'informe imprimible és una fase posterior. La integració amb ITACA, inclosa l'anotació com a `CO` o `AA`, també és posterior.

## Fitxers afectats previstos

- `routes/direccion.php` — accions protegides de gestió de regles i aplicació/consulta de resolucions.
- `app/Http/Controllers/DireccionConvalidacioController.php` o controladors específics del catàleg/resolucions — punts d'entrada de Direcció.
- `app/Application/Convalidacio/` — càrrega/validació YAML, detecció de casos elegibles i generació immutable de resolucions.
- `app/Entities/` i `database/migrations/` — persistència de generacions i instantànies dels casos resolts.
- `resources/views/intranet/convalidacions/direccion/` — accés al YAML, taula de regles, elegibilitat, confirmació i consulta del registre.
- `tests/Feature/` i `tests/Unit/` — validació YAML, regles/condicions, permisos, snapshots, idempotència i absència d'efectes sobre ITACA.

## Riscos i punts a validar

- El YAML actual conté regles sense `legal_basis` (per exemple, algunes entrades de 1665 i 1708); no s'aplicaran fins que s'indique la base normativa o es deshabiliten explícitament amb `enabled: false`.
- Diverses regles identifiquen l'origen només pel nom, sense codi. Cal un mapa inequívoc contra les dades reals d'ITACA abans d'aplicar-les.
- Les regles `all_of` que exigixen PRL i les de certificats EOI no es poden verificar només amb les qualificacions XML. No s'inclouran fins que hi haja una font d'evidència explícita; OCR queda fora d'esta fase.
- Falta rebre la correspondència entre cicles d'origen i les hores setmanals dels mòduls d'anglés a la Comunitat Valenciana. Fins que s'incorpore, només les regles d'Aprofundiment d'anglés que depenen d'esta correspondència quedaran pendents.
- Les generacions automàtiques són independents del circuit de sol·licituds manuals i no equivalen encara a una anotació en ITACA.

Status: ready_for_review
