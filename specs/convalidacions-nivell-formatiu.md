# Convalidacions — nivell formatiu del cicle

Issue: #323
Status: ready_for_review

## Objectiu

Conservar en cada petició el nivell formatiu del cicle actual de matrícula i el nivell del cicle d'origen identificat en l'XML acadèmic. La dada ha de quedar com una fotografia de la sol·licitud i no dependre de canvis posteriors en la matrícula ni en els XML.

## Escenaris

### Escenari 1: nivell del cicle actual de matrícula

Given el mòdul destí està associat a un únic cicle de matrícula
When es prepara la petició
Then es guarda el tipus de formació indicat per `ciclos.tipo`
And es guarda també la normativa indicada per `ciclos.normativa`
And es conserven els literals del tipus en valencià i castellà segons les opcions de la intranet
And estos valors queden copiats en la petició i no es recalculen en consultes posteriors

### Escenari 2: nivell del cicle d'origen en resultats del propi centre

Given l'alumne selecciona un mòdul aprovat en un XML acadèmic
And el curs del mòdul té una jerarquia vàlida fins a la família professional
When el sistema valida i guarda la petició
Then identifica el node `curso` immediatament inferior a la família professional en la cadena de pares
And guarda el codi i els noms valencià/castellà d'eixe node com a nivell formatiu d'origen
And no confon eixe node amb el node de família professional ni amb el cicle més pròxim al mòdul

### Escenari 3: jerarquia d'origen incompleta o ambigua

Given el node del mòdul no arriba a una família professional única, o no es pot identificar el node immediatament inferior a esta
When l'alumne intenta presentar la petició de propi centre
Then la petició es rebutja amb un missatge funcional
And no es guarda cap capçalera, petició ni document parcial

### Escenari 4: visualització del nivell formatiu

Given una petició conserva el nivell de matrícula i, si correspon, el nivell d'origen
When l'alumne o Direcció consulta la petició
Then veu separadament el tipus i normativa del cicle actual i el nivell del cicle d'origen
And la informació mostrada és la fotografia guardada en presentar la petició

## Regles de negoci

- La intranet considera `ciclos.tipo` com el tipus de formació (p. ex. grau mitjà, grau superior o cicle formatiu bàsic) i `ciclos.normativa` com la normativa del cicle. S'han de conservar separadament; no s'ha d'inferir un a partir de l'altre.
- El codi numèric de `ciclos.tipo` i els literals bilingües es guarden junts per preservar la identificació original i facilitar la consulta.
- Els literals del tipus actual provenen de les opcions bilingües de la intranet (`config/auxiliares.php`), vinculades al valor de `ciclos.tipo`.
- Per als estudis d'origen, el nivell és el node `curso` de la jerarquia XML que té com a pare directe el node arrel de família professional.
- Per als orígens diferents de «Propi centre», no hi ha dades XML de nivell d'origen: els camps corresponents queden nuls.
- La selecció de nivell no arriba des del navegador; el servidor la resol de la base de dades o de l'XML validat.
- Les dades són instantànies immutable per petició i poden quedar nul·les en peticions antigues.

## Fitxers previstos

- `app/Application/Convalidacio/ConvalidacioService.php` — capturar el tipus i la normativa del cicle de matrícula.
- `app/Application/Convalidacio/ResultatsAcademicsXmlService.php` — identificar el curs immediatament inferior a la família en la jerarquia.
- `database/migrations/` — afegir camps nullable per al nivell de matrícula i el nivell d'origen.
- `app/Entities/Convalidacio.php` — exposar els camps nous.
- `resources/views/intranet/convalidacions/alumno/show.blade.php` i `resources/views/intranet/convalidacions/direccion/show.blade.php` — mostrar les dades conservades.
- `tests/Feature/ConvalidacioFlowTest.php` i `tests/Unit/Application/Convalidacio/ResultatsAcademicsXmlServiceTest.php` — cobrir captura, jerarquia, persistència i visualització.

## Riscos

- Cal validar amb la forma real de l'XML que el node directament inferior a la família representa el nivell formatiu en tots els formats/anys carregats.
- `ciclos.tipo` i `ciclos.normativa` són conceptes diferents; ajuntar-los en un únic literal podria perdre informació.
- Les peticions anteriors a la migració no tindran dades històriques reconstruïdes.
