# Sprint 2 - Reduccio de deute tecnic JS legacy

Data: 2026-03-10
Issue: #77

## Objectiu

Reduir efectes colaterals globals de `ppIntranet.js` i `custom.js` i fer la carrega legacy mes controlada per vista.

## Mapa modular actual

### `resources/assets/js/ppIntranet.js`

- Dependencies:
- `jquery`
- DataTables (`datatables.net-*`)
- `jszip`
- `pdfmake`
- Moduls funcionals:
- `confirm`: confirmacions de links/botons amb `data-confirm`.
- `loading-text`: estat de carrega en botons amb `data-loading-text`.
- `paperera`: confirmacio d'esborrat d'avis.
- `fitxar`: confirmacio en eixida de fitxatge.
- `help-popup`: popup d'ajuda.
- `fullscreen`: botó de pantalla completa.

### `resources/assets/js/custom.js`

- Fitxer legacy de Gentelella amb inicialitzadors multipagina.
- Depen de plugins opcionals (flot, morris, echarts, daterangepicker, etc.).
- Manté moltes traces de depuracio (`console.log`) en codi original.

## Canvis aplicats en Sprint 2

- S'evita doble inicialitzacio de `ppIntranet.js` amb flag global:
- `window.__INTRANET_PPINTRANET_INITIALIZED__`
- S'afegeix carrega condicional de funcionalitats de `ppIntranet.js` via:
- `data-legacy-features` en el `<body>`
- S'afegeix politica de logging de depuracio:
- `data-js-debug="1"` activa logs de debug.
- En produccio, els logs de debug no ixen en consola.

## Manual de carrega per vista

Per activar només funcionalitats concretes en una vista Blade:

```blade
@section('legacy_features', 'confirm,loading-text,fullscreen')
```

Si no es defineix `legacy_features`, es manté comportament retrocompatible (s'activen totes).

Per desactivar completament el bundle legacy en una vista:

- Layout clàssic:
- `@section('skip_legacy_js', true)`
- Layout component:
- passar `$skipLegacyJs = true`

## Seguent fase recomanada

- Extraure de `custom.js` blocs de DataTables/formularis a mòduls separats.
- Crear entrypoints específics per domini funcional (`tables`, `forms`, `calendar`).
- Eliminar inicialitzadors de plugins no utilitzats en el projecte.
