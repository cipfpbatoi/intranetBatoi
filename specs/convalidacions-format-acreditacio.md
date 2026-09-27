# Convalidacions — format llegible de l'acreditació acadèmica

Issue: #323
Status: ready_for_review

## Relació amb les specs existents

Este canvi concreta la jerarquia tipogràfica de l'acreditació descrita en `specs/convalidacions-taula-acreditacions.md`. No altera la informació disponible ni el procés de tramitació.

## Escenaris

### Escenari 1: codi i nom del mòdul acreditat llegibles

Given una acreditació de «Estudis cursats al propi centre»
When l'alumne la veu en la taula de la sol·licitud o en el resum final
Then el codi del mòdul es mostra amb el format visual `GS 0179`
And el codi apareix en negreta
And el nom del mòdul apareix en negreta, junt amb el codi, dins del mateix bloc
And els dos elements tenen separació visual suficient per a no formar una sola paraula

### Escenari 2: cicle i modalitat amb jerarquia clara

Given una acreditació de «Estudis cursats al propi centre»
When l'alumne la consulta en la composició o el resum final
Then únicament el nom del cicle apareix en cursiva i separat del mòdul acreditat
And l'any i la nota es mostren en una línia posterior al cicle
And la modalitat «Estudis cursats al propi centre» apareix en una línia independent i en cursiva

### Escenari 3: nota acadèmica sense decimals artificials

Given un resultat acadèmic importat d'ITACA amb una nota numèrica entera
When apareix en el selector, el detall, la taula, el resum o les pantalles de consulta
Then la nota es mostra sense decimals, per exemple `Nota 9`
And la còpia immutable manté el valor numèric necessari per al processament futur

## Regles de negoci

- El format `GS 0179` és exclusivament de presentació: el codi complet original es conserva sense modificar.
- La separació visual del codi ha de funcionar amb codis de prefix alfabètic seguit de dígits; els codis que no complisquen eixe patró es mostren tal com arriben.
- La nota mostrada es normalitza a enter perquè ITACA només permet guardar valors enters en este flux.
- La modalitat s'ha de separar de la línia de nota mitjançant un bloc propi, no únicament amb espais.
- El codi intern del cicle no es mostra, encara que es conserve en la còpia immutable.
- Els noms i codis dinàmics continuen inserint-se amb `textContent`.

## Fitxers afectats

- `resources/views/intranet/convalidacions/alumno/create.blade.php` — format del selector, detall, taula principal i resum final.
- `resources/views/intranet/convalidacions/alumno/show.blade.php` — nota sense decimals en el detall de l'alumne.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` — nota sense decimals en el detall de Direcció.
- `tests/Feature/ConvalidacioFlowTest.php` — contracte del nou format i de la nota entera.
- `specs/convalidacions.md` i `specs/convalidacions-taula-acreditacions.md` — alineació de la presentació una vegada validada.

## Riscos

- No s'ha de modificar el codi acadèmic persistit ni usar el format amb espai per a validar-lo.
- Cal aplicar el mateix criteri a la taula i al resum perquè no contradiguen la mateixa acreditació.
- Arredonir una nota amb decimals no és necessari amb dades d'ITACA; si n'arribara alguna de llegat, cal evitar alterar el valor persistit.
