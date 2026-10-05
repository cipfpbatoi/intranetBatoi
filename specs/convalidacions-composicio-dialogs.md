# Convalidacions — composició mitjançant diàlegs i revisió final

Issue: #323
Status: ready_for_review

## Relació amb la spec principal

Este canvi substituïx la disposició visual de l'escenari 2 de `specs/convalidacions.md`. No modifica les regles de validació, els orígens, els documents, la idempotència ni el moment de persistència: la sol·licitud continua sense guardar-se fins a la tramitació final.

## Escenaris

### Escenari 1: espai de composició clar i inicialment buit

Given un alumne que inicia una nova sol·licitud
When s'obri la pantalla de composició
Then veu una capçalera «Nova sol·licitud» i una secció «Mòduls de la sol·licitud»
And veu el botó principal «Afegir mòdul»
And no veu desplegats els camps de mòdul, origen ni documentació
And veu un estat buit que explica que encara no ha afegit cap mòdul
And el botó «Revisar sol·licitud» està desactivat fins que incorpore almenys una petició

### Escenari 2: incorporació d'un mòdul des d'un diàleg

Given un alumne en la composició d'una sol·licitud
When prem «Afegir mòdul»
Then s'obri un diàleg ample «Afegir mòdul a la sol·licitud»
And el diàleg mostra progressivament el mòdul destí, la forma de justificació i només els camps requerits per eixe origen
And per a propi centre mostra el mòdul superat, cicle, any i nota, però no la convocatòria
And per a orígens externs mostra el document i la declaració responsable
And les titulacions derivades a Secretaria mostren l'avís i no es poden afegir
When completa les dades i prem «Afegir a la sol·licitud»
Then el diàleg es tanca
And la petició apareix com un element compacte dins de «Mòduls de la sol·licitud»
And el formulari del diàleg queda preparat per a afegir un altre mòdul
And una confirmació visual discreta informa que el mòdul s'ha incorporat sense ocupar espai amb una alerta

### Escenari 3: gestió de la composició temporal

Given una sol·licitud amb una o més peticions incorporades
When l'alumne consulta la pantalla principal
Then cada element es mostra en una taula amb el mòdul destí, l'acreditació i l'acció d'eliminar
And els controls interns i els fitxers preparats per a l'enviament no són visibles
And el comptador indica «Total de mòduls a convalidar: N»
And l'alumne pot eliminar qualsevol petició abans de tramitar
And en eliminar-la el mòdul torna a estar disponible en el diàleg d'incorporació
And cap d'estes accions crea encara dades en el servidor

### Escenari 4: revisió final abans de tramitar

Given una composició amb almenys una petició
When l'alumne prem «Revisar sol·licitud»
Then s'obri un diàleg de resum amb totes les peticions i les seues dades rellevants
And el resum diferencia clarament el mòdul que vol convalidar de l'acreditació aportada
And per a documents externs mostra el nom del fitxer seleccionat
And l'alumne pot tornar a la composició per afegir o eliminar peticions
And el botó «Presentar sol·licitud» obri una confirmació abans de l'enviament
And només el botó «Sí, presentar» de la confirmació envia el formulari i persistix la sol·licitud

### Escenari 5: cancel·lació de la sol·licitud

Given un alumne en la pantalla de composició
When prem «Cancel·lar»
Then el sistema demana confirmació abans d'abandonar la pantalla
And si rebutja la confirmació conserva intacta la composició
But si l'accepta torna al panell sense guardar cap petició

## Regles de negoci

- La pantalla principal representa exclusivament què forma part de la sol·licitud; els camps d'edició viuen en el diàleg d'incorporació.
- «Afegir a la sol·licitud» només modifica la composició temporal del navegador.
- «Revisar sol·licitud» no persistix ni envia dades; només obri el resum final.
- «Tramitar sol·licitud» és l'única acció que envia el formulari al servidor.
- Els identificadors, fitxers i declaracions necessaris es conserven com a controls ocults dins de cada element temporal, sense mostrar-los com un formulari ple.
- El diàleg d'incorporació usa el component Bootstrap 5 disponible en el projecte, és navegable amb teclat i retorna el focus al botó d'origen quan es tanca.
- En dispositius estrets els diàlegs són desplaçables i la taula principal té desplaçament horitzontal propi.
- Es mantenen la confirmació de cancel·lació, la prevenció de duplicats i totes les validacions actuals.

## Fitxers afectats

- `resources/views/intranet/convalidacions/alumno/create.blade.php` — nova composició principal, diàleg d'incorporació, diàleg de revisió i coordinació JavaScript.
- `tests/Feature/ConvalidacioFlowTest.php` — contracte HTML dels dos diàlegs, botons, resum i únic enviament final.
- `tests/Browser/ConvalidacioCompositionTest.php` o prova JavaScript equivalent — obertura/tancament, addició, eliminació, revisió i tramitació.
- `specs/convalidacions.md` — substitució de la disposició anterior quan el canvi quede validat.

## Riscos

- Els inputs de fitxer s'han de traslladar a l'element temporal sense perdre el fitxer seleccionat quan es tanca el diàleg.
- Cap botó del diàleg d'incorporació o revisió pot enviar accidentalment el formulari principal.
- En eliminar una petició també s'ha d'eliminar el seu input de fitxer ocult i reactivar el mòdul destí.
- El resum final ha de construir-se amb text segur i no amb HTML procedent dels noms de fitxer o literals.
- La gestió del focus, `aria-labelledby` i `aria-live` és necessària perquè el flux continue sent accessible.
