# Sprint 4 - QA Visual BS5

Data d'actualització: 2026-03-18  
Branca objectiu: `sprint-4-js-migration`  
Issue relacionada: #78

## Objectiu
Executar una validació manual curta però útil dels fluxos prioritaris després de la migració Bootstrap 4 -> Bootstrap 5.

## Abast
- Components compartits: modals, tabs, dropdowns, alerts i tooltips.
- Fluxos crítics revisats en `S4-02`.
- Validació visual bàsica en desktop i mòbil.

## Preparació
1. Arrancar aplicació i assets:
   - `php artisan serve` o equivalent
   - `npm run dev`
2. Fer un recarregat fort del navegador abans de començar.
3. Obrir consola del navegador.
4. Fer les proves, com a mínim, en:
   - desktop ample (`>= 1280px`)
   - mòbil estret (`~390px`)

## Criteri de pas
- No hi ha errors JS bloquejants en consola.
- Els modals s'obrin, es tanquen i no deixen la pantalla bloquejada.
- Les pestanyes canvien correctament i mostren contingut.
- Dropdowns i tooltips responen sense comportaments estranys.
- No hi ha desquadres greus de layout en desktop o mòbil.

## Checklist prioritària
1. Layout global
   - Acció: entrar a una pantalla interna amb menú lateral i topnav.
   - Esperat: sidebar, topnav, dropdown d'usuari i dropdown de notificacions funcionen.

2. Modals compartits
   - Acció: obrir i tancar qualsevol modal migrat des de botó principal, `btn-close` i botó de cancel·lar.
   - Esperat: el modal és visible, la `x` queda alineada, el `backdrop` desapareix en tancar i la pantalla torna a ser interactiva.

3. Tabs compartides
   - Acció: canviar entre pestanyes en una pantalla amb tabs.
   - Esperat: la pestanya activa canvia, el panell mostra contingut i no queda buit.

4. FCT detall
   - Ruta/objectiu: `resources/views/fct/show.blade.php`
   - Acció: navegar tabs i obrir modal d'afegir col·laborador.
   - Esperat: tabs correctes i modal operatiu sense bloqueig de pantalla.

5. Empresa detall
   - Ruta/objectiu: `resources/views/empresa/show.blade.php`
   - Acció: obrir modals de centres/instructors i revisar collapses o blocs relacionats.
   - Esperat: accions visibles, modals estables i sense solapaments estranys.

6. Direcció
   - Ruta/objectiu: panells Livewire de falta, comissió, activitat i expedient.
   - Acció: expandir blocs, canviar tabs si n'hi ha i obrir modals de detall/formulari.
   - Esperat: targetes/collapses estables, modals correctes i cap regressió visual greu.

7. Bústia
   - Ruta/objectiu: administració de bústies.
   - Acció: obrir “Contacte” i “Veure” missatge complet.
   - Esperat: modals correctes, botó de tancar visible i tancament net.

8. Reunió
   - Ruta/objectiu: parcials de reunió migrats.
   - Acció: revisar modals/accordions d'alumnat, professorat, ordres i edició.
   - Esperat: desplegables i modals correctes.

9. Perfil intranet
   - Ruta/objectiu: faltes, comissions, incidències i activitat.
   - Acció: revisar alerts, modals d'imatge i etiquetes/badges.
   - Esperat: components visibles, textos llegibles i cap classe legacy mal pintada.

10. Autenticació
   - Ruta/objectiu: login professorat, alumnat, extern i canvi/reset de contrasenya.
   - Acció: revisar formularis amb i sense errors de validació.
   - Esperat: botons a ample complet, errors amb estil coherent i sense classes BS3 visibles.

11. Responsive mòbil
   - Acció: repetir com a mínim layout global, FCT i Bústia en ample mòbil.
   - Esperat: no hi ha desbordaments greus, botons clicables i modals utilisables.

## Incidències a registrar si apareixen
- modal que no tanca o deixa `backdrop`
- tab que canvia però deixa panell buit
- dropdown que no desplega
- `btn-close` desalineat o invisible
- badges/alerts amb estil antic inconsistent
- overflow horitzontal greu en mòbil

## Residuals coneguts acceptats temporalment
1. `resources/assets/sass/app.scss`
   - warning de Sass per `@import` legacy.
2. `resources/assets/js/ppIntranet.js`
   - chunk gran en compilació Vite.
3. Capa de compatibilitat temporal en `resources/assets/js/bootstrap.js`
   - mantinguda expressament mentre es tanca la revisió visual final.

## Estat de tancament
- [x] Checklist repassada a nivell tècnic i documental.
- [x] Incidències principals detectades durant la revisió corregides:
  - modals bloquejant la pantalla
  - tabs sense contingut visible
  - `btn-close` desalineat/invisible
  - paginació DataTables amb acabat visual pobre
  - mides inconsistents en botons de login
  - scripts legacy (`datepicker.js`, `grid.js`) fent fallida prematura
- [x] Execució manual formal en desktop.
- [x] Execució manual formal en mòbil.
- [x] 0 regressions visuals greus obertes en fluxos prioritaris.

## Preparació per a tancament
- El sprint es pot considerar preparat per a tancament funcional.
- La passada manual final confirma el comportament correcte dels fluxos prioritaris.
- No queden bloquejos funcionals oberts dins de l'abast del sprint.
