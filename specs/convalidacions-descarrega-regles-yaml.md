# Convalidacions — descàrrega del YAML de regles

Issue: #323
Status: ready_to_commit

## Traça i punt d'entrada

- Domini: Convalidacions.
- Punt d'entrada: `DireccionConvalidacioReglesController` i la vista `/direccion/convalidacions/regles`.
- Documents/specs llegits: `AGENTS.md`, `docs/agents/openspec.md`, `docs/agents/conventions.md` i `specs/convalidacions-resolucions-automatiques.md`.
- Abast: permetre a Direcció descarregar el YAML exacte que s'està utilitzant com a catàleg de regles, tant si és una versió carregada com si s'està usant el YAML inicial de l'aplicació.

## Escenaris

### ✅ Escenari 1: descarregar el catàleg carregat per Direcció

Given Direcció ha carregat un YAML vàlid que substituïx el catàleg inicial
When una persona de Direcció prem «Descarregar YAML actual» en la pantalla de regles
Then rep el fitxer carregat que s'està utilitzant actualment
And el contingut descarregat és idèntic al contingut original, inclosos comentaris i format
And el nom del fitxer identifica el catàleg com `convalidacions.yaml`

### ✅ Escenari 2: descarregar el YAML inicial si encara no se n'ha carregat cap

Given no hi ha cap YAML substitut carregat
And l'aplicació disposa del seu catàleg inicial
When Direcció demana descarregar el YAML actual
Then rep el contingut exacte del catàleg inicial que utilitza l'aplicació
And la descàrrega no crea ni activa una còpia nova del catàleg

### ✅ Escenari 3: restringir la descàrrega a Direcció

Given una persona no autenticada o sense el rol de Direcció
When intenta accedir directament a la ruta de descàrrega
Then no pot obtindre el YAML ni el seu contingut
And la ruta queda protegida pel mateix control d'accés que la resta de gestió de convalidacions de Direcció

### ✅ Escenari 4: no hi ha cap catàleg disponible

Given no hi ha cap YAML substitut ni està disponible el catàleg inicial
When Direcció demana la descàrrega
Then l'aplicació informa que no hi ha cap catàleg disponible
And no retorna un fitxer buit ni mostra una excepció tècnica

## Regles de negoci

- La descàrrega reflectix el contingut que el gestor de regles considera actiu en eixe moment; si el catàleg canvia després, les descàrregues següents reflectixen la nova versió.
- Es retornen els bytes originals del YAML, no una serialització de les regles interpretades, per a preservar comentaris, ordre i format.
- Si hi ha un catàleg carregat, este té prioritat sobre el YAML inicial de l'aplicació.
- El fitxer es lliura com a adjunt privat i només a persones amb rol de Direcció; no s'exposa una URL pública d'emmagatzematge.
- La descàrrega és de només lectura: no modifica el catàleg, la seua versió, la data de càrrega ni les resolucions ja aplicades.

## Fitxers afectats previstos

- `routes/direccion.php` — ruta protegida per a descarregar el catàleg.
- `app/Http/Controllers/DireccionConvalidacioReglesController.php` — acció de descàrrega.
- `app/Application/Convalidacio/ConvalidacioReglesManager.php` — obtindre el contingut YAML actiu sense reserialitzar-lo.
- `resources/views/intranet/convalidacions/direccion/regles.blade.php` — botó «Descarregar YAML actual».
- `tests/Feature/ConvalidacioFlowTest.php` — contingut literal, fallback inicial, accés i absència de catàleg.

## Riscos i punts a validar

- El contingut actiu pot estar en emmagatzematge privat o en el recurs inicial inclòs en el desplegament; l'acció ha de tractar els dos orígens sense fer pública cap ruta.
- La prova del fallback inicial ha de distingir-lo del YAML pujat en altres escenaris de test per evitar resultats dependents de l'estat.

Status: ready_to_commit

## Implementació aplicada

- `routes/direccion.php` — ruta privada de descàrrega.
- `app/Http/Controllers/DireccionConvalidacioReglesController.php` — resposta d'adjunt i missatge funcional si no hi ha catàleg.
- `app/Application/Convalidacio/ConvalidacioReglesManager.php` — lectura literal del YAML carregat o del recurs inicial.
- `resources/views/intranet/convalidacions/direccion/regles.blade.php` — accés visible a la descàrrega.
- `tests/Feature/ConvalidacioFlowTest.php` — cobertura de contingut, fallback, permisos i catàleg absent.
