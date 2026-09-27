# Convalidacions — cicle nominal i separació del mòdul professional

Issue: #323
Status: ready_for_review

## Relació amb les specs existents

Esta proposta refina el format de `specs/convalidacions-format-acreditacio.md`. Només afecta la presentació de l'acreditació; les dades acadèmiques persistides no canvien.

## Escenaris

### Escenari 1: cicle identificat només pel seu nom

Given una acreditació de «Estudis cursats al propi centre»
When apareix en el detall, la taula de la sol·licitud o el resum final
Then mostra únicament el nom del cicle formatiu en cursiva
But no mostra el codi intern del cicle

### Escenari 2: mòdul professional visualment separat

Given una acreditació amb un mòdul professional superat
When l'alumne consulta la seua informació
Then el codi del mòdul i el seu nom apareixen en una línia pròpia
And el cicle apareix en una línia següent, separat visualment del mòdul
And l'any i la nota apareixen en una línia posterior al nom del cicle amb separadors llegibles

### Escenari 3: coherència entre les pantalles

Given una mateixa acreditació acadèmica
When es mostra al selector, al detall, a la taula, al resum i a les vistes d'alumnat o Direcció
Then no apareix en cap cas el codi d'identificació del cicle
And la nota es manté com a enter i la modalitat continua en cursiva

## Regles de negoci

- El codi del cicle es conserva internament per a la còpia immutable, però no es renderitza.
- La separació entre mòdul, cicle, any, nota i modalitat es resol amb elements o línies diferenciades, no depén d'espais dins d'una cadena.
- El codi del mòdul professional continua disponible i es mostra amb el seu format de presentació (`GS 0179` quan corresponga).

## Fitxers afectats

- `resources/views/intranet/convalidacions/alumno/create.blade.php` — selector, detall, taula principal i resum.
- `resources/views/intranet/convalidacions/alumno/show.blade.php` — ocultar el codi de cicle.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` — ocultar el codi de cicle.
- `tests/Feature/ConvalidacioFlowTest.php` — contracte d'absència del codi de cicle i de separació visual.
- `specs/convalidacions-format-acreditacio.md` — alineació una vegada validat.

## Riscos

- No s'ha de confondre el codi del cicle —que s'oculta— amb el del mòdul professional —que es manté.
- La construcció del resum al navegador ha de conservar els inputs ocults de la tramitació mentre elimina el codi de la presentació.
