# Sprint 4 - QA Regressió Manual

Data: 2026-03-12  
Branca objectiu: `sprint-4-js-migration`

## Objectiu
Validar que la retirada addicional de jQuery en Sprint 4 no introdueix regressions en els mòduls migrats.

## Preparació
1. Iniciar app i assets:
   - `php artisan serve` (o docker equivalent)
   - `npm run dev`
2. Obrir consola del navegador per revisar errors JS.
3. Entrar amb usuari amb permisos de gestió (professor/direcció).

## Criteri de pas
- Sense errors JS bloquejants en consola durant els fluxos.
- Crides `fetch` amb resposta 2xx en flux normal.
- Modals de mòduls migrats sense tancament inesperat.
- Taules i accions mantenen comportament previ.

## Checklist Sprint 4
1. Col·laboració (grid i modal)
   - Acció: afegir, editar i eliminar accions des de la graella.
   - Esperat: flux complet sense `$.ajax` ni errors de modal.

2. Empresa (detall i baixa)
   - Acció: obrir detall, descarregar document, eliminar amb confirmació.
   - Esperat: accions correctes i UI estable.

3. Materials (Material, Inventari, Lot, ArticleLot)
   - Acció: alta/edició/baixa i moviments principals.
   - Esperat: peticions correctes i actualització de taules sense regressions.

4. Reserva
   - Acció: seleccionar recurs, navegar dies, reservar i alliberar rang d'hores.
   - Esperat: càrrega de franges per dia, validacions correctes i operacions per API sense regressions.

5. FCT grid
   - Acció: obrir comentari telefònic, editar seguiment, borrar evidència, drag&drop.
   - Esperat: modal `#dialogo` funciona, comentaris persisteixen, esborrat i còpia operatius.

6. Faltes
   - Acció: rebutjar falta, obrir modal de contrasenya, enviar formulari.
   - Esperat: accions de modal i submit correctes sense jQuery directe al mòdul.

7. Mòduls curts de gestió
   - Acció: provar resolució/accions en Reunion, Solicitud, Incidencia i FCT PDF.
   - Esperat: modals i submits amb URLs correctes, sense errors JS en consola.

## Residuals coneguts (acceptats temporalment)
1. `public/js/common/ui-helpers.js`
   - Fallback jQuery de `.modal(...)` per compatibilitat BS4/BS5.
2. `public/js/Fct/grid.js`
   - Inicialització `datetimepicker` via plugin jQuery.
3. Blocs encara pendents de migració en Sprint 4
   - `public/js/Horario/cambiar.js`
   - `public/js/Guardia/edit.js`
   - `public/js/Guardia/edit-biblio.js`

## Estat de tancament
- [ ] QA manual completada i validada.
- [ ] 0 regressions crítiques obertes en àmbit Sprint 4.
