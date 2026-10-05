# Convalidacions — declaració responsable conservada en la composició

Issue: #323
Status: ready_for_review

## Escenaris

### Escenari 1: declaració externa inclosa en tramitar

Given un alumne que incorpora una petició externa amb document i declaració responsable marcada
When obri el resum i tramita la sol·licitud
Then la declaració responsable es manté associada al mateix ítem que el document
And el backend la rep com un booleà vàlid

### Escenari 2: declaració absent

Given una petició externa sense declaració responsable
When l'alumne intenta tramitar-la
Then el domini rebutja la petició amb el missatge funcional d'origen extern
But no mostra un error genèric del format del camp HTTP

## Regles de negoci

- Abans d'enviar el formulari, cada ítem extern torna a assegurar el seu input ocult de declaració dins de la seua cel·la d'acreditació.
- La validació HTTP admet el booleà opcional; el servei de domini és qui exigix declaració i document conjuntament.

## Fitxers afectats

- \`resources/views/intranet/convalidacions/alumno/create.blade.php\` — garantia del camp ocult abans d'enviar.
- \`app/Http/Controllers/AlumnoConvalidacioController.php\` — validació HTTP booleana.
- \`tests/Feature/ConvalidacioFlowTest.php\` — regressió d'ítem extern amb declaració absent.
