# Convalidacions — visibilitat de l'automatització en Direcció

Issue: #323
Status: ready_to_commit

## Traça i punt d'entrada

- Domini: Convalidacions.
- Punt d'entrada: llistat de Direcció `direccion/convalidacions/` i detall d'una sol·licitud.
- Documents/specs llegits: `AGENTS.md`, `docs/agents/fct/fct-map.md`, `docs/agents/conventions.md`, `docs/agents/ui/grid-datatables.md`, `specs/convalidacions.md`, `specs/convalidacions-context-matricula.md` i `specs/convalidacions-resolucions-automatiques.md`.
- Abast: fer visible en el llistat si una sol·licitud presentada té totes, algunes o cap de les seues peticions elegibles segons el YAML actiu, i identificar al detall la regla candidata o aplicada per a cada petició. No canvia els criteris d'elegibilitat ni l'acció que resol les peticions.

## Escenaris

### ✅ Escenari 1: accés a l'automatització des del panell de convalidacions

Given una persona de Direcció en `direccion/convalidacions/`
When consulta el llistat de sol·licituds presentades
Then té un accés visible a la gestió de les regles automàtiques
And veu un botó «Aplicar convalidacions automàtiques» per executar les peticions elegibles del catàleg actiu
And cada sol·licitud mostra un indicador resum de l'elegibilitat de les seues peticions segons el catàleg YAML actiu
And la consulta de la pàgina només previsualitza els casos i no resol cap petició

### ✅ Escenari 2: confirmació de l'aplicació des del panell principal

Given hi ha una o més peticions elegibles en sol·licituds ja presentades
When Direcció prem «Aplicar convalidacions automàtiques» des del llistat principal
Then abans d'executar l'acció veu un diàleg que indica quantes peticions es resoldran automàticament
And el diàleg avisa que les peticions no elegibles continuaran pendents per a revisió manual
And només en confirmar s'envia l'acció explícita d'aplicar les peticions elegibles
And en cancel·lar no canvia cap petició
And si no hi ha peticions elegibles, el botó queda deshabilitat o s'informa clarament que no hi ha cap cas per aplicar

### ✅ Escenari 3: sol·licitud completament automàtica o parcialment automàtica

Given una sol·licitud presentada amb més d'una petició pendent
And una o més peticions coincidixen amb regles habilitades, verificables i aplicables del YAML
When Direcció consulta el llistat o el detall de la sol·licitud
Then si totes les peticions pendents són elegibles, la sol·licitud s'identifica com a «Automàtica»
And si només una part de les peticions pendents és elegible, s'identifica com a «Parcialment automàtica» i es mostra quantes es poden aplicar del total pendent
And Direcció pot aplicar les peticions elegibles encara que les altres de la mateixa sol·licitud requerisquen revisió manual
And les peticions no elegibles continuen pendents i no impedixen aplicar les que sí que ho són

### ✅ Escenari 4: cap petició elegible

Given una sol·licitud presentada sense cap petició pendent que coincidisca amb una regla aplicable
When Direcció consulta el llistat o el detall
Then la sol·licitud s'identifica com a «Revisió manual» o equivalent, sense suggerir que hi ha una regla aplicable
And cap petició canvia d'estat per haver-se consultat la previsualització

### ✅ Escenari 5: regla corresponent a cada petició

Given Direcció consulta una sol·licitud amb peticions elegibles, no elegibles o ja resoltes automàticament
When obri el detall de la sol·licitud
Then cada petició elegible pendent identifica la regla del YAML que li correspon i indica que està disponible per a aplicar
And cada petició resolta automàticament identifica la regla realment aplicada, la versió del catàleg, el resultat i la base normativa conservats en la petició
And una petició sense coincidència no mostra cap regla com si li aplicara
And el canvi posterior del YAML no modifica la regla ni les dades ja guardades en una resolució automàtica

## Regles de negoci

- L'elegibilitat s'obté de la previsualització del servei de regles existent i de les dades persistides en les peticions presentades; la vista no torna a llegir els XML ni reimplementa les condicions YAML.
- Només es compten les peticions pendents sobre les quals l'acció automàtica podria actuar. Les peticions terminalment resoltes, denegades o ja aplicades no es compten com a pendents elegibles.
- «Automàtica» significa que totes les peticions pendents de la sol·licitud són elegibles; «Parcialment automàtica» significa que almenys una, però no totes, ho són; «Revisió manual» significa que cap petició pendent és elegible.
- Una sol·licitud amb peticions automàtiques ja resoltes i peticions pendents no elegibles mostra l'estat de les pendents i conserva en cada petició resolta el detall immutable de la regla aplicada.
- L'aplicació continua sent una acció explícita de Direcció; carregar o refrescar el llistat/detall mai no canvia estats.
- El botó del llistat principal executa l'aplicació global ja existent: resol totes les peticions actualment elegibles segons el YAML actiu, no només una sol·licitud seleccionada.
- El diàleg de confirmació informa del nombre calculat en la previsualització. En confirmar, el servidor torna a avaluar les regles i aplica només les peticions que continuen elegibles en eixe moment.
- Una regla només es presenta com a aplicable si la previsualització actual la considera elegible. Les coincidències amb resultats o bases normatives incompatibles no es presenten com a aplicables.
- Les regles candidates i les regles ja aplicades es distingixen visualment i textualment; no s'ha de confondre una previsualització amb una resolució efectiva.
- No es modifica l'estat de les peticions no elegibles quan s'apliquen les elegibles de la mateixa sol·licitud.

## Fitxers afectats previstos

- `app/Http/Controllers/DireccionConvalidacioController.php` — proporcionar el resum d'elegibilitat al llistat sense duplicar l'avaluació.
- `app/Application/Convalidacio/ConvalidacioAutomaticaService.php` — exposar, si cal, un resum per sol·licitud a partir de la mateixa previsualització.
- `app/Http/Controllers/DireccionConvalidacioReglesController.php` — reutilitzar l'acció d'aplicació existent des del formulari del llistat.
- `resources/views/intranet/convalidacions/direccion/index.blade.php` — mostrar els indicadors i el botó d'aplicació amb confirmació.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` — mostrar per petició la regla candidata o la regla aplicada.
- `tests/Feature/ConvalidacioFlowTest.php` — cobrir sol·licituds completes, parcials, manuals, regla per petició i absència d'efectes laterals en consultar.

## Riscos i decisions

- El resum s'ha de calcular per petició i agregar-se per sol·licitud; tractar tota la sol·licitud com una única coincidència podria impedir el cas parcial que es vol permetre.
- Cal mantindre clara la diferència entre «elegible per a aplicar ara» i «ja resolta automàticament», especialment després d'executar l'acció o canviar el YAML.
- El llistat actual carrega totes les peticions i no pagina. Si la previsualització de totes les sol·licituds afecta el rendiment, la implementació haurà d'agrupar les coincidències en una sola passada i no executar una previsualització completa per cada sol·licitud.
- La pàgina existent `/direccion/convalidacions/regles` continuarà sent la gestió del YAML. Tant eixa pàgina com el llistat principal oferiran l'acció explícita d'aplicar, amb confirmació; així l'acció queda visible sense llevar la pàgina de gestió detallada.

Status: ready_to_commit
