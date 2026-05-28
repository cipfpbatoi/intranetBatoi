# MIG-17 Proves Visuals Smoke Tests

Data: 2026-03-08  
Objectiu: validar ràpidament que la UI base i els fluxos crítics no han regressat després de la migració MIG-06..MIG-16.

## Cobertura mínima (manual)

- Layout principal (`sidebar`, `topnav`, `content`, `footer`)
- Pantalles amb formularis (inputs, selects, checkbox/radio, validacions visuals)
- Pantalles amb taules/grids (DataTables i responsive)
- Pantalles amb calendari/date-time pickers
- Perfil/menú d'usuari i navegació principal

## Matriu smoke (checklist)

| Mòdul | Ruta/Pantalla | Què validar visualment | Estat |
|---|---|---|---|
| Layout | `/` (home intranet) | Estructura general, amplària, marges, cap solapament | ☐ |
| Auth | `/login` i recuperació password | Inputs, errors, botons, responsive | ☐ |
| Perfil | `/perfil/edit` | Formulari complet, alerts d'error/success, datepicker | ☐ |
| Guardies | `/guardias/guardia` | Taula, filtres, botons d'acció | ☐ |
| Reserves | `/reservas/reserva` | Formulari reserva, camps de data/hora, validació visible | ☐ |
| FCT | `/fct/*` (llistat + detall) | Grids, modal/accions, datetime pickers | ☐ |
| Col·laboració | `/colaboracion/*` | Taules, estats, accions ràpides i feedback visual | ☐ |
| Faltes | `/falta/*` | Formulari + llistats, coherència d'estils | ☐ |

## Criteris de pas MIG-17

- No hi ha errors de maquetació bloquejants en desktop (>= 1280px) ni mòbil (<= 390px).
- No hi ha components trencats (botons invisibles, overlays, menús inoperatius).
- Datepicker/datetimepicker es mostren i formategen data de forma coherent amb locale.
- DataTables i paginació continuen renderitzant sense degradació visual evident.

## Evidència recomanada per PR

- 1 captura per cada fila de la matriu smoke.
- Nom de fitxer suggerit: `mig17-<modul>-<pantalla>.png`.
- Adjuntar-les al PR de tancament MIG-19.

## Notes d'execució

- Esta checklist és intencionalment curta per a detectar regressions grans abans de MIG-18.
- Les correccions funcionals/visuals detectades es registren en MIG-18.
