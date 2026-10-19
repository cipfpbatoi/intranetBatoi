# Proposta: EIE LOE → IPE II per coincidència del nom XML

Issue: #323
Status: ready_for_review

## Traça

- Domini: convalidacions automàtiques.
- Punt d'entrada: `ConvalidacioAutomaticaService::resultatRegla()` avalua les peticions ja presentades; `ConvalidacioReglesManager` carrega el YAML actiu.
- Documents/specs llegits: `AGENTS.md`, `docs/agents/openspec.md`, `docs/agents/conventions.md`, `specs/convalidacions.md` i `specs/convalidacions-resolucions-automatiques.md`.
- Abast: habilitar la regla EIE LOE → IPE II a partir del nom bilingüe literal guardat des de l'XML privat, tant si l'EIE d'origen és de GM com de GS. No inclou OCR ni certificats d'altres centres.

## Escenaris

### Escenari 1: reconéixer EIE LOE pel nom de l'XML del centre

Given una petició presentada demana IPE II (`1710`)
And l'alumne ha seleccionat un resultat aprovat del centre
And el nom valencià o castellà guardat en la petició identifica exactament «Empresa i iniciativa emprenedora» / «Empresa e iniciativa emprendedora», sense distingir majúscules/minúscules ni espais repetits
When Direcció previsualitza o aplica les regles automàtiques
Then la petició coincidix amb la regla EIE LOE → IPE II sense necessitar el codi del mòdul d'origen
And la regla no deduïx una coincidència per semblança o per una subcadena genèrica

### Escenari 2: l'origen pot ser de GM o de GS

Given el resultat EIE LOE aprovat identificat pel nom correspon a un cicle d'origen de GM o de GS
And el mòdul destí IPE II pertany a un cicle de matrícula de GM o de GS
When s'avalua la regla
Then la diferència de grau entre origen i destí no impedix la convalidació
And s'aplica el resultat i tractament de nota definits en el YAML per l'article 127.5

### Escenari 3: mantindre la revisió manual quan l'origen no és verificable

Given IPE II s'acredita amb documentació d'un altre centre, sense un resultat XML propi seleccionat, o amb un nom que no coincidix exactament amb els noms admesos
When s'avalua l'automatització
Then no s'aplica la regla EIE
And la petició continua disponible per a revisió manual de Direcció

## Regles de negoci

- L'article 127.5 del RD 659/2023 establix la convalidació d'IPE I/II aportant FOL/EIE d'estudis previs, amb resolució automàtica i conservació de la qualificació anterior quan siga numèrica.
- La disposició addicional sexta del RD 1085/2020 especifica que les equivalències d'EIE de la LOE s'entenen també aplicables a IPE II.
- El nom de l'EIE en l'XML és la clau de reconeixement perquè el codi varia per cicle (els exemples aportats inclouen, entre altres, `1557`, `0756`, `1357`, `0230`, `0618`, `0494`, `0050`, `0158`, `0381`, `1134`, `0347`, `0022`, `0063`, `0107`, `0507`, `0645` i `1377`). Cap d'estos codis és identificador universal de l'EIE.
- Els literals admesos són «Empresa e iniciativa emprendedora» i «Empresa i iniciativa emprenedora». La comparació és exacta després de normalitzar majúscules/minúscules i espais, com en la resta de regles per nom; no s'admet una coincidència aproximada.
- La regla no restringix el grau d'origen ni el de destí: GM↔GM, GM↔GS, GS↔GM i GS↔GS.
- Només s'automatitza un resultat aprovat del centre ja validat i guardat en la petició. Els documents externs continuen requerint revisió manual; no s'inferix cap resultat mitjançant OCR.
- No es modifiquen les regles FOL→IPE I, les regles de mateix codi IPE, ni altres regles d'EIE.
- La qualificació (`CO`/`AA`) i si es preserva la nota han de mantindre's tal com indique la regla YAML vigent per a EIE, llevat que l'usuari aprove explícitament un canvi.

## Fitxers afectats previstos

- `resources/convalidacions/regles-lfp.yaml` — substituir les entrades actuals `1710-from-eie-gm-loe` i `1710-from-eie-gs-loe` per dues regles equivalents basades en els literals castellà i valencià, sense segmentació per grau.
- `tests/Feature/ConvalidacioFlowTest.php` — cobrir noms valencià/castellà, origen GM/GS, destí GM/GS i no-coincidència/manual.
- `specs/convalidacions-resolucions-automatiques.md` — incorporar la regla quan s'aplique i estiga validada.

## Riscos

- El YAML carregat en producció pot diferir del fitxer inclòs al codi. Després d'aplicar i validar el canvi, Direcció haurà d'importar el YAML actualitzat des de la interfície si el catàleg privat ja està carregat.
- El catàleg actual ja conté dues entrades EIE→1710 amb resultat `CO` i nota preservada, però separades per grau i amb noms interns (`EIE GM LOE`, `EIE GS LOE`). El motor compara el nom configurat amb els noms guardats de manera exacta, així que estes entrades no coincidixen amb els literals XML aportats.
- No cal ampliar el motor: ja normalitza majúscules/minúscules i espais i compara el nom configurat amb els dos camps de nom guardats. Les dues regles equivalents (una per idioma) poden coincidir alhora; el motor ja agrupa coincidències amb el mateix resultat i base normativa com una sola aplicació.

## Referències normatives

- [RD 659/2023, article 127.5](https://www.boe.es/buscar/act.php?id=BOE-A-2023-16889)
- [RD 1085/2020, disposició addicional sexta, modificada pel RD 500/2024](https://www.boe.es/buscar/act.php?id=BOE-A-2020-17274)

## Implementació

- El YAML substituïx les entrades per nivell d'origen per dues regles de nom, castellà i valencià, amb el mateix resultat `CO`, nota preservada i art. 127.5.
- La prova de regressió cobrix els quatre encreuaments d'origen/destí GM/GS, normalització de majúscules, codis d'origen variables, i exclou l'origen extern i els noms no reconeguts.
- Verificació: `php artisan test --filter=ConvalidacioFlowTest` — 72 proves i 780 assertions superades.
