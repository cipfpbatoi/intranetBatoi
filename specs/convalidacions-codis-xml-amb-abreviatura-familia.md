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

La forma composta no és una convenció general per a tots els mòduls. Només s’accepta per als codis base `1708` (Sostenibilitat, tant GM com GS), `1664` (Digitalització GM) i `1665` (Digitalització GS). El sufix varia segons la família professional d’origen: per exemple, els codis `1708150`, `1665150` i `1664130` només són vàlids si `150` o `130` és l’abreviatura XML de la família d’origen guardada en la petició. El nivell i la resta de condicions els continua determinant la regla YAML concreta.

## Regles de negoci

1. Es conserva sempre el codi d’origen literal rebut i guardat en la sol·licitud, inclòs el sufix.
2. Un codi d’origen pot coincidir amb el codi base de la regla si és igual literalment, com fins ara.
3. Per als codis base `1708`, `1664` i `1665`, també pot coincidir si és exactament la concatenació del codi base de la regla i l’`abreviatura_xml` de la família professional d’origen identificada en la sol·licitud.
4. No s’accepten coincidències per prefix genèric, eliminació arbitrària de dígits ni sufixos que no corresponguen a una abreviatura coneguda.
5. Quan la regla exigeix `same_professional_family`, la comprovació existent entre la família d’origen i la de matrícula continua sent obligatòria, a més de validar el sufix contra la família d’origen.
6. Per a qualsevol altre codi base, només s’accepta la coincidència literal exacta; no s’hi aplica la forma composta encara que el sufix coincidisca amb una abreviatura existent.
7. La base normativa i la resta de condicions continuen sent les definides per la regla YAML. La normalització del codi no crea per si mateixa una regla ni fa automàtica una sol·licitud que no complisca les condicions.

## Escenaris BDD

### Escenari 1: Sostenibilitat amb sufix de família en GM i GS ✅

Given l’alumne ha cursat Sostenibilitat de GM o GS amb el codi XML `1708` més l’abreviatura de la seua família
And la família d’origen està identificada en la sol·licitud
When Direcció consulta la regla per al mòdul destí `1708`
Then s’aplica només la regla YAML corresponent al nivell del mòdul destí
And el sufix ha de coincidir exactament amb l’`abreviatura_xml` de la família d’origen
And es conserva el codi XML compost literal en la traçabilitat de la resolució.

### Escenari 2: Digitalització diferenciada per nivell ✅

Given l’alumne ha cursat Digitalització GM amb el codi `1664` més l’abreviatura de la família d’origen
Or l’alumne ha cursat Digitalització GS amb el codi `1665` més l’abreviatura de la família d’origen
When Direcció consulta la regla automàtica
Then `1664` només coincideix amb la regla YAML de Digitalització GM
And `1665` només coincideix amb la regla YAML de Digitalització GS
And la família i el nivell han de complir les condicions de la regla YAML.

### Escenari 3: el sufix no habilita altres mòduls ✅

Given una regla YAML per a un codi base diferent de `1708`, `1664` o `1665`
And el codi d’origen és el codi base més una abreviatura XML
When Direcció consulta si la regla és aplicable
Then la coincidència composta es rebutja encara que el sufix siga una abreviatura vàlida
And la regla només pot coincidir amb el codi literal exacte.

### Escenari 4: sufix desconegut o família diferent ✅

Given una regla per a `1708`, `1664` o `1665`
And el sufix no correspon a l’`abreviatura_xml` de la família d’origen, o la regla exigeix mateixa família i aquesta condició no es complix
When Direcció consulta si la regla és aplicable
Then la regla no s’aplica
And la petició continua disponible per a revisió manual.

## Canvis previstos

- Reutilitzar `departamentos.abreviatura_xml` i `departamentos.codigo_xml`; no afegir camps duplicats.
- Ajustar la coincidència del codi d’origen en `ConvalidacioAutomaticaService` perquè accepte la forma base + abreviatura coneguda només per a `1708`, `1664` i `1665`; per a la resta, mantindre la coincidència exacta.
- Mantindre les regles YAML amb codis base i les condicions actuals.
- Afegir proves de regressió per a codi compost, codi base literal, sufix desconegut i regla de mateixa família.
- Actualitzar la documentació de les regles automàtiques perquè descriga aquesta forma de coincidència i la traçabilitat del codi literal.

## Riscos i límits

- No s’infereix cap equivalència entre famílies ni entre codis només perquè compartisquen prefix.
- Les abreviatures han de correspondre a departaments amb `codigo_xml` i `abreviatura_xml` vàlids; una dada absent deixa el cas en revisió manual.
- Aquesta proposta no modifica els resultats, les bases normatives ni les regles d’elegibilitat existents.
