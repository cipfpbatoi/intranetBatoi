# Convalidacions — context del cicle de matrícula

Issue: #323
Status: ready_for_review

## Objectiu

En cada petició, conservar el cicle actual de matrícula del mòdul destí i la família professional del cicle. Estes dades complementen —no substituïxen— el cicle i la família del mòdul d'origen ja guardats per a les peticions de propi centre.

La còpia ha d'identificar tant les claus com els literals bilingües: ID intern i codi del cicle, noms valencià/castellà del cicle, ID del departament que identifica la família i noms valencià/castellà de la família.

La família del cicle actual no s'ha de deduir directament de la jerarquia dels XML acadèmics: estos poden descriure cicles i legislacions antigues que ja no corresponen a la classificació actual de la intranet.

## Escenaris

### Escenari 1: còpia del context de matrícula en cada petició

Given un alumne matriculat en un o més grups i una petició per a un mòdul destí
When presenta la sol·licitud
Then cada petició guarda l'ID i el codi del cicle de matrícula que correspon al mòdul destí dins dels grups de l'alumne
And guarda els noms del cicle en valencià i castellà
And guarda el codi de la família professional identificada en l'XML i els seus noms en valencià i castellà
And estes dades queden com una còpia immutable de la situació en el moment de presentar la sol·licitud
And es mantenen diferenciades de les dades del cicle i família del mòdul d'origen

### Escenari 2: família professional resolta des d'una relació vigent

Given el cicle de matrícula del mòdul destí està relacionat amb un departament mitjançant `ciclos.departamento`
When el sistema prepara la petició
Then obté l'ID del departament i els noms valencià/castellà de la família professional des dels camps específics del departament
And l'ID del departament identifica la família professional associada
And conserva també el codi i l'abreviatura ITACA del node arrel correlacionat
And no utilitza la jerarquia dels XML acadèmics per classificar el cicle actual
And no confia en cap dada de cicle o família enviada pel navegador

### Escenari 3: cicle o família de matrícula no resolubles

Given el mòdul destí no es pot associar a un únic cicle actual de l'alumne, o el departament no té noms de família professional configurats
When l'alumne intenta presentar la sol·licitud
Then el sistema rebutja la tramitació amb un missatge funcional que indique que no s'ha pogut identificar el cicle o la família professional
And no guarda la capçalera, cap petició ni cap document parcial

### Escenari 4: inicialització de la correspondència amb ITACA

Given el node arrel de família professional de l'XML més recent s'ha correlacionat amb un departament de la intranet
When s'inicialitzen les dades dels departaments
Then es guarden el codi (`codigo_xml`), l'abreviatura (`abreviatura_xml`) i els noms de família en valencià i castellà
And els noms es prenen dels atributs `nombre_val` i `nombre_cas` de l'XML
And només s'inicialitzen els departaments que tinguen un o més cicles associats
And les entitats externes i altres nodes que no siguen famílies no es vinculen a cap departament de cicles

### Escenari 4: consulta posterior independent de les fonts

Given una sol·licitud presentada amb el context de matrícula
When l'alumne o Direcció consulta qualsevol petició
Then pot veure el cicle i la família professional de matrícula amb els codis i literals conservats
And la consulta no depén que el grup, el cicle o l'XML continuen sense canvis

## Regles de negoci

- El context de matrícula s'emmagatzema en cada petició, perquè una sol·licitud pot incloure mòduls de cicles diferents.
- El cicle de matrícula s'obté relacionant els grups actuals de l'alumne amb el mòdul destí mitjançant `modulo_grupos`, `modulo_ciclos` i `ciclos`.
- La família professional del cicle actual s'obté mitjançant la relació interna `ciclos.departamento` i l'ID de `departamentos` és el seu identificador.
- La taula `departamentos` necessita els camps `familia_professional_val`, `familia_professional_cas`, `codigo_xml` i `abreviatura_xml`. No s'han de reutilitzar `departamentos.vliteral` i `departamentos.cliteral`, perquè són els literals del departament.
- Els noms bilingües i els atributs XML inicials es carreguen des dels nodes arrel de família del XML més recent, correlacionats amb els departaments segons la confirmació de Caporalia i contrastats amb la nomenclatura oficial.
- La càrrega inicial s'aplica a tots els departaments que tinguen almenys un cicle associat; els departaments sense cicles no necessiten aquests valors.
- Els codis `codigo_xml` i `abreviatura_xml` documenten la correspondència amb ITACA; durant una tramitació, la família actual es resol per `ciclos.departamento`, no es torna a buscar en l'XML.
- Si hi ha més d'un cicle possible per al mòdul destí, no se'n selecciona un arbitràriament.
- Les dades de matrícula són una fotografia immutable; no es recalculen quan canvia la matrícula o s'elimina un XML.
- Les peticions existents continuen sent vàlides i els camps nous han de permetre `null`; no es fa una reconstrucció històrica automàtica.

## Fitxers previstos

- `app/Application/Convalidacio/ConvalidacioService.php` — validar i capturar el cicle de matrícula i la família del destí en tramitar.
- `app/Entities/Convalidacio.php` — exposar els nous camps de context de matrícula.
- `app/Entities/Departamento.php` — exposar els noms bilingües i els identificadors XML de la família professional del departament.
- `database/migrations/` — afegir noms de família i camps `codigo_xml`/`abreviatura_xml` a `departamentos`, i columnes de cicle/família de matrícula a `convalidacions`, sense alterar les dades d'origen existents.
- `database/seeders/` o una migració de dades — inicialitzar la correspondència dels set departaments de cicles amb les famílies identificades en l'XML més recent.
- `resources/views/intranet/convalidacions/alumno/show.blade.php` — mostrar el context conservat a l'alumne.
- `resources/views/intranet/convalidacions/direccion/show.blade.php` — mostrar el context conservat a Direcció.
- `tests/Feature/ConvalidacioFlowTest.php` — cobrir resolució, persistència, consulta i casos ambigus/incomplets.

## Riscos

- Un mateix mòdul destí podria estar associat a més d'un cicle entre els grups d'un alumne; cal rebutjar l'ambigüitat per evitar una classificació incorrecta.
- No s'han de relacionar codis o literals dels XML amb els de la intranet per semblança: poden haver canviat entre LOE i LFP.
- Els camps de família d'origen ja existents no s'han de reutilitzar per a la família del cicle de matrícula: són conceptes diferents i poden no coincidir.

## Correspondència proposada amb l'XML més recent

L'XML més recent disponible declara una exportació de 21/09/2026. La consulta de la base de dades troba set departaments amb un o més cicles associats (28 cicles en total). La proposta és guardar estes dades a `departamentos`:

| ID departament | Cicles associats | `codigo_xml` | `abreviatura_xml` | `familia_professional_val` | `familia_professional_cas` |
|---:|---:|---|---|---|---|
| 10 | 4 | `3306172290` | `039` | `HOTELERIA I TURISME` | `HOSTELERÍA Y TURISMO` |
| 25 | 1 | `3306167239` | `150` | `SEGURETAT I MEDI AMBIENT` | `SEGURIDAD Y MEDIO AMBIENTE` |
| 24 | 5 | `3306169525` | `190` | `INFORMÀTICA I COMUNICACIONS` | `INFORMÁTICA Y COMUNICACIONES` |
| 5 | 3 | `3306173908` | `001` | `ADMINISTRACIÓ I GESTIÓ` | `ADMINISTRACIÓN Y GESTIÓN` |
| 2 | 4 | `3306170110` | `143` | `SERVICIS SOCIOCULTURALS I A LA COMUNITAT` | `SERVICIOS SOCIOCULTURALES Y A LA COMUNIDAD` |
| 6 | 6 | `3306170441` | `061` | `SANITAT` | `SANIDAD` |
| 3 | 5 | `3306168275` | `130` | `IMATGE PERSONAL` | `IMAGEN PERSONAL` |

El node `ENTITATS EXTERNES` (`3306200102`, `ENT_EXT`) no es vincula a cap departament de cicles. L'usuari ha confirmat la relació, els camps i la correspondència proposada; la implementació no deduirà la família del cicle matriculat des de l'XML en temps d'execució.
