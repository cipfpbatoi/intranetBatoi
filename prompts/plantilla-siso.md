# Plantilla canònica de prompt SISO

> Substitueix els marcadors `{{...}}` i elimina les indicacions que no apliquen.

## Context i Rol

Actua com a `{{rol o especialitat}}` en el projecte intranetBatoi.
Domini: `{{domini}}`. Llig `AGENTS.md` i `{{documents de domini}}` abans de treballar.

## Tasca

`{{acció concreta que cal completar}}`

## Especificacions

- Resultat esperat: `{{comportament o lliurable}}`
- Entrades i condicions: `{{dades, rols o precondicions}}`
- Límits d'abast: `{{què no s'ha de canviar}}`
- Criteris d'acceptació: `{{Given / When / Then o llista verificable}}`

## Criteris de qualitat

- Segueix les convencions i la spec del domini.
- No inventes requisits ni refactoritzes fora de l'abast.
- Afig o actualitza proves de regressió quan canvie el comportament.
- Si falta context imprescindible, no suposes.

## Format de resposta

`{{fitxers, resum, proves executades i format requerit}}`

## Iteració

Presenta qualsevol ambigüitat bloquejant com una única pregunta concreta i espera resposta.
Després de completar la tasca, resumeix els canvis i les proves; espera revisió abans d'ampliar l'abast.
