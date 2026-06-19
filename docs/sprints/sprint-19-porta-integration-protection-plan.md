# Sprint 19 - Protecció d'integració per als endpoints de porta

## Objectiu

Separar la protecció específica dels endpoints de porta/cotxe del model general d'autenticació d'usuari, aplicant una capa d'integració pròpia i més adequada al tipus de trànsit que reben.

## Context

Els endpoints afectats són:

- `/api/porta/obrir`
- `/api/porta/obrir-automatica`
- `/api/eventPorta`
- `/api/eventPortaSortida`

No encaixen bé en `auth:sanctum` perquè no representen navegació ni API d'usuari, sinó un flux tècnic d'integració.

## Decisió actual

La protecció es deixa separada en dos casos:

- ruta manual de prova:
  - `X-Parking-Token`
- webhooks de càmera:
  - `X-Parking-Token`
  - `allowlist` d'IP/CIDR

## Raó de separar token i allowlist

El flux funcional assumit és:

1. la càmera detecta
2. la càmera crida directament almenys els webhooks d'entrada/eixida
3. la intranet processa i resol si obri la porta

Per tant:

- als webhooks de càmera sí els convé una `allowlist`
- a la ruta manual `/api/porta/obrir` no li convé quedar lligada a una IP concreta, perquè no és el mateix flux

## Tall actual

- middleware específic de parking
- registre d'alias en `Kernel`
- configuració de `PARKING_INTEGRATION_TOKEN`
- configuració de `PARKING_INTEGRATION_ALLOWLIST`
- aplicació del middleware als quatre endpoints
- proves `Feature` per a:
  - absència de token
  - token incorrecte
  - token correcte
  - IP no permesa en webhooks de càmera
  - rutes reals protegides

## Possible evolució futura

Només si es confirma que algun dispositiu extern crida directament estos endpoints, es podria afegir després:

- `allowlist` d'IP o CIDR
- signatura HMAC
- o un model separat per endpoint extern vs invocació interna

## Criteri

De moment, el criteri sa és:

- **ruta manual**: token
- **webhooks de càmera**: token + allowlist
