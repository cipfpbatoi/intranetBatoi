# Sprint 17 - Auditoria i migració de frontend legacy Vue 2

## Objectiu

Inventariar què queda realment en Vue 2 i decidir, per cada peça viva, si convé:

- migrar-la a Vue 3
- passar-la a Livewire
- o eliminar-la perquè ja és residual

## Problema

Ara mateix el frontend històric de la intranet no respon a un únic model.

Conviuen:

- Blade clàssic
- jQuery i JS legacy
- Livewire en parts noves
- possibles restes de Vue 2

Això fa difícil respondre preguntes bàsiques:

- què queda realment en Vue 2
- què continua viu i què és mort
- què s'hauria de migrar a Vue 3
- i què encaixaria millor en Livewire

## Decisió d'enfoc

No es farà una migració “Vue 2 -> Vue 3” a cegues.

Abans cal decidir destí per bloc funcional.

La tecnologia no és la decisió principal. La decisió principal és el tipus de peça:

- component interactiu desacoblat -> candidat a Vue 3
- panell CRUD fortament lligat a Laravel -> candidat a Livewire
- peça simple o residual -> Blade + JS simple o eliminació

## Fases

### Tall A - Inventari real de Vue 2

- localitzar:
  - dependències Vue 2
  - entrypoints
  - components
  - vistes que els munten
  - punts on encara hi ha muntatge de Vue
- diferenciar:
  - ús viu
  - ús dubtós
  - residual

Fitxers a mirar:

- [`package.json`](/Users/igomis/Code/intranetBatoi/package.json)
- [`webpack.mix.js`](/Users/igomis/Code/intranetBatoi/webpack.mix.js)
- [`resources/assets/js`](/Users/igomis/Code/intranetBatoi/resources/assets/js)
- Blade que monte components

### Tall B - Classificació per destí

Per cada peça trobada:

- `migrar a Vue 3`
- `passar a Livewire`
- `deixar en Blade + JS`
- `retirar`

Esta classificació ha de ser explícita i justificada.

### Tall C - Pilot

Triar un únic cas viu i acotat per fer la primera migració real.

Criteris del pilot:

- visible
- acotat
- amb poc acoblament transversal

### Tall D - Guard rails

- evitar crear més codi nou en Vue 2
- deixar criteri de cap a on han d'anar els nous desenvolupaments

## Criteris per decidir tecnologia

### Quan Vue 3

- interacció rica i local
- estat de client clar
- component desacoblat de backend Laravel
- UI amb més comportament que formulari

### Quan Livewire

- llistats
- formularis CRUD
- filtres de panell
- interacció fortament lligada a models Laravel
- components on el servidor continue sent la font principal de veritat

### Quan Blade + JS

- comportament menut
- poc estat
- cost de migració superior al valor

## Fora d'abast

- reescriure tot el frontend
- eliminar jQuery de tot el projecte
- portar cada peça antiga a una sola tecnologia per dogma

## Resultat esperat del sprint

- inventari real del que queda en Vue 2
- classificació per destí
- recomanació clara de línia futura
- primer candidat de migració pilot

## Referència

- issue remot: `#116`
