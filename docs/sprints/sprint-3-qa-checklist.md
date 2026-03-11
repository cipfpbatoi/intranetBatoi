# Sprint 3 - QA Regressió Manual

Data: 2026-03-11  
Branca objectiu: `sprint-3-js-migration`

## Objectiu
Validar que la migració a JavaScript nadiu no ha introduït regressions funcionals en fluxos crítics.

## Preparació
1. Iniciar app i assets:
   - `php artisan serve` (o entorn docker equivalent)
   - `npm run dev`
2. Entrar amb un usuari amb permisos amplis (professor/direcció) i un usuari alumne.
3. Obrir consola del navegador per detectar errors JS no controlats.

## Criteri de pas
- Sense errors JavaScript bloquejants en consola.
- Les crides XHR/fetch retornen 2xx en flux normal.
- Cap modal es tanca immediatament ni queda bloquejada.
- Les taules carreguen i responen (ordenació, resize, accions).

## Checklist Prioritzat
1. Navegació lateral i submenús
   - Acció: obrir/tancar submenús repetidament.
   - Esperat: no fa scroll automàtic al top; manté posició vertical.

2. Signatura
   - Acció: obrir modal des de `.sign`, `.a1` i `.signatura`; provar submit.
   - Esperat: modal obri/tanca correctament, dades carregades, sense 401 inesperats.

3. Comissió
   - Acció: crear comissió, posar quilometratge 0 i >0.
   - Esperat: itinerari només s’activa amb quilometratge >0.

4. FCT/FCTCAP
   - Acció: obrir llistat, marcar/desmarcar checkboxes d’estat.
   - Esperat: canvis persistits per API i rollback visual si falla la petició.

5. Reunion/Seguimiento (edició inline)
   - Acció: clicar `editGrupo`, modificar camps (`input/select/textarea`), guardar i cancel·lar.
   - Esperat: desa per API, mostra errors en `#error` quan toca, i confirmacions funcionen.

6. Empresa
   - Acció: carregar llistat convenis, obrir detall i document, provar delete amb cancel/confirm.
   - Esperat: taula visible i estable, enllaços construïts bé, confirmació de baixa correcta.

7. Actividad (llistat i fitxers)
   - Acció: provar taula `#datatable` i càrrega/eliminació de fitxers en dropzone.
   - Esperat: cap error JS, càrrega correcta i restauració visual en error d’eliminació.

8. Poll
   - Acció: moure sliders en enquestes i comprovar “No Avaluat”.
   - Esperat: amb valor 0 es veu “No Avaluat”; amb valor >0 s’oculta.

9. Notificacions
   - Acció: esborrar notificació des de llistat.
   - Esperat: petició correcta i estat UI coherent després de l’acció.

10. Modals compartides (helper global)
   - Acció: obrir/ocultar modals en mòduls migrats diferents.
   - Esperat: comportament homogeni en BS4/BS5, sense tancaments espontanis.

## Registre d'incidències
Per cada incidència, guardar:
1. URL exacta.
2. Rol/usuari.
3. Passos mínims de reproducció.
4. Error de consola o resposta XHR/fetch.
5. Captura si aplica.
