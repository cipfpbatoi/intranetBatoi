# Sprint 3 P4 - Pla de retirada progressiva de legacy en `direccion/actividad`

## Estat actual

El panell principal de Direcció d'activitats ja és:

- `/direccion/actividad`

I la ruta antiga de prova queda com a redirecció de compatibilitat:

- `/direccion/actividad-livewire` -> `/direccion/actividad`

El panell nou ja resol una part important del flux de Direcció, però encara reutilitza diverses peces del mòdul legacy. Açò implica que **encara no convé retirar tot el legacy** sense desacoblar abans les dependències que queden.

## Peces implicades

- `routes/direccion.php`
  - `GET /direccion/actividad` -> vista `resources/views/actividad/livewire-panel.blade.php`
  - `GET /direccion/actividad-livewire` -> redirecció de compatibilitat
  - component `app/Livewire/ActividadDireccionPanel.php`

### Controller legacy encara reutilitzat

- `app/Http/Controllers/ActividadController.php`

## Què ja substituïx el panell Livewire

El panell nou de `ActividadDireccionPanel` ja resol:

- llistat d'activitats extraescolars de Direcció
- filtre textual per professor o nom d'activitat
- filtre per estat
- filtre per departament
- paginació
- autoritzar activitat pendent
- tornar una activitat registrada a l'estat anterior
- rebutjar activitat amb motiu
- marcar activitat com a tramitada en ITACA
- mostrar detall en modal
- mostrar document associat des del modal
- mostrar PDF de valoració des del modal quan toca
- mostrar botons globals condicionals per:
  - autoritzar pendents
  - imprimir autoritzades
- ser la ruta principal de Direcció
- resoldre les accions de fila principals directament des del component Livewire:
  - autoritzar
  - tornar a l'estat anterior
  - rebutjar
  - marcar ITACA
  - veure en modal

## Què encara depén del legacy

### 1. Bulk autoritzar activitats pendents

Ja desacoblat del controller legacy:

- `routes/direccion.php`
  - `GET /direccion/actividad/autorizar`
- `app/Http/Controllers/Direccion/Actividad/AuthorizeController.php`

Punt important:

- este mètode no només canvia estat
- també sincronitza Google Calendar si hi ha credencials

### 2. Imprimir activitats autoritzades

Ja desacoblat del controller legacy:

- `routes/direccion.php`
  - `GET /direccion/actividad/pdf`
- `app/Http/Controllers/Direccion/Actividad/PrintController.php`

### 3. Gestor documental

Ja desacoblat del controller legacy:

- `routes/direccion.php`
  - `GET /direccion/actividad/{actividad}/gestor`
- `app/Http/Controllers/Direccion/Actividad/GestorController.php`

### 4. PDF i vista de valoració

El modal del pilot ja no depén del controller legacy per al PDF de valoració:

- `routes/direccion.php`
  - `GET /direccion/actividad/{actividad}/pdfVal`
- `app/Http/Controllers/Direccion/Actividad/ValuePdfController.php`

La vista de valoració encara continua en legacy:

- `routes/profesor.php`
  - `GET /actividad/{actividad}/showVal`
- `app/Http/Controllers/ActividadController.php::showValue()`

### 5. Show/detall complet legacy

Encara existix el flux complet de detall i gestió:

- `routes/profesor.php`
  - `GET /actividad/{actividad}/detalle`
- `app/Http/Controllers/ActividadController.php::detalle()`
- vistes associades d'`extraescolares`

Ara mateix Direcció no depén d'esta ruta per al flux principal. Continua sent part important del mòdul legacy de professorat.

### 6. Edició i esborrat

El pilot actual encara **no** ha substituït:

- editar activitat
- esborrar activitat

Per tant, el legacy continua sent l'únic camí funcional complet per a eixes operacions.

## Peces legacy que no s'han de tocar encara

No convé eliminar encara:

- `ActividadController::showValue()`
- `ActividadController::detalle()`
- les rutes de professorat associades al detall i valoració

Motiu:

- continuen tenint rutes actives
- continuen sent el flux operatiu de professorat
- algunes encapsulen més lògica de negoci que una simple redirecció

## Peces candidates a bridge

En `ActividadController` hi ha actualment una barreja de:

- CRUD clàssic
- detall ampliat
- autorització d'estats
- integració amb Google Calendar
- valoració posterior
- ITACA

La retirada correcta no passa per esborrar el controller, sinó per convertir-lo progressivament en un **bridge**.

## Ordre recomanat de retirada

### Fase 1. Consolidar el pilot de Direcció

Objectiu:

- que el panell Livewire cobrisca clarament el cas principal de Direcció

Accions:

- afegir el que falte del flux real de Direcció
- validar funcionalment amb usuaris reals o checklist manual

### Fase 2. Desacoblar accions globals

Objectiu:

- que el pilot nou deixe de dependre de rutes legacy per a les accions massives

Accions:

- bulk autoritzar i impressió d'autoritzades ja desacoblats en controllers de Direcció
- gestor documental i PDF de valoració ja desacoblats en controllers de Direcció
- les accions de fila de Direcció ja no depenen de rutes pròpies del legacy
- queda revisar si convé extraure més lògica comuna a servici específic

Esta és la primera fase amb millor retorn tècnic.

### Fase 3. Decidir l'abast del detall

Objectiu:

- decidir si el modal actual és suficient per a Direcció o si cal un detall més ric

Accions possibles:

- mantindre el modal i deixar el detall legacy només per professorat
- o portar més dades/accions al panell nou

Ací convé no migrar “per migrar”: només el que Direcció utilitza realment.

### Fase 4. Atacar edició i esborrat

Objectiu:

- que Direcció no haja de tornar al panell legacy per a operacions bàsiques

Accions:

- decidir si l'edició va en modal Livewire
- o si s'obri un formulari específic nou

### Fase 5. Retirada visible del legacy

Només quan el pilot cobrisca el flux principal de Direcció:

- amagar l'accés a `/direccion/actividad-livewire`
- deixar el legacy temporalment només per compatibilitat interna
- simplificar `ActividadController`

## Criteri de retirada segura

Una peça legacy d'`actividad` només s'hauria d'eliminar si es complixen les tres:

1. el panell nou ja no la crida
2. el flux equivalent està cobert funcionalment
3. no queda cap altra ruta rellevant depenent d'eixa peça

## Diferència respecte a `comision` i `falta`

`actividad` està en un punt intermig:

- més avançat que el legacy pur perquè ja hi ha un panell nou funcional
- menys desacoblat que `falta` en algunes operacions
- més complex que `comision` per les integracions laterals:
  - Google Calendar
  - valoració
  - ITACA
  - detall ampliat amb participants

Per això en este mòdul convé anar amb més disciplina i menys pressa.

## Recomanació immediata

El següent treball amb millor retorn és:

1. documentar quines parts del detall legacy són de professorat i quines val la pena mantindre només fora de Direcció
2. simplificar `ActividadController` una vegada quede clar l'abast real
3. executar regressió manual curta del panell final de Direcció

Actualització:

- `ActividadController::autorizar()` i `ActividadController::printAutoritzats()` ja no tenen sentit per a Direcció i es poden considerar retirats del flux actual

## Decisió pràctica

No convé llevar el legacy d'`actividad` ara.

Sí convé:

- identificar-lo
- desacoblar primer les accions globals i els accessos documentals de Direcció
- mantindre el detail legacy mentre no estiga clar què necessita exactament Direcció

En este mòdul, el primer objectiu bo no és “recrear tot el legacy”, sinó **fer que el panell nou deixe de dependre de rutes legacy per a les accions massives i de control**.

Actualització:

- Direcció ja no necessita `show` complet, només el modal enriquit, document i valoració
