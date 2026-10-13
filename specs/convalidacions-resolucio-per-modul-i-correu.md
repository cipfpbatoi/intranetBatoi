# Resolució individual de mòduls i notificació a l’alumnat

## Estat

Status: ready_for_review

## Traça i punt d’entrada

- **Domini:** sol·licituds i resolucions de convalidació.
- **Punt d’entrada actual:** `DireccionConvalidacioController::resolve`, que actualitza una petició, i `ConvalidacioService::revisar`, que desa l’estat individual.
- **Docs/specs llegits:** `AGENTS.md`, `docs/agents/conventions.md`, `docs/agents/fct/fct-map.md`, `docs/agents/notificacions/notificacions-map.md`, `docs/agents/openspec.md`, `specs/convalidacions.md` i `specs/convalidacions-resolucions-automatiques.md`.
- **Abast proposat:** conservar la resolució independent per petició i avisar l’alumne per correu quan Direcció canvie l’estat d’un mòdul de la seua sol·licitud. No definir ni afegir un estat global de capçalera.

## Escenaris

### Escenari 1: resoldre mòduls independentment

Given una sol·licitud conté diverses peticions de mòduls en procés
When Direcció canvia l’estat d’una petició concreta
Then només canvia l’estat i l’observació d’eixe mòdul
And la resta de mòduls de la sol·licitud mantenen els seus estats i observacions
And no es calcula ni es guarda un estat global per a la sol·licitud.

### Escenari 2: avisar l’alumne del canvi d’un mòdul

Given una persona de Direcció canvia vàlidament l’estat d’una petició
When el canvi queda guardat
Then s’envia un correu a l’adreça de l’alumne titular de la sol·licitud
And el correu identifica el mòdul afectat i mostra el nou estat i l’observació guardada, si n’hi ha
And el correu no inclou documents adjunts ni dades d’altres mòduls o alumnes.

### Escenari 3: avisos per canvis successius

Given Direcció canvia l’estat d’una petició i després en canvia una altra petició de la mateixa sol·licitud
When cada canvi queda guardat
Then cada canvi genera un avís referit exclusivament al mòdul modificat
But tornar a guardar exactament el mateix estat i la mateixa observació no genera un correu duplicat.

### Escenari 4: canvi invàlid o fallada d’enviament

Given la validació rebutja el canvi o no es pot guardar la petició
When la petició no s’actualitza
Then no s’envia cap correu
Given la petició sí que s’ha guardat però falla l’enviament del correu
Then es conserva la resolució guardada
And Direcció rep un avís que la notificació no s’ha pogut enviar
And la fallada no mostra dades tècniques ni desfà ni duplica la resolució.

## Regles de negoci

- L’estat continua pertanyent a cada petició/mòdul, no a la capçalera de sol·licitud.
- S’utilitzen els estats i les validacions actuals; esta proposta no n’afig ni en redefineix cap.
- Cada correu es destina únicament a l’alumne de la petició modificada i informa només d’eixe mòdul.
- Els correus s’envien després que el canvi haja quedat guardat. Una fallada de correu no desfà la resolució.
- Un canvi rebutjat o una petició sense canvi efectiu no genera notificació.
- El correu no inclou els fitxers privats aportats per l’alumne.

## Fitxers afectats previstos

- `specs/convalidacions.md` — incorporar el comportament aprovat als escenaris i regles del flux manual.
- `app/Http/Controllers/DireccionConvalidacioController.php` i/o `app/Application/Convalidacio/ConvalidacioService.php` — connectar el canvi individual amb la notificació, després de guardar-lo.
- `resources/views/email/convalidacions/` i la classe de correu o job corresponent — plantilla del missatge per a l’alumnat.
- `tests/Feature/ConvalidacioFlowTest.php` — independència entre mòduls, destinació del correu, canvis sense duplicats i errors d’enviament.

## Riscos

- Una sol·licitud parcialment resolta no tindrà cap estat agregat; l’alumnat rebrà un avís per cada mòdul que canvie.
- Cal evitar que un error del servidor de correu perda una resolució ja guardada o revele dades d’altres peticions.
- Si l’alumne no té una adreça electrònica utilitzable, el canvi d’estat ha de continuar sent possible i Direcció ha d’entendre que no s’ha pogut notificar.
