# Índex de coneixement de domini

Documents vius per a agents d'IA. Cada fitxer té **alta cohesió** (tot el que conté pertany al seu context) i **baix acoblament** (és autoexplicatiu sense dependre d'altres fitxers).

Quan la IA es comporta malament en un domini, **refines el fitxer** corresponent; no el prompt.

> **Comença ací si véns de nou:** [`tetris.md`](tetris.md) explica com està organitzat el repo per a treballar amb agents (les 4 peces, la Regla Zero i la regla de l'adaptador prim per a configs d'IA).

## Convencions generals

| Fitxer | Contingut |
|---|---|
| [`conventions.md`](conventions.md) | Estil de codi, patrons del projecte, commits |
| [`testing-docker.md`](testing-docker.md) | Execució de tests, scripts Composer, Docker/Selenium |

## FCT (Formació en Centres de Treball)

Dominis: annexos, signatures, SAO, expedients, empreses, col·laboracions.

| Fitxer | Contingut |
|---|---|
| [`fct/fct-map.md`](fct/fct-map.md) | Rutes, controladors, entitats, vistes, correus |
| [`fct/signatures.md`](fct/signatures.md) | Flux `/signatura`, `sendTo`/`signed`, plantilles Annex I/II/III/V |
| [`fct/sao-selenium.md`](fct/sao-selenium.md) | Descàrregues SAO, depuració Selenium |

## Activitats complementàries i extraescolars

| Fitxer | Contingut |
|---|---|
| [`activitats/activitats-map.md`](activitats/activitats-map.md) | Rutes, fitxers clau, camps llegats, coordinador, PDFs |

## Specs de comportament

Les especificacions BDD (Given/When/Then) viuen a [`specs/`](../../specs/). Cada spec descriu el comportament esperat d'un bounded context, independent de la tecnologia.

| Spec | Bounded context |
|---|---|
| [`specs/fct.md`](../../specs/fct.md) | FCT: annexos, signatura, expedients |
| [`specs/activitats.md`](../../specs/activitats.md) | Activitats: creació, visites, PDF |
| [`specs/comisions.md`](../../specs/comisions.md) | Comissions: estats, FCTs associades, PDF |
| [`specs/guardies.md`](../../specs/guardies.md) | Guàrdies: presència, panell donde, coincidències |
| [`specs/horaris.md`](../../specs/horaris.md) | Horaris: canvi temporal, proposta JSON, bulk apply |

## Flux OpenSpec

Procediment per a implementar funcionalitats amb aprovació humana en tres passos (`propose → apply → archive`). Documentació: [`openspec.md`](openspec.md).

## Pipeline IA revisora ≠ generadora

La IA que escriu el codi no pot revisar-lo amb objectivitat. Documentació del workflow de revisió creuada: [`ia-review-pipeline.md`](ia-review-pipeline.md).
