# Proposta: acreditació alternativa d'estudis al propi centre

Esta proposta complementa [`convalidacions.md`](convalidacions.md). Se centra en evitar que l'alumnat confonga els estudis superats que el sistema troba en l'XML amb altres vies d'acreditació que requerixen revisió manual.

## Escenaris

### Escenari 1: petició ordinària amb un mòdul aprovat del propi centre

Given l'alumne selecciona «Estudis cursats al propi centre» i el sistema mostra un mòdul aprovat amb la seua nota
When prepara la petició
Then el formulari no mostra inicialment els camps per a pujar fitxers
And davall del selector veu en este ordre els enllaços «No trobes el mòdul o necessites aportar altres estudis?» i «Vols aportar més documentació?»
When selecciona «Vols aportar més documentació?»
Then veu un avís que normalment no cal adjuntar-la perquè el mòdul i la nota ja consten en els resultats del centre
And pot afegir documentació complementària
And el cas de FOL LOGSE per a IPE I continua avisant que cal adjuntar el certificat corresponent i exigint-lo abans d'afegir la petició

### Escenari 2: el mòdul aprovat no apareix en els resultats disponibles

Given l'alumne ha seleccionat un mòdul destí i estudis cursats al propi centre
When no troba en la llista el mòdul que vol aportar
Then pot seleccionar «No trobe el mòdul o necessite aportar altres estudis» sense abandonar la petició
And indica en un camp de text quin mòdul o estudis vol fer servir
And pot adjuntar documentació de suport
And la petició queda identificada com a acreditació alternativa i pendent de revisió manual, sense presentar el text o els fitxers com a dades verificades per l'XML
And si el destí és IPE I i declara que l'origen és FOL de LOGSE, es manté l'exigència del certificat de Prevenció de Riscos Laborals

### Escenari 3: adjunts complementaris i declaració responsable

Given qualsevol petició incorpora un o més documents, tant si és una acreditació complementària del propi centre com una acreditació alternativa
When l'alumne revisa la sol·licitud
Then la declaració responsable s'aplica a tots els documents adjunts segons el comportament actual
And els documents es mostren associats a la petició i es guarden en emmagatzematge privat
And si no s'adjunta cap document en el cas ordinari del propi centre, no es demana la declaració responsable

## Regles de negoci

- La selecció ordinària del propi centre continua basant-se exclusivament en un resultat aprovat trobat i verificat en els XML privats.
- L'acreditació alternativa no inventa ni valida un resultat acadèmic: la descripció de l'alumne i els adjunts són informació declarada, subjecta a revisió manual.
- Una petició alternativa no pot entrar en una resolució automàtica que necessite la coincidència d'un resultat d'origen verificat.
- La via alternativa conserva el mòdul destí de matrícula vigent; no permet sol·licitar un mòdul destí alié a la matrícula.
- La descripció alternativa és lliure: no es demanen categories addicionals que compliquen la selecció de l'alumne.
- En el flux ordinari del propi centre, els adjunts són opcionals i ocults fins que l'alumne decidix afegir-ne.
- L'excepció actual de FOL LOGSE per a IPE I es manté: quan l'alumne declara que és LOGSE, el certificat continua sent obligatori.
- La declaració responsable existent continua aplicant-se a tots els documents adjunts.
- La informació aportada com a alternativa ha de distingir-se clarament en la revisió de Direcció de les dades verificades per l'XML.

## Fitxers afectats previstos

- `resources/views/intranet/convalidacions/alumno/create.blade.php` — separar visualment el camí ordinari de l'alternatiu i amagar els camps d'adjunts fins que s'activen.
- `app/Entities/Convalidacio.php` i `database/migrations/` — persistir el tipus d'acreditació alternativa i la descripció declarada sense confondre-la amb codis de mòduls verificats.
- `app/Application/Convalidacio/ConvalidacioService.php` — validar l'alternativa, documents i condicions de matrícula, i excloure-la de l'automatització basada en XML.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` i `resources/views/intranet/convalidacions/alumno/show.blade.php` — identificar l'acreditació alternativa en consulta i revisió.
- `tests/Feature/ConvalidacioFlowTest.php` — cobrir els fluxos ordinari, alternativa, adjunts, declaració i exclusió de l'automatització.
- `specs/convalidacions.md` — integrar els escenaris aprovats.

## Riscos

- Cal guardar la descripció alternativa separadament de `modulo_origen_codigo`; reutilitzar aquest camp per a text lliure podria trencar regles automàtiques o consultes existents.
- La via alternativa requerix una migració i una revisió del model que rep Direcció; no s'ha d'implementar com un bypass que accepte un `resultat_origen_id` manipulat.
- Si l'alumne descriu uns estudis com a «cicle complet», l'expressió és només informació descriptiva: no es modela com una entitat ni implica una regla o equivalència automàtica.
- S'ha de mantindre la declaració responsable comuna i la validació del nombre, tipus i grandària dels fitxers.

Status: ready_for_review
