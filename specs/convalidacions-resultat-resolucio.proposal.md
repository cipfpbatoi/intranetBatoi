# Proposta: resultat i nota de les convalidacions acceptades

Esta proposta complementa [`convalidacions.md`](convalidacions.md). Ha sigut aprovada, implementada i validada; els escenaris definitius s'han integrat en la spec principal.

## Escenaris

### Escenari 1: Direcció resol manualment una petició com a acceptada

Given Direcció revisa una petició de convalidació que encara no té resolució
When selecciona l'estat `Acceptada` (o assigna directament `Acceptada (Convalidada)`)
Then ha d'indicar el resultat de la convalidació: `CO` o `AA`
And ha d'indicar si es conserva la nota d'origen o si la resolució no porta nota
And si es conserva, la nota resultant és la nota d'origen guardada en la petició
And la resolució, la persona responsable i la data queden guardades en eixa petició, sense alterar les altres de la sol·licitud

### Escenari 2: una regla automàtica resol una petició

Given una petició compleix una regla automàtica vàlida
When Direcció aplica la resolució automàtica
Then es guarden el resultat `CO` o `AA` i el tractament de la nota definits per la regla
And si la regla conserva la nota, es guarda la nota d'origen com a nota resultant; si no, la nota resultant queda buida
And Direcció pot consultar el resultat i el tractament de la nota en la petició i en el llistat/PDF per mòdul
And la resolució conserva també la regla i la base normativa aplicades

### Escenari 3: distingir les resolucions automàtiques en el llistat per mòdul

Given Direcció consulta `/direccion/convalidacions/moduls` amb peticions resoltes manualment i automàticament
When una petició té una regla automàtica realment aplicada
Then el llistat mostra un robotet amb un hint accessible que indica «Resolució automàtica»
And les peticions resoltes manualment no mostren el robotet
And una regla només candidata o disponible, però no aplicada, no es presenta com una resolució automàtica

## Regles de negoci

- `Acceptada` no és suficient per a deduir el resultat de la convalidació ni si s'aplica una nota.
- En una resolució manual, `CO`/`AA` i el tractament de la nota són obligatoris quan es marca una petició com a `Acceptada` o s'assigna directament `Acceptada (Convalidada)`.
- En una resolució automàtica, el resultat i el tractament de la nota provenen de la regla YAML aplicada; no es demana una segona decisió manual.
- El robotet identifica únicament una resolució amb una regla automàtica aplicada i guardada; no indica elegibilitat ni una regla candidata.
- Si es conserva la nota, s'utilitza la nota d'origen registrada en la petició. Si no hi ha nota d'origen, no es pot presentar una nota resultant.
- El resultat i la nota pertanyen a cada petició de mòdul, no a la sol·licitud global.

## Fitxers afectats previstos

- `app/Http/Controllers/DireccionConvalidacioController.php` i `app/Application/Convalidacio/ConvalidacioService.php` — validar i guardar la resolució manual per mòdul.
- `app/Entities/Convalidacio.php` i una migració nova — persistir resultat i política/nota de resolució comunes a les vies manual i automàtica, sense perdre les instantànies de regles automàtiques.
- `app/Application/Convalidacio/ConvalidacioAutomaticaService.php` — registrar el resultat automàtic en els camps comuns de resolució.
- `resources/views/intranet/convalidacions/direccion/show.blade.php`, `resources/views/intranet/convalidacions/direccion/moduls.blade.php` i vistes PDF per mòdul — permetre indicar la resolució manual i mostrar-ne el resultat, la nota i l'origen automàtic de manera coherent.
- `tests/Feature/ConvalidacioFlowTest.php` — cobrir resolució manual, automàtica i la indicació visual de procedència en el llistat.
- `specs/convalidacions.md` — integrar els escenaris aprovats en la spec principal.

## Riscos

- Els camps existents `resultat_automatic` i `nota_resultat_automatic` només descriuen actualment l'automatització; reutilitzar-los per a decisions manuals seria semànticament confús. La proposta preveu camps comuns i conservar les instantànies automàtiques per a traçabilitat.
- S'assumix que en producció no hi haurà resolucions manuals acceptades anteriors a este canvi; si se'n detecta alguna, es revisarà com una incidència separada, sense inferir-ne ni migrar-ne el resultat.
- Si la nota d'origen és nul·la i Direcció selecciona conservar-la, el sistema ha de rebutjar la resolució amb un missatge clar.

Status: ready_to_commit
