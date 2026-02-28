# Estudi realista: Gentelella, Tailwind i eliminació de `Form::` / `Field::`

## 1) Estat actual (foto real del codi)

### Frontend/layout
- El layout principal (`resources/views/layouts/intranet.blade.php`) carrega:
  - `mix('css/gentelella.css')`
  - `mix('js/gentelella.js')`
  - `mix('js/ppIntranet.js')`
- Hi ha acoblament directe a classes de Gentelella (`nav-md`, `main_container`, `left_col`, `right_col`, `x_panel`, `x_title`, `nav_menu`, etc.) en almenys **40 referències** en vistes.
- Build actual amb Laravel Mix (`webpack.mix.js`) concatena molts plugins legacy (jQuery + bootstrap plugin ecosystem).

### Versions i stack
- `package.json` actual:
  - `gentelella: ^1.4.0`
  - `bootstrap: ^4.0.0`
  - `vue: ^2.5.7`
  - `laravel-mix: ^6.0.13`
- No hi ha Tailwind instal·lat en el projecte.

### Formularis (`Form::` / `Field::`)
- Referències detectades:
  - `Form::` -> **42**
  - `Field::` -> **16**
  - Total -> **58**
- També hi ha dependència col·lateral de `Html::style/script/image` en vistes (aprox. **88** referències), que no és objectiu principal però condiciona la neteja final.

## 2) Opcions estratègiques

## Opció A. Mantindre Gentelella actual (mínim risc, mínim guany)
- Què és:
  - No canvi d’UI framework.
  - Només sanejament i reducció de deute tècnic.
- Pros:
  - Risc funcional baix.
  - Cost curt termini baix.
- Contres:
  - Manté stack legacy (jQuery-heavy, bootstrap antic, Mix + Vue2 antic).
  - No resol modernització ni UX.
- Cost estimat:
  - 1-2 setmanes (neteja i estabilització).

## Opció B. Actualitzar a Gentelella modern (v2.x) sense canviar a Tailwind
- Què és:
  - Migrar de Gentelella `^1.4.0` a línia moderna (bootstrap 5, estructura nova de build).
  - Adaptar classes i JS personalitzat.
- Pros:
  - Menys canvi visual que un redisseny total.
  - Millora de dependències i seguretat frontend.
- Contres:
  - No és “drop-in”; hi ha trencaments en CSS/JS i plugins.
  - Continua sent plantilla externa amb estils/opinions pròpies.
- Cost estimat:
  - 4-8 setmanes segons cobertura de pantalles.

## Opció C. Migrar a plantilla/base pròpia amb Tailwind
- Què és:
  - Construir layout i components amb Tailwind (i opcionalment component library).
  - Retirar progressivament Gentelella, jQuery plugins i CSS legacy.
- Pros:
  - Control total de disseny i consistència.
  - Millor mantenibilitat a mitjà/llarg termini.
  - Modernització real (build i frontend).
- Contres:
  - Cost inicial major.
  - Requereix pla per no parar evolutiu funcional.
- Cost estimat:
  - 8-14 setmanes (incremental per mòduls).

## 3) Recomanació pragmàtica

Recomanació realista: **estratègia híbrida en 2 tracks**.

1. Track A (immediat): eliminar `Form::` i `Field::` (independent de la plantilla).
2. Track B (UI): després decidir entre:
   - B1: pujar a Gentelella modern per reduir risc de salt, o
   - B2: anar directament a Tailwind si s’accepta major cost inicial.

Açò redueix risc perquè el desacoblament de formularis és útil en qualsevol escenari.

## 4) Pla per eliminar `Form::` i `Field::` (sense bloquejar l’app)

## Fase 0. Congelar API legacy
- Mantindre l’adapter actual de compatibilitat (`Field` propi) per no trencar.
- Prohibir nous usos de `Form::` i `Field::` en codi nou.

## Fase 1. Components Blade base
- Crear/estandarditzar components:
  - `x-form.form`, `x-form.input`, `x-form.textarea`, `x-form.select`, `x-form.checkbox`, `x-form.radio-group`, `x-form.file`.
- Incloure gestió centralitzada de:
  - `old()`, errors, labels, required, help text, classes.

## Fase 2. Migració per volum (quick wins)
- Convertir primer fitxers amb més pes:
  - `resources/views/email/view.blade.php` (23 usos)
  - `resources/views/extraescolares/partials/value.blade.php`
  - `resources/views/themes/bootstrap/formodal.blade.php`
  - `resources/views/components/form/dynamic-model-form.blade.php`
- Després la resta de parcials menuts.

## Fase 3. Retirada final
- Quan `Form::/Field::` = 0:
  - llevar alias de config,
  - retirar dependències i wrappers innecessaris,
  - executar regressió de formularis crítics.

### Cost estimat del track `Form::/Field::`
- 1-2 setmanes de treball efectiu (58 usos actuals).

## 5) Pla UI: Gentelella modern o Tailwind

## Si triem Gentelella modern (pas intermedi)
- Migrar layout base i menú.
- Adaptar JS de sidebar/topnav (`resources/assets/js/custom.js`) a la nova estructura.
- Revisar plugins: DataTables, daterangepicker, wizard, dropzone, etc.
- Benefici: menys xoc visual inicial.

## Si triem Tailwind (objectiu final)
- Preparar design tokens i components base (layout, cards, forms, tables, alerts).
- Migració per mòduls:
  - home + navegació
  - pantalles CRUD comunes
  - mòduls especials (gràfiques, calendari, dropzone, etc.)
- Convivència temporal: Tailwind + CSS legacy per evitar big-bang.

## 6) Riscos reals i mitigacions

- Risc: regressions visuals en pàgines antigues.
  - Mitigació: migració per mòdul + checklist visual + captures abans/després.
- Risc: plugins jQuery sense equivalent directe.
  - Mitigació: encapsular per pàgina; substituir només quan toque eixa pantalla.
- Risc: allargar terminis per dependències ocultes.
  - Mitigació: fer PoC de 2 pantalles representatives abans de comprometre calendari final.

## 7) Proposta de calendari executable

1. Setmana 1:
   - tancar `Form::`/`Field::` en fitxers de més volum,
   - definir components base de formulari.
2. Setmana 2:
   - deixar `Form::`/`Field::` a zero,
   - QA de fluxos CRUD principals.
3. Setmana 3:
   - PoC UI (una pantalla amb Gentelella modern i una amb Tailwind) i decisió final.
4. Setmanes següents:
   - migració UI per mòduls segons opció triada.

## 8) Fonts externes (estat de Gentelella)

- Repositori oficial: https://github.com/ColorlibHQ/gentelella
- Releases: https://github.com/ColorlibHQ/gentelella/releases
- Web oficial: https://gentelella.com/

