# Sprint 30 - Migració de fichar a Vue 3

## Objectiu

Substituir el bloc Vue 2 de `fichar` per `Vue 3`, mantenint la mateixa funcionalitat i deixant-lo com a pilot de migració del frontend reactiu actual.

## Punt de partida

Segons l'auditoria del Sprint 17, el bloc `fichar` és el millor candidat inicial per a `Vue 3` perquè:

- està ben delimitat
- té punts de muntatge clars
- consumix API JSON
- té estat client local i interacció rica

Peces implicades:

- [`resources/assets/js/components/fichar/ControlSemanaView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/fichar/ControlSemanaView.vue)
- [`resources/assets/js/components/fichar/ControlResumenRangoView.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/fichar/ControlResumenRangoView.vue)
- [`resources/assets/js/components/utils/ControlNav.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/utils/ControlNav.vue)
- [`resources/assets/js/components/utils/AppMsg.vue`](/Users/igomis/Code/intranetBatoi/resources/assets/js/components/utils/AppMsg.vue)

Punts de muntatge detectats:

- [`resources/views/fichar/control.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/fichar/control.blade.php)
- [`resources/views/fichar/resumen-rango.blade.php`](/Users/igomis/Code/intranetBatoi/resources/views/fichar/resumen-rango.blade.php)

## Abast

- migrar els components de `fichar` a `Vue 3`
- adaptar el bundle principal si cal
- mantindre el contracte amb les APIs existents
- evitar regressions visuals i funcionals

## Talls proposats

### Tall A. Preparació del runtime

- decidir convivència temporal `Vue 2` + `Vue 3` o extracció progressiva
- ajustar entrypoints i carregadors
- delimitar clarament el nou muntatge de `fichar`

### Tall B. Migració dels components

- `ControlSemanaView`
- `ControlResumenRangoView`
- components auxiliars necessaris

### Tall C. Validació

- comprovació manual de les dos pantalles
- revisió de peticions API
- comprovació del bundle i del mode de càrrega

## Criteri d'acceptació

- el bloc `fichar` deixa de dependre de `Vue 2`
- es manté la funcionalitat actual
- queda com a pilot vàlid per a la resta de migracions
