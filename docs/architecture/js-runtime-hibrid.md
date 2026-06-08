# Runtime JS híbrid (legacy + Vite)

## Objectiu

Mantindre estable la Intranet legacy basada en jQuery i, al mateix temps, permetre migrar gradualment cap a Vite.

## Modes disponibles

- `legacy`: només carrega scripts clàssics de `public/js`.
- `hybrid`: carrega scripts clàssics i també `@vite('resources/assets/js/app.js')`.
- `vite`: carrega només bundles Vite (`legacy-app.js`, `app.js`, `ppIntranet.js`).

## Configuració actual

- Per defecte, els layouts funcionen en mode `hybrid`.
- Layout component (`x-layouts.app`): prop `jsMode`.
- Layout clàssic (`layouts.intranet`): secció Blade `js_mode`.

## Exemples d'ús

### Component layout

```blade
<x-layouts.app title="Pantalla nova" jsMode="vite">
    ...
</x-layouts.app>
```

### Layout clàssic

```blade
@extends('layouts.intranet')
@section('js_mode', 'legacy')
```

## Pla de retirada de legacy (futur)

1. Migrar scripts de `public/js/<Model>/*.js` a `resources/assets/js/...` per mòduls.
2. En cada pantalla migrada, canviar `jsMode` a `vite`.
3. Quan no quede cap pantalla en `legacy` o `hybrid`, eliminar:
   - càrrega de `public/js/app.js`
   - càrrega de `public/js/ppIntranet.js`
4. Fer neteja final de fitxers legacy en `public/js`.

## Estat actualitzat

- La càrrega de `public/js/components/app.js` ja s'ha retirat dels layouts.
- `Vue 2` ja no forma part del codi executable del projecte.
- El runtime híbrid continua existint perquè encara conviuen `legacy-app.js`, `app.js` i `ppIntranet.js`.
