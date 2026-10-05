# Convalidacions — presentació confirmada de la sol·licitud

Issue: #323
Status: ready_for_review

## Escenaris

### Escenari 1: acció final amb nom inequívoc

Given un alumne que revisa una sol·licitud amb almenys una petició
When consulta l'acció final del diàleg de revisió
Then veu el botó «Presentar sol·licitud»
And cap botó amb el text «Tramitar sol·licitud» envia el formulari

### Escenari 2: confirmació abans de persistir

Given un alumne que prem «Presentar sol·licitud»
When encara no ha confirmat l'acció
Then s'obri un diàleg de confirmació que informa que la sol·licitud es presentarà
And pot tornar al resum sense enviar cap dada
And la sol·licitud només s'envia després de prémer «Sí, presentar»

### Escenari 3: un únic enviament real

Given el diàleg de confirmació obert
When l'alumne confirma la presentació
Then només el botó «Sí, presentar» envia el formulari principal
And la idempotència de servidor continua protegint dobles clics o reintents

## Regles de negoci

- «Revisar sol·licitud» continua sent una acció sense persistència.
- Tancar o cancel·lar el diàleg de confirmació no modifica la composició temporal.
- El diàleg és accessible, amb títol associat i botons explícits.

## Fitxers afectats

- \`resources/views/intranet/convalidacions/alumno/create.blade.php\` — text del botó, nou diàleg i coordinació JavaScript.
- \`tests/Feature/ConvalidacioFlowTest.php\` — contracte dels botons, diàleg i únic enviament.
- \`specs/convalidacions-composicio-dialogs.md\` i \`specs/convalidacions.md\` — alineació del flux de presentació.
