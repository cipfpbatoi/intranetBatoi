# Convalidacions — documents de suport i catàleg de FOL LOGSE

Issue: #323
Status: ready_for_review

## Objectiu

Reduir opcions i preguntes especials del formulari, permetre que l'alumnat aporte i identifique documentació de suport, i detectar FOL LOGSE del propi centre mitjançant el codi oficial facilitat.

## Escenaris

### Escenari 1: catàleg de mòduls FOL LOGSE

Given els codis de FOL LOGSE aportats per l'usuari
When s'instal·la o actualitza l'aplicació
Then existeix una taula de referència amb `codigo`, `modul`, `cicle`, `nivell` i `sistema`
And els codis es conserven com a text, inclosos els zeros inicials
And s'hi carreguen les 20 files del llistat proporcionat

| codigo | modul | cicle | nivell | sistema |
| --- | --- | --- | --- | --- |
| 009001 | Formació i orientació laboral | Desenvolupament d'Aplicacions Informàtiques | GS | LOGSE |
| 010001 | Formació i orientació laboral | Administració i Finances | GS | LOGSE |
| 012602 | Formació i orientació laboral | Gestió Administrativa | GM | LOGSE |
| 016001 | Formació i orientació laboral | Comerç | GM | LOGSE |
| 028001 | Formació i orientació laboral | Cures Auxiliars d'Infermeria | GM | LOGSE |
| 035602 | Formació i orientació laboral | Comerç Internacional | GS | LOGSE |
| 042602 | Formació i orientació laboral | Sistemes de Regulació i Control Automàtics | GS | LOGSE |
| 045001 | Formació i orientació laboral | Animació Sociocultural | GS | LOGSE |
| 049001 | Formació i orientació laboral | Muntatge i Manteniment d'Instal·lacions de Fred, Climatització i Producció de Calor | GM | LOGSE |
| 051001 | Formació i orientació laboral | Estètica | GS | LOGSE |
| 055001 | Formació i orientació laboral | Dietètica | GS | LOGSE |
| 058001 | Formació i orientació laboral | Imatge | GS | LOGSE |
| 060001 | Formació i orientació laboral | Desenvolupament de Projectes Urbanístics i Operacions Topogràfiques | GS | LOGSE |
| 061001 | Formació i orientació laboral | Sistemes de Telecomunicació i Informàtics | GS | LOGSE |
| 070602 | Formació i orientació laboral | Documentació Sanitària | GS | LOGSE |
| 072602 | Formació i orientació laboral | Integració Social | GS | LOGSE |
| 082602 | Formació i orientació laboral | Gestió i Organització dels Recursos Naturals i Paisatgístics | GS | LOGSE |
| 206602 | Formació i orientació laboral | Caracterització | GM | LOGSE |
| 239602 | Formació i orientació laboral | Prevenció de Riscos Professionals | GS | LOGSE |
| 240602 | Formació i orientació laboral | Atenció Sociosanitària | GM | LOGSE |

### Escenari 2: classificació de FOL del propi centre

Given l'alumne demana convalidar IPE I i selecciona un mòdul superat al propi centre
When el codi del mòdul coincideix exactament amb el catàleg FOL LOGSE
Then el formulari preselecciona LOGSE en la declaració obligatòria de l'alumne
And conserva el nivell i el cicle del catàleg per a la revisió de Direcció
And l'alumne confirma que va cursar eixe mòdul sota LOGSE
And exigeix que aporte el certificat PRL com a document de suport identificat per l'alumne

### Escenari 3: documentació en estudis d'un altre centre

Given l'alumne selecciona «Estudis o certificats acadèmics d'un altre centre»
When prepara la petició
Then pot adjuntar entre un i tres documents
And cada fitxer inclou un camp de text obligatori que n'indica el tipus o contingut
And es manté la declaració responsable existent
And l'acceptació única de la declaració responsable es demana en el resum final i cobreix tots els fitxers adjunts de la petició

### Escenari 4: l'alumne declara si els estudis d'origen són LOGSE per a IPE I

Given l'alumne demana convalidar IPE I amb qualsevol modalitat d'origen
When indica quin mòdul o estudis previs aporta
Then la interfície li indica que revise el certificat acadèmic o expedient per saber si va cursar el mòdul segons LOGSE
And l'alumne declara obligatòriament sí o no, i es guarda la resposta en la petició
And si el codi del resultat propi coincideix amb el catàleg FOL LOGSE, la dada del catàleg es mostra perquè l'alumne la confirme
And Direcció pot consultar la declaració junt amb la resta de dades d'origen

### Escenari 5: FOL LOGSE d'un altre centre per a IPE I

Given l'alumne demana IPE I i aporta estudis d'un altre centre
When declara que l'origen és FOL LOGSE
Then, per a IPE I, indica si el mòdul d'origen és FOL i declara si el va cursar segons LOGSE
And pot identificar separadament el certificat acadèmic i el certificat PRL dins del límit de tres documents
And la petició no es tramita si falta algun document requerit per al cas
And Direcció revisa el contingut, incloses les 30 hores de GM o 50 hores de GS

### Escenari 6: retirada de l'opció PRL independent

Given l'alumne crea una petició nova
When consulta les modalitats d'origen
Then «Prevenció de riscos (LOGSE)» no apareix com a modalitat independent
And les peticions antigues amb eixe origen continuen llegibles per Direcció

### Escenari 7: prova independent amb Higiene del Medi Hospitalari

Given l'alumne està matriculat en un grup on cursa el mòdul 028503 «Higiene del medi hospitalari i neteja del material»
When crea una petició per a convalidar eixe mòdul amb la documentació d'origen corresponent
Then el mòdul destí apareix perquè forma part de la matrícula vigent
And la petició es pot tramitar pel flux general de convalidacions
And esta prova no canvia ni condiciona les regles específiques de FOL LOGSE per a IPE I

### Escenari 8: consulta i cicle de vida dels adjunts

Given una petició conté diversos documents
When l'alumne revisa el resum o Direcció consulta la petició
Then cada document es mostra amb el tipus indicat i es pot descarregar per separat amb les autoritzacions actuals
And els fitxers continuen en emmagatzematge privat
And eliminar una petició elimina tots els seus adjunts
And els documents existents abans d'esta funcionalitat continuen accessibles

## Regles de negoci

- El catàleg conté exactament els codis i dades proporcionats en esta petició; `codigo` és text i és la clau de coincidència exacta amb el codi del mòdul d'origen.
- Per a IPE I, l'alumne és responsable de revisar la documentació acadèmica i declarar si va cursar els estudis d'origen segons LOGSE; esta declaració queda registrada per a Direcció. Per a altres mòduls destí no es pregunta ni es guarda esta dada.
- No s'inferix LOGSE per semblança de noms ni de cicles. En peticions d'IPE I amb resultats del propi centre, només el codi que coincidix exactament amb el catàleg FOL permet preseleccionar LOGSE; l'alumne sempre ho confirma.
- La declaració afirmativa de LOGSE, per si sola, no activa ni determina cap requisit de convalidació ni de documentació addicional.
- La regla especial de PRL s'aplica únicament a IPE I (1709) quan l'origen és FOL LOGSE. En FOL LOGSE extern, cal conservar l'evidència acadèmica i el PRL en fitxers diferenciats; els textos descriptius els aporta l'alumne i Direcció en comprova el contingut.
- En estudis d'un altre centre per a IPE I, l'alumne identifica explícitament si l'origen és FOL; no s'inferix esta dada dels noms lliures dels documents.
- En estudis d'un altre centre continua sent obligatori aportar almenys un document acadèmic i acceptar una declaració responsable que cobreix tots els fitxers adjunts. El màxim total és de tres fitxers per petició individual.
- Si s'adjunten documents en qualsevol modalitat, inclosos els estudis del propi centre, la declaració responsable es demana una sola vegada en el resum final i cobreix tots els fitxers de la sol·licitud; en el propi centre sense adjunts no es demana.
- Per a les altres modalitats, els adjunts són opcionals i tenen el mateix màxim de tres fitxers. Si s'adjunta un fitxer, el seu text descriptiu és obligatori.
- Cada fitxer i descripció formen una unitat; no s'accepten més de tres ni descripcions buides, i es mantenen els límits actuals de tipus i mida.
- L'opció `prl_logse` deixa d'estar disponible per a noves peticions, però els registres antics no s'eliminen ni perden una etiqueta llegible.
- El mòdul destí només es pot sol·licitar si pertany a la matrícula vigent de l'alumne. No s'afig una excepció de producció per a proves.
- Direcció continua sent responsable de valorar la documentació i resoldre manualment la convalidació.

## Traça i punt d'entrada

- Domini: Convalidacions.
- Punt d'entrada: `ConvalidacioQueryService::modulsActuals`, `ResultatsAcademicsXmlService`, `ConvalidacioService::validarItem`, formulari de l'alumne i descàrregues de Direcció.
- Documents llegits: `AGENTS.md`, `docs/agents/openspec.md`, `docs/agents/conventions.md`, `docs/agents/fct/fct-map.md`, `specs/convalidacions-fol-logse-ipe.md`.
- Abast: catàleg FOL LOGSE aplicable només a IPE I, fins a tres adjunts etiquetats per petició individual, retirada d'una modalitat redundant, preservació de documents antics i prova independent amb el mòdul de Higiene.

## Fitxers previstos

- `database/migrations/` — taula del catàleg i càrrega dels 20 registres; taula d'adjunts i preservació dels documents actuals.
- `app/Application/Convalidacio/ResultatsAcademicsXmlService.php` — coincidència exacta del codi d'origen contra el catàleg.
- `app/Application/Convalidacio/ConvalidacioService.php` i `app/Entities/Convalidacio.php` — límits, persistència, privacitat i eliminació de múltiples documents.
- `app/Application/Convalidacio/ConvalidacioQueryService.php` — disponibilitat basada en matrícula vigent.
- `resources/views/intranet/convalidacions/alumno/` i `resources/views/intranet/convalidacions/direccion/` — formulari, resum, consulta i descàrregues.
- `app/Http/Controllers/AlumnoConvalidacioController.php`, `app/Http/Controllers/DireccionConvalidacioController.php`, `routes/alumno.php`, `routes/direccion.php` — validació i descàrregues protegides.
- `tests/Feature/ConvalidacioFlowTest.php` i tests unitaris del lector XML — límit d'adjunts, retenció, accés i coincidència de codis.

## Riscos

- La petició anterior ja ha afegit `document_prl_*` a la base de dades local. La nova migració ha de migrar o exposar eixos fitxers en la nova estructura, no eliminar-los ni duplicar-los de manera visible.
- Les peticions existents només tenen camps per al document principal i el PRL; cal preservar ambdós quan es passe a adjunts múltiples.
- El mòdul 028503 existeix en la intranet com «Higiene del medi hospitalari i neteja de material» i està associat a cinc grups. La disponibilitat real continuarà depenent de la matrícula del compte de prova.
- Si el compte de prova no està matriculat en un grup que curse el mòdul 028503, el sistema no l'ha d'oferir artificialment; cal usar un compte amb eixa matrícula.
- La substitució de documentació quan Direcció demana correccions ha de mantindre la vinculació del fitxer amb la seua descripció i no ha d'esborrar els altres adjunts.
