# Sprint 1 - Inventari de dependencia de `ppIntranet.js`

Data: 2026-03-10
Issue: #80

## Resultat

Actualment `ppIntranet.js` es carrega de manera global des de dos layouts base:

- `resources/views/layouts/intranet.blade.php`
- `resources/views/components/layouts/app.blade.php`

Conclusio:

- Qualsevol vista que hereta estos layouts depén indirectament de `public/js/ppIntranet.js`.

## Notes d'impacte

- El bundle legacy (`ppIntranet.js`) continua acoblat a funcionalitats antigues (jQuery/DataTables).
- Esta carrega global amplia el risc d'efectes laterals JS en pantalles que no necessiten eixe codi.

## Seguent pas recomanat (Sprint 2)

- Introduir càrrega condicional per domini funcional o per pàgina.
- Separar inicialitzadors de taules/formularis en mòduls menuts.
