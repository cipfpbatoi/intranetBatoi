# Convalidacions — resposta controlada davant errors tècnics

Issue: #323
Status: ready_for_review

## Escenaris

### Escenari 1: fallada tècnica durant la tramitació

Given un alumne que ha preparat una sol·licitud vàlida, incloent-hi documentació externa i declaració responsable
When ocorre una fallada tècnica inesperada durant la tramitació
Then torna al formulari amb un missatge comprensible
And no veu el detall tècnic ni una pàgina d'error de Laravel
And no es crea cap sol·licitud ni petició parcial

### Escenari 2: traçabilitat del problema tècnic

Given una fallada tècnica no prevista
When el sistema la controla per a l'alumne
Then registra l'excepció en el log d'aplicació per a la seua investigació

## Regles de negoci

- Els errors funcionals previstos mantenen el seu missatge específic.
- Els errors tècnics no exposen informació de configuració, discs ni traça a l'alumnat.
- La transacció existent continua sent la garantia de no persistir dades parcials.

## Fitxers afectats

- \`app/Http/Controllers/AlumnoConvalidacioController.php\` — frontera HTTP d'errors tècnics durant la tramitació.
- \`tests/Feature/ConvalidacioFlowTest.php\` — regressió del missatge segur, rollback i registre.
- \`specs/convalidacions.md\` — alineació de la garantia de tramitació.
