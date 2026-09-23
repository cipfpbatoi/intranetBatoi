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

## Regles de negoci

- La continuïtat només usa actes arxivades anteriors del mateix grup i curs.
- Els punts d'una acta no poden quedar sense contingut auditable.
- Una acta arxivada és immutable des del flux d'arxivament.
- L'arxivament ha de ser atòmic davant d'errors de generació o registre del document.
- El PDF de l'acta ha d'identificar el lloc de la reunió.
