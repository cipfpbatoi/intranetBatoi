# Proposta: fer més clara i permetre diversos documents en una aportació de personal

- Estat: `ready_to_commit`
- Domini: convalidacions, documentació i UX operativa
- Punt d'entrada: aportació de Secretaria/Direcció des del detall d'una petició

## Context

El diàleg actual permet afegir un únic document. Quan s'adjunta un fitxer, la seua descripció és obligatòria, però el formulari no ho explica ni destaca el camp si falta; això pot semblar un error tècnic. A més, «Tipus de document» i «Comentari» no diferencien prou bé la identificació del fitxer d'una nota general sobre l'entrega.

## Escenaris

### ✅ Escenari 1: explicar la descripció de cada fitxer

**Given** Secretaria o Direcció obri «Afegir documentació rebuda»
**When** consulta els camps del diàleg
**Then** veu una descripció clara que la descripció identifica el contingut de cada fitxer, per exemple «Certificat acadèmic» o «Certificat PRL»
**And** el camp de comentari s'identifica com una nota general opcional sobre l'entrega, diferent de la descripció del fitxer.

### ✅ Escenari 2: indicar visualment una descripció que falta

**Given** s'ha seleccionat un fitxer sense indicar-ne la descripció
**When** Secretaria o Direcció intenta afegir-lo a l'expedient
**Then** no es tanca el diàleg ni es perd el fitxer seleccionat
**And** el camp de descripció corresponent es destaca visualment i mostra un missatge que indica què falta
**And** el servidor manté la validació i no guarda l'aportació fins que la descripció siga vàlida.

### ✅ Escenari 3: afegir diversos fitxers en una mateixa aportació

**Given** una petició està en estat `Revisar documentació` i encara té capacitat disponible dins del límit vigent
**When** Secretaria o Direcció afig un o més fitxers en el diàleg
**Then** cada fitxer té el seu propi camp de descripció obligatòria
**And** pot afegir o llevar files de fitxer abans d'enviar
**And** pot incloure un comentari general opcional per a tota l'aportació
**And** cada fitxer queda guardat com una aportació traçable de la persona i rol que l'ha registrat
**And** els documents previs de l'alumne o del personal es conserven
**And** si s'incorpora almenys un fitxer, la petició passa a `En procés`.

### ✅ Escenari 4: respectar el límit de documents i l'estat permés

**Given** la petició ja ha arribat al màxim de tres documents o no està en `Revisar documentació`
**When** el personal consulta o intenta enviar una aportació
**Then** la interfície no permet afegir més fitxers quan s'ha esgotat la capacitat
**And** el servidor rebutja qualsevol excés o petició fora de l'estat permés sense guardar fitxers parcials
**And** les autoritzacions actuals de Secretaria i Direcció es mantenen.

## Regles de negoci

- La descripció és obligatòria per cada fitxer i serveix per identificar-ne el contingut; no és el comentari general de l'aportació.
- El comentari és opcional i compartit per l'enviament de l'aportació.
- Es manté el màxim vigent de tres documents per petició, comptant adjunts existents i llegats.
- La validació client-side només facilita la correcció; el servidor continua sent autoritatiu.
- Cada fitxer es guarda sense substituir els anteriors i conserva la traçabilitat de persona, rol i data.
- La petició només torna a `En procés` si s'ha guardat almenys un fitxer; un comentari a soles manté `Revisar documentació`.
- L'acció continua restringida a Secretaria o Direcció i a l'estat `Revisar documentació`.

## Fitxers previsibles

- `resources/views/intranet/convalidacions/direccion/show.blade.php` — interfície repetible de fitxers, aclariments i errors visibles.
- `app/Http/Controllers/DireccionConvalidacioController.php` — validació de llistes de fitxers i descripcions.
- `app/Application/Convalidacio/ConvalidacioService.php` — guardar diverses aportacions en una operació amb límit i neteja segura si falla.
- `tests/Feature/ConvalidacioFlowTest.php` — regressions de descripció absent, múltiples fitxers, límit, preservació i transició d'estat.

## Riscos

- L'operació ha de ser atòmica: si falla la validació o l'emmagatzematge d'un fitxer, no han de quedar aportacions parcials ni fitxers orfes.
- El nombre màxim s'ha de calcular incloent els documents relacionals, les aportacions i els camps llegats de documentació.
- Un comentari comú no ha de duplicar-se com si cada fitxer tinguera una observació diferent, però cada fitxer ha de continuar identificat i traçable.

Status: ready_to_commit
