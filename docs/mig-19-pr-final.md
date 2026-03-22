# MIG-19 Preparació PR final a Laravel12

Data: 2026-03-08

## Objectiu

Deixar preparat el contingut i la checklist per obrir la PR final de la migració UI (`Gentelella -> Bootstrap/components`) cap a `Laravel12`.

## Abast tancat en esta sèrie MIG

- MIG-06..MIG-11: ajustos de layout, topnav/sidebar, formularis i taules.
- MIG-12: localització i formats de date/datetime pickers.
- MIG-13: validació i sanejament de `custom.js` (legacy init guards).
- MIG-14: retirada de dependència `gentelella` del build.
- MIG-15: neteja d'assets públics no utilitzats (`gentelella.css/js`).
- MIG-16: actualització de documentació i checklist.
- MIG-17: definició i execució de checklist smoke visual.
- MIG-18: correcció de regressions finals detectades (clics/layout i mides d'icones).

## Commits principals a incloure en la PR

- `d7155055` `[MOD] MIG-10... #67`
- `286a4398` `[FIX] MIG-11... #68`
- `dc1bfbb6` `[MOD] MIG-12... #69`
- `5b778bc5` `[MOD] Harden legacy init blocks in custom.js #70`
- `56ada8c8` `[MOD] Retira dependència Gentelella del build #71`
- `6ca13488` `[DEL] Neteja assets legacy Gentelella no utilitzats #72`
- `62b9a6d3` `[MOD] Actualitza docs i checklist... #73`
- `82c94ec0` `[ADD] Defineix smoke tests visuals... #74`
- `7ae6c318` `[FIX] Corregeix regressions visuals post-migració UI #75`

## Plantilla de descripció PR (copiar i adaptar)

```
## Resum
Tanca la fase MIG-06..MIG-19 de migració UI cap a stack Bootstrap/components, eliminant dependència de build de Gentelella i corregint regressions visuals finals.

## Issues relacionades
Closes #63
Closes #64
Closes #65
Closes #66
Closes #67
Closes #68
Closes #69
Closes #70
Closes #71
Closes #72
Closes #73
Closes #74
Closes #75
Closes #76

## Canvis principals
- Layout principal i topnav/sidebar compatibles amb stack actual.
- Formularis i taules revisats.
- Date/datetime pickers amb localització.
- `custom.js` legacy amb inicialitzacions protegides.
- Build sense `gentelella` + neteja d'assets públics.
- Documentació i checklist MIG actualitzades.

## Validació
- [x] Compilació assets (`NODE_OPTIONS=--openssl-legacy-provider npm run dev`)
- [x] Smoke visual executat segons `docs/mig-17-smoke-tests.md`
- [x] Regressions finals corregides (MIG-18)

## Evidència visual
- Adjuntar captures de la matriu smoke (`docs/mig-17-smoke-tests.md`).

## Riscos/notes
- Es manté codi legacy en `public/js/app.js` i `public/css/app.css` per compatibilitat temporal.
- Fase següent recomanada: desacoblament progressiu de legacy global en PRs petites.
```

## Checklist pre-merge

- [ ] Branca actualitzada amb `Laravel12` sense conflictes.
- [ ] Captures smoke pujades a la PR.
- [ ] Revisió de fitxers generats (`public/*`) segons política del repositori.
- [ ] Confirmar que no s'inclouen fitxers locals (`.cache/`, scripts ad-hoc no versionables).
- [ ] Assignar reviewers i etiquetes de release.
