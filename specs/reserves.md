# Reserves

## ✅ Reserva periòdica regulada

**Given** una reserva amb diverses dates i hores
**When** es confirma
**Then** les peticions s'envien en sèrie, amb un màxim de cinc per segon
**And** els botons de reserva i alliberament queden bloquejats fins a acabar.

## ✅ Espera davant del límit de peticions

**Given** una resposta HTTP 429
**When** el servidor indica `Retry-After`
**Then** la cua espera el termini i reprén només la petició rebutjada
**And** les operacions confirmades no es repeteixen.

## ✅ Resultat parcial

**Given** una fallada de xarxa o un error diferent de 429
**When** es processa la cua
**Then** s'atura i mostra les operacions confirmades respecte del total
**And** no repeteix automàticament una escriptura de resultat incert.

## Presentació de notificacions

L'etiqueta del professor es tradueix i el nom del mes respecta la llengua indicada.
