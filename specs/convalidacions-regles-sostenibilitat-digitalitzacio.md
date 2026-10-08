# Regles automàtiques de Sostenibilitat i Digitalització

## Estat

Status: ready_to_commit

## Domini i traça

- **Domini:** convalidacions automàtiques LFP.
- **Punt d’entrada:** `resources/convalidacions/regles-lfp.yaml`, avaluat per `ConvalidacioAutomaticaService`.
- **Docs/specs llegits:** `specs/convalidacions-resolucions-automatiques.md`, `specs/convalidacions-codis-xml-amb-abreviatura-familia.md`, `docs/agents/fct/fct-map.md`.
- **Abast:** corregir les regles YAML de Digitalització (`1664` GM, `1665` GS) i Sostenibilitat (`1708`) segons els criteris i bases normatives confirmats per l’usuari.

## Regles de negoci

1. **Condició comuna:** totes les regles automàtiques de Digitalització i Sostenibilitat només s'apliquen si la família professional d'origen i la del cicle de matrícula coincideixen. Si són diferents o no es poden identificar, no hi ha convalidació automàtica.
2. Digitalització es convalida automàticament entre mòduls del mateix nivell i mateixa família: `1664` GM amb `1664` GM, i `1665` GS amb `1665` GS.
3. Digitalització GS (`1665`) també es pot convalidar per Digitalització GM (`1664`) quan coincideix la família professional. El destí GM rep resultat `AA` i conserva la nota.
4. La direcció GM (`1664`) → GS (`1665`) no és una equivalència vàlida i no s’aplica automàticament.
5. Totes les equivalències vàlides de Digitalització fan referència a l’art. `126.3.b` i el resultat és `AA` amb la nota d’origen.
6. Sostenibilitat (`1708`) es convalida entre GM i GS en qualsevol direcció sempre que la família professional siga la mateixa. El resultat és `AA`, es conserva la nota i la base és l’art. `126.3.c`.
7. Els codis compostos continuen limitats a `1708`, `1664` i `1665`; el sufix ha de coincidir amb l’abreviatura XML de la família d’origen, segons la spec de codis compostos.

## Taula resum

| Mòdul d’origen | Mòdul destí | Condició | Resultat |
|---|---|---|---|
| Digitalització GM (`1664`) | Digitalització GM (`1664`) | Mateixa família professional | `AA`, conserva la nota — art. 126.3.b |
| Digitalització GS (`1665`) | Digitalització GS (`1665`) | Mateixa família professional | `AA`, conserva la nota — art. 126.3.b |
| Digitalització GS (`1665`) | Digitalització GM (`1664`) | Mateixa família professional | `AA`, conserva la nota — art. 126.3.b |
| Digitalització GM (`1664`) | Digitalització GS (`1665`) | — | No es convalida |
| Sostenibilitat GM (`1708`) | Sostenibilitat GM (`1708`) | Mateixa família professional | `AA`, conserva la nota — art. 126.3.c |
| Sostenibilitat GM (`1708`) | Sostenibilitat GS (`1708`) | Mateixa família professional | `AA`, conserva la nota — art. 126.3.c |
| Sostenibilitat GS (`1708`) | Sostenibilitat GM (`1708`) | Mateixa família professional | `AA`, conserva la nota — art. 126.3.c |
| Sostenibilitat GS (`1708`) | Sostenibilitat GS (`1708`) | Mateixa família professional | `AA`, conserva la nota — art. 126.3.c |

Si la família és diferent o no es pot identificar, no s’aplica cap d’aquestes convalidacions automàtiques.

## Escenaris BDD

### Escenari 1: Digitalització del mateix nivell i família ✅

Given l’origen és Digitalització GM (`1664`) i el destí és Digitalització GM
Or l’origen és Digitalització GS (`1665`) i el destí és Digitalització GS
And la família professional d’origen i de destí és la mateixa
When Direcció aplica les regles automàtiques
Then la petició es resol com a `AA`
And es conserva la nota d’origen
And es registra l’art. `126.3.b`.

### Escenari 2: Digitalització GS com a origen de Digitalització GM ✅

Given el mòdul d’origen és Digitalització GS (`1665`)
And el mòdul destí és Digitalització GM (`1664`)
And la família professional d’origen i de destí és la mateixa
When Direcció aplica les regles automàtiques
Then la petició es resol com a `AA`
And es conserva la nota d’origen
And es registra l’art. `126.3.b`.

### Escenari 3: Digitalització GM com a origen de Digitalització GS ✅

Given el mòdul d’origen és Digitalització GM (`1664`)
And el mòdul destí és Digitalització GS (`1665`)
When Direcció consulta les regles
Then no hi ha cap equivalència automàtica aplicable per a aquesta direcció.

### Escenari 4: Sostenibilitat entre graus diferents ✅

Given l’origen és Sostenibilitat GM o GS (`1708`)
And el destí és Sostenibilitat GM o GS (`1708`)
And la família professional d’origen i de destí és la mateixa
When Direcció aplica les regles automàtiques
Then la petició es resol com a `AA` independentment del nivell de l’origen i del destí
And es conserva la nota d’origen
And es registra l’art. `126.3.c`.

### Escenari 5: família professional diferent o desconeguda ✅

Given l’origen i el cicle de matrícula pertanyen a famílies professionals diferents, o alguna de les famílies no es pot identificar
And s'avalua qualsevol regla automàtica de Digitalització o Sostenibilitat
When Direcció avalua la regla
Then no aplica la convalidació automàtica.

## Fitxers afectats previstos

- `resources/convalidacions/regles-lfp.yaml` — corregir les equivalències, resultats i bases normatives de Digitalització i Sostenibilitat.
- `specs/convalidacions-resolucions-automatiques.md` — incorporar els escenaris i invariants confirmats.
- `tests/Feature/ConvalidacioFlowTest.php` — verificar les direccions permeses i prohibides, les notes i les bases normatives.

## Riscos

- No permetre la direcció Digitalització GM → GS, però sí la direcció Digitalització GS → GM quan coincidisca la família.
- No resoldre cap cas quan les famílies professionals no coincidisquen.
