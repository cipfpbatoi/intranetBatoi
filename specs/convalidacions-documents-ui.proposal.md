# Proposta: simplificar els controls dels documents adjunts

- Estat: aprovada; implementació pendent
- Issue relacionada: #323
- Domini: interfície d'alumnat de convalidacions
- Punt d'entrada: `resources/views/intranet/convalidacions/alumno/show.blade.php`
- Docs/specs llegits: `AGENTS.md`, `docs/agents/conventions.md`, `docs/agents/openspec.md`, `specs/convalidacions.md`, `specs/convalidacions-documents-addicionals.proposal.md`
- Abast: reduir el soroll visual en la gestió d'adjunts sense canviar permisos, validacions, límit, persistència ni estats.

## Escenaris BDD

### Escenari 1: consultar els adjunts sense veure formularis oberts

Given una petició amb documents adjunts
When l'alumne obri el detall de la sol·licitud
Then cada document es mostra com una fila compacta amb la descripció i l'enllaç de descàrrega
And no es mostren inicialment els camps de selecció de fitxer per substituir-los

### Escenari 2: mostrar el formulari només quan l'alumne vol afegir documentació

Given una petició editable amb menys de tres documents
When l'alumne prem «Afig un document»
Then es desplega un únic formulari per a indicar la descripció i seleccionar el fitxer
And els adjunts existents es mantenen visibles i sense canvis
And en arribar a tres documents no es mostra l'acció per a afegir-ne cap altre

### Escenari 3: mostrar la substitució només per al document triat

Given una petició editable amb un o més documents adjunts
When l'alumne prem «Canviar» en un document concret
Then només es desplega el selector de fitxer per a eixe document
And els selectors de substitució dels altres documents continuen ocults
And la substitució conserva el comportament actual d'actualitzar només el document seleccionat

### Escenari 4: conservar el formulari visible si hi ha errors

Given l'alumne ha desplegat el formulari d'afegir o substituir un document
When l'enviament torna amb errors de validació
Then el formulari corresponent continua desplegat i mostra els errors
And els altres formularis continuen ocults

## Regles i invariants

- La presentació no altera les regles ja aprovades per a afegir i substituir documents.
- Els formularis només es poden obrir en els estats `Enviada` i `Revisar documentació`.
- El límit màxim de tres documents, la declaració responsable i les validacions de servidor continuen vigents.
- La selecció del fitxer es manté associada a la petició/document correcte; no es canvia el comportament de persistència.

## Fitxers previsibles

- `resources/views/intranet/convalidacions/alumno/show.blade.php` — mostrar controls sota demanda i compactar la llista.
- `tests/Feature/ConvalidacioFlowTest.php` — assegurar que el llistat és net, els controls es despleguen i els errors es mostren sense perdre el context.

## Riscos

- Si s'utilitza JavaScript per als desplegables, cal mantindre l'accés amb teclat i no dependre exclusivament del ratolí.
- Els formularis d'afegir i substituir no han de niar-se ni compartir els noms dels camps; cadascun ha d'enviar només l'acció triada.
- La plantilla també mostra documents llegats; els controls han de continuar funcionant per a estos fitxers.

Status: awaiting_approval
