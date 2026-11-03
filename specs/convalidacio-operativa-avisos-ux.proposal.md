# Proposta: aclarir avisos de canvi d'estat i aportació documental

- Estat: `ready_to_commit`
- Domini: convalidacions, notificacions i UX operativa
- Punt d'entrada: formulari de resolució i detall de petició de Direcció/Secretaria

## Context

La resolució d'una petició ja intenta enviar un correu quan hi ha un canvi efectiu d'estat, encara que no s'afija un comentari nou. No s'envia un altre correu quan es guarda el mateix estat i la mateixa observació. La vista actual no fa prou visible esta diferència i mostra sempre els camps d'aportació documental complets, encara que l'acció siga puntual.

## Escenaris

### ✅ Escenari 1: avisar de l'alumne quan canvia l'estat sense comentari nou

**Given** una petició té una adreça de correu vàlida i una observació anterior, o no en té cap
**When** Direcció o Secretaria canvia l'estat sense escriure un comentari nou
**Then** el canvi es guarda i s'intenta enviar el correu de notificació amb el nou estat i l'observació vigent, si n'hi ha
**And** la interfície informa clarament si el correu s'ha enviat o si no s'ha pogut enviar perquè falta una adreça vàlida o ha fallat l'enviament
**But** si se selecciona el mateix estat i no hi ha cap altre canvi, no s'envia cap correu.

### ✅ Escenari 2: explicar l'efecte de guardar la resolució

**Given** el personal autoritzat consulta el formulari d'estat d'una petició
**When** prepara un canvi d'estat
**Then** veu un avís breu que un canvi efectiu notificarà l'alumne per correu encara que no s'afiga un comentari nou
**And** veu que guardar sense cap canvi no generarà una notificació nova
**And** les observacions continuen sent obligatòries per als estats que ja les exigixen.

### ✅ Escenari 3: presentar l'aportació documental com una acció contextual

**Given** Secretaria o Direcció consulta una petició en estat `Revisar documentació`
**When** obri el detall de la petició
**Then** veu un botó clar «Afegir documentació rebuda» i una indicació que pot incorporar un fitxer, un comentari o tots dos
**And** el formulari complet no ocupa la vista fins que prem el botó
**When** prem el botó
**Then** s'obri un diàleg amb els camps de document i comentari
**And** es manté la regla actual: els fitxers previs no se substituïxen i només es pot aportar documentació en `Revisar documentació`.

### ✅ Escenari 4: no mostrar l'acció documental fora de l'estat requerit

**Given** la petició està en qualsevol estat diferent de `Revisar documentació`
**When** el personal autoritzat consulta el detall
**Then** no veu el botó ni el diàleg d'aportació documental
**And** una petició directa continua rebutjant-se al servidor sense guardar canvis.

## Regles de negoci

- Una notificació està vinculada a un canvi efectiu d'estat o observació, no al fet que s'haja escrit text nou en el camp de comentari.
- Si l'estat no canvia i tampoc canvien les dades de resolució o l'observació, no es torna a notificar.
- La notificació continua sent individual per mòdul i només es considera enviada quan el procés de correu acaba correctament.
- L'acció d'aportar documentació es presenta com a botó i diàleg, però conserva les autoritzacions, l'estat requerit, les validacions i la traçabilitat existents.

## Fitxers previsibles

- `resources/views/intranet/convalidacions/direccion/show.blade.php` — ajuda contextual de notificació i diàleg d'aportació documental.
- `app/Http/Controllers/DireccionConvalidacioController.php` — missatges de resultat del correu, si cal ajustar-ne la claredat.
- `tests/Feature/ConvalidacioFlowTest.php` — regressions per canvi d'estat sense comentari nou i estat sense canvi.
- `specs/convalidacions.md` — incorporar els escenaris validats en el pas d'arxiu.

## Riscos

- El correu depén que l'alumne tinga una adreça vàlida i que el transport de correu funcione; un error de correu no ha de revertir l'estat guardat.
- El canvi d'interfície no ha d'alterar la regla d'observació obligatòria per a `Denegada`, `Revisar documentació` i `Aportar documentació original a Secretaria`.
- El diàleg ha de ser accessible i no pot deixar camps obligatoris actius quan està tancat.

Status: ready_to_commit
