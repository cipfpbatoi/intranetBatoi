# Sprint 4 - Consolidació postmigració i retirada jQuery residual

Data: 2026-03-12  
Branca objectiu: `sprint-4-js-migration`

## Objectiu
Completar la retirada progressiva de jQuery en mòduls encara actius, consolidar infraestructura comuna JS i reduir el risc en codi legacy transversal.

Issue relacionada: #80

## Estat d'entrada
- Sprint 3 tancat funcionalment (QA manual completada en fluxos crítics).
- Encara hi ha dependències jQuery en mòduls no atacats en Sprint 3.
- Existeix helper compartit de modal (`public/js/common/ui-helpers.js`) per compatibilitat.

## Backlog prioritzat

### S4-01 Reauditoria real de dependències jQuery
Prioritat: Alta

Tasques:
- Recalcular inventari de fitxers amb ús de `$`, `$.ajax`, `.modal(...)`.
- Separar: codi propi, codi vendor i codi de transició acceptable.
- Actualitzar document de pendents amb dades verificables.

Criteris d'acceptació:
- Informe actualitzat amb recompte real i llista de fitxers pendents.
- Classificació per risc i impacte funcional.

### S4-02 Migració vertical Gestió de materials
Prioritat: Alta

Àmbit:
- `public/js/Material/index.js`
- `public/js/Inventario/index.js`
- `public/js/Lote/index.js`
- `public/js/ArticuloLote/index.js`

Tasques:
- Substituir `$.ajax` per `fetch` amb capa comuna auth/errors.
- Substituir manipulació jQuery de modal/DOM per API nativa + helper comú.

Criteris d'acceptació:
- Flux alta/edició/baixa estable.
- Sense regressions funcionals ni errors JS bloquejants.

### S4-03 Migració vertical Col·laboració/Empresa detall
Prioritat: Mitjana-Alta

Àmbit:
- `public/js/Colaboracion/grid.js`
- `public/js/Colaboracion/modal.js`
- `public/js/Empresa/detalle.js`
- `public/js/Empresa/delete.js`

Tasques:
- Eliminar dependència de jQuery en accions de taula i modals.
- Centralitzar confirmacions i gestió d'errors.

Criteris d'acceptació:
- Operacions de col·laboració i empresa funcionals en flux complet.
- Sense `$.ajax` en fitxers migrats.

### S4-04 Codi transversal legacy (`ppIntranet` / `custom`)
Prioritat: Mitjana

Tasques:
- Delimitar què és imprescindible mantindre temporalment.
- Extraure comportaments comuns reutilitzables fora de jQuery.
- Definir estratègia de retirada per blocs (no big bang).

Criteris d'acceptació:
- Pla de retirada per fases amb impacte estimat.
- Reducció mesurable de codi jQuery transversal.

### S4-05 QA i criteri de tancament
Prioritat: Alta

Tasques:
- Crear checklist de regressió Sprint 4 per verticals atacades.
- Executar validació manual i documentar incidències.

Criteris d'acceptació:
- Checklist completada.
- 0 regressions crítiques obertes en àmbit Sprint 4.

## Ordre recomanat
1. S4-01
2. S4-03 (quick wins de migració curta)
3. S4-02
4. S4-04
5. S4-05

## Definició de fet (DoD) Sprint 4
- Dependències jQuery reduïdes de forma verificable en mòduls objectiu.
- Fluxos de materials i col·laboració estables sense regressions.
- Documentació actualitzada de pendents residuals.
