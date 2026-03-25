# Sprint 33 - Extracció del runtime de fichar

## Objectiu

Separar `fichar` del bundle global `resources/assets/js/app.js` per deixar el mòdul preparat per a la migració posterior a `Vue 3`.

## Problema detectat

El Sprint 30 definia `fichar` com a pilot de migració a `Vue 3`, però el runtime real continua sent:

- `vue@2`
- `@vitejs/plugin-vue2`
- un únic entrypoint global per a peces heterogènies (`fichar`, `guardias`, `reservas`)

Això fa que migrar `fichar` directament implique tocar el mateix bundle on encara viuen altres blocs no decidits.

## Decisió

Abans d'introduir `Vue 3`, es farà un tall de preparació:

- moure `fichar` a un entrypoint propi
- carregar-lo només en les seues vistes
- mantindre `guardias` i `reservas` en el bundle global actual

## Abast

- crear `resources/assets/js/fichar-app.js`
- llevar els components de `fichar` de `resources/assets/js/app.js`
- afegir l'entrypoint específic a `vite.config.mjs`
- carregar-lo només en:
  - [`resources/views/fichar/control.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/fichar/control.blade.php)
  - [`resources/views/fichar/resumen-rango.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/fichar/resumen-rango.blade.php)

## Resultat esperat

- `fichar` queda desacoblat del runtime global
- el següent sprint pot atacar `Vue 3` només sobre `fichar`
- es redueix el risc de regressió sobre `guardias` i `reservas`

## Fora d'abast

- instal·lar encara `vue@3`
- migrar sintaxi de components
- resoldre el destí final de `guardias` o `reservas`

## Criteri d'acceptació

- les dos vistes de `fichar` continuen carregant correctament
- `app.js` deixa de registrar components de `fichar`
- el build reconeix un entrypoint independent per a `fichar`
