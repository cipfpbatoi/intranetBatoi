# Sprint 3 - Checklist de regressió manual

Data: 2026-03-16
Branca objectiu: `sprint-3-livewire-vue`

## Objectiu
Disposar d'una checklist curta i executable per validar els fluxos crítics tocats en Sprint 3 abans de donar-lo per tancat funcionalment.

## 1. Auth i token

- `POST /api/auth/exchange`
  - intercanvia `api_token` legacy per Bearer Sanctum
  - retorna `access_token`
- `GET /api/auth/me`
  - accepta Bearer Sanctum
  - rebutja `api_token` legacy passat en query string
- `POST /api/auth/logout`
  - revoca el token actual

## 2. Direcció - Faltes

Ruta: `direccion/falta`

- carrega el llistat sense errors JS
- filtre per professor escrivint funciona
- filtre per estat funciona
- `Nova falta` obri modal
- crear falta guarda i refresca el llistat correctament
- editar una falta no autoritzada obri modal i guarda
- rebutjar funciona i el formulari es veu correctament
- `Veure` obri modal
- `Document` obri el justificant quan existix
- no apareixen accions indegudes en faltes autoritzades

## 3. Direcció - Comissions

Ruta: `direccion/comision`

- carrega el llistat sense errors JS
- filtre per professor i estat funciona
- `Veure` obri modal
- el modal mostra document si `idDocumento` existix
- `Editar` només ix en estats `< 3`
- `Esborrar` és sempre l'últim botó i no ix quan no toca
- `Autoritzar comissions pendents (N)` actua i refresca
- `Imprimir Comissions autoritzades (N)` obri informe i refresca
- bloc `Pagaments pendents`:
  - es pot obrir/tancar
  - els checkboxes actualitzen el comptador al moment
  - `Imprimir pagaments seleccionats` obri informe i després refresca

## 4. Direcció - Activitats

Ruta: `direccion/actividad`

- carrega el llistat sense errors JS
- filtre textual i per departament funciona
- `Autoritzar activitats pendents (N)` actua i refresca
- `Imprimir activitats autoritzades (N)` obri informe i refresca
- formulari de rebuig es veu dalt
- `Veure` obri modal
- el modal mostra:
  - tipus
  - departament
  - justificació RA
  - objectius
  - participants
  - grups participants
  - conflicte d'horari docent del professorat participant
- `Gestor Documental` funciona
- `Mostrar valoració` funciona quan toca

## 5. Direcció - Expedients

Ruta: `direccion/expediente`

- carrega el llistat sense errors JS
- filtre textual i per estat funciona
- `Autoritzar expedients pendents (N)` actua i refresca
- `Imprimir expedients autoritzats (N)` obri informe i refresca
- formulari de rebuig es veu dalt
- `Veure` obri modal
- el modal mostra dades bàsiques correctes
- `Gestor Documental` funciona
- `PDF` individual funciona si existix

## 6. Signatura

Ruta: `signatura`

Resultat revisió manual:

- validada el `2026-03-17`
- sense regressions bloquejants reportades en el flux revisat

- botons A1/A5/A3 obrin modal i carreguen dades
- no hi ha errors de consola en obrir modals
- enviament múltiple no queda bloquejat
- pujada d'A3 obri modal de càrrega i completa flux
- la càrrega de taula en `signatura` usa els endpoints API amb Bearer vàlid

## 7. FCT crítica

Ruta base: fluxos de tutor FCT

Resultat revisió manual:

- validada el `2026-03-17`
- sense regressions bloquejants reportades en el flux revisat

- grid principal carrega sense errors JS
- accions principals del grid funcionen
- modals essencials s'obrin/tanquen bé
- no hi ha dependència trencada de jQuery en els fitxers migrats

## Resultat de tancament

Per considerar `S3-06` tancada cal:

- executar esta checklist
- registrar incidències trobades
- no deixar regressions crítiques obertes en auth, Direcció o Signatura
