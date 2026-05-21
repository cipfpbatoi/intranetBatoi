# DWES Microreptes Autocorreccio

Nom proposat del repositori: `dwes-microreptes-autocorreccio`.

## Finalitat

Repositori central del professorat per definir microreptes de DWES, rúbriques, polítiques globals i una base inicial d'autocorrecció assistida per IA.

El model previst és que cada alumne treballe al seu repositori individual, mentre este repositori actua com a font de veritat per a configuració, criteris i política d'avaluació.

## Estructura

- `global/`: polítiques comunes, estil de feedback i esquema de resposta del corrector.
- `course/`: configuració docent centralitzada del microrepte actiu per grup o alumne.
- `microreptes/`: definició dels microreptes, rúbriques i prompts base.
- `scripts/`: utilitats Node.js per validar, llistar i resoldre microreptes.
- `docs/`: arquitectura, flux de treball i decisions.
- `.github/`: workflows i plantilles de GitHub.
- `examples/`: exemples de configuració i payloads futurs.

## Flux previst

1. El professor defineix microreptes i rúbriques.
2. El professor configura el microrepte actiu en `course/active-challenges.json`.
3. L'alumne treballa al seu repositori individual.
4. Un workflow resol quin microrepte correspon a l'alumne sense que haja de tocar cap fitxer del seu repo.
5. Es genera feedback provisional i, si cal, revisió docent.

## Microrepte actiu centralitzat

El microrepte actiu es resol des d'este repositori del professor. Primer es comprova si l'alumne té una assignació específica; si no, s'aplica l'assignació del grup. Això permet canviar el repte actiu de forma centralitzada, sense commits als repositoris individuals de l'alumnat.

## Com afegir un nou microrepte

1. Crea una carpeta en `microreptes/`, per exemple `mr04-sessions`.
2. Afig `challenge.json`, `rubric.json` i `prompt.md`.
3. Documenta proves futures en `tests/README.md`.
4. Documenta contractes o fixtures en `expected/README.md`.
5. Executa `npm run validate`.

## Estat actual

Base inicial executable:

- tres microreptes d'exemple;
- rúbriques inicials;
- polítiques globals;
- resolució centralitzada de microrepte actiu;
- workflows de validació i dry-run;
- sense crides reals a OpenAI.

## Pròxims passos

- Definir l'estructura final d'evidències dels repositoris d'alumnes.
- Afegir tests reals per microrepte.
- Connectar el dry-run amb repositoris d'alumnes.
- Incorporar OpenAI quan el contracte de dades estiga estabilitzat.
