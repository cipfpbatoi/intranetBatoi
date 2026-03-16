# Sprint 3 - Cobertura de proves

Data: 2026-03-16  
Branca objectiu: `sprint-3-livewire-vue`

## Objectiu
Deixar constància breu de quina cobertura automàtica existeix sobre el treball real executat en Sprint 3 i quins buits continuen sent manuals.

## Cobertura existent

### 1. Panells Livewire de Direcció

Coberts amb proves `Feature`:

- [FaltaDireccionPanelTest.php](/Users/igomis/Code/intranetBatoi/tests/Feature/FaltaDireccionPanelTest.php)
- [ComisionDireccionPanelTest.php](/Users/igomis/Code/intranetBatoi/tests/Feature/ComisionDireccionPanelTest.php)
- [ActividadDireccionPanelTest.php](/Users/igomis/Code/intranetBatoi/tests/Feature/ActividadDireccionPanelTest.php)
- [ExpedienteDireccionPanelTest.php](/Users/igomis/Code/intranetBatoi/tests/Feature/ExpedienteDireccionPanelTest.php)

Cobrixen, en general:

- render del llistat
- filtres principals
- accions bàsiques de canvi d'estat
- càrrega del detall/modal
- regressions de dades transformades per al panell

### 2. Controllers nous de Direcció

Coberts amb proves `Unit` específiques:

- [FaltaDireccionControllersTest.php](/Users/igomis/Code/intranetBatoi/tests/Unit/FaltaDireccionControllersTest.php)
- [ComisionDireccionControllersTest.php](/Users/igomis/Code/intranetBatoi/tests/Unit/ComisionDireccionControllersTest.php)
- [ActividadDireccionControllersTest.php](/Users/igomis/Code/intranetBatoi/tests/Unit/ActividadDireccionControllersTest.php)
- [ExpedienteDireccionControllersTest.php](/Users/igomis/Code/intranetBatoi/tests/Unit/ExpedienteDireccionControllersTest.php)

Cobrixen:

- bulk actions desacoblades
- gestor documental desacoblat
- PDF individual/col·lectiu desacoblat on toca
- redireccions noves de Direcció

### 3. Fluxos legacy encara vius

Coberts parcialment amb proves `Feature` o `Unit`:

- [FaltaControllerFeatureTest.php](/Users/igomis/Code/intranetBatoi/tests/Feature/FaltaControllerFeatureTest.php)
- [ExpedienteControllerFeatureTest.php](/Users/igomis/Code/intranetBatoi/tests/Feature/ExpedienteControllerFeatureTest.php)
- [ActividadControllerFeatureTest.php](/Users/igomis/Code/intranetBatoi/tests/Feature/ActividadControllerFeatureTest.php)
- [ComisionControllerFeatureTest.php](/Users/igomis/Code/intranetBatoi/tests/Feature/ComisionControllerFeatureTest.php)
- [ExpedienteControllerTest.php](/Users/igomis/Code/intranetBatoi/tests/Unit/ExpedienteControllerTest.php)

### 4. Auth/token i estabilització general

Coberts amb:

- [ApiAuthTokenExchangeFeatureTest.php](/Users/igomis/Code/intranetBatoi/tests/Feature/ApiAuthTokenExchangeFeatureTest.php)
- proves API associades a fluxos crítics ja presents en `tests/Feature`

## Cobertura suficient per al Sprint 3

La cobertura és **suficientment bona** per al treball nou realment introduït en Sprint 3:

- panells nous de Direcció
- bridges/controllers nous
- ajustos crítics d'auth/token

No és una cobertura exhaustiva del sistema complet, però sí de la part nova i de major risc introduïda en este sprint.

## Buits actuals

### 1. No hi ha E2E de navegador

No hi ha proves browser per validar:

- modals Bootstrap
- refrescos automàtics després d'impressió
- comportament visual complet del DOM

### 2. No tota la UI Blade està coberta

Els tests validen dades i render parcial, però no cada text, botó o branca visual de les plantilles.

### 3. El legacy de professorat continua cobert només parcialment

Especialment en:

- `ActividadController`
- `ExpedienteController`
- `ComisionController`
- `FaltaController`

El focus del sprint no ha sigut cobrir exhaustivament eixos mòduls, sinó no trencar-los mentre Direcció migrava.

## Recomanació de tancament

Abans de donar Sprint 3 per quasi tancat, el més rendible és:

1. executar una regressió manual curta dels quatre panells de Direcció
2. validar que no hi ha errors de consola ni rutes mortes
3. no invertir més ara en proves exhaustives del legacy residual, llevat que aparega una incidència concreta

## Conclusió

No hi ha proves de tot.

Sí hi ha proves suficients i defensables del que realment s'ha construït i desacoblat en Sprint 3.
