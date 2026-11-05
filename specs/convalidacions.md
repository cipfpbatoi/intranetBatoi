# Convalidacions — comportament vigent

Issue: #323
Status: ready_to_commit

Esta és la spec funcional consolidada del domini. Les especificacions complementàries i fitxers `.proposal.md` de `specs/` es conserven com a traça de decisions i implementacions; no són una cua de funcionalitats pendents si el comportament ja està reflectit ací. Per a l'estat exacte de les regles automàtiques, preval el YAML actiu de `resources/convalidacions/regles-lfp.yaml`.

## Resum

- Una sol·licitud agrupa una o més convalidacions independents, una per mòdul. L'estat, resultat, nota i data de canvi són propis de cada mòdul; la sol·licitud no té estat global.
- L'alumnat crea i presenta la sol·licitud després de revisar-ne el resum. No es guarda cap esborrany persistent. Els mòduls nous comencen en `Enviada`.
- En els estudis del propi centre, l'origen seleccionable prové dels resultats aprovats dels XML privats. També es pot descriure manualment un altre estudi quan no apareix o no és un mòdul seleccionable. Direcció revisa manualment esta alternativa.
- Es poden aportar fins a tres documents per mòdul, amb descripció individual. La declaració responsable s'aplica a tots els documents adjunts. La documentació anterior es conserva quan se n'afig de nova.
- Direcció i Secretaria comparteixen l'operativa sobre sol·licituds i mòduls; la configuració administrativa continua reservada a Direcció.
- Les regles automàtiques s'apliquen només a mòduls que complixen una regla del YAML. Una sol·licitud pot tindre resolució automàtica parcial; la resta queda per a revisió manual. No hi ha integració d'escriptura amb ITACA.
- Direcció gestiona el YAML, els XML acadèmics i els catàlegs auxiliars (FOL LOE i correspondències d'anglés). El període dels XML s'extrau del contingut, no del nom del fitxer.
- Direcció controla des de Configuració si l'alumnat pot presentar noves sol·licituds i pot bloquejar-les específicament per a les matrícules de juliol.

## Escenaris vigents

### ✅ 1. Privacitat i composició de la sol·licitud

Given un alumne autenticat amb matrícula vigent
When crea una sol·licitud, afig mòduls destí i revisa el resum
Then cada mòdul apareix com una petició independent i editable abans de presentar
And no pot repetir el mateix mòdul ni sol·licitar-ne un que ja tinga una petició no denegada
And en confirmar es crea, de manera transaccional i idempotent, la sol·licitud amb les peticions en `Enviada`
And només pot consultar, modificar o descarregar les seues pròpies dades.

### ✅ 2. Estudis cursats al propi centre

Given l'alumne vol justificar una convalidació amb estudis del propi centre
When selecciona un resultat acadèmic
Then només pot seleccionar un resultat aprovat del seu NIA en els XML privats
And veu el mòdul, el cicle, l'any/període i la nota, sense exposar el fitxer XML ni la convocatòria
And el servidor torna a validar l'alumne, el mòdul destí i el resultat abans de guardar
And es conserva una còpia de les dades d'origen perquè els canvis futurs de l'XML no alteren la petició.

### ✅ 3. Altres estudis, documents i FOL LOGSE

Given l'alumne no troba el mòdul d'origen o aporta estudis/certificats d'un altre centre
When descriu els estudis i, si escau, adjunta documentació
Then la descripció és informativa i no activa per si mateixa cap equivalència automàtica
And pot aportar fins a tres documents vàlids per petició, indicant el tipus de cadascun
And la declaració responsable acceptada cobreix tots els documents adjunts
And els fitxers s'emmagatzemen privadament i les comprovacions es fan també al servidor
And l'alumne és responsable d'indicar si els estudis FOL d'origen són LOGSE; esta indicació i l'avís/certificat PRL només són pertinents quan sol·licita IPE I
And cap detecció OCR/IA ni verificació automàtica de certificats forma part del flux actual.

### ✅ 4. Revisió, correcció i aportació documental

Given una petició presentada
When Direcció o Secretaria inicia la revisió de la sol·licitud
Then només les peticions en `Enviada` passen a `En procés`; les altres no canvien
And l'alumnat pot corregir documentació només en `Enviada` o `Revisar documentació`
And Direcció o Secretaria pot demanar documentació i, mentre la petició estiga en `Revisar documentació`, afegir un comentari, un o més documents descrits o tots dos
And els fitxers previs es conserven; una aportació amb fitxer torna la petició a `En procés`, mentre que un comentari a soles no canvia l'estat
And queda registrada la persona, el rol i la data de cada aportació.

### ✅ 5. Resolució individual i notificació

Given una petició no terminal
When Direcció o Secretaria la resol o en canvia l'estat
Then la transició s'aplica només a eixe mòdul i es registra responsable, observació i data de l'últim canvi d'estat
And els canvis d'observació sense canvi d'estat no alteren eixa data
And en resoldre com a `Acceptada`, s'indica `CO` o `AA` i si es conserva una nota; una nota transcrita manualment és un enter entre 5 i 10
And cada canvi efectiu d'estat o observació notifica l'alumne del mòdul afectat, sense adjuntar documents ni informació d'altres peticions.

### ✅ 6. Estats i permisos de modificació

Given una petició de convalidació
When es consulta o s'intenta modificar
Then els estats vàlids són `Enviada`, `En procés`, `Denegada`, `Revisar documentació`, `Aportar documentació original a Secretaria`, `Acceptada` i `Acceptada (Convalidada)`
And l'alumne només pot editar documentació en `Enviada` o `Revisar documentació`
And `Acceptada (Convalidada)` s'assoleix des de `Acceptada`, individualment o en l'operació confirmada del PDF
And l'estat antic `Resolta` es normalitza a `Acceptada`; `Realitzada` i `Realitzada pendent de revisió` no són estats vigents
And `Denegada` és l'únic estat que permet tornar a sol·licitar el mateix mòdul.

### ✅ 7. Regles automàtiques

Given una sol·licitud presentada i una petició amb origen verificable
When Direcció aplica les convalidacions automàtiques
Then només es resolen les peticions que coincidixen completament amb una regla activa del YAML
And una petició sense regla coincident continua per a revisió manual
And es permet un resultat parcial dins d'una mateixa sol·licitud
And resultat, tractament de nota, regla, versió i base normativa queden guardats com a dades de la resolució
And els llistats distingixen regles candidates de resolucions ja aplicades.

### ✅ 8. Regles de referència al YAML

Given les regles carregades de `resources/convalidacions/regles-lfp.yaml`
When es comprova una petició
Then Digitalització només és automàtica amb la mateixa família: mateixa titulació/codi o GS origen cap a GM destí, mai GM cap a GS
And Sostenibilitat (1708) és automàtica entre GM i GS dins de la mateixa família
And FOL LOE identificat pel catàleg pot convalidar IPE I amb `CO` i nota segons l'article 127.5
And EIE LOE identificat pel nom del mòdul pot convalidar IPE II amb `CO` i nota segons l'article 127.5
And les altres regles, incloses les d'anglés i FOL LOGSE, són exactament les descrites al YAML; no s'infereixen equivalències addicionals.

### ✅ 9. Gestió operativa, PDFs i ITACA

Given una persona autoritzada de Direcció o Secretaria
When consulta la gestió de convalidacions
Then pot consultar sol·licituds completes i, en una vista separada, filtrar peticions per mòdul i estat
And pot generar un PDF ordenat per nom de l'alumne, amb mòdul, estat, sol·licitud, data de canvi i CO/AA/nota quan consten
And un PDF filtrat per `Acceptada` pot, amb confirmació explícita, marcar en la intranet només les peticions incloses que continuen acceptades com a `Acceptada (Convalidada)`
And la persona responsable queda registrada
But esta acció no escriu en ITACA: el registre allí és manual i queda fora de l'abast actual.

### ✅ 10. Administració, XML i seguretat

Given Direcció administra el flux de convalidacions
When gestiona el bloqueig de proves, regles, XML o catàlegs
Then estes opcions són exclusives de Direcció; Secretaria manté només l'accés operatiu descrit
And el bloqueig de proves està actiu per defecte, pot requerir la contrasenya configurada al servidor i no l'exposa a l'alumnat
And els XML i documents es mantenen privats, i les descàrregues passen per autorització
And es pot eliminar una sol·licitud només com a prova, amb confirmació explícita i avís de pèrdua de traçabilitat.

### ✅ 11. Obertura de noves sol·licituds

Given que Direcció consulta `/direccion/convalidacions/configuracio`
When activa o desactiva `Permetre noves sol·licituds` i `Bloquejar noves sol·licituds per a matrícules de juliol`
Then els dos controls persistixen de forma independent i només Direcció els pot administrar
And si les noves sol·licituds estan tancades, cap alumne no pot obrir ni presentar una sol·licitud nova i veu l'avís de tancament general
And si les noves sol·licituds estan obertes però el bloqueig de juliol està actiu, només es bloqueja l'alumnat amb `alumnos.fecha_matricula` en el mes `07`, amb un avís específic
And el bloqueig general preval sobre el bloqueig de juliol
And el servidor torna a comprovar el bloqueig en mostrar i en tramitar la sol·licitud, sense crear dades parcials
But l'alumnat conserva la consulta i les correccions documentals permeses de les sol·licituds existents.

## Límit de l'abast actual

- No hi ha OCR/IA per verificar certificats ni extracció automàtica de dades de documents.
- No hi ha integració que marque o actualitze convalidacions en ITACA.
- `Acceptada (Convalidada)` deixa constància de l'acció en la intranet, no d'una operació real en ITACA.
- Les equivalències automàtiques no es deduïxen del text lliure de l'alumne: depenen de les regles configurades i de les dades verificables disponibles.

## Traça i validació

Les especificacions detallades de composició, XML, catàlegs, regles, rols, documents, notificacions i PDFs es conserven en els fitxers complementaris de `specs/`. Esta spec és el resum funcional per a revisar el comportament complet sense haver de reconstruir-lo a partir de propostes històriques.

Validació executada en la branca de treball: 114 proves focalitzades de convalidacions i contracte de rutes, 1.153 assertions, totes correctes. La suite completa no forma part d'esta validació.
