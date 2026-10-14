# Proposta: nota en una resolució manual de convalidació

Esta proposta complementa [`convalidacions.md`](convalidacions.md) i la proposta de resultat de resolució.

## Escenaris

### ✅ Escenari 1: Direcció decidix manualment el tractament de la nota

Given una petició es resol manualment com a `Acceptada` i no s'ha aplicat una regla automàtica
When Direcció tria conservar la nota
Then pot introduir la nota que corresponga segons la documentació aportada
And la nota és obligatòria i ha de ser un enter entre 5 i 10
And el sistema guarda la nota introduïda com a nota resultant de la resolució del mòdul
And si la petició ja té `nota_origen`, esta es mostra com a valor inicial editable

### ✅ Escenari 2: Direcció resol manualment sense nota

Given una petició es resol manualment com a `Acceptada`
When Direcció indica que la resolució no porta nota
Then no ha d'introduir cap nota
And la petició guarda `mode_nota_resolucio = none` i cap nota resultant

### ✅ Escenari 3: Direcció marca individualment una petició acceptada com a convalidada

Given una petició està en estat `Acceptada`, tant si s'ha resolt manualment com automàticament
When Direcció la canvia individualment a `Acceptada (Convalidada)`
Then el sistema guarda el nou estat i notifica l'alumne
And conserva el resultat (`CO`/`AA`) i el tractament i valor de la nota ja registrats
And no demana tornar a indicar el resultat ni la nota
But si la petició no està en estat `Acceptada`, el sistema rebutja el canvi

## Regles de negoci

- En el pas de resolució manual a `Acceptada`, Direcció decidix si hi ha nota i pot introduir-ne el valor quan tria «Conservar la nota».
- La dada de resultat i nota s'establix en passar a `Acceptada`; la transició individual posterior a `Acceptada (Convalidada)` conserva eixes dades.
- `Acceptada (Convalidada)` només es pot assignar individualment des de `Acceptada`, tant per a resolucions manuals com automàtiques; també es manté el pas massiu existent del PDF.
- Si hi ha `nota_origen`, es proposa com a valor inicial editable; el valor final es guarda en `nota_resultat_resolucio` i no s'altera la dada immutable `nota_origen`.
- No s'intenta llegir automàticament cap certificat ni adjunt.
- La nota manual és un enter entre 5 i 10: només s'introduïx en este flux si el certificat acredita que el mòdul està superat.
- El resultat `CO`/`AA` i el tractament de la nota continuen sent obligatoris en les resolucions acceptades.
- No es modifica el flux automàtic ni es reinterpreten certificats o adjunts.

## Fitxers afectats previstos

- `resources/views/intranet/convalidacions/direccion/show.blade.php` — permetre a Direcció introduir o editar la nota quan una resolució manual acceptada conserva nota.
- `app/Http/Controllers/DireccionConvalidacioController.php` i `app/Application/Convalidacio/ConvalidacioService.php` — validar i guardar l'enter manual sense alterar les dades d'origen.
- `resources/views/intranet/convalidacions/direccion/show.blade.php`, `moduls.blade.php` i `moduls-pdf.blade.php` — reflectir el resultat guardat de manera coherent.
- `tests/Feature/ConvalidacioFlowTest.php` — cobrir entrada manual amb i sense nota d'origen i resolució sense nota.
- `specs/convalidacions.md` — integrar els escenaris aprovats.

## Riscos

- Una nota introduïda manualment depén de la valoració de Direcció sobre la documentació; la interfície ha d'indicar clarament que s'ha d'introduir el valor acreditat.
- Cal evitar que canviar el desplegable després d'introduir una nota deixe un valor obsolet que es guarde amb `mode_nota_resolucio = none`.
- Les peticions ja acceptades sense nota no s'actualitzen automàticament; només s'afegirà la nota quan Direcció les torne a revisar mitjançant el flux previst.

Status: ready_to_commit
