# Sprint 3 (Fase B) - Migració funcional incremental Livewire/Vue

Data: 2026-03-12  
Branca objectiu: `sprint-3-livewire-vue`  
Issue mare: #79

## Context
La fase de retirada de jQuery/estabilització JS (S3/S4 anteriors) està avançada.  
Ara es reprén l'objectiu original de Sprint 3: migració funcional incremental a Livewire/Vue 3, evitant big-bang.

## Criteri de selecció de pilots
- Impacte real en usuaris interns.
- Domini funcional acotat (sense dependències ocultes massives).
- Possibilitat de convivència temporal (ruta o vista commutable).
- Facilitat de QA manual per rol.

## Pilot proposat (P1)
### P1 - `direccion/falta` a Livewire
Motiu:
- Pantalla crítica de Direcció amb accions d'estat (`acceptar/rebutja/alta`) i feedback immediat.
- Actualment depén de flux legacy (profile cards + JS modal).
- Bona candidata per centralitzar estat i accions en component servidor.

Inventari tècnic inicial:
- Ruta: `routes/direccion.php` (`/falta` i accions relacionades).
- Panell: `app/Http/Controllers/PanelFaltaController.php`
- Accions: `app/Http/Controllers/FaltaController.php`
- Vista actual: `resources/views/intranet/partials/profile/falta.blade.php`
- JS actual: `public/js/Falta/index.js`
- Modal explicació: `resources/views/intranet/partials/modal/explicacion.blade.php`

## Backlog fase B

### S3B-01 Inventari funcional i criteris d'acceptació (P1)
Prioritat: Alta

Tasques:
- Tancar mapa de comportaments de `direccion/falta` (estat, permisos, transicions).
- Definir paritat funcional mínima (MVP) per a la versió Livewire.

Criteri d'acceptació:
- Document curt amb fluxos i edge-cases validats.

### S3B-02 Component Livewire `FaltaDireccionPanel`
Prioritat: Alta

Tasques:
- Crear component per a llistat, filtre bàsic i accions de workflow.
- Integrar acció de rebuig amb captura de motiu.
- Mantindre autorització existent (policies/gates).

Criteri d'acceptació:
- Des de la nova vista, es poden fer les mateixes accions clau que ara.

### S3B-03 Integració no disruptiva
Prioritat: Alta

Tasques:
- Afegir vista/route de pilot sense trencar la ruta legacy.
- Permetre toggle simple (ruta nova o flag per entorn).

Criteri d'acceptació:
- Legacy continua operativa; pilot accessible per QA.

### S3B-04 QA regressió focalitzada (P1)
Prioritat: Alta

Tasques:
- Checklist manual específic per Direcció.
- Verificar que la ruta legacy no regressa després de la integració pilot.

Criteri d'acceptació:
- 0 regressions crítiques obertes en flux de faltes.

## Següent pilot (P2) recomanat
- `empresaSC`/`empresa` (llistats i operacions), només després de validar P1.

## Definició de fet (DoD) fase B
- 1 vertical funcional en Livewire en producció interna (o preproducció) amb paritat MVP.
- Evidència QA manual i traçabilitat de canvis.
- Sense trencar flux legacy existent.
