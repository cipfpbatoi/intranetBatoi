# Cotxes i accés a l'aparcament

Especificació del bounded context que gestiona els vehicles del professorat i el reconeixement de matrícules en els accessos al centre.

## Regles de negoci

- Les matrícules es persistixen en majúscules, sense espais ni guions.
- La normalització s'aplica abans de validar la longitud i la unicitat.
- Les matrícules només poden contindre caràcters alfanumèrics una vegada normalitzades.
- Dos formats que només diferixen en espais, guions o majúscules representen la mateixa matrícula.
- Les matrícules rebudes de les càmeres es normalitzen amb la mateixa regla que les altes del professorat.
- Les dades llegades es normalitzen sense eliminar registres; si dos cotxes del mateix professor quedarien duplicats, la migració s'atura abans de modificar-los.

## Escenaris

### ✅ L'alta normalitza la matrícula

**Given** un professor que introduïx una matrícula amb espais, guions o minúscules

**When** dona d'alta el vehicle

**Then** la matrícula queda guardada en majúscules i sense separadors.

### ✅ L'edició normalitza la matrícula

**Given** un vehicle existent

**When** el professor edita la matrícula amb espais, guions o minúscules

**Then** el valor actualitzat queda guardat en el format canònic.

### ✅ Els formats equivalents són duplicats

**Given** un professor que ja té registrada una matrícula

**When** intenta donar d'alta la mateixa matrícula amb altres espais, guions o majúscules

**Then** l'alta es rebutja com a duplicada.

### ✅ La càmera reconeix matrícules amb separadors

**Given** una matrícula autoritzada guardada en format canònic

**When** la càmera envia el mateix valor amb espais, guions o minúscules

**Then** el sistema reconeix el vehicle i aplica el flux d'accés autoritzat.

### ✅ La migració saneja les dades existents amb seguretat

**Given** matrícules llegades amb espais, guions o minúscules

**When** s'executa la migració

**Then** es normalitzen els cotxes i l'historial d'accessos, però l'operació s'atura sense canvis si produiria un duplicat per al mateix professor.
