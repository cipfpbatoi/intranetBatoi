# Sprint 27 - Posada al dia de proves Dusk

## Objectiu

Recuperar `Dusk` com a capa de validació realment fiable per als fluxos crítics del projecte, deixant de dependre d'una suite parcialment envellida, lenta i massa acoblada a supòsits d'entorn o dades històriques.

## Context

Durant l'Sprint 24 d'upgrade a `Laravel 13`, la suite `Dusk` s'ha revisitat per comprovar si podia usar-se com a regressió de navegador.

La conclusió ha sigut:

- en host local, una part de `Dusk` no era executable de forma vàlida perquè `.env.dusk.local` depén de `mysql` i `selenium` per nom de servei
- dins del contenidor, `Dusk` sí arranca i `Selenium` respon
- però hi ha suites que fallen i no es poden usar com a criteri net de regressió perquè fa temps que no tenim una passada verda recent i fiable

Els casos més clars són:

- [`ApiAuthCoexistenceTest.php`](/Users/igomis/Code/intranetBatoi/tests/Browser/ApiAuthCoexistenceTest.php)
- [`ApiPendingAuthFlowTest.php`](/Users/igomis/Code/intranetBatoi/tests/Browser/ApiPendingAuthFlowTest.php)
- [`ComisionViewSmokeTest.php`](/Users/igomis/Code/intranetBatoi/tests/Browser/ComisionViewSmokeTest.php)
- [`ColaboracionBookInteractionTest.php`](/Users/igomis/Code/intranetBatoi/tests/Browser/ColaboracionBookInteractionTest.php)

## Problemes actuals

- dependència forta d'entorn Docker concret
- execució lenta i amb diagnòstic costós
- ús de dades històriques o supòsits febles sobre professorat i `api_token`
- proves que barregen massa objectius:
  - arrencada d'entorn
  - login UI
  - auth API
  - contracte HTTP
  - navegació visual
- nomenclatura de tests que suggereix regressió fiable quan en realitat alguns són encara proves exploratòries o de transició

## Principi de sanejament

`Dusk` no ha de ser la primera línia per validar contractes HTTP o auth de backend si això ja es pot defensar amb `Feature`.

El paper sa de `Dusk` hauria de quedar reduït a:

- fluxos de navegador que realment necessiten navegador
- comprovacions de convivència UI + sessió + JS
- smoke tests curts de pantalles crítiques

I traure de `Dusk` el que en realitat és millor provar en `Feature`.

## Tall A. Classificació de la suite actual

- separar les proves de `tests/Browser` en tres grups:
  - `smoke` de pantalles
  - fluxos UI crítics
  - proves híbrides que en realitat són contractes API i haurien de migrar a `Feature`
- identificar quines proves depenen de dades reals/fràgils
- identificar quines proves poden usar factories o fixtures més controlades

## Tall B. Entorn executable estable

- deixar explícit que `Dusk` s'ha d'executar dins del contenidor de l'aplicació
- revisar `.env.dusk.local` per reduir dependències innecessàries i aclarir prerequisits
- documentar una ordre canònica única d'execució
- verificar estat de `Selenium`, base de dades i assets abans de la suite

## Tall C. Reducció d'acoblament

- traure de `Dusk` les comprovacions que ja existixen o haurien d'existir com a `Feature`
- simplificar helpers de login i obtenció de Bearer quan el valor funcional siga només comprovar navegador
- substituir IDs ficticis i supòsits històrics per dades preparades de manera explícita quan siga possible

## Tall D. Primera tanda a rehabilitar

Prioritat alta:

- [`ApiAuthCoexistenceTest.php`](/Users/igomis/Code/intranetBatoi/tests/Browser/ApiAuthCoexistenceTest.php)
- [`ApiPendingAuthFlowTest.php`](/Users/igomis/Code/intranetBatoi/tests/Browser/ApiPendingAuthFlowTest.php)

Prioritat mitjana:

- [`ComisionViewSmokeTest.php`](/Users/igomis/Code/intranetBatoi/tests/Browser/ComisionViewSmokeTest.php)
- [`ColaboracionBookInteractionTest.php`](/Users/igomis/Code/intranetBatoi/tests/Browser/ColaboracionBookInteractionTest.php)

## Resultat esperat

- una suite `Dusk` més curta, més ràpida i amb objectiu clar
- una separació neta entre proves de navegador i proves de contracte backend
- una ordre d'execució fiable i repetible
- recuperació d'un subconjunt de `Dusk` que sí puga usar-se com a regressió defensable

## Criteri de tancament

Este sprint es podrà considerar ben encaminat quan es complisca tot açò:

- existeix una manera documentada i repetible d'executar `Dusk`
- `ApiAuthCoexistenceTest` i `ApiPendingAuthFlowTest` ja no siguen una caixa negra envellida
- hi haja almenys un subconjunt curt de `Dusk` que passe de manera fiable i que tinga valor real de regressió
