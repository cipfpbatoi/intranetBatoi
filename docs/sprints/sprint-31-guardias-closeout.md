# Sprint 31 - Guardias queda en Livewire

## Decisió

`guardias` queda consolidat en `Livewire`.

No es continua la via `Vue 2` ni es planteja migració a `Vue 3` per a este bloc.

## Evidència revisada

- la vista real de control és [`resources/views/guardias/control.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/guardias/control.blade.php) i monta [`app/Livewire/Controlguardia.php`](/Users/igomis/Code/intranetBatoi/app/Livewire/Controlguardia.php)
- no hi ha cap punt de muntatge viu de `control-guardia-view`
- els components Vue de `guardias` només quedaven referenciats des de [`resources/assets/js/app.js`](/Users/igomis/Code/intranetBatoi/resources/assets/js/app.js)

## Execució feta

- retirada la importació i registre global de `ControlGuardiaView` de [`resources/assets/js/app.js`](/Users/igomis/Code/intranetBatoi/resources/assets/js/app.js)
- eliminats:
  - [`resources/assets/js/components/guardias/ControlGuardiaView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/guardias/ControlGuardiaView.vue)
  - [`resources/assets/js/components/guardias/ControlGuardiaItem.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/guardias/ControlGuardiaItem.vue)

## Resultat

- `guardias` queda amb una única tecnologia activa
- desapareix l'ambigüitat entre Vue i Livewire
- es reduïx codi mort en el bundle Vue legacy
