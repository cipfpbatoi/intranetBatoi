# Estudi de desacoblament de paquets conflictius

## Objectiu
Reduir l'acoblament del codi a paquets antics, forks locals i dependències de binaris externs, mantenint la funcionalitat actual.

## Resum executiu
Paquets i blocs amb més deute tècnic:

1. `mikehaertl/php-pdftk` (`0.8.1`, publicat en 2020)
2. `h4cc/wkhtmltopdf-amd64` (`0.12.4`, publicat en 2018, binari Linux `amd64`)
3. `igomis/laravel-html` (fork local de `styde/html`)
4. Capa legacy de `Jenssegers\Date\Date` (shim sobre Carbon)

Els dos primers afecten PDF i portabilitat. El tercer afecta una gran part de la UI (alerts, forms, helpers HTML). El quart no és un paquet instal·lat, però sí una API legacy molt utilitzada que convé retirar progressivament.

## Inventari d'ús actual

### Dependències Composer implicades
- `composer.json:29` `mikehaertl/php-pdftk`
- `composer.json:17` `barryvdh/laravel-snappy`
- `composer.json:20` `h4cc/wkhtmltopdf-amd64`
- `composer.json:10` `barryvdh/laravel-dompdf` (alternativa ja instal·lada)
- `composer.json:21` `igomis/laravel-html` (path repository a `packages/html`)
- `packages/html/composer.json:11` depén de `laravelcollective/html`

### Configuració i entorn
- `config/snappy.php:7` binari hardcoded a `vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64`
- `.env:54` i plantilles d'entorn: `PDF_DRIVER="SnappyPdf"`
- `config/constants.php:9` driver per defecte `SnappyPdf`
- `docs/setup.md:118` documenta ús de `wkhtmltopdf`
- `config/app.php:156` registra `Collective\Html\HtmlServiceProvider`
- `config/app.php:170` registra `Intranet\Providers\HtmlServiceProvider`
- `config/app.php:223` alias global `Date => Jenssegers\Date\Date::class`
- `composer.json:60` autoload de `bootstrap/legacy_jenssegers_date.php`

### Codi acoblat a `mikehaertl/php-pdftk`
- Serveis centrals:
  - `app/Services/Document/FDFPrepareService.php:22`
  - `app/Services/Document/DocumentService.php:276`
  - `app/Services/Document/DocumentService.php:313`
- Controladors actius:
  - `app/Http/Controllers/ColaboracionController.php:145`
  - `app/Http/Controllers/ColaboracionController.php:155`
- Flux legacy (deprecated):
  - `app/Http/Controllers/Deprecated/DualController.php:23` i múltiples crides
- Altres punts de concatenació via servei:
  - `app/Console/Commands/UploadAnexes.php:114`
  - `app/Http/Controllers/PanelFctAvalController.php:505`

### Codi acoblat a `igomis/laravel-html` / `styde/html`
- Alertes (`Styde\Html\Facades\Alert`) repartides en gran part de controladors i serveis.
- Components de formulari i plantilles:
  - `resources/views/components/form/*.blade.php` (`Field::...`, `Form::...`)
  - múltiples vistes amb `Html::script`, `Html::style`, `Html::image`
- Middleware:
  - `app/Http/Kernel.php:34` `\Styde\Html\Alert\Middleware::class`
- Proveïdor personalitzat:
  - `app/Providers/HtmlServiceProvider.php:10` amplia `Styde\Html\HtmlServiceProvider`

### Codi acoblat a `Jenssegers\Date\Date` (legacy)
- Alias global:
  - `config/app.php:223`
- Shim de compatibilitat:
  - `bootstrap/legacy_jenssegers_date.php:1` (classe `Jenssegers\Date\Date` que hereta de Carbon)
- Ús extensiu en helpers:
  - `app/Support/Helpers/DateHelpers.php:5`
  - ús de `Date::setlocale(...)` en diversos punts (`DateHelpers`, controladors legacy)

## Tipus de risc

1. **Portabilitat**
   - `h4cc/wkhtmltopdf-amd64` no és neutre per plataforma.
2. **Mantenibilitat**
   - `mikehaertl/php-pdftk` i el flux `pdftk` estan molt repartits.
3. **Acoblament transversal de UI**
   - `styde/html` està en middleware, facades, formularis, vistes i serveis.
4. **Debt de compatibilitat de dates**
   - `Jenssegers\Date\Date` es manté artificialment amb shim; és codi pont que cal retirar.
5. **Evolució tècnica**
   - Qualsevol actualització de stack obliga a continuar carregant binaris externs i APIs legacy.
6. **Cobertura de tests limitada en PDF real**
   - Hi ha tests unitaris de servei, però no cobertura d'integració de render/merge/fill amb binaris.

## Proposta de desacoblament per fases

## Fase 1 (quick win, baix risc, PDF)
Objectiu: desacoblar configuració de binaris del `vendor`.

1. Fer configurable `wkhtmltopdf` per env (`WKHTMLTOPDF_BINARY`) i deixar de dependre de `h4cc`.
2. Mantindre `barryvdh/laravel-snappy` temporalment.
3. Verificar generació de PDFs en Docker i host.

Resultat: es pot eliminar `h4cc/wkhtmltopdf-amd64` sense canviar flux funcional.

## Fase 2 (baix-mig risc, PDF)
Objectiu: traure `pdftk` de les operacions de concatenació.

1. Crear `PdfMergeServiceInterface`.
2. Implementar adapter amb `setasign/fpdi` (ja present al projecte).
3. Substituir:
   - `FDFPrepareService::joinPDFs()`
   - concatenacions en `DocumentService`.

Resultat: part important de `mikehaertl/php-pdftk` queda fora de la via principal.

## Fase 3 (mig-alt risc, PDF)
Objectiu: encapsular i reduir `pdftk` per a formularis.

1. Crear `PdfFormServiceInterface` (fill, flatten, stamp).
2. Moure tota la lògica de `FDFPrepareService` a un adapter d'infraestructura.
3. Eliminar instanciacions directes `new Pdf(...)` de controladors (`ColaboracionController`, legacy).

Resultat: el domini deixa de dependre del paquet; sols la infraestructura podria usar-lo temporalment.

## Fase 4 (opcional, alt impacte, PDF)
Objectiu: eliminar `mikehaertl/php-pdftk` del tot.

Opcions:
1. Reemplaçar plantilles FDF/PDF per plantilles Blade + render HTML->PDF.
2. O adoptar una llibreria de formularis PDF mantinguda (si es valida funcionalment).

Resultat: eixida completa de `pdftk`.

## Fase 5 (mig-alt risc, UI `styde/html`)
Objectiu: desacoblar la capa HTML/Form/Alert de `igomis/laravel-html`.

1. Introduir capa pròpia de notificacions (ex: `AppAlert`) per no dependre de `Styde\Html\Facades\Alert`.
2. Migrar progressivament vistes de `Form::`, `Html::`, `Field::` a Blade components/helpers propis.
3. Retirar middleware de `Styde\Html\Alert\Middleware` quan la capa pròpia estiga completa.
4. Deixar `igomis/laravel-html` només com a compatibilitat temporal mentre es migren mòduls.

Resultat: reducció del lock-in de UI i simplificació futura de Laravel.

## Fase 6 (baix-mig risc, dates legacy)
Objectiu: eliminar l'API antiga `Jenssegers\Date\Date` i el shim.

1. Substituir imports de `Jenssegers\Date\Date` per `Illuminate\Support\Carbon`.
2. Reemplaçar `Date::setlocale(...)` per `Carbon::setLocale(...)` o configuració centralitzada.
3. Actualitzar `app/Support/Helpers/DateHelpers.php` a Carbon natiu.
4. Eliminar alias `Date` de `config/app.php` i traure `bootstrap/legacy_jenssegers_date.php` de l'autoload.

Resultat: menys compatibilitat artificial i API de dates estàndard.

## Recomanació concreta
Ordre recomanat:

1. **Primer** eliminar `h4cc/wkhtmltopdf-amd64` (impacte baix, guany alt).
2. **Després** migrar merges a FPDI.
3. **Finalment (bloc PDF)** atacar formularis `pdftk` amb adapter i retirada progressiva del paquet.
4. **En paral·lel**, iniciar migració de `styde/html` per mòduls (començant per alerts i components de formulari més utilitzats).
5. **Tancar** amb eliminació del shim de `Jenssegers\Date\Date`.

## Validació mínima per fase

1. Tests unitaris de serveis de document.
2. Test d'integració de:
   - PDF simple per vista
   - ZIP de múltiples PDFs
   - formulari emplenat + flatten
   - concatenació final
3. Test funcional de formularis i render de components Blade afectats per `Form/Html/Field`.
4. Test de helpers de data (`fechaString`, `month`, `hour`) amb idioma `ca` i `es`.
5. Prova manual de rutes clau de secretaria i col·laboració.

## Cost estimat

1. Fase 1: 0.5-1 dia
2. Fase 2: 1-2 dies
3. Fase 3: 2-4 dies
4. Fase 4: variable (segons volum de plantilles FDF)
5. Fase 5: 4-8 dies (segons volum de vistes amb `Form/Html/Field`)
6. Fase 6: 1-2 dies (si es fa després de neteja prèvia de helpers)

## Nota addicional (tooling)
S'ha observat que la versió local de Composer mostra molts avis deprecats en PHP 8.3 durant `composer why`. No bloqueja l'estudi, però convé actualitzar Composer per reduir soroll i risc operatiu.
