# Sprint 32 - Decidir reservas entre Vue 3 o Livewire

## Objectiu

Decidir el futur del bloc `reservas`, triant si convé:

- migrar-lo a `Vue 3`
- o reescriure'l en `Livewire`

## Punt de partida

L'auditoria del Sprint 17 ha classificat `reservas` com el bloc més ambigu:

- té interacció rica i estat client
- però també està acoblat a formulari, permisos i backend Laravel

Peces implicades:

- [`resources/assets/js/components/reservas/ReservasView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/ReservasView.vue)
- [`resources/assets/js/components/reservas/RecursosSelect.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/RecursosSelect.vue)
- [`resources/assets/js/components/reservas/HorasTable.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/HorasTable.vue)
- [`resources/assets/js/components/reservas/ProfesSelect.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/ProfesSelect.vue)
- [`resources/assets/js/components/reservas/HorasSelect.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/reservas/HorasSelect.vue)
- [`app/Http/Controllers/ReservaController.php`](/Users/igomis/Code/intranetBatoi/app/Http/Controllers/ReservaController.php)

## Pregunta clau

És més valuós mantindre una UI client rica en `Vue 3` o acostar el flux a Laravel amb `Livewire`?

## Talls proposats

### Tall A. Inventari funcional

- localitzar el punt de muntatge real i el flux d'ús actual
- identificar dependències exactes amb API i permisos

### Tall B. Decisió tecnològica

- `Vue 3` si es prioritza interacció client i continuïtat del model actual
- `Livewire` si es prioritza simplificar integració amb backend i formularis

### Tall C. Pla d'execució

- descompondre la peça en subblocs
- decidir si hi ha convivència temporal o reescriptura més directa

## Criteri d'acceptació

- `reservas` queda classificat amb una direcció clara
- hi ha un pla executable i no ambigu
- es redueix la incertesa principal del bloc Vue 2 restant
