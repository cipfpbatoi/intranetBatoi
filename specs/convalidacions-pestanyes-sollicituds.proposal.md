# Convalidacions — pestanyes per a sol·licituds en seguiment i resoltes

Issue: #323
Status: ready_for_review

## Objectiu

Evitar que les sol·licituds amb tots els mòduls ja resolts s'acumulen en el llistat principal de Direcció. La pantalla tindrà una pestanya de seguiment i una altra de resoltes, amb una classificació clara quan una sol·licitud conté mòduls en estats diferents.

## Escenaris

### Escenari 1: obrir el llistat en seguiment

Given Direcció obri la gestió de sol·licituds de convalidació
When es mostra el llistat inicial
Then apareix seleccionada la pestanya «En seguiment»
And només es mostren les sol·licituds que tinguen almenys un mòdul pendent de resolució
And els recomptes, l'automatització i les accions de fila continuen referits als mòduls de cada sol·licitud
And el filtre existent «Només amb acció pendent de Direcció» continua permetent reduir el llistat als casos amb mòduls `Enviada` o `En procés`

### Escenari 2: consultar les sol·licituds resoltes

Given Direcció està en el llistat de sol·licituds
When selecciona la pestanya «Resoltes»
Then es mostren les sol·licituds en què tots els mòduls estan en `Acceptada`, `Denegada` o `Acceptada (Convalidada)`
And `Acceptada` es considera resolta encara que encara no s'haja marcat com a `Acceptada (Convalidada)`
And les sol·licituds resoltes deixen d'aparéixer en «En seguiment»
And es manté l'accés al detall de cada sol·licitud
And no es mostra l'apartat ni la columna d'automatització, perquè només s'apliquen al seguiment

### Escenari 3: classificar sol·licituds amb estats mixtos o pendents d'una altra persona

Given una sol·licitud conté diversos mòduls
When almenys un mòdul continua en `Enviada`, `En procés`, `Revisar documentació` o `Aportar documentació original a Secretaria`
Then la sol·licitud es manté en «En seguiment» encara que els altres mòduls estiguen resolts
And només passa a «Resoltes» quan tots els seus mòduls estan en `Acceptada`, `Denegada` o `Acceptada (Convalidada)`

### Escenari 4: conservar la navegació entre pestanyes i filtres

Given Direcció canvia de pestanya o aplica el filtre d'acció pendent
When es carrega el llistat corresponent
Then la pestanya seleccionada i el filtre actiu queden reflectits en la URL i en la interfície
And canviar entre pestanyes o filtrar no modifica cap sol·licitud, mòdul ni estat
And les pestanyes apareixen per damunt de l'apartat de resolució automàtica
And «En seguiment» mostra el nombre de sol·licituds i «Resoltes» no mostra cap recompte
And el recompte de «En seguiment» inclou cada sol·licitud amb algun mòdul en un estat no final

## Regles de negoci

- La unitat de classificació és la sol·licitud completa, però la decisió d'incloure-la es basa en els estats individuals dels mòduls.
- Una sol·licitud està «Resoltes» només si té almenys un mòdul i tots estan en `Acceptada`, `Denegada` o `Acceptada (Convalidada)`.
- `Acceptada` significa que Direcció ja ha resolt el mòdul; el pas posterior a `Acceptada (Convalidada)` no impedeix que la sol·licitud deixe el llistat de seguiment.
- El recompte de la pestanya «En seguiment» compta sol·licituds amb algun mòdul no final; el filtre «Només amb acció pendent de Direcció» continua limitat a `Enviada` o `En procés`.
- La secció de resolució automàtica i la seua columna només es mostren en «En seguiment».
- Una sol·licitud amb estats mixtos continua en seguiment fins que es resolga l'últim mòdul pendent.
- `Revisar documentació` i `Aportar documentació original a Secretaria` no es consideren resoltes: encara esperen una actuació per a completar el procés.
- La separació és només de consulta; no crea estats nous, no altera dades ni canvia permisos, accions de revisió, correus, automatitzacions o documents.
- El llistat s'ha de filtrar en la consulta de dades abans de carregar les relacions, per a evitar tornar a enviar a la pantalla tot l'històric i millorar-ne el temps de càrrega.
- La vista de «Resoltes» conserva l'accés de consulta al detall, però no mostra accions que no siguen vàlides per a l'estat actual; s'apliquen les mateixes polítiques i validacions existents.

## Fitxers afectats previstos

- `app/Application/Convalidacio/ConvalidacioQueryService.php` — consultes separades per a sol·licituds en seguiment i resoltes.
- `app/Http/Controllers/DireccionConvalidacioController.php` — selecció de pestanya, recomptes i preservació del filtre.
- `resources/views/intranet/convalidacions/direccion/index.blade.php` — posició de les pestanyes, recompte en seguiment i filtre contextual.
- `tests/Feature/ConvalidacioFlowTest.php` — classificació de sol·licituds amb tots els estats, casos mixtos, recomptes i navegació.
- `specs/convalidacions.md` — incorporar els escenaris quan s'arxive la implementació.

## Riscos

- Considerar `Acceptada` resolta és una decisió explícita d'esta proposta: la gestió d'ITACA continua sent una tasca posterior i no manté la sol·licitud en seguiment.
- Una sol·licitud amb un mòdul en espera de documentació es queda en seguiment encara que Direcció no hi haja d'actuar immediatament; així no es confon amb una resolució final.
- El filtratge SQL ha de tractar correctament les sol·licituds amb diversos mòduls i no duplicar capçalera ni files.
- No es modifica el llistat independent «Convalidacions per mòdul» ni el seu filtre per estat.

Status: ready_for_review
