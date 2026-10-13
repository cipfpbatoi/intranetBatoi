# Convalidacions — organització del panell de Direcció

Issue: #323

## Abast

Reorganitzar la pantalla principal de gestió de convalidacions perquè quede clar si s'estan consultant sol·licituds o resolent mòduls, separar les eines de configuració de les accions diàries i agrupar els controls temporals de proves.

No canvia les regles de convalidació, els estats, els permisos ni el comportament de les accions existents.

## Escenaris

### ✅ Escenari 1: distingir el llistat de sol·licituds del llistat per mòdul

Given una persona de Direcció està en la pantalla principal de convalidacions
When consulta la navegació entre els llistats
Then la vista actual s'identifica clarament com a «Sol·licituds»
And l'altra opció s'identifica com a «Convalidacions per mòdul»
And els textos expliquen breument què es consulta en cada vista, sense usar «Veure» com a única indicació de l'acció

### ✅ Escenari 2: mantindre visibles els controls de proves sense ocupar la pantalla

Given la pantalla principal mostra si l'accés de l'alumnat està obert o restringit
When una persona de Direcció consulta els controls de proves
Then l'estat actual apareix resumit i visible en un desplegable o secció plegable
And en desplegar-lo pot consultar o canviar el bloqueig i veure la contrasenya de proves
And el tancament del desplegable no oculta quin és l'estat actual

### ✅ Escenari 3: agrupar la gestió de regles, correspondències i avaluacions

Given una persona de Direcció està en la pantalla principal de convalidacions
When necessita gestionar les regles automàtiques, les correspondències d'anglés o les avaluacions d'ITACA
Then accedix a una secció «Configuració de convalidacions» dins de l'aplicació
And troba allí enllaços diferenciats a les tres eines
And la pantalla principal deixa de mostrar estes eines com a accions principals
And els permisos actuals de Direcció es mantenen

### ✅ Escenari 4: identificar l'aplicació de convalidacions automàtiques com una acció

Given hi ha peticions elegibles per a resolució automàtica
When Direcció consulta la pantalla principal
Then l'acció per a aplicar-les es presenta com un botó principal clarament accionable
And el text indica que aplicarà les convalidacions elegibles i n'indica la quantitat
And es conserva la confirmació i el comportament actual de l'acció

## Regles de negoci

- La pantalla principal correspon al llistat de sol·licituds; el llistat independent de peticions per mòdul continua accessible des d'una opció explícita.
- L'estat del bloqueig de proves ha de continuar visible encara que els seus controls estiguen plegats.
- La nova secció de configuració només agrupa els accessos existents; no amplia permisos ni modifica les eines.
- Aplicar convalidacions automàtiques continua sent una acció separada de la configuració i només afecta les peticions elegibles segons les regles vigents.

## Fitxers afectats previstos

- `resources/views/intranet/convalidacions/direccion/index.blade.php` — jerarquia visual, etiquetes, estat plegable de proves i presentació de l'acció automàtica.
- `resources/views/intranet/convalidacions/direccion/configuracio.blade.php` — nova pàgina d'accés a eines de configuració.
- `routes/direccion.php` — ruta de la secció de configuració, si cal.
- `app/Http/Controllers/DireccionConvalidacioController.php` — acció de pàgina de configuració, si cal.
- `tests/Feature/ConvalidacioFlowTest.php` — comprovacions de contingut, navegació i permisos.

## Riscos

- Canviar o retirar noms de ruta existents podria trencar enllaços guardats; es preferix conservar les rutes actuals i només reubicar-ne l'accés visual.
- Plegar els controls sense mostrar-ne l'estat podria amagar un bloqueig actiu; per això l'estat resumit ha de quedar visible.
- El botó d'aplicació automàtica no ha de suggerir que s'aplica a totes les peticions, només a les elegibles.

Status: ready_to_commit
