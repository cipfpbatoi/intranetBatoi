# Convalidacions — espaiat entre les línies de l'acreditació

Issue: #323
Status: ready_for_review

## Escenari

Given una acreditació mostrada en la taula principal o en el resum final
When conté mòdul, cicle, any/nota i modalitat
Then cada línia visible és un bloc directe dins de la mateixa cel·la
And els inputs tècnics ocults conviuen amb estos blocs dins de la mateixa cel·la

## Regles de negoci

- `d-block` aporta salt de línia, no espai vertical; cada bloc visible usa un marge inferior explícit, excepte l'últim.
- No s'introduïx cap contenidor intermedi entre la cel·la i les línies visibles o els inputs ocults.

## Fitxers afectats

- `resources/views/intranet/convalidacions/alumno/create.blade.php` — blocs directes de les dades visibles dins de la cel·la.
- `tests/Feature/ConvalidacioFlowTest.php` — contracte de l'espaiat Bootstrap.
