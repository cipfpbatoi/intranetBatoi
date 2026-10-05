# Convalidacions — any d'aprovació i confirmació de cancel·lació

Issue: #323
Status: ready_for_review

## Relació amb les specs existents

Este canvi amplia `specs/convalidacions.md` i `specs/convalidacions-xml.md` sense alterar la resta del flux. L'any d'aprovació prové de l'atribut `curso` de l'element arrel `centro` de l'exportació d'ITACA.

## Escenaris

### Escenari 1: obtenció automàtica de l'any d'aprovació

Given una avaluació d'ITACA amb l'atribut `centro@curso` en format de quatre dígits
And un alumne amb un mòdul aprovat en eixa avaluació
When el sistema mostra els mòduls superats disponibles
Then cada resultat mostra automàticament l'any d'aprovació junt amb el mòdul, el cicle i la nota
And la convocatòria es conserva internament però no es mostra
And l'any no es demana ni es pot modificar des del navegador
And el mateix any apareix en el resum abans de tramitar la sol·licitud

### Escenari 2: còpia immutable de l'any

Given un alumne que selecciona un mòdul aprovat al propi centre
When tramita la sol·licitud
Then la petició guarda una còpia de l'any d'aprovació obtingut de l'XML
And l'alumne i Direcció veuen l'any en el detall de la petició
And l'any continua disponible encara que posteriorment se substituïsca o s'elimine l'avaluació d'ITACA original

### Escenari 3: avaluació sense un any utilitzable

Given una persona de Direcció que intenta afegir una avaluació d'ITACA
When `centro@curso` no existix o no conté exactament un any de quatre dígits
Then el sistema rebutja el fitxer abans de guardar-lo
And informa que l'avaluació no conté un curs acadèmic vàlid
And no substituïx ni elimina cap avaluació anterior

### Escenari 4: confirmació en cancel·lar la composició

Given un alumne en la pantalla de nova sol·licitud
When prem «Cancel·lar»
Then el sistema demana confirmació abans d'abandonar el formulari
And si rebutja la confirmació roman en la pantalla amb la composició intacta
But si accepta la confirmació torna al panell de convalidacions sense guardar cap sol·licitud

## Regles de negoci

- L'any d'aprovació és `centro@curso`; no es deduïx del nom del fitxer, de la data d'exportació ni de la data de tramitació.
- El format admés és un any de quatre dígits.
- L'any forma part del resultat acadèmic seleccionable i de la còpia immutable de la petició.
- Les dades de convalidacions existents es netegen en la migració perquè la funcionalitat encara no ha arribat a producció.
- L'any és obligatori per a peticions del propi centre; els orígens externs no tenen any d'aprovació procedent d'ITACA.
- La confirmació s'aplica al botó «Cancel·lar» de la composició, tant si ja hi ha mòduls afegits com si el formulari encara està buit.
- Cancel·lar no crea esborranys ni persistix dades.

## Fitxers afectats

- `app/Application/Convalidacio/ResultatsAcademicsXmlService.php` — extraure, validar i retornar `centro@curso`.
- `app/Application/Convalidacio/ConvalidacioService.php` — copiar l'any validat en tramitar.
- `app/Entities/Convalidacio.php` — permetre i tipar el nou atribut immutable.
- `database/migrations/*_add_any_origen_to_convalidacions.php` — netejar les dades no productives i afegir el camp sense reescriure migracions ja aplicades.
- `resources/views/intranet/convalidacions/alumno/create.blade.php` — mostrar l'any i confirmar el botó «Cancel·lar».
- `resources/views/intranet/convalidacions/alumno/show.blade.php` — mostrar l'any a l'alumne.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` — mostrar l'any a Direcció.
- `tests/Unit/Application/Convalidacio/ResultatsAcademicsXmlServiceTest.php` — extracció i rebuig d'anys absents o invàlids.
- `tests/Feature/ConvalidacioFlowTest.php` — còpia immutable i presentació posterior.
- `tests/Feature/DireccionConvalidacioXmlTest.php` — validació de la càrrega de l'avaluació.
- `tests/Browser/ConvalidacioFlowTest.php` o prova JavaScript equivalent — acceptació i rebuig de la confirmació de cancel·lació.

## Riscos

- El camp ha de permetre `null` només perquè els orígens externs no provenen d'ITACA; el servei ha de garantir-lo sempre per a propi centre.
- Modificar una migració ja aplicada faria divergir entorns; cal una migració nova.
- L'atribut d'ITACA es diu `curso`, però representa l'any acadèmic de l'exportació; no s'ha de confondre amb el curs 1r/2n del grup.
- Una confirmació global de `beforeunload` podria molestar en tramitar o manipular el formulari; l'abast queda restringit al botó «Cancel·lar».
