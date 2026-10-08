# Codis de mòdul XML amb sufix d’abreviatura de família

## Estat

Status: ready_to_commit

## Domini i traça

- **Domini:** convalidacions automàtiques LFP.
- **Punt d’entrada:** `ConvalidacioAutomaticaService::resultatRegla`.
- **Docs/specs llegits:** `specs/convalidacions-resolucions-automatiques.md`, `specs/convalidacions-context-matricula.md`, `docs/agents/fct/fct-map.md`.
- **Abast:** interpretar codis d’origen d’ITACA que incorporen al final l’abreviatura XML de la família professional, sense alterar el codi base definit en les regles YAML.

## Context

En la intranet, alguns codis de mòdul es guarden sense el sufix de família. ITACA/Conselleria pot identificar el mateix mòdul amb un codi compost pel codi base més l’abreviatura XML de la família. Per exemple, el codi `1708130` correspondria al mòdul base `1708` de la família amb abreviatura `130` (Imatge Personal).

El departament ja disposa dels camps `codigo_xml` i `abreviatura_xml`, amb les correspondències de famílies incorporades. Per tant, no s’afig una columna duplicada: la coincidència ha de reutilitzar l’abreviatura guardada al departament. Les regles YAML continuen descrivint el codi base del mòdul, per exemple `1708`.

## Regles de negoci

1. Es conserva sempre el codi d’origen literal rebut i guardat en la sol·licitud, inclòs el sufix.
2. Un codi d’origen pot coincidir amb el codi base de la regla si és igual literalment, com fins ara.
3. També pot coincidir si és exactament la concatenació del codi base de la regla i l’`abreviatura_xml` de la família professional d’origen identificada en la sol·licitud.
4. No s’accepten coincidències per prefix genèric, eliminació arbitrària de dígits ni sufixos que no corresponguen a una abreviatura coneguda.
5. Quan la regla exigeix `same_professional_family`, la comprovació existent entre la família d’origen i la de matrícula continua sent obligatòria, a més de validar el sufix contra la família d’origen.
6. La base normativa i la resta de condicions continuen sent les definides per la regla YAML. La normalització del codi no crea per si mateixa una regla ni fa automàtica una sol·licitud que no complisca les condicions.

## Escenaris BDD

### Escenari 1: codi base més abreviatura XML de la família ✅

Given una sol·licitud amb el mòdul d’origen `1708130`
And la família professional d’origen correspon al departament amb `codigo_xml` i `abreviatura_xml` `130`
And existeix una regla YAML per al codi base `1708` que compleix les altres condicions
When Direcció consulta si la regla és aplicable
Then el codi d’origen coincideix amb el codi base `1708` més l’abreviatura `130`
And s’apliquen només el resultat i la base normativa de la regla YAML
And es conserva `1708130` com a codi literal de la sol·licitud i en la traçabilitat de la resolució.

### Escenari 2: codi d’origen sense sufix ✅

Given una sol·licitud amb un codi d’origen igual al codi base d’una regla YAML
When Direcció consulta si la regla és aplicable
Then la coincidència exacta continua funcionant com abans
And no és necessari trobar cap abreviatura XML.

### Escenari 3: sufix desconegut o que no correspon a la família d’origen ✅

Given una sol·licitud amb un codi d’origen que comença pel codi base d’una regla
And el sufix no coincideix amb l’`abreviatura_xml` de la família professional d’origen, o no hi ha una correspondència coneguda
When Direcció consulta si la regla és aplicable
Then la regla no es considera aplicable per coincidència de codi
And la sol·licitud continua disponible per a revisió manual.

### Escenari 4: regla amb requisit de mateixa família ✅

Given una regla YAML que exigeix `same_professional_family`
And el codi compost d’origen té un sufix vàlid per a la família d’origen
But la família professional d’origen és diferent de la família del cicle de matrícula
When Direcció consulta si la regla és aplicable
Then la regla no s’aplica encara que el codi base i el sufix siguen vàlids.

## Canvis previstos

- Reutilitzar `departamentos.abreviatura_xml` i `departamentos.codigo_xml`; no afegir camps duplicats.
- Ajustar la coincidència del codi d’origen en `ConvalidacioAutomaticaService` perquè accepte només la forma base exacta o base + abreviatura coneguda de la família d’origen.
- Mantindre les regles YAML amb codis base i les condicions actuals.
- Afegir proves de regressió per a codi compost, codi base literal, sufix desconegut i regla de mateixa família.
- Actualitzar la documentació de les regles automàtiques perquè descriga aquesta forma de coincidència i la traçabilitat del codi literal.

## Riscos i límits

- No s’infereix cap equivalència entre famílies ni entre codis només perquè compartisquen prefix.
- Les abreviatures han de correspondre a departaments amb `codigo_xml` i `abreviatura_xml` vàlids; una dada absent deixa el cas en revisió manual.
- Aquesta proposta no modifica els resultats, les bases normatives ni les regles d’elegibilitat existents.
