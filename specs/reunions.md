# Reunions i actes

Especificació funcional de la creació, continuïtat i arxivament de les actes de reunió.

## Escenaris implementats

### ✅ Escenari 1: Revisar els acords de l'acta anterior

**Donat** que existeix una acta anterior arxivada del mateix grup i curs
**I** conté el punt «Acords adoptats» amb contingut
**Quan** es genera una nova acta del grup
**Aleshores** el punt «Revisió d'acords adoptats a la sessió anterior» conté els acords de l'acta anterior.

### ✅ Escenari 2: Mantindre el seguiment de l'alumnat NESE

**Donat** que existeix una acta anterior arxivada del mateix grup i curs
**I** conté el punt «Alumnes amb dificultats acadèmiques i mesures a adoptar» amb contingut
**Quan** es genera una nova acta del grup
**Aleshores** el mateix punt conté la informació de seguiment de l'acta anterior.

### ✅ Escenari 3: Evitar punts sense contingut

**Donat** un punt d'una acta amb valor nul, espais o HTML sense text real
**Quan** es genera o s'arxiva l'acta
**Aleshores** el resum del punt és «No procedeix»
**I** el contingut amb text real es conserva sense canvis.

### ✅ Escenari 4: Mostrar el lloc de la reunió

**Donat** que una reunió té un espai assignat
**Quan** es genera el PDF de l'acta
**Aleshores** el document mostra la descripció de l'espai
**I** usa el codi de l'espai si no hi ha descripció.

### ✅ Escenari 5: Seleccionar únicament l'acta anterior corresponent

**Donat** que existeixen actes de diferents grups o tipus de reunió
**Quan** es busca l'acta anterior per heretar contingut
**Aleshores** només es considera l'última acta arxivada anterior del mateix grup i curs
**I** no es confon amb una reunió de grup de treball
**I** es mantenen compatibles les actes llegades sense identificador de grup.

### ✅ Escenari 6: Arxivar una acta de manera segura

**Donat** un professor que pot gestionar l'acta
**Quan** arxiva l'acta
**Aleshores** la normalització, el registre documental i l'estat d'arxivament es processen com una única operació
**I** qualsevol error desfà els canvis de base de dades i elimina el PDF parcial creat.

### ✅ Escenari 7: Protegir les actes arxivades

**Donat** una acta ja arxivada
**Quan** es torna a sol·licitar l'arxivament o falta el seu fitxer
**Aleshores** l'acta i els seus punts no es modifiquen
**I** només el convocant o el tutor actual autoritzat pot arxivar una acta oberta.

### ✅ Escenari 8: Restringir la consulta de reunions i actes

**Donat** un professor autenticat
**Quan** consulta una reunió, les seues dades sensibles o el PDF de l'acta
**Aleshores** l'accés es permet al convocant, al tutor actual, als assistents i a Direcció o Administració
**I** un professor alié rep una resposta d'accés denegat
**I** els llistats API només inclouen les reunions visibles per a l'usuari.

### ✅ Escenari 9: Autoritzar les mutacions de reunions

**Donat** una reunió oberta
**Quan** se sol·licita modificar-la, eliminar-la, gestionar participants, notificar-la o arxivar-la
**Aleshores** l'operació només es permet al convocant o al tutor actual autoritzat
**I** un professor alié rep una resposta d'accés denegat
**I** una reunió arxivada és immutable excepte pel flux explícit de desarxivament autoritzat.

### ✅ Escenari 10: Validar la pertinença dels recursos fills

**Donat** una ordre, un professor o un alumne relacionat amb una reunió
**Quan** se sol·licita modificar-lo o eliminar-lo des de la ruta d'una altra reunió
**Aleshores** l'operació es rebutja com a recurs no trobat
**I** no es modifica cap ordre, assistència ni pivot d'una reunió distinta.

### ✅ Escenari 11: Restringir els camps mutables de l'API

**Donat** un payload API amb camps de propietat, arxiu o relació pare
**Quan** es crea o actualitza una reunió, una ordre o una assistència
**Aleshores** només es validen i persisteixen els camps funcionals permesos
**I** el convocant de la reunió nova és sempre el professor autenticat
**I** no es poden transferir la propietat, l'estat d'arxiu, el fitxer ni la reunió pare mitjançant camps injectats.

### ✅ Escenari 12: Usar verbs HTTP segurs per a les mutacions

**Donat** una operació que elimina o canvia l'estat d'una reunió o dels seus participants
**Quan** s'invoca la ruta web corresponent
**Aleshores** la ruta exigeix `POST` o `DELETE` amb protecció CSRF
**I** una petició `GET` no executa la mutació.

## Regles de negoci

- La continuïtat només usa actes arxivades anteriors del mateix grup i curs.
- Els punts d'una acta no poden quedar sense contingut auditable.
- Una acta arxivada és immutable des del flux d'arxivament.
- L'arxivament ha de ser atòmic davant d'errors de generació o registre del document.
- El PDF de l'acta ha d'identificar el lloc de la reunió.
- Les dades sensibles d'una reunió només són visibles per al convocant, el tutor actual, els assistents i els rols de Direcció o Administració.
- Les mutacions d'una reunió oberta corresponen al convocant o al tutor actual autoritzat.
- Tot recurs fill s'ha de resoldre dins de la reunió pare indicada abans de modificar-lo.
- Els payloads API no poden alterar camps de propietat, arxiu, fitxer ni relacions pare fora dels fluxos explícits.
- Cap operació destructiva de reunions pot executar-se mitjançant `GET`.
