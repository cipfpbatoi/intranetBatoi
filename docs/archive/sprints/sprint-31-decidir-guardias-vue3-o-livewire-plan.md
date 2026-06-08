# Sprint 31 - Decidir guardias entre Vue 3 o Livewire

## Objectiu

Decidir i executar la retirada de la branca Vue 2 de `guardias`, escollint explícitament entre:

- migració a `Vue 3`
- consolidació definitiva en `Livewire`

## Punt de partida

L'auditoria del Sprint 17 ha vist que:

- encara existixen components Vue 2 de `guardias`
- però també hi ha una pantalla ja basada en `Livewire`

Peces implicades:

- [`resources/assets/js/components/guardias/ControlGuardiaView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/guardias/ControlGuardiaView.vue)
- [`resources/assets/js/components/guardias/ControlGuardiaItem.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/guardias/ControlGuardiaItem.vue)
- [`resources/views/guardias/control.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/guardias/control.blade.php)
- [`app/Livewire/Controlguardia.php`](/Users/igomis/Code/intranetBatoi/app/Livewire/Controlguardia.php)

## Pregunta clau

Cal confirmar si la via funcional bona ja és la de `Livewire`.

Si és així, este sprint no hauria de migrar a `Vue 3`, sinó:

- retirar la resta Vue 2
- sanejar el pipeline
- evitar doble manteniment

## Talls proposats

### Tall A. Inventari funcional

- comprovar si encara hi ha punts de muntatge reals del component Vue 2
- verificar si la pantalla `Livewire` ja cobreix l'ús actual

### Tall B. Decisió

- si `Livewire` ja substituïx la peça: retirada de la branca Vue 2
- si no: decidir migració controlada a `Vue 3`

### Tall C. Sanejament

- eliminar codi, assets o entrypoints innecessaris
- deixar una sola via activa per a `guardias`

## Criteri d'acceptació

- `guardias` queda amb una única tecnologia activa
- desapareix l'ambigüitat actual entre Vue 2 i Livewire
- es redueix deute tècnic del bundle legacy
