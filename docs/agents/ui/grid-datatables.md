# Graelles i DataTables

## Bounded Context

- Components Blade de taula: `resources/views/components/grid/table.blade.php` i components relacionats de `resources/views/components/grid/`.
- Panells i botons: `app/UI/Panels/Panel`, `app/UI/Panels/Pestana`, `app/UI/Botones/`.
- Controladors CRUD amb graella: `app/Http/Controllers/Core/BaseController.php` i controladors que defineixen `$gridFields`.
- JavaScript legacy de DataTables: revisar `resources/assets/js/` abans de tocar inicialitzacions.

## Regles De Graella

- Una taula DataTables ha de mantindre coherència entre capçaleres, files i peu: si afegeixes o lleves columnes, actualitza tots els punts que generen cel·les.
- No renderitzes files de placeholder dins de `<tbody>` per a l'estat buit. Deixa el `<tbody>` sense files i deixa que DataTables gestione el missatge de "sense dades".
- L'error `Requested unknown parameter '1' for row 0, column 1` sol indicar que una fila té menys cel·les que columnes declarades; comprova primer files buides, colspans i columnes d'accions.
- Evita inicialitzar dues vegades la mateixa taula. Si un model té JavaScript propi de graella, comprova la llista de models gestionats i la inicialització comuna.
- Mantín dimensions i textos compactes en taules d'ús operatiu; les graelles són superfícies de treball, no pàgines de màrqueting.

## Validació Recomanada

- Per canvis en la taula base: `php artisan test --filter=GridTableComponentTest`.
- Per canvis de rutes o controladors amb graella: test Feature del controlador afectat i `php artisan test --filter=RouteNameContractTest`.
- Si el canvi toca assets JS/SCSS: `npm run dev` o el build més acotat disponible.
