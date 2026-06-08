# Sprint 15 - Modularització funcional de la intranet

## Objectiu

Convertir la intranet en un sistema amb mòduls activables de manera declarativa, de forma que:

- es puguen activar o desactivar blocs funcionals complets
- el menú no siga l'única font de veritat
- la configuració siga versionable i controlada per codi
- es puga reduir càrrega funcional en centres o desplegaments que no necessiten tots els mòduls

## Diagnòstic actual

Ara mateix el concepte de “mòdul” està repartit en massa llocs:

- menús en BBDD
- rols i permisos en `config/*`, policies i gates
- rutes registrades sempre, encara que certa funcionalitat no es vulga usar
- panells i vistes que assumixen que el mòdul està actiu
- restes legacy que encara condicionen navegació o visibilitat

Conseqüència:

- amagar una entrada del menú no desactiva realment el mòdul
- la font de veritat funcional queda dispersa
- costa molt saber què està “activat” de debò en una instal·lació

## Decisió d'arquitectura

La font de veritat ha d'eixir de la BBDD i passar a configuració versionada.

Model proposat:

- base contractual en [`config/modules.php`](/Users/igomis/Code/intranetBatoi/config)
- la BBDD pot continuar existint per a ordenació o personalització visual del menú
- però no ha de decidir per ella sola si un mòdul està actiu o no

En resum:

- `config/modules.php` governa disponibilitat funcional
- `menus` en BBDD governa presentació, ordre o personalització

## Model objectiu

Cada mòdul hauria de definir almenys:

- `enabled`
- `label`
- `menu`
- `depends_on`
- `routes`
- `abilities`

Exemple conceptual:

```php
return [
    'colaboraciones' => [
        'enabled' => true,
        'label' => 'Col·laboracions',
        'menu' => true,
        'depends_on' => ['fct'],
    ],
    'mantenimiento' => [
        'enabled' => true,
        'label' => 'Manteniment',
        'menu' => true,
        'depends_on' => [],
    ],
];
```

## Principis

1. Un mòdul no és només una entrada de menú.
   També afecta rutes, vistes, polítiques i casos d'ús.

2. El sistema ha de fallar de manera segura.
   Si un mòdul està desactivat:
   - no s'hauria de mostrar al menú
   - les seues rutes haurien de quedar bloquejades o no registrades
   - els seus panells no s'haurien de poder renderitzar

3. La configuració ha de ser fàcil de revisar en PR.
   Per això la base ha d'estar en `config/`, no amagada en dades de la base.

4. No es farà un “big bang”.
   La modularització s'ha d'introduir per capes.

## Fases d'implementació

### Tall A - Registre central de mòduls

- crear [`config/modules.php`](/Users/igomis/Code/intranetBatoi/config)
- definir un primer registre curt de mòduls reals:
  - `fct`
  - `colaboraciones`
  - `mantenimiento`
  - `encuestas`
  - `comisiones`
  - `documentos`
- afegir helper o servici tipus:
  - `module_enabled('colaboraciones')`
  - `module_config('fct')`

Fitxers probables:

- [`config/modules.php`](/Users/igomis/Code/intranetBatoi/config)
- [`app/Support/Helpers/MyHelpers.php`](/Users/igomis/Code/intranetBatoi/app/Support/Helpers/MyHelpers.php)
  o un servici dedicat nou

### Tall B - Integració en menú

- fer que la construcció del menú puga ignorar entrades de mòduls desactivats
- mantindre `menus` com a catàleg de navegació, però filtrat per `config/modules.php`
- no eliminar encara la BBDD de menú

Fitxers probables:

- [`app/Application/Menu/MenuService.php`](/Users/igomis/Code/intranetBatoi/app/Application/Menu/MenuService.php)
- [`app/Entities/Menu.php`](/Users/igomis/Code/intranetBatoi/app/Entities/Menu.php)

### Tall C - Guard rails de rutes/panells

- crear middleware o gate funcional per a mòduls desactivats
- exemple:
  - si `colaboraciones` està desactivat, `/colaboracion` i `/misColaboraciones` no haurien d'obrir-se
- primer, bloqueig segur
- després, on tinga sentit, es pot estudiar no registrar certes rutes

Fitxers probables:

- middleware nou
- providers o grups de rutes

### Tall D - Primer mòdul pilot

Aplicar el sistema complet a un domini concret i limitat.

Mòdul recomanat per a pilot:

- `colaboraciones`

Per què:

- té menú propi
- té panells identificables
- és prou visible per validar el sistema
- però no és tan transversal com FCT completa

### Tall E - Dependències i documentació

- modelar `depends_on`
- documentar quins mòduls necessiten altres
- exemple:
  - `colaboraciones` depén de `fct`
  - `misColaboraciones` depén també de `colaboraciones`

## Què no faria en la primera passada

- migrar tots els menús fora de la BBDD de colp
- registrar rutes condicionalment a tots els dominis des del primer dia
- tocar tots els mòduls alhora
- intentar convertir legacy residual i modularització en el mateix sprint

## Riscos

- confondre “menú ocult” amb “mòdul desactivat”
- tallar rutes que encara tenen consum lateral o dependències indirectes
- crear massa configuració abans de tenir un pilot real

## Criteris d'acceptació del primer sprint

- existix un registre central de mòduls en `config/modules.php`
- existix una API senzilla per consultar si un mòdul està actiu
- el menú pot filtrar entrades segons el mòdul
- almenys un mòdul pilot queda governat pel sistema
- la desactivació del mòdul pilot no mostra el menú i bloqueja l'accés funcional

## Recomanació

No començar per `fct` ni per un mòdul transversal massa gran.

Com a pilot, faria:

1. registre central
2. filtrat de menú
3. middleware de bloqueig
4. pilot amb `colaboraciones`

És el punt amb millor equilibri entre valor i risc.
