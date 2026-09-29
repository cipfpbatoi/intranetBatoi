# Convalidacions — resultats acadèmics des d'XML privat

Issue: #323
Status: ready_for_review

## Relació amb la spec principal

Este canvi substituïx el comportament de «propi centre» descrit en l'escenari 3 de `specs/convalidacions.md`. En el cas general, la convalidació es justifica amb un mòdul aprovat al centre, no amb un cicle complet.

El cas especial en què un mòdul es convalida per haver superat un cicle queda expressament fora d'esta proposta fins que es definisquen les seues regles.

## Escenaris

### ✅ Escenari 1: consulta dels mòduls aprovats al propi centre

Given un alumne autenticat que prepara una petició amb origen «Estudis cursats al propi centre»
And Direcció ha incorporat una o més avaluacions anuals d'ITACA en format XML vàlid
When el sistema busca el seu NIA en tots els XML disponibles
Then mostra únicament els resultats amb nota igual o superior a 5 en l'avaluació final ordinària (`FI`) o extraordinària (`EX`)
And cada opció identifica el mòdul aprovat i mostra també el cicle i la nota
And la convocatòria es conserva internament però no es mostra a l'alumnat ni a Direcció
And no mostra el nom, la ruta ni cap altra dada identificativa del fitxer XML
And si el mateix mòdul apareix en anys o cicles diferents, cada resultat es mostra com una opció diferenciada

### ✅ Escenari 2: selecció i validació del mòdul origen

Given un alumne que ha seleccionat un mòdul destí de la matrícula vigent
When selecciona un resultat aprovat del propi centre com a «Mòdul superat»
Then pot afegir la petició sense adjuntar document ni acceptar la declaració responsable
And el backend torna a comprovar que el resultat seleccionat pertany al NIA autenticat i continua present en els XML privats
And no confia en codis, notes, convocatòries ni literals enviats directament pel navegador
And una selecció manipulada, suspesa o pertanyent a un altre alumne és rebutjada

### ✅ Escenari 3: còpia immutable del resultat aportat

Given una petició vàlida basada en un mòdul aprovat al propi centre
When l'alumne tramita la sol·licitud
Then la petició guarda una còpia del codi i nom del mòdul origen, el codi i nom del cicle, la nota i la convocatòria
And la consulta posterior de la petició no depén de tornar a llegir l'XML original
And l'alumne i Direcció poden consultar les dades acadèmiques copiades, però no el fitxer XML d'origen
And si Direcció elimina o substituïx posteriorment l'XML, les sol·licituds ja tramitades conserven les dades originals

### ✅ Escenari 4: incorporació privada d'una avaluació anual d'ITACA

Given una persona amb rol de Direcció o administració
When puja el fitxer XML d'una avaluació d'ITACA
Then el sistema valida l'extensió `.xml`, no depén del MIME informat pel navegador o el servidor, comprova la mida configurada i valida l'estructura mínima del contingut abans de conservar-lo
And el processa sense permetre xarxa ni resolució d'entitats externes
And el guarda en emmagatzematge privat fora de `public/` i del control de versions
And una persona sense rol de Direcció ni administració no pot accedir a l'operació ni al llistat
And un fitxer invàlid no substituïx ni deixa parcialment guardat cap fitxer anterior

### ✅ Escenari 5: gestió des de Direcció sense exposició dels documents

Given una persona amb rol de Direcció o administració i avaluacions d'ITACA emmagatzemades
When obri el gestor d'avaluacions d'ITACA
Then veu només les metadades necessàries per identificar cada càrrega
And pot pujar, substituir o eliminar un XML
But no pot editar el contingut ni descarregar-lo des del navegador
And no existeix cap ruta pública o autenticada de descàrrega dels XML
And en eliminar un XML deixa de participar en consultes futures, però no altera peticions ja tramitades

### ✅ Escenari 6: absència o fallada de les fonts acadèmiques

Given un alumne que selecciona «Estudis cursats al propi centre»
When no hi ha XML disponibles, cap XML conté resultats aprovats per al seu NIA o una font no es pot processar
Then el formulari no inventa ni completa resultats amb altres dades de l'aplicació
And informa que no s'han trobat mòduls aprovats disponibles per a seleccionar
And no exposa noms de fitxer, rutes internes, dades d'altres alumnes ni detalls tècnics de l'error
And la resta d'orígens de convalidació continua disponible

### Escenari 7: pujada d'una o més avaluacions des de Direcció

Given una persona amb rol de Direcció i fitxers d'avaluació XML
When selecciona un o més fitxers en el gestor, fins al màxim configurat per pujada
Then pot completar la pujada amb un sol fitxer o amb diversos
And el sistema valida i processa cada fitxer independentment
And incorpora els XML vàlids i mostra quins fitxers han fallat i el motiu de cada error
And si cap fitxer és vàlid, no incorpora cap i mostra els errors

### Escenari 8: superació del màxim per pujada

Given una persona amb rol de Direcció que selecciona més fitxers que el màxim configurat
When intenta enviar la pujada
Then la interfície l'avisa abans d'enviar-la i impedeix l'enviament
And el servidor rebutja qualsevol petició que supere el màxim

## Regles de negoci

- La unitat ordinària aportada com a estudi previ és un mòdul aprovat concret; el cicle és informació contextual.
- Un resultat està aprovat quan `FI >= 5`; si no, pot estar aprovat quan `EX >= 5`. La convocatòria que acredita l'aprovat es conserva internament per a traçabilitat.
- Els resultats repetits en fitxers, anys o cicles diferents no es fusionen automàticament.
- El servidor valida sempre el resultat contra el NIA de l'alumne autenticat abans de tramitar.
- Les peticions guarden una còpia immutable de les dades acadèmiques necessàries i no una dependència viva amb el fitxer.
- Els noms dels XML només es mostren dins del gestor restringit de Direcció i les rutes no s'exposen mai.
- El gestor permet pujar, llistar metadades i eliminar; no permet substituir, editar ni descarregar el contingut.
- Els XML no es desen en un disc públic, no tenen URL de descàrrega i queden fora del control de versions.
- El MIME reportat durant la pujada no és fiable ni uniforme; l'extensió i l'estructura real de l'avaluació XML determinen si s'accepta.
- Els tests del lector utilitzen XML sintètic autocontingut; no incorporen exportacions reals ni dades personals.
- El cas especial de convalidació per cicle no forma part d'este canvi.

## Fitxers previstos

- `app/Application/Convalidacio/ResultatsAcademicsXmlService.php` — lectura segura dels XML i consulta de resultats per NIA.
- `app/Application/Convalidacio/ConvalidacioQueryService.php` — exposició dels mòduls aprovats al formulari.
- `app/Application/Convalidacio/ConvalidacioService.php` — revalidació de la selecció i còpia immutable en tramitar.
- `app/Entities/Convalidacio.php` — atributs del mòdul origen i eliminació de la relació ordinària amb cicle.
- `app/Http/Controllers/AlumnoConvalidacioController.php` — dades i validació de la selecció de mòdul aprovat.
- `app/Http/Controllers/DireccionConvalidacioXmlController.php` — càrrega, substitució i eliminació restringides.
- `routes/direccion.php` — rutes del gestor privat integrades en convalidacions.
- `resources/views/intranet/convalidacions/alumno/create.blade.php` — selector dels resultats aprovats.
- `resources/views/intranet/convalidacions/alumno/show.blade.php` — consulta de la còpia acadèmica.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` — consulta de la còpia acadèmica per Direcció.
- `resources/views/intranet/convalidacions/direccion/xml.blade.php` — gestor de metadades i operacions permeses.
- `config/filesystems.php` i `config/convalidacions.php` — disc privat i límit de càrrega.
- `.gitignore` — exclusió defensiva del directori d'XML, encara que `storage/` ja està ignorat.
- `database/migrations/*_replace_ciclo_origen_with_modulo_snapshot.php` — substitució explícita del model de dades actual.
- `database/migrations/*_remove_convalidacions_xml_main_menu.php` — retirada de l'accés principal incorrecte.
- `tests/Unit/Application/Convalidacio/ResultatsAcademicsXmlServiceTest.php` — FI, EX, suspesos, jerarquia de cicle, múltiples fitxers i XML invàlid.
- `tests/Feature/ConvalidacioFlowTest.php` — selecció, manipulació, còpia immutable i consulta posterior.
- `tests/Feature/DireccionConvalidacioXmlTest.php` — autorització, validació, substitució, eliminació i absència de descàrrega.

## Riscos

- Un mateix codi de mòdul pot aparéixer en diferents cicles o anys; la selecció necessita una identitat opaca que distingisca cada resultat sense exposar el fitxer.
- Les exportacions contenen dades personals de molts alumnes; els errors, logs i tests no han de copiar fragments dels XML.
- Llegir tots els XML en cada visita pot créixer en cost amb els anys; la primera versió ha de mantindre el lector encapsulat per poder afegir índex o memòria cau sense canviar el contracte.
- La migració actual a `ciclo_origen_id` ja pot estar aplicada en algun entorn; la correcció ha de ser una nova migració segura i no una reescriptura destructiva de l'historial.
- El reemplaçament d'un XML ha de ser atòmic per evitar que una càrrega fallida elimine l'única còpia vàlida.
- No es pot assumir que els codis de mòdul dels XML tinguen sempre una fila vigent en `modulos`; les dades acadèmiques s'han de conservar com una còpia, no com una clau forana obligatòria.
