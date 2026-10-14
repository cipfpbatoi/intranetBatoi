# Nous estats de convalidació per mòdul

## Estat

Status: ready_for_review

## Traça i punt d’entrada

- **Domini:** sol·licituds i resolucions de convalidació.
- **Punt d’entrada:** `DireccionConvalidacioController::resolve` i el selector d’estat de cada petició en el detall de Direcció.
- **Docs/specs llegits:** `AGENTS.md`, `docs/agents/conventions.md`, `docs/agents/openspec.md`, `specs/convalidacions.md`, `specs/convalidacions-resolucio-per-modul-i-correu.md` i l’acta `261007 - Reunio Direccio canvis convalidacions.md`.
- **Abast:** afegir els estats nous que falten de l’acta al catàleg per a cada petició de mòdul i permetre que Direcció els assigne individualment. Mantindre els estats actuals i tots els registres tal com estan; no fer cap migració ni canvi massiu.

## Escenaris

### Escenari 1: estats nous disponibles per a cada mòdul

Given una sol·licitud conté una o més peticions de mòduls
When Direcció obri el detall de la sol·licitud
Then cada petició té el seu propi selector d’estat
And Direcció pot assignar individualment els nous estats `Enviada`, `Acceptada` i `Acceptada (Convalidada)`
And els estats actuals continuen disponibles segons les regles vigents
And canviar l’estat d’una petició no altera les altres peticions.

### Escenari 2: estat inicial de les peticions noves

Given l’alumnat tramita una sol·licitud nova
When es creen les peticions dels mòduls
Then cada petició nova queda en estat `Enviada`
And les peticions ja existents conserven el seu estat actual.

### Escenari 3: conservació dels registres existents

Given hi ha sol·licituds amb peticions en qualsevol dels estats actuals
When s’incorporen els nous estats al sistema
Then cap registre existent canvia d’estat
And no s’executa cap migració de dades ni conversió automàtica
And els estats antics continuen sent llegibles i gestionables.

### Escenari 4: notificació del canvi individual

Given Direcció assigna a una petició un dels nous estats
When el canvi queda guardat
Then s’aplica la notificació individual ja definida per als canvis d’estat
And el correu identifica només el mòdul afectat i el nou estat
And una petició sense canvi efectiu no genera un correu duplicat.

## Regles de negoci

- L’estat continua pertanyent a cada petició/mòdul; no es crea un estat agregat de sol·licitud.
- Els únics estats nous d’esta proposta són `Enviada`, `Acceptada` i `Acceptada (Convalidada)`. `En procés`, `Denegada` i `Revisar documentació` ja existeixen amb les etiquetes actuals equivalents.
- Es conserven tots els estats actuals, inclosos els que no apareixen en la llista de l’acta.
- Direcció pot assignar els estats nous per a cada mòdul des del seu selector individual.
- Les peticions noves s’inicien en `Enviada`; no es migra, reescriu ni normalitza cap dada existent.
- `Acceptada (Convalidada)` és terminal perquè indica que la petició s'ha marcada com a convalidada en la intranet; el registre en ITACA és una tasca manual posterior. `Acceptada` continua editable per Direcció per a completar el procés posterior.
- Els canvis efectius als estats nous reutilitzen la notificació individual per correu ja definida.

## Fitxers afectats previstos

- `app/Entities/Convalidacio.php` — afegir les etiquetes dels estats nous sense llevar les actuals i definir l’estat per defecte.
- `app/Http/Controllers/DireccionConvalidacioController.php` — admetre els estats nous en la resolució individual.
- `app/Application/Convalidacio/ConvalidacioService.php` — iniciar peticions noves en `Enviada` i validar els nous estats sense modificar registres antics.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` — mostrar els nous estats en el selector de cada mòdul.
- `tests/Feature/ConvalidacioFlowTest.php` — cobrir assignació individual, conservació dels estats antics i notificacions.
- `specs/convalidacions.md` — documentar la coexistència dels estats antics i nous i la resolució per mòdul.

## Riscos

- L’acta descriu estats de sol·licitud, mentre que el criteri acordat ací els aplica a cada mòdul; cap estat global de capçalera s’afegirà.
- `Enviada` pot implicar edició de l’alumnat en un flux futur; esta proposta només la fixa com a estat inicial i no canvia les possibilitats actuals d’edició.
- Els registres antics mantindran literalment els seus valors actuals, incloses diferències de capitalització.
