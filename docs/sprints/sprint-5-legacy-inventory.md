# Sprint 5 - Inventari inicial de legacy, deprecated i bridges

Data d'actualització: 2026-03-18  
Branca: `sprint-5-legacy-cleanup`

## Objectiu

Fer un primer tall operatiu per distingir:

- codi mort probable
- bridge temporal justificat
- legacy encara viu i no eliminable ara

## 1. Candidats de neteja segura immediata

### 1.1. Alias de ruta `*-livewire` que només redirigixen

Fitxer:

- [`routes/direccion.php`](/Users/igomis/Code/intranetBatoi/routes/direccion.php)

Entrades:

- `Route::redirect('/comision-livewire', '/direccion/comision')`
- `Route::redirect('/expediente-livewire', '/direccion/expediente')`
- `Route::redirect('/actividad-livewire', '/direccion/actividad')`
- `Route::redirect('/falta-livewire', '/direccion/falta')`

Diagnòstic:

- no són flux funcional propi
- només mantenen compatibilitat nominal
- són bons candidats a retirada si no queda cap enllaç intern ni cap ús real extern

Estat:

- referències internes actives: **no detectades**
- documentació històrica: **sí**

Acció executada:

- eliminades les quatre redireccions de compatibilitat en este tall

Seguiment:

- mantindre només la documentació històrica com a context de migració

### 1.2. Flux birrets/ITACA marcat com a deprecated

Fitxers principals:

- [`routes/direccion.php`](/Users/igomis/Code/intranetBatoi/routes/direccion.php)
- [`routes/profesor.php`](/Users/igomis/Code/intranetBatoi/routes/profesor.php)
- [`app/Http/Controllers/PanelFaltaItacaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelFaltaItacaController.php)
- [`resources/assets/js/components/fichar/BirretItacaView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/fichar/BirretItacaView.vue)
- [`app/Http/Controllers/API/FaltaItacaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/FaltaItacaController.php)
- [`app/Application/FaltaItaca/FaltaItacaWorkflowService.php`](/Users/igomis/Code/intranetBatoi/app/Application/FaltaItaca/FaltaItacaWorkflowService.php)

Diagnòstic:

- està explícitament marcat com a deprecated
- sembla un subflux funcional antic, no només un helper
- no és neteja segura automàtica sense confirmar ús real

Estat:

- considerat en desús per decisió funcional

Acció executada en este tall:

- retirades les rutes web i de Direcció del flux legacy de birrets/ITACA
- retirada la càrrega del component Vue `birret-itaca-view` del bundle principal
- eliminat el panell web legacy de Direcció `PanelFaltaItacaController`
- eliminat el component Vue legacy `BirretItacaView`
- eliminats els controllers web legacy `ItacaController` i `FaltaItacaController`
- eliminada la vista web legacy `resources/views/falta/itaca.blade.php`
- eliminada l'opció "Oblit Birret" del llistat PDF mensual
- simplificat `MensualController` perquè només use el flux vigent d'absències

Pendent:

- eliminats en este tall final:
  - `app/Entities/Falta_itaca.php`
  - `app/Application/FaltaItaca/FaltaItacaWorkflowService.php`
  - `app/Http/Controllers/API/FaltaItacaController.php`
  - rutes API `/itaca/*`
  - documentació OpenAPI associada
  - vista PDF `resources/views/pdf/comunicacioBirret.blade.php`
- afegida migració de retirada de la taula `faltas_itaca`
- `ItacaController` s'ha recuperat com a arxiu de referència en [`app/Http/Controllers/Deprecated/ItacaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/Deprecated/ItacaController.php), però sense rutes actives

### 1.3. Bloc DUAL/FCTDUAL

Fitxers principals:

- [`app/Http/Controllers/Deprecated/DualController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/Deprecated/DualController.php)
- [`app/Http/Controllers/API/DualController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/DualController.php)
- [`app/Http/Controllers/PanelDualController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelDualController.php)
- [`app/Http/Controllers/PanelPGDualController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelPGDualController.php)
- [`app/Http/Controllers/CicloDualController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/CicloDualController.php)
- vistes sota [`resources/views/dual`](/Users/igomis/Code/intranetBatoi/resources/views/dual)

Diagnòstic:

- està ben marcat com a deprecated
- el volum és gran
- no és tall menut: demana inventari específic d’entrades i consum

Estat:

- considerat en desús per decisió funcional

Acció executada en este tall:

- retirades les rutes d’entrada web dels panells legacy `DUAL/FCTDUAL`
- eliminats els controllers web legacy:
  - `PanelDualController`
  - `PanelPGDualController`
  - `CicloDualController`
- eliminada la vista `resources/views/fctdual/index.blade.php`
- eliminats els residuals sense rutes actives:
  - `app/Http/Controllers/Deprecated/DualController.php`
  - `app/Http/Controllers/API/DualController.php`
  - `app/Http/Requests/DualRequest.php`
  - `app/Http/Requests/CicloDualRequest.php`
  - `app/Http/Resources/DualResource.php`
  - `app/Entities/Dual.php`
  - `resources/views/intranet/partials/modal/anexeVI.blade.php`
  - tot el paquet `resources/views/dual/*`

Pendent:

- revisar si convé netejar també claus de traducció `models.Dual.*` que només descriuen accions ja retirades

## 2. Bridges justificats que no convé retirar ara

### 2.1. Panells de Direcció migrats

Mòduls:

- comissió
- activitat
- expedient
- falta

Diagnòstic:

- tenen patró nou funcional
- però encara mantenen bridges o rutes residuals per professorat/compatibilitat

Acció recomanada:

- no eliminar controllers legacy generalistes a cegues
- continuar mòdul a mòdul quan toque

### 2.2. Compatibilitat API legacy `api_token`

Fitxers principals:

- [`app/Http/Middleware/LegacyApiTokenDeprecation.php`](/Users/igomis/Code/intranetBatoi/app/Http/Middleware/LegacyApiTokenDeprecation.php)
- [`app/Http/Middleware/ApiTokenToBearer.php`](/Users/igomis/Code/intranetBatoi/app/Http/Middleware/ApiTokenToBearer.php)
- [`app/Http/Controllers/API/AuthTokenController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/AuthTokenController.php)
- diversos `public/js/*` que encara injecten `api_token`

Diagnòstic:

- és legacy viu
- està cobert per tests i documentació
- no convé llevar-lo sense tancar abans el front i els clients

Acció recomanada:

- mantindre'l separat del sanejament UI
- atacar-lo en una línia de retirada pròpia

## 3. Infraestructura legacy transversal

Fitxers principals:

- [`resources/assets/js/ppIntranet.js`](/Users/igomis/Code/intranetBatoi/resources/assets/js/ppIntranet.js)
- [`resources/assets/sass/app.scss`](/Users/igomis/Code/intranetBatoi/resources/assets/sass/app.scss)

Diagnòstic:

- no és codi mort
- és infraestructura de convivència
- s’ha de reduir, però no amb una poda “a ull”

Acció recomanada:

- tall específic d’infra després del primer inventari

## 4. Peces que apareixen com a següent pilot clar

### 4.1. Incidències

Fitxers actuals:

- [`app/Http/Controllers/PanelIncidenciaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/PanelIncidenciaController.php)
- [`app/Http/Controllers/IncidenciaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/IncidenciaController.php)
- [`resources/views/intranet/partials/profile/incidencia.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/intranet/partials/profile/incidencia.blade.php)
- [`routes/mantenimiento.php`](/Users/igomis/Code/intranetBatoi/routes/mantenimiento.php)

Diagnòstic:

- és el mòdul de panell que ha quedat clarament fora del patró nou
- no és codi mort
- és el millor candidat de modernització estructural

Acció recomanada:

- següent pilot funcional després del primer tall de poda segura

## 5. Proposta de primer tall executable

### Tall A1. Confirmació de rutes `*-livewire`

Passos:

1. buscar si queden referències internes a estes rutes
2. si no n’hi ha, eliminar redireccions de compatibilitat
3. afegir nota curta a documentació de sprint

Resultat:

- completat per a `comision`, `expediente`, `actividad` i `falta`

### Tall A2. Inventari de flux birrets/ITACA

Passos:

1. localitzar totes les rutes i entrades de menú
2. comprovar si hi ha vistes o JS encara carregats des del flux viu
3. decidir si es retira complet o es deixa congelat

### Tall A3. Preparació del pilot d’incidències

Passos:

1. mapar les accions actuals de `PanelIncidenciaController`
2. decidir abast del primer Livewire nou
3. separar què queda en bridge temporal

## Conclusió

El primer tall segur no és “esborrar molt”, sinó:

1. confirmar i podar redireccions de compatibilitat ja inútils
2. aïllar fluxos deprecated grossos sense tocar-los encara
3. usar incidències com a següent modernització estructural real
# Execució addicional

- Retirat el residual web de `birret/ITACA` que encara quedava penjant de mensuals.
- Simplificat [`app/Http/Controllers/MensualController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/MensualController.php) perquè només genere el llistat vigent d'absències.
- Eliminada l'opció "Oblit Birret" de [`resources/views/falta/imprime.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/falta/imprime.blade.php).
- Eliminats els punts d'entrada web orfes:
  - `app/Http/Controllers/FaltaItacaController.php`
  - `app/Http/Controllers/ItacaController.php`
  - `resources/views/falta/itaca.blade.php`
### 2.2. Incidències en migració a panell nou

Fitxers clau:

- [`app/Livewire/IncidenciaMantenimientoPanel.php`](/Users/igomis/Code/intranetBatoi/app/Livewire/IncidenciaMantenimientoPanel.php)
- [`resources/views/incidencia/livewire-panel.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/incidencia/livewire-panel.blade.php)
- [`resources/views/livewire/incidencia-mantenimiento-panel.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/livewire/incidencia-mantenimiento-panel.blade.php)
- [`app/Http/Controllers/MantenimientoIncidenciaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/MantenimientoIncidenciaController.php)

Acció executada:

- la portada de manteniment d'incidències ja entra per panell Livewire nou
- el flux de transicions i ordres de treball ha quedat separat del CRUD de professorat

Pendent:

- retirar el controller legacy `PanelIncidenciaController`
- desacoblat `PanelOrdenTrabajoController` de la vista `profile.incidencia` mitjançant `profile.orden_incidencia`
- eliminada la vista legacy orfe `resources/views/intranet/partials/profile/incidencia.blade.php`
- queda fora d'este tall la neteja de textos/config històrics (`Dual`, `Birret`, `Fctdual`) perquè requerix revisió funcional de menús
