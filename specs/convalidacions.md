# Convalidacions — MVP de sol·licitud i gestió manual

Issue: #323
Status: ready_to_commit

## Abast

Primera versió del procediment perquè l'alumnat presente una sol·licitud amb una o més peticions de convalidació de mòduls i perquè Direcció les revise i resolga manualment.

El circuit cobert és: crear la composició temporal de la sol·licitud, afegir-hi peticions, confirmar-ne el resum, tramitar-la, revisar cada petició, requerir una correcció documental o resoldre-la i consultar-ne el resultat.

No hi ha esborranys persistents: la capçalera i les peticions només es guarden quan l'alumne confirma «Tramitar sol·licitud».

Este domini és independent de les convalidacions o exempcions d'FCT i no reutilitza `FctConvalidacion`.

## Escenaris

### Escenari 1: consulta privada i inici d'una sol·licitud

Given un alumne autenticat
When obri el panell «Convalidar»
Then veu únicament les seues sol·licituds amb la data de tramitació, els mòduls inclosos, l'estat de cada petició i les observacions de Direcció que existisquen
And pot iniciar una «Nova sol·licitud»
And si intenta consultar o descarregar una sol·licitud, petició o document d'un altre alumne mitjançant un identificador manipulat, el sistema denega l'accés

### Escenari 2: composició d'una sol·licitud amb diversos mòduls

Given un alumne amb matrícula vigent que està preparant una nova sol·licitud
When afig dos o més mòduls destí vàlids mitjançant «Afegir mòdul a convalidar»
Then cada mòdul queda representat com una petició independent dins de la mateixa composició
And pot eliminar qualsevol petició abans de tramitar
And no pot afegir dues vegades el mateix mòdul destí
And abans de guardar veu un resum de totes les peticions incloses
And el formulari comença buit i només mostra les dades de la petició que està component, no una targeta completa per cada mòdul matriculat
And en afegir un altre mòdul el resum conserva les peticions anteriors i confirma visualment quantes n'hi ha
And la composició es mostra en una taula i el resum final s'obri en un diàleg abans de tramitar

### Escenari 3: petició basada en un mòdul aprovat al propi centre

Given un alumne que ha seleccionat un mòdul destí de la seua matrícula vigent
When indica que aporta estudis cursats al propi centre
Then pot seleccionar com a «Mòdul superat» un resultat aprovat que conste en els XML acadèmics privats
And veu el mòdul, el cicle i la nota, però no la convocatòria ni el fitxer XML d'origen
And el mòdul destí i el resultat origen queden identificats separadament
And pot afegir la petició sense document adjunt
And el sistema valida en backend que el destí pertany a la matrícula vigent i que el resultat aprovat correspon realment a l'alumne autenticat
And la petició guarda una còpia immutable de les dades acadèmiques seleccionades

### Escenari 4: petició basada en estudis externs o certificats

Given un alumne que ha seleccionat un mòdul destí de la seua matrícula vigent
When selecciona «Estudis o certificats acadèmics d'un altre centre», «Certificat d'escola oficial d'idiomes» o «Prevenció de riscos (LOGSE)»
Then ha d'acceptar la declaració responsable
And ha d'adjuntar un únic fitxer PDF, JPG, JPEG o PNG que complisca l'extensió, el tipus MIME i el límit de mida configurat
And si falta algun requisit o el tipus no és vàlid, el sistema no permet afegir ni tramitar la petició i mostra un error específic
And si tot és vàlid, la petició es pot afegir i el document queda destinat a emmagatzematge privat
And el selector d'estudi previ no es mostra en estos casos

### Escenari 5: titulació que s'ha de tramitar per Secretaria

Given un alumne que està afegint un mòdul a convalidar
When selecciona «Títol universitari o FP1/FP2»
Then el sistema informa que este tipus de convalidació s'ha de consultar amb Secretaria
And no crea ni afig cap petició a la sol·licitud
And no mostra ni exigix document adjunt ni declaració responsable

### Escenari 6: una petició oberta no es pot duplicar

Given un alumne que ja ha sol·licitat la convalidació d'un mòdul
When la petició anterior està en qualsevol estat diferent de `Denegada`
Then el mòdul no apareix entre els destins disponibles d'una nova sol·licitud
And el backend rebutja igualment un identificador manipulat que intente tornar-lo a sol·licitar
But si l'última petició aplicable està `Denegada`, l'alumne pot tornar a sol·licitar el mòdul
And si la petició es resol com a `Acceptada`, el mòdul continua bloquejat encara que la baixa de matrícula encara no s'haja sincronitzat

### Escenari 7: tramitació transaccional i idempotent

Given una composició amb una o més peticions vàlides
When l'alumne confirma «Sí, presentar» després de revisar la sol·licitud
Then es crea una única capçalera associada a l'alumne autenticat
And es creen totes les peticions associades inicialment en estat `Enviada`
And l'operació és transaccional i no deixa dades parcials si falla qualsevol part
And un doble clic o un reintent de la mateixa tramitació no crea capçaleres ni peticions duplicades
And el sistema mostra una confirmació de la tramitació
But si ocorre una fallada tècnica inesperada, l'alumne torna al formulari amb un missatge segur i sense dades parcials

### Escenari 8: revisió manual per Direcció

Given una persona amb perfil de Direcció i una petició que no està en estat terminal
When consulta la sol·licitud, descarrega un adjunt autoritzat o canvia manualment l'estat de la petició
Then pot veure l'alumne, el mòdul destí, l'origen, la data, l'estat, l'observació i la resta de peticions de la sol·licitud
And pot assignar qualsevol estat admés
And l'observació és obligatòria per a `Denegada`, `Revisar documentació` i `Aportar documentació original a Secretaria`
And es guarden en la petició l'última persona responsable, la data del canvi i l'observació
And el canvi no altera l'estat de les altres peticions de la mateixa sol·licitud
And el llistat de mòduls permet filtrar per l'estat i l'origen de cada petició

### ✅ Escenari 8a: iniciar la revisió d'una sol·licitud

Given una sol·licitud conté peticions en diferents estats
When Direcció selecciona «Revisar» des del llistat de sol·licituds
Then només les peticions que estaven en estat `Enviada` passen a `En procés`
And les peticions en qualsevol altre estat conserven l'estat, les observacions i la traçabilitat existents
And s'envia a l'alumne una notificació per cada petició que haja canviat

### ✅ Escenari 8b: identificar les sol·licituds pendents de Direcció

Given Direcció consulta el llistat de sol·licituds
When activa el filtre «Només amb acció pendent de Direcció»
Then només veu sol·licituds amb almenys una petició `Enviada` o `En procés`
And cada fila mostra el recompte total de mòduls pendents i un hint accessible amb el desglossament entre revisió i resolució

### Escenari 9: correcció documental i petició terminal

Given una petició externa en estat `Revisar documentació`
When l'alumne consulta l'observació de Direcció, substituïx únicament el fitxer adjunt i confirma
Then el nou document queda en emmagatzematge privat
And només eixa petició torna a `En procés`
And no es poden modificar el mòdul destí, l'origen, el tipus ni altres dades ja tramitades
But si la petició està en estat `Acceptada (Convalidada)`, tant l'alumne com Direcció només poden consultar-la i no poden modificar-ne l'estat, les dades ni el document

### ✅ Escenari 9a: l'alumne pot actualitzar documents mentre la petició està Enviada

Given una petició de l'alumne està en estat `Enviada`
When l'alumne substituïx un document adjunt
Then es conserva l'estat `Enviada` i només canvia el document seleccionat
And l'alumne no pot modificar la documentació en cap altre estat llevat de `Revisar documentació`

### ✅ Escenari 10: accés de proves bloquejat per Direcció

Given una persona de Direcció al panell de convalidacions
When activa el bloqueig temporal per a proves
Then totes les rutes d'alumnat de convalidacions requerixen la contrasenya de proves, incloses les accions i descàrregues
And l'alumnat que encara no haja introduït la contrasenya és redirigit a una pantalla d'accés
And només una contrasenya correcta concedix accés durant la sessió actual
And una contrasenya incorrecta no concedix accés i mostra un error
And canviar l'estat del bloqueig invalida els accessos de prova concedits en el cicle anterior
And quan Direcció desactiva el bloqueig, l'alumnat pot accedir sense contrasenya

### ✅ Escenari 11: eliminació explícita d'una sol·licitud de prova

Given una persona de Direcció que consulta una sol·licitud
When selecciona «Eliminar sol·licitud de prova»
Then el sistema demana confirmació i adverteix que només s'ha d'eliminar si és una prova perquè es perd tota la traçabilitat
And si confirma, s'eliminen la capçalera, totes les peticions i els documents privats associats
And si cancel·la, no s'elimina cap dada
And cap persona que no siga de Direcció pot executar l'acció, encara que envie una petició directa

### ✅ Escenari 12: resum de l'automatització al llistat de Direcció

Given una persona de Direcció consulta les sol·licituds presentades
When hi ha peticions pendents elegibles segons el YAML actiu
Then el llistat mostra 🤖 si totes les peticions pendents són elegibles, ✍️ si totes requerixen revisió manual, o 🤖✍️ si la resolució és mixta
And el hint accessible de cada icona n'explica el significat i els recomptes automàtics i manuals
And si no hi ha peticions pendents, la cel·la d'automatització queda buida
And consultar o refrescar el llistat no resol cap petició

### ✅ Escenari 12a: resumir l'acció pendent de Direcció

Given una persona de Direcció consulta el llistat de sol·licituds
When una sol·licitud té mòduls `Enviada` o `En procés`
Then la cel·la mostra el recompte total dins d'una etiqueta destacada, sense desglossament textual visible
And un hint accessible explica quants mòduls estan `Enviada` i quants `En procés`
And si no hi ha mòduls pendents d'acció, la cel·la queda buida
And el botó de la fila continua dient «Revisar»

### ✅ Escenari 12b: aplicar immediatament el filtre del llistat

Given Direcció està veient el llistat de sol·licituds
When canvia el desplegable entre totes les sol·licituds i les que tenen acció pendent
Then el llistat s'actualitza immediatament sense cap botó «Filtrar»

### ✅ Escenari 13: aplicar les peticions elegibles des del llistat

Given hi ha una o més peticions elegibles en sol·licituds ja presentades
When Direcció prem «Aplicar convalidacions automàtiques» en el llistat principal
Then confirma l'acció després de veure quantes peticions s'aplicaran
And només en confirmar s'apliquen les peticions que continuen sent elegibles
And les peticions no elegibles de la mateixa sol·licitud continuen pendents per a revisió manual
And si cancel·la, cap petició canvia d'estat

### ✅ Escenari 14: identificar la regla candidata o aplicada

Given Direcció consulta el detall d'una sol·licitud
When una petició és elegible o ja s'ha resolt automàticament
Then veu la regla candidata o la regla realment aplicada, respectivament
And les resolucions aplicades conserven visible la versió, el resultat i la base normativa guardats
And una petició sense regla coincident no es presenta com a automàtica

### ✅ Escenari 15: control d'accés amb interruptor

Given una persona de Direcció consulta el panell de convalidacions
When activa o desactiva el control d'accés de proves
Then un interruptor accessible reflectix l'estat actual i el canvi conserva el mecanisme de bloqueig existent
And quan l'accés està bloquejat es mostra un avís groc explícit
And quan l'accés està obert desapareix l'avís de bloqueig

### ✅ Escenari 16: mostrar la contrasenya només a Direcció

Given la contrasenya de proves efectiva està configurada al servidor
When Direcció consulta el panell protegit
Then veu eixa contrasenya per poder comunicar-la a l'alumnat mentre dure el bloqueig
But l'alumnat no la rep en les seues pàgines, missatges o respostes

### ✅ Escenari 17: descarregar el YAML actual de regles

Given una persona de Direcció consulta la gestió de regles automàtiques
And hi ha un catàleg vàlid carregat o el catàleg inicial de l'aplicació està disponible
When selecciona «Descarregar YAML actual»
Then rep com a adjunt privat el contingut literal del catàleg actiu, sense perdre comentaris ni format
And si no hi ha un catàleg carregat, rep el YAML inicial que utilitza l'aplicació
And la descàrrega no canvia ni activa cap versió del catàleg
But una persona sense rol de Direcció no pot obtindre el fitxer
And si no hi ha cap catàleg disponible, Direcció rep un missatge funcional i no un fitxer buit ni una excepció tècnica

### Escenari 18: notificar per correu cada canvi individual de mòdul

Given una persona de Direcció canvia vàlidament l'estat o l'observació d'una petició
When el canvi efectiu queda guardat
Then s'envia un correu a l'alumne titular amb el mòdul afectat, el nou estat i l'observació si n'hi ha
And la resta de peticions de la sol·licitud no canvien
And el missatge no inclou adjunts ni informació d'altres mòduls o alumnes
But si es torna a guardar exactament el mateix estat i observació, no s'envia un altre correu
And si no hi ha una adreça vàlida o falla l'enviament, es conserva el canvi i Direcció rep un avís

### ✅ Escenari 19: llistats separats de sol·licituds i convalidacions

Given una persona autoritzada de Direcció o Secretaria consulta la gestió de convalidacions
When obri el llistat de sol·licituds
Then veu una fila per sol·licitud amb la referència, l'alumne, la data i el nombre de mòduls
And el llistat no presenta ni filtra per un estat únic de sol·licitud
When obri el llistat de convalidacions per mòdul
Then veu una fila per cada mòdul sol·licitat amb l'alumne, el mòdul, l'estat individual i la sol·licitud a què pertany
And pot filtrar este llistat únicament per l'estat del mòdul
And el filtre retorna només els mòduls coincidents, sense incloure com a coincidents els altres mòduls de la mateixa sol·licitud
And no es mostren filtres per origen, NIA ni codi de mòdul
And Direcció pot obrir la sol·licitud completa per consultar-ne el context

### ✅ Escenari 20: PDF del llistat de convalidacions

Given hi ha un llistat de mòduls per Direcció o Secretaria
When selecciona «Descarregar PDF» i tria un estat en el diàleg
Then el document inclou una entrada independent per cada mòdul que coincidix amb els filtres
And cada entrada identifica l'alumne, el mòdul, el seu estat i la sol·licitud de referència amb la data
And el PDF manté visibles els estats diferents dels mòduls d'una mateixa sol·licitud
And el PDF no tracta ni presenta la sol·licitud com si tinguera un estat propi
And el PDF no incorpora ni exposa els fitxers adjunts de l'alumnat
And si no se selecciona cap estat, el PDF inclou tots els estats
And cancel·lar el diàleg no inicia la descàrrega

### Escenari 21: registre en ITACA pendent de definició

Given el procediment per a registrar les convalidacions en ITACA encara no està definit
When una persona autoritzada de Secretaria o Direcció consulta les convalidacions per mòdul
Then veu l'avís «Funcionalitat pendent de definir»
And el botó «Marcar com a registrades en ITACA» i les caselles de selecció estan deshabilitats
And la consulta, els filtres i la descàrrega del PDF continuen disponibles
When envia directament una petició al servidor per a registrar mòduls en ITACA
Then el servidor rebutja l'acció sense modificar cap estat, traçabilitat ni enviar correus

## Regles de negoci

### Model i composició

- Una sol·licitud correspon a un únic alumne i conté una o més peticions de convalidació.
- Cada petició correspon a un únic mòdul destí de la matrícula vigent de l'alumne.
- El mateix mòdul destí no es pot repetir dins d'una mateixa sol·licitud.
- Un alumne no pot crear una nova petició d'un mòdul si ja en té una anterior en qualsevol estat diferent de `Denegada`.
- `Denegada` és l'únic estat que torna a habilitar el mòdul per a una nova sol·licitud; `Acceptada` i `Acceptada (Convalidada)` el mantenen bloquejat.
- El mòdul destí i, quan corresponga, el mòdul aprovat d'origen es validen en backend; no es confia en identificadors enviats pel navegador.
- La relació conceptual obligatòria és `1 sol·licitud : N peticions`, encara que la nomenclatura tècnica definitiva s'adapte als patrons del projecte.
- No es poden editar ni cancel·lar sol·licituds ja tramitades, llevat de la substitució documental expressament permesa.

### Orígens i documentació

- «Propi centre» requerix seleccionar un mòdul aprovat que conste en els XML acadèmics privats i no exigix adjunt en l'MVP.
- El cicle i la nota acompanyen el mòdul com a informació contextual visible; la nota d'ITACA es mostra sense decimals i la convocatòria queda copiada en la petició únicament per a traçabilitat interna.
- El cas especial de convalidació basada en un cicle complet queda ajornat fins que es definisquen les seues regles.
- Els orígens externs es presenten en tres opcions compactes: estudis o certificats acadèmics d'un altre centre, certificat d'EOI i prevenció de riscos LOGSE.
- Els tres orígens externs admesos exigixen la declaració «Declare que la informació aportada és original i que dispose dels originals en cas que se'm demanen» i un únic adjunt.
- La interfície mostra els formats admesos i el límit de mida configurat per l'aplicació.
- Els adjunts es guarden en emmagatzematge privat, mai en `public/` ni en un disc públic.
- La descàrrega passa sempre per autorització i només està disponible per a l'alumne propietari i per a Direcció.
- «Títol universitari o FP1/FP2» només mostra l'avís de Secretaria i no genera cap petició.
- El document i la declaració només es mostren per als orígens externs tramitables; no apareixen per a propi centre ni per a titulacions derivades a Secretaria.
- En substituir documentació, l'alumne només pot canviar el fitxer de la petició afectada.

### Estats i revisió

- L'estat pertany a cada petició, no a la capçalera de la sol·licitud.
- Els estats admesos són:
  1. `Enviada`.
  2. `En procés`.
  3. `Denegada`.
  4. `Revisar documentació`.
  5. `Aportar documentació original a Secretaria`.
  6. `Acceptada`.
  7. `Acceptada (Convalidada)`.
- `Enviada` és l'estat inicial de tota petició nova tramitada; els registres anteriors mantenen l'estat que ja tenien.
- Les peticions antigues en estat `Resolta` es normalitzen a `Acceptada` amb una actualització de dades; no s'elimina cap petició ni sol·licitud. Els estats `Realitzada` i `Realitzada pendent de revisió` deixen d'estar disponibles i no s'inclouen en cap neteja de registres.
- `Acceptada (Convalidada)` és terminal perquè indica que la convalidació ja s'ha gravat en ITACA.
- `Acceptada` no és terminal; tanmateix, la transició a `Acceptada (Convalidada)` des del llistat per mòdul està deshabilitada mentre el procediment d'ITACA estiga pendent de definició.
- Direcció pot canviar manualment qualsevol petició no terminal a un altre estat admés.
- En iniciar la revisió d'una sol·licitud, només les peticions `Enviada` passen a `En procés`; les que ja han avançat no es modifiquen.
- L'alumnat pot substituir documentació només en `Enviada` o `Revisar documentació`; en el primer cas l'estat es manté, i en el segon torna a `En procés`.
- El filtre del llistat s'aplica automàticament en canviar el desplegable.
- El llistat usa una etiqueta destacada per al recompte de mòduls pendents i icones accessibles per al resum de l'automatització; sense accions o peticions pendents, les cel·les corresponents queden buides.
- L'observació és obligatòria en passar a `Denegada`, `Revisar documentació` o `Aportar documentació original a Secretaria`.
- Cada petició conserva, com a mínim, l'última persona que ha canviat l'estat, la data del canvi i l'observació.
- Una mateixa sol·licitud pot contindre peticions en estats diferents.
- La sol·licitud és una agrupació de peticions i no té un estat únic; les consultes i filtres per estat s'apliquen a cada petició de mòdul.
- El llistat de sol·licituds mostra una fila per capçalera i no es filtra per estat de mòdul; el llistat de convalidacions mostra una fila per mòdul i és el lloc dels filtres i operacions per estat.
- La persona configurada com a càrrec de Secretaria pot accedir només al llistat per mòdul i al seu PDF; no pot obrir el detall ni gestionar la configuració de Direcció.
- El llistat per mòdul només es filtra per estat; el PDF operatiu permet triar l'estat en un diàleg i l'opció buida inclou tots els estats. Els PDF sempre identifiquen la sol·licitud d'origen i no incorporen fitxers adjunts.
- Mentre el procediment d'ITACA estiga pendent de definició, el servidor rebutja les peticions de transició a `Acceptada (Convalidada)` des del llistat sense canviar dades ni notificar l'alumnat.
- Cada canvi efectiu d'estat o observació es notifica per correu de manera individual a l'alumne titular; no es crea un estat global de sol·licitud.
- Desar sense canvis en l'estat ni en l'observació no envia un correu duplicat. Si l'adreça no és vàlida o falla el correu, el canvi ja guardat no es desfà i Direcció rep un avís funcional.
- Els correus de canvi d'estat no inclouen documents adjunts ni informació de les altres peticions de la sol·licitud.

### Autorització i seguretat

- L'alumne només pot consultar, tramitar i corregir les seues pròpies sol·licituds i peticions.
- Direcció gestiona totes les sol·licituds i peticions; Secretaria té accés als llistats i PDF descrits en els escenaris 19–21, però no per això obté accés a la configuració de regles, als XML d'avaluació ni al bloqueig de proves. El registre en ITACA continua desactivat fins que se'n definisca el procediment.
- No es confia en IDs, estats, orígens, tipus ni permisos rebuts des del navegador sense validació al servidor.
- Els missatges, les observacions i els adjunts no exposen informació d'altres persones.
- El bloqueig de proves es persistix perquè siga consistent entre peticions i dispositius, i per defecte l'accés de l'alumnat està obert.
- La contrasenya de proves es configura al servidor (`CONVALIDACIONS_ACCESS_PASSWORD`); només es mostra en clar en el panell privat protegit per rol de Direcció, i mai en HTML de l'alumnat, superfícies públiques o missatges d'error.
- Canviar el bloqueig genera un nou cicle i invalida les autoritzacions temporals de sessions anteriors.
- L'eliminació de sol·licituds només està disponible per a Direcció i elimina també els documents privats de totes les peticions.
- La confirmació d'eliminació avisa explícitament de la pèrdua irreversible de traçabilitat; l'eliminació no és una resolució ordinària.

## Components previstos

- Migracions i entitats separades per a la capçalera i les peticions.
- Policy de propietat per a alumnat i accés gestor per a Direcció.
- Rutes d'alumnat en `routes/alumno.php` i rutes protegides en `routes/direccion.php`.
- Entrada «Convalidar» al menú de l'alumnat i panells de presentació, consulta i revisió.
- Emmagatzematge privat i descàrrega mitjançant un controlador amb autorització.
- Gestor privat dels XML acadèmics integrat en el panell de convalidacions de Direcció, sense edició ni descàrrega del contingut.
- Vistes separades de sol·licituds i convalidacions per mòdul per a Direcció i Secretaria, amb filtres individuals i PDF operatiu a nivell de mòdul.
- Registre en ITACA posposat fins que se'n definisca el procediment; la interfície i el servidor mantenen deshabilitada l'acció.
- Control persistent del bloqueig d'accés de proves i middleware per protegir totes les rutes d'alumnat de convalidacions.
- Eliminació de sol·licituds de prova per Direcció amb neteja transaccional de peticions i documents privats.
- Tests Unit i Feature per a propietat, autorització, validació, estats, adjunts, transaccionalitat, idempotència i contracte de rutes.
- Tests Feature per al cicle de bloqueig amb contrasenya i l'eliminació confirmada de sol·licituds de prova.

## Expressament posposat

- Càlcul automàtic d'equivalències i taules de correspondències.
- Tasques nocturnes i qualsevol processament automàtic.
- Accions massives sobre sol·licituds o estats, inclosa la transició de mòduls `Acceptada` a `Acceptada (Convalidada)`, mentre el procediment d'ITACA estiga pendent de definició.
- OCR, IA, models en la DGX Spark i fine-tuning.
- Altres notificacions de panell o correus diferents de l'avís per canvi individual d'estat definit en l'escenari 18.
- Gestió general de permisos de Secretaria fora dels llistats i PDF descrits en els escenaris 19–21.
- Historial complet de transicions.
- Múltiples documents per petició i versionat documental.
- Edició o cancel·lació d'una sol·licitud ja tramitada.
- Text normatiu definitiu i enllaços oficials sobre titulacions universitàries.

## Criteri global de finalització

- Tots els escenaris anteriors disposen de tests automatitzats.
- La creació de la capçalera i de totes les peticions és transaccional i idempotent davant reintents.
- Cap alumne pot consultar o descarregar dades d'un altre.
- Cap adjunt és accessible directament des d'un disc públic.
- Les rutes superen `RouteNameContractTest`.
- La interfície seguix els patrons existents de Bootstrap 5 i Livewire 3.
