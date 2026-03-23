# Sprint 28 - Refactor del sistema de permisos

## Objectiu

Preparar la simplificació del sistema de rols i permisos del projecte, que ara mateix es basa en composició de rols mitjançant multiplicació de nombres primers.

No és un sprint prioritari ni urgent, però sí un candidat clar a deute tècnic estructural.

## Context actual

Ara mateix els rols estan definits en [`config/roles.php`](/Users/igomis/Code/intranetBatoi/config/roles.php) amb valors primers:

- `profesor = 3`
- `alumno = 5`
- `mantenimiento = 7`
- `administrador = 11`
- etc.

Quan un usuari té diversos rols, el seu valor guardat és el producte d'eixos primers. Després, el sistema resol si un rol està contingut dins del rol compost comprovant divisibilitat.

La lògica clau està en:

- [`app/Support/Helpers/MyHelpers.php`](/Users/igomis/Code/intranetBatoi/app/Support/Helpers/MyHelpers.php)
  - `rolesUser($rolUsuario)`
  - `esRol($rolUsuario, $rol)`

En concret:

- `rolesUser()` recorre tots els rols i comprova `if ($rolUsuario % $rol == 0)`
- `esRol()` depén directament d'eixa descomposició

## Problema

Encara que el model actual funciona, té diversos inconvenients:

- és poc intuïtiu per a qui no coneix el truc matemàtic dels primers
- dificulta la lectura i manteniment del codi
- obliga a recordar valors màgics (`11`, `31`, `43`, etc.)
- fa més complicat evolucionar cap a permisos més fins que el simple “té o no té este rol”
- no s'alinea amb enfocaments més normals de control d'accés

És un sistema enginyós, però massa opac per al valor real que aporta.

## Alternatives a estudiar

## Opció A. Mantindre rols, però en binari

Substituir la composició per primers per una `bitmask` clàssica:

- `1 << 0`
- `1 << 1`
- `1 << 2`
- etc.

Avantatges:

- la comprovació passa a ser bit a bit
- és més estàndard que el sistema de primers
- continua sent compacte a nivell d'emmagatzematge

Inconvenients:

- continua sent un enter compost amb certa opacitat
- seguix sent fàcil acabar amb valors màgics si no es disciplina bé l'API

## Opció B. Taula pròpia de rols

Passar a un model relacional clàssic:

- taula `roles`
- taula pivot `profesor_role` o equivalent

Avantatges:

- model molt més explícit i llegible
- millor evolució futura cap a permisos o capacitats més detallades
- més fàcil d'integrar amb polítiques, `Gate` i auditories

Inconvenients:

- canvi més gran de dades i de consultes
- cal revisar imports, autenticació, menús, filtres i policies

## Opció C. Taula de permisos/capacitats

Anar un pas més enllà i separar:

- rols
- permisos
- assignacions

Això és el model més flexible, però probablement també és massa per al problema actual si només es busca simplificar el sistema existent.

## Hipòtesi inicial

La millora mínima amb menys risc seria:

1. eliminar la codificació per primers
2. passar, com a mínim, a una representació més estàndard

Però la direcció més sana a mitjà termini sembla:

1. rols explícits en taula pròpia
2. adaptadors de compatibilitat temporal amb el sistema actual
3. eventual transició a polítiques/permisos més declaratius

## Tall A. Inventari real d'ús

- localitzar tots els punts on es crida `esRol()` i `rolesUser()`
- classificar-los:
  - visibilitat de menú
  - accés a controladors
  - lògica de domini
  - filtres de documents
  - generació de claims JWT
- identificar on hi ha comparacions literals per número de rol

## Tall B. Decisió de model

- comparar cost real de:
  - `bitmask`
  - taula de rols
  - taula de permisos
- decidir si l'objectiu del sprint és:
  - només simplificar representació interna
  - o aprofitar per modernitzar de veritat el control d'accés

## Tall C. Estratègia de migració

- definir com es migraria el camp `rol` actual dels usuaris
- decidir si convé:
  - migració directa
  - convivència temporal
  - capa d'adaptació
- revisar impacte sobre imports i sincronitzacions externes

## Tall D. Compatibilitat funcional

- protegir comportaments actuals amb proves
- garantir que no es trenquen:
  - menús
  - accés a panells
  - permisos API
  - policies
  - claims o mapes de rols externs

## Resultat esperat

- una decisió tècnica clara sobre el futur del sistema de rols
- un inventari dels punts afectats
- una estratègia de migració viable
- una base més simple i mantinguble que l'actual codificació per nombres primers

## Prioritat

Sprint de millora tècnica, no prioritari.

És raonable deixar-lo planificat ara i executar-lo només quan hi haja temps per atacar-lo amb calma, perquè toca una peça transversal i sensible.
