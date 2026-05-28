# Convencions Generals

## Estructura

- `app/` conté domini, serveis, controladors, policies, finders i helpers.
- `routes/` està separat per rol: `public.php`, `todos.php`, `profesor.php`, `alumno.php`, `direccion.php`, `administrador.php`, `conserge.php`, `mantenimiento.php`, `jefeDpto.php`; API en `routes/api.php`.
- `resources/views/` conté Blade. Plantilles de correu en `resources/views/email/`.
- Assets en `resources/assets/js` i `resources/assets/sass`, compilats amb Laravel Mix.
- Migracions i seeders en `database/`; tests en `tests/Feature` i `tests/Unit`.
- Paquets i extensions locals en `packages/` i `plugins/`.

## Patrons Del Projecte

- Alerts: usar `use Intranet\Services\UI\AppAlert as Alert;`.
- Vistes renderitzen alertes amb `Intranet\Services\UI\AppAlert::render()`.
- Text visible: Valencià. Mantindre bilingüe només quan la plantilla existent ja ho és.
- Rutes web: buscar primer en el fitxer de rol corresponent.
- Autorització: revisar policies abans de canviar accions sensibles.
- No duplicar lògica si ja hi ha `Application/*`, `Services/*`, `Finders/*` o `Presentation/*`.

## Estil

- PHP PSR-12, indentació de 4 espais.
- Classes modificades o noves han de tindre PHPDoc actualitzat.
- Mantindre canvis acotats a la petició.
- Evitar tocar `composer.lock`, assets generats o fitxers no relacionats si ja estan modificats per l'usuari.

## Commits

- Prefixos: `[MOD]`, `[ADD]`, `[DEL]`, `[FIX]`.
- Abans de commit, intentar consultar issues amb `gh issue list` o `gh issue status`.
- Si `gh` no està autenticat o no hi ha xarxa, continuar i indicar-ho en el resum.
- No afegir atribució d'IA en missatges de commit.
