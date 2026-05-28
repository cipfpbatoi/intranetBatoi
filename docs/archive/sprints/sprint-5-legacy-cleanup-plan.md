# Sprint 5 - Pla de sanejament de legacy, deprecated i codi mort

Data d'actualització: 2026-03-18  
Branca de partida recomanada: `Laravel12`  
Origen: tancament funcional d'Sprint 4 i residuals oberts d'Sprint 3

## Decisió de context

La branca actual de treball de `Sprint 4` es pot considerar **tancada**:

- la branca local/remote `sprint-4-issue-78` està en el commit final `[MOD] Tanca Sprint 4 Bootstrap 5 #78`
- el remot `origin/Laravel12` ja incorpora el merge de `sprint-4-issue-78` via PR `#93`

Per tant, no convé continuar acumulant neteja estructural sobre una branca de QA visual ja amortitzada. El següent tall hauria d'obrir-se com a branca nova sobre `Laravel12`.

## Objectiu

Atacar el deute tècnic pendent amb un ordre que reduïsca risc:

1. inventariar i marcar el que és `deprecated` de forma explícita
2. eliminar codi mort real i ponts que ja no tenen consum
3. reduir infraestructura legacy compartida
4. portar la gestió d'incidències al mateix patró de panell de Direcció que ja s'ha aplicat en:
   - faltes
   - comissions
   - activitats
   - expedients

## Diagnòstic resumit

### 1. Deute legacy ja identificat

- `resources/assets/js/ppIntranet.js`
  - continua com a bundle de compatibilitat compartida
- `resources/assets/js/bootstrap.js`
  - manté capa temporal de compatibilitat
- `resources/assets/sass/app.scss`
  - conserva `@import` i ajustos de convivència visual
- bloc DUAL/FCTDUAL
  - ja marcat en diversos punts com a `deprecated`

### 2. Direcció ja té patró nou en diversos mòduls

Els mòduls següents ja tenen ruta principal nova i retirada progressiva del panell antic:

- `comision`
- `actividad`
- `expediente`
- `falta`

El patró comú és:

- ruta principal nova en `routes/direccion.php`
- component Livewire propi
- controllers específics de Direcció per a bulk/pdf/gestor
- legacy mantingut només com a bridge per a professorat o compatibilitat temporal

### 3. Incidències és el mòdul que ha quedat fora del patró

Actualment incidències continua basada en:

- [`app/Http/Controllers/PanelIncidenciaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelIncidenciaController.php)
- [`app/Http/Controllers/IncidenciaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/IncidenciaController.php)
- [`resources/views/intranet/partials/profile/incidencia.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/incidencia.blade.php)
- [`routes/mantenimiento.php`](/Users/igomis/Code/intranetBatoi/routes/mantenimiento.php)
- [`routes/profesor.php`](/Users/igomis/Code/intranetBatoi/routes/profesor.php)

No hi ha ara mateix cap equivalent a:

- `IncidenciaDireccionPanel`
- vista `resources/views/incidencia/livewire-panel.blade.php`
- controllers específics de Direcció/Manteniment per a accions laterals

## Criteri de priorització

No convé mesclar en un mateix tall:

- neteja grossa d'infra legacy
- migració funcional gran d'incidències

La recomanació és treballar en **dos fronts dins del mateix sprint**, però amb ordre clar:

1. sanejament segur i quasi mecànic
2. migració funcional d'incidències

## Pla proposat

### Fase 0. Tallar branca nova i congelar l'anterior

Objectiu:

- no continuar fent créixer `sprint-4-issue-78`

Accions:

- obrir branca nova sobre `Laravel12`
- deixar `sprint-4-issue-78` només com a referència de tancament
- si cal, crear issue nova específica de sanejament post-S4

Nom recomanat de branca:

- `sprint-5-legacy-cleanup`

### Fase 1. Inventari executable de deprecated i bridges

Objectiu:

- distingir entre:
  - codi mort real
  - bridge temporal
  - legacy encara viu

Accions:

- reauditar `@deprecated`, comentaris `legacy` i rutes de compatibilitat
- fer una taula per cada peça amb:
  - qui la consumix
  - si és Direcció
  - si és professorat
  - si és API
  - si té proves

Primers candidats evidents:

- bloc DUAL/FCTDUAL
- rutes `*-livewire` que només redirigixen
- helpers de compatibilitat BS4/BS5
- pantalles residuals exclusives de Direcció ja substituïdes en Sprint 3

### Fase 2. Eliminar codi mort segur

Objectiu:

- llevar el que ja no té cap consumidor real

Accions:

- revisar vistes, JS i rutes que ja no tenen entrada des del flux actual
- eliminar només peces que complisquen les tres:
  1. cap ruta activa les usa
  2. cap component nou les referencia
  3. hi ha prova o verificació manual equivalent

Quick wins esperables:

- residuals exclusius de pilots de Direcció ja retirats en Sprint 3
- fitxers JS marcats com a `deprecated` i sense càrrega activa
- comentaris o alias antics que només confonen el camí viu

### Fase 3. Reduir infraestructura legacy transversal

Objectiu:

- baixar el cost de convivència global

Accions:

- traure funcionalitat de `resources/assets/js/ppIntranet.js` cap a mòduls més petits o helpers específics
- revisar si `resources/assets/js/bootstrap.js` continua necessitant tota la capa de compatibilitat temporal
- convertir warnings Sass residuals en backlog tancable per lots

Ordre recomanat:

1. `bootstrap.js`
2. `ppIntranet.js`
3. `app.scss`

Motiu:

- és infraestructura compartida que afecta molts fluxos
- però no convé tocar-la al mateix temps que el canvi funcional d'incidències

### Fase 4. Tractar incidències com a panell de Direcció/Manteniment

Objectiu:

- replicar el patró que ja funciona en `falta`, `comision`, `actividad` i `expediente`

Decisió funcional recomanada:

- considerar incidències com un **panell operatiu de Manteniment amb vista de govern semblant a Direcció**
- no continuar ampliant `PanelIncidenciaController`

Accions:

- crear component Livewire nou:
  - `app/Livewire/IncidenciaMantenimientoPanel.php`
- crear vista d'entrada nova:
  - `resources/views/incidencia/livewire-panel.blade.php`
- moure la ruta principal de `routes/mantenimiento.php` al panell nou
- mantindre `IncidenciaController` com a bridge temporal només per:
  - CRUD de professorat
  - accions que encara no s'hagen passat al component nou

### Fase 5. Desacoblar accions d'incidències del controller legacy

Objectiu:

- que el panell nou no depenga de `PanelIncidenciaController`

Accions mínimes del primer tall:

- llistat amb filtres per estat/responsable/tipus
- veure detall
- autoritzar
- desautoritzar
- rebutjar amb motiu
- resoldre
- generar ordre de treball

Accions que poden quedar en bridge temporal:

- edició completa del formulari
- flux de notificació si encara depén fortament del modal antic
- detalls UI específics del perfil legacy

### Fase 6. Retirada visible del legacy d'incidències

Només quan el panell nou cobrisca el flux real de manteniment:

- deixar `PanelIncidenciaController` fora de la ruta principal
- eliminar [`resources/views/intranet/partials/profile/incidencia.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/incidencia.blade.php) si ja no és necessari
- simplificar [`app/Http/Controllers/IncidenciaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/IncidenciaController.php) a bridge de professorat

## Ordre de treball recomanat

### Tall A. Preparació i poda segura

- inventari `deprecated`/legacy/codi mort
- eliminació de codi mort segur
- documentar què queda com a bridge

### Tall B. Pilot nou d'incidències

- component Livewire nou
- ruta principal nova
- accions bàsiques d'estat i ordre desacoblades

### Tall C. Retirada i simplificació

- retirada del panell antic
- neteja de vistes i botons legacy
- revisió final de proves i regressió manual curta

## Riscos

- `IncidenciaController` barreja CRUD, autorització, resolució i generació d'ordres de treball
- incidències toca dos dominis alhora:
  - professorat
  - manteniment
- una eliminació massa agressiva pot trencar fluxos de professorat encara vius

## Criteri de retirada segura

Una peça legacy només s'ha d'eliminar si es complixen les tres:

1. no queda cap ruta principal apuntant a ella
2. el flux equivalent ja està cobert en el panell nou o en un bridge clar
3. hi ha prova automàtica o checklist manual equivalent

## Recomanació final

El següent pas amb millor retorn és:

1. obrir branca nova des de `Laravel12`
2. fer inventari curt de `deprecated` i codi mort eliminable
3. usar incidències com a següent pilot de modernització estructural

Si hem de triar només una línia forta per al següent sprint, la millor és:

- **incidències com a nou panell operatiu tipus Direcció/Manteniment**

perquè al mateix temps:

- elimina legacy visible
- unifica patró d'interfície
- redueix dependència de `Panel*Controller`
- deixa més clar què és bridge i què és flux viu
