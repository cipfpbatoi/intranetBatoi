# Sprint 32 - Reservas queda en Blade + JS

## Decisió

`reservas` no continua en `Vue`.

El flux real queda consolidat en Laravel clàssic:

- vista Blade
- script específic `public/js/Reserva/edit.js`
- backend i permisos ja acoblats al controlador Laravel

No es justifica migrar esta branca morta a `Vue 3` ni reescriure-la ara en `Livewire`.

## Evidència revisada

- la vista viva és [`resources/views/reservas/reserva.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/reservas/reserva.blade.php)
- la interacció funcional real està en [`public/js/Reserva/edit.js`](/Users/igomis/Code/intranetBatoi/public/js/Reserva/edit.js)
- no hi ha cap punt de muntatge viu de `reservas-view`
- els components Vue de `reservas` només quedaven referenciats des de [`resources/assets/js/app.js`](/Users/igomis/Code/intranetBatoi/resources/assets/js/app.js)

## Execució feta

- retirada la importació residual de `ReservasView` de [`resources/assets/js/app.js`](/Users/igomis/Code/intranetBatoi/resources/assets/js/app.js)
- eliminats:
  - [`resources/assets/js/components/reservas/ReservasView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/ReservasView.vue)
  - [`resources/assets/js/components/reservas/RecursosSelect.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/RecursosSelect.vue)
  - [`resources/assets/js/components/reservas/HorasTable.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/HorasTable.vue)
  - [`resources/assets/js/components/reservas/ProfesSelect.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/ProfesSelect.vue)
  - [`resources/assets/js/components/reservas/HorasSelect.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/HorasSelect.vue)

## Resultat

- desapareix la branca Vue 2 sense ús real
- `reservas` queda classificat amb una direcció clara
- es reduïx codi mort del runtime frontend
