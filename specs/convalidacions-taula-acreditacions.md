# Convalidacions — taula de mòduls i acreditacions simplificades

Issue: #323
Status: ready_for_review

## Relació amb les specs existents

Este canvi refina la presentació definida en `specs/convalidacions-composicio-dialogs.md`. No modifica la validació acadèmica ni la còpia immutable: la nota continua disponible i la convocatòria continua guardada internament, però esta última deixa de mostrar-se en les pantalles funcionals.

## Escenaris

### Escenari 1: composició presentada com una taula

Given un alumne que ha incorporat una o més peticions a la composició
When consulta «Mòduls de la sol·licitud»
Then veu una taula amb les columnes «Mòdul a convalidar», «Acreditació» i «Accions»
And la primera columna mostra el codi i el nom del mòdul destí
And la segona columna mostra l'acreditació en línies diferenciades
And la tercera columna conté únicament el botó de paperera
And els inputs tècnics i els fitxers preparats continuen ocults

### Escenari 2: acreditació del propi centre

Given una petició justificada amb estudis cursats al propi centre
When apareix en la taula principal o en el resum final
Then l'acreditació mostra en negreta el codi i el nom del mòdul superat
And en una segona línia mostra el codi i el nom del cicle en negreta, l'any d'aprovació i la nota sense decimals
And identifica la modalitat «Estudis cursats al propi centre» en una línia secundària i en cursiva
But no mostra la convocatòria ordinària (`FI`) ni extraordinària (`EX`)

### Escenari 3: acreditació externa

Given una petició justificada amb documentació externa
When apareix en la taula principal o en el resum final
Then l'acreditació mostra la modalitat de justificació en negreta
And mostra el nom del fitxer en una segona línia
And no mostra controls de fitxer ni declaracions dins de la taula

### Escenari 4: confirmació visual discreta i total inequívoc

Given un alumne que afig o elimina una petició de la composició
When torna a la pantalla principal
Then el canvi de la fila i del comptador aporten la confirmació visual
And l'anunci d'accessibilitat es manté en una regió `aria-live` no intrusiva
But no apareix una alerta verda ocupant espai en la sol·licitud
And el peu mostra «Total de mòduls a convalidar: N» en lloc de «N mòduls»

### Escenari 5: nota visible sense convocatòria

Given un resultat acadèmic aprovat al propi centre
When l'alumne el selecciona, revisa la composició o consulta la petició tramitada
Then veu la nota obtinguda
And Direcció també veu la nota en el detall de revisió
But cap d'estes pantalles mostra si l'aprovat correspon a `FI` o `EX`

## Regles de negoci

- La nota continua formant part de la còpia immutable i es mostra; qualsevol regla futura per conservar-la o convertir-la en un 5 queda fora d'este canvi.
- La convocatòria continua disponible internament per traçabilitat, però no es mostra a alumnat ni Direcció.
- La taula principal i la taula del resum final utilitzen la mateixa jerarquia d'informació.
- El codi i el nom del mòdul destí no es confonen amb el codi i el nom del mòdul acreditat.
- Els textos dinàmics es construïxen amb `textContent` per evitar interpretar noms de fitxer o literals com HTML.
- L'estat buit es manté fora del `<tbody>` i desapareix quan existix almenys una fila.

## Fitxers afectats

- `resources/views/intranet/convalidacions/alumno/create.blade.php` — taula principal, taula de revisió, acreditació estructurada, anunci discret i nou total.
- `resources/views/intranet/convalidacions/alumno/show.blade.php` — mantindre la nota i ocultar la convocatòria.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` — mantindre la nota i ocultar la convocatòria.
- `tests/Feature/ConvalidacioFlowTest.php` — contracte de columnes, contingut visible, total i absència de convocatòria.
- `specs/convalidacions.md`, `specs/convalidacions-xml.md`, `specs/convalidacions-composicio-dialogs.md` i `specs/convalidacions-any-i-cancellacio.md` — alineació de la presentació quan el canvi quede validat.

## Riscos

- La fila es genera al navegador; les cel·les han de conservar els inputs ocults sense trencar l'estructura de la taula.
- En mòbil, la taula necessita un contenidor responsiu i amplàries que prioritzen l'acreditació.
- Ocultar la convocatòria no pot eliminar-la del lector XML ni de la còpia immutable, perquè pot ser útil per a traçabilitat o regles futures.
- El codi del mòdul destí ha d'obtindre's de l'identificador ja validat i no d'un text editable del navegador.
