# Sprint 18 - Inventari d'acoblament transversal

## Objectiu

Mapar els acoblaments reals entre rutes, controllers, vistes, JS i models en els dominis més carregats, per tindre una base sòlida abans de continuar amb:

- modularització
- seguretat API
- migracions frontend
- i nous fluxos FCT

## Dominis prioritaris

- `FCT`
- `colaboraciones`
- `documentos`

## Problema

Moltes decisions recents tenen la mateixa arrel: hi ha massa dependències implícites entre capes.

Exemples típics:

- vistes que pressuposen relacions completes
- JS que captura més interaccions de les que li tocarien
- controllers que governen més d'un flux funcional
- models que actuen com a punt de pas entre dominis que haurien d'estar més separats

Això dificulta:

- modularitzar
- protegir bé l'API
- migrar Vue 2 o jQuery
- i afegir casos especials com reserves, excepcions FCT o panells nous

## Què vol dir “acoblament transversal”

En este sprint, vol dir qualsevol punt on un domini depén fortament d'un altre a través de:

- rutes
- controllers
- vistes Blade
- JS/asset legacy
- models i relacions
- documents o recursos d'impressió

## Fases

### Tall A - Mapa per domini

Per a cada domini prioritari:

- rutes implicades
- controllers implicats
- vistes principals
- JS associat
- models i entitats dependents

Resultat:

- fitxa curta per domini

### Tall B - Punts de fricció

Detectar i classificar:

- dependències implícites
- pressupòsits fràgils
- acoblaments circulars
- responsabilitats barrejades

Etiquetes recomanades:

- `fort`
- `fràgil`
- `lateral`
- `legacy`

### Tall C - Fronteres recomanades

Per cada domini, proposar:

- què hauria de quedar dins
- què hauria d'eixir fora
- quina capa hauria de governar cada decisió

Exemple:

- `colaboraciones` no hauria de governar seguiments FCT reals
- `FCT` no hauria de pressupostar sempre empresa i documentació si volem casos especials

### Tall D - Recomanacions per sprints futurs

Deixar pont clar cap a:

- `#114` modularització
- `#115` hardening API
- `#116` migració Vue 2

## Resultat esperat

- document d'inventari transversal per domini
- llista de punts crítics
- recomanacions de frontera
- priorització de refactors futurs

## Criteris d'acceptació

- cada domini prioritari té un mapa mínim complet
- els punts d'acoblament crítics queden identificats
- les recomanacions són prou concretes per alimentar sprints posteriors

## Referència

- issue remot: `#117`
