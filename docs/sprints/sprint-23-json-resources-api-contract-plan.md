# Sprint 23 - Contractes API amb JsonResource

Issue remot:
- pendent de crear

## Objectiu

Eliminar la serialització implícita basada en models Eloquent dins dels endpoints API i passar a contractes explícits amb `JsonResource`.

## Problema actual

Ara mateix una part de l'API continua depenent de:

- `sendResponse($item)` sobre models sencers
- accessors de presentació del model
- `fillable` i `inputTypes` com a heurística per construir payloads
- decisions de format dins de controladors genèrics

Açò provoca:

- contractes d'eixida poc explícits
- acoblament entre API i detalls interns del model
- regressions fàcils quan canvia un accessor o un `fillable`
- dificultat per distingir payload de domini i payload de formulari

## Principi de disseny

- cada endpoint ha de tindre un contracte d'eixida explícit
- eixe contracte ha de viure en un `JsonResource` o `ResourceCollection`
- els models no han de decidir per si sols el JSON públic
- `show`, `index` i `edit` poden necessitar recursos diferents
- `edit` ha de considerar-se un payload de formulari, no una representació general del model

## Tall A. Inventari de serialització actual

- localitzar endpoints API que retornen:
  - models sencers
  - col·leccions de models
  - arrays híbrids construïts a mà
- separar-los per patró:
  - `index`
  - `show`
  - `edit`
  - endpoints específics de negoci

## Tall B. Classificació de recursos

- definir tres famílies de recursos:
  - recursos de lectura (`ShowResource`)
  - recursos de llistat (`IndexResource` o col·leccions)
  - recursos de formulari (`EditResource`)
- evitar reutilitzar el mateix resource per a casos amb contracte diferent

## Tall C. Pilot sobre dominis prioritaris

- migrar primer els dominis ja tocats en el sprint de dates:
  - `Actividad`
  - `Comision`
  - `Expediente`
  - `AlumnoFct`
- crear:
  - `ActividadEditResource`
  - `ComisionEditResource`
  - `ExpedienteEditResource`
  - `AlumnoFctEditResource`

## Tall D. Desacoblament del controlador base

- reduir responsabilitats de [`ApiResourceController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/API/ApiResourceController.php)
- llevar-li la lògica de “deduir” payloads d'edició
- deixar-lo com a infraestructura mínima o com a fallback temporal

## Tall E. Regles de format

- els `EditResource` han de retornar format canònic per camp:
  - `date` -> `YYYY-MM-DD`
  - `datetime-local` -> `YYYY-MM-DDTHH:mm` o contracte pactat si el camp encara és legacy
  - `time` -> `HH:mm`
  - `checkbox` -> `0/1` o `true/false`, però amb criteri únic
- els `ShowResource` poden usar format de lectura si així ho requerix la UI
- no barrejar formats de lectura amb formats d'edició

## Tall F. Proves

- afegir proves per resource i per endpoint
- verificar especialment:
  - camps text presents
  - dates amb format correcte
  - absència de camps interns no desitjats
  - estabilitat del contracte quan canvien accessors del model

## Tall G. Estratègia de migració

- no intentar migrar tota l'API en un sol sprint
- fer rollout per domini
- mantindre compatibilitat temporal només on hi haja dependències reals
- documentar quins endpoints queden encara en serialització legacy

## Resultat esperat

- contractes API explícits i previsibles
- menys dependència d'accessors i `fillable`
- millor separació entre model intern i JSON públic
- millor base per a continuar simplificant frontend i controladors API
