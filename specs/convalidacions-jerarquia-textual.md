# Convalidacions — jerarquia textual definitiva de l'acreditació

Issue: #323
Status: ready_for_review

## Relació amb les specs existents

Esta proposta substituïx només la jerarquia tipogràfica de les specs de format anteriors. No canvia les dades, la validació ni la tramitació.

## Escenaris

### Escenari 1: mòduls en negreta sense cursiva

Given una petició amb mòdul destí i una acreditació de «Estudis cursats al propi centre»
When es mostra en la taula o el resum final
Then el mòdul destí es mostra complet en negreta i sense cursiva, per exemple `0179 — Anglés professional GS`
And el mòdul acreditat es mostra complet en negreta i sense cursiva, per exemple `0369 — Implantació de Sistemes Operatius`

### Escenari 2: cicle en cursiva i separat

Given una acreditació del propi centre
When apareix davall del mòdul acreditat
Then el nom del cicle apareix sol, en cursiva i sense negreta
And no mostra el codi intern del cicle
And la línia següent mostra `Any N · Nota N` amb separadors visibles

### Escenari 3: separació estable entre tots els blocs

Given una acreditació que conté mòdul, cicle, any, nota i modalitat
When l'alumne la visualitza
Then cada bloc ocupa una línia pròpia o està unit per un separador amb espais als dos costats
And la modalitat continua en cursiva en una línia independent
And no apareixen textos enganxats com el nom del mòdul seguit immediatament del cicle

## Regles de negoci

- El format del codi del mòdul continua sent exclusivament visual i no modifica l'identificador persistit.
- No es mostra el codi del cicle en cap pantalla funcional.
- Els mòduls professionals es generen com un únic bloc de text en negreta; no es mescla l'estil del codi amb el del nom.
- La presentació s'ha de construir amb nodes de text i elements separats perquè els espais no depenguen del navegador.

## Fitxers afectats

- `resources/views/intranet/convalidacions/alumno/create.blade.php` — format del detall, taula i resum generats al navegador.
- `resources/views/intranet/convalidacions/alumno/show.blade.php` — jerarquia en la consulta de l'alumne.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` — jerarquia en la consulta de Direcció.
- `tests/Feature/ConvalidacioFlowTest.php` — contracte de classes/estructura i absència d'informació enganxada.
- `specs/convalidacions-format-acreditacio.md` i `specs/convalidacions-cicle-sense-codi.md` — alineació quan es valide.

## Riscos

- El resum de navegador usa el mateix origen de dades que la taula: els canvis de format han de mantindre's en tots dos.
- Els inputs ocults de la petició no poden introduir nodes visibles dins de l'acreditació.
