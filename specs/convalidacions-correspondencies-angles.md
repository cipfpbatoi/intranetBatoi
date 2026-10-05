# Convalidacions — correspondències de cicles amb anglés de 5 hores o més

Issue: #323
Status: ready_for_review

## Traça i punt d'entrada

- Domini: Convalidacions.
- Punt d'entrada: opció «Gestionar correspondències d'anglés» dins de `direccion/convalidacions/`.
- Documents/specs llegits: `AGENTS.md`, `docs/agents/fct/fct-map.md`, `docs/agents/conventions.md`, `specs/convalidacions-resolucions-automatiques.md` i `specs/convalidacions-indicadors-automatitzacio.md`.
- Abast: persistir, importar per CSV i gestionar amb CRUD la correspondència entre cicles d'anglés que complixen el mínim de 5 hores setmanals i el cicle formatiu del qual formen part; usar la correspondència per a verificar les condicions YAML `minimum_weekly_hours`.

## Esquema de dades proposat

Una fila representa un cicle d'anglés que compleix el mínim de 5 hores setmanals i el vincula amb el cicle formatiu que el conté:

| Camp | Significat |
|---|---|
| `codi_cicle_angles` | Codi exacte del cicle d'anglés, tractat com a text per conservar zeros inicials |
| `nom_cicle_angles_val` / `nom_cicle_angles_cas` | Nom valencià i castellà del cicle d'anglés |
| `codi_cicle_contenidor` | Codi exacte del cicle formatiu del qual forma part |
| `nom_cicle_contenidor_val` / `nom_cicle_contenidor_cas` | Nom valencià i castellà del cicle contenidor |
| `es_grau_superior` | Indicador explícit de si el cicle d'anglés és de grau superior |

La presència d'una fila significa que la correspondència ha sigut revisada i que el cicle d'anglés té almenys 5 hores setmanals. No s'introduïx un camp d'hores numèric perquè no forma part de les dades sol·licitades.

## Escenaris

### Escenari 1: importar correspondències CSV ✅

Given Direcció obri la gestió de correspondències d'anglés
When puja un CSV amb les columnes de codi i noms bilingües dels dos cicles i l'indicador de grau superior
Then el sistema valida la capçalera, els codis, els noms i els valors de l'indicador abans de modificar dades
And tracta els codis com a cadenes i conserva els zeros inicials
And rebutja fitxers invàlids o files amb dades incompletes sense canviar cap dada existent
And l'aplicació de les files vàlides és transaccional

### Escenari 2: substituir dades per cicle sense afectar-ne altres ✅

Given el CSV conté correspondències d'un o més cicles contenidors
When Direcció importa el CSV
Then per cada `codi_cicle_contenidor` present, el conjunt de correspondències existent se substituïx completament pel conjunt del fitxer
And les correspondències de cicles contenidors absents del fitxer no canvien
And si falla la validació o la transacció, es conserva íntegre el conjunt anterior

### Escenari 3: consultar i mantindre les correspondències amb CRUD ✅

Given una persona de Direcció en la gestió de correspondències
When consulta, crea, edita o elimina una fila
Then veu els codis, els dos noms de cada cicle i si el cicle d'anglés és de grau superior
And els formularis validen els mateixos camps que la importació
And els codis no es convertixen a nombres ni perden zeros inicials
And només persones amb el rol de Direcció poden accedir a les rutes o executar canvis

### Escenari 4: accedir-hi des del panell de convalidacions ✅

Given una persona de Direcció en `direccion/convalidacions/`
When vol mantindre la correspondència d'hores d'anglés
Then troba una opció visible «Gestionar correspondències d'anglés» al panell
And l'opció obri la pantalla de consulta, importació i CRUD

### Escenari 5: aplicar la correspondència en les regles d'hores ✅

Given una petició presentada amb estudis d'anglés al propi centre
And la regla YAML exigix `minimum_weekly_hours: 5`
When el sistema avalua la petició
Then considera complida la condició només si el codi exacte del cicle d'origen té una correspondència importada que acredita el mínim
And conserva i mostra els codis i noms bilingües dels dos cicles i el nivell GS/GM de la correspondència
And si no hi ha una correspondència inequívoca, la petició queda pendent per a revisió manual
And l'avaluació no rellegix ni modifica els XML d'avaluacions acadèmiques

## Regles de negoci

- La correspondència es guarda en una taula pròpia del domini de convalidacions; no s'inferix dels noms ni es mescla amb cicles, departaments o fitxers XML d'avaluacions.
- Els codis són cadenes, poden contindre zeros inicials i són la clau de comparació; els literals bilingües són dades descriptives conservades.
- Cada fila indica un cicle d'anglés qualificat amb almenys 5 hores i el cicle contenidor al qual pertany.
- `es_grau_superior` és obligatori i descriu el cicle d'anglés, no el contenidor.
- El CSV conté les dades ja filtrades/confirmades per la persona responsable; no s'infereixen hores a partir dels noms.
- Una importació reemplaça totes les files dels cicles contenidors presents en el CSV, i no toca altres cicles. La validació completa precedix qualsevol esborrat o inserció.
- La correspondència alimenta únicament la comprovació de `minimum_weekly_hours`; no modifica per si sola el YAML, els resultats de regles ni l'estat de peticions.
- El CRUD manual i la importació utilitzen les mateixes validacions i preserven les regles d'autorització de Direcció.
- No s'exposen dades d'alumnat ni XML originals en esta interfície.

## CSV proposat

Capçalera exacta:

```csv
codi_cicle_angles,nom_cicle_angles_val,nom_cicle_angles_cas,codi_cicle_contenidor,nom_cicle_contenidor_val,nom_cicle_contenidor_cas,es_grau_superior
```

El camp `es_grau_superior` accepta `1/0` o `si/no` (sense distingir majúscules i minúscules); els altres camps són obligatoris i els codis es lligen com a text.

## Fitxers afectats previstos

- `routes/direccion.php` — rutes protegides del CRUD i de la importació.
- `app/Entities/` i `database/migrations/` — entitat i taula de correspondències amb unicitat per cicle d'anglés/contenidor.
- `app/Application/Convalidacio/` — servei transaccional d'importació, reemplaçament per cicle, CRUD i consulta exacta per al motor de regles.
- `app/Http/Controllers/` — punt d'entrada de Direcció per al llistat, formularis i importació.
- `resources/views/intranet/convalidacions/direccion/` — pantalla de gestió, formulari d'importació i formularis CRUD.
- `resources/views/intranet/convalidacions/direccion/index.blade.php` — enllaç des del panell principal.
- `app/Application/Convalidacio/ConvalidacioAutomaticaService.php` — verificar `minimum_weekly_hours` usant la taula en lloc de deixar la condició sempre pendent.
- `resources/views/intranet/convalidacions/direccion/regles.blade.php` — exposar el motiu/resum amb els detalls de correspondència quan pertoque.
- `tests/Feature/ConvalidacioFlowTest.php` — import vàlid/invàlid, reemplaçament aïllat per cicle, CRUD, permisos i coincidència automàtica.

## Riscos i supòsits per a validar

- Supòsit: «per cicle machacar» significa reemplaçar el conjunt de files per cada codi de `cicle_contenidor` present en el CSV, no esborrar cicles absents del fitxer.
- Supòsit: una fila del CSV ja certifica que el cicle d'anglés complix `>= 5` hores setmanals; per això no es guarda una quantitat d'hores concreta.
- La correspondència ha de comparar codis XML exactes amb el codi de cicle guardat en la petició. Si eixa dada de la petició no identifica el cicle d'anglés que descriu el CSV, cal ajustar la font/identificador abans de donar per aplicable la regla.
- Els valors de `es_grau_superior` poden arribar amb variants de format; el parser ha de rebutjar literals desconeguts i no interpretar-los de manera permissiva.

Status: ready_for_review
