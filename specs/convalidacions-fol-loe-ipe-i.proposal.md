# Convalidacions — catàleg FOL LOE i regla per a IPE I

Issue: #323
Status: ready_for_review

## Objectiu

Identificar els mòduls de Formació i Orientació Laboral (FOL) LOE mitjançant un catàleg editable de codis, i aplicar de manera automàtica la regla de convalidació de FOL LOE a IPE I quan l'origen siga verificable en les avaluacions del centre.

## Regla proposada en llenguatge natural

Si l'alumne té aprovat un mòdul FOL LOE identificat per un codi del catàleg i demana convalidar IPE I, es pot convalidar automàticament tant si el FOL d'origen és de grau mitjà com de grau superior, i tant si l'IPE I de destí és de grau mitjà com de grau superior. El resultat és `CO` i es conserva la nota numèrica d'origen. La regla es basa en l'article 127.5 del [Reial decret 659/2023](https://www.boe.es/eli/es/rd/2023/07/18/659/con). La diferència de grau no limita la correspondència.

En la versió actual, l'automatització només és possible quan l'alumne ha pogut seleccionar el resultat aprovat de l'XML privat del centre. Si aporta un certificat extern o no pot seleccionar el mòdul d'origen, Direcció ho revisa manualment perquè encara no tenim una extracció automàtica de codis en documents. Això és una limitació tècnica actual, no una exclusió de la regla: si més avant un OCR identifica i valida inequívocament un codi LOE del catàleg, es podrà aplicar la mateixa regla sense crear una equivalència nova. Esta regla no modifica ni amplia les regles d'EIE, ni reutilitza el requisit especial de PRL de FOL LOGSE.

## Codis contrastats amb els XML del centre

Els dos XML locals (`2024-25.xml` i `avaluacio-2025-26.xml`) confirmen els 20 codis facilitats i també els codis `0218` i `0755`. El mateix contingut identifica `0106` com a Farmàcia i Parafarmàcia (GM). Els noms següents són els del cicle trobats en l'XML més recent; cal que Direcció els puga editar si detecta una errada.

| Codi | Cicle (castellà) | Cicle (valencià, XML) | Nivell |
| --- | --- | --- | --- |
| 0021 | Educación Infantil (LOE) | Educació Infantil (LOE) | GS |
| 0049 | Cocina y Gastronomía | Cuina i Gastronomia | GM |
| 0062 | Emergencias Sanitarias | Emergències Sanitàries | GM |
| 0106 | Farmacia y Parafarmacia | Farmàcia i Parafarmàcia | GM |
| 0157 | Servicios en Restauración | Serveis en Restauració | GM |
| 0218 | Atención a Personas en Situación de Dependencia | Atenció a persones en situació de dependència | GM |
| 0229 | Sistemas Microinformáticos y Redes | Sistemes Microinformàtics i Xarxes | GM |
| 0346 | Integración Social (LOE) | Integració Social (LOE) | GS |
| 0380 | Administración de Sistemas Informáticos en Red | Administració de Sistemes Informàtics en Xarxa | GS |
| 0449 | Gestión Administrativa (LOE) | Gestió Administrativa (LOE) | GM |
| 0493 | Desarrollo de Aplicaciones Multiplataforma | Desenrotllament d'Aplicacions Multiplataforma | GS |
| 0506 | Dirección de Cocina | Direcció de Cuina | GS |
| 0617 | Desarrollo de Aplicaciones Web | Desenrotllament d'Aplicacions Web | GS |
| 0644 | Estética y Belleza | Estètica i Bellesa | GM |
| 0658 | Administración y Finanzas (LOE) | Administració i Finances (LOE) | GS |
| 0665 | Asistencia a la Dirección | Assistència a la Direcció | GS |
| 0755 | Estética Integral y Bienestar | Estètica Integral i Benestar | GS |
| 0851 | Peluquería y Cosmética Capilar | Perruqueria i Cosmètica Capil·lar | GM |
| 1133 | Animación Sociocultural y Turística | Animació Sociocultural i Turística | GS |
| 1356 | Imagen para el Diagnóstico y Medicina Nuclear | Imatge per al Diagnòstic i Medicina Nuclear | GS |
| 1376 | Laboratorio Clínico y Biomédico | Laboratori Clínic i Biomèdic | GS |
| 1556 | Química y Salud Ambiental | Química i Salut Ambiental | GS |

Esta relació és el conjunt trobat en els XML disponibles, no una afirmació que siga el catàleg exhaustiu de tots els cicles LOE que han existit. `0106` queda identificat pel [RD del títol de Farmàcia i Parafarmàcia](https://www.boe.es/diario_boe/txt.php?id=BOE-A-2008-818), `0218` pel [RD d'Atenció a Persones en Situació de Dependència](https://www.boe.es/eli/es/rd/2011/11/04/1593) i `0755` pel [RD d'Estètica Integral i Benestar](https://www.boe.es/diario_boe/txt.php?id=BOE-A-2011-12722). `028001` es deixa fora perquè ja està catalogat com a LOGSE; `CV0005` i `CV0006` apareixen en els XML associats a FP Bàsica, no com a FOL LOE.

## Escenaris

### Escenari 1: inicialitzar el catàleg de FOL LOE

Given s'instal·la l'aplicació amb el catàleg inicial de FOL LOE
When es consulta la configuració de convalidacions
Then existeix un registre per a cada codi de la relació contrastada d'esta proposta
And cada registre conserva el codi numèric com a text de longitud variable, el nom del cicle en castellà, el nom del cicle en valencià i el nivell `GM` o `GS`
And els codis no es confonen amb els del catàleg FOL LOGSE

### Escenari 2: administrar el catàleg des de la configuració de Direcció

Given una persona autoritzada de Direcció obri la configuració de convalidacions
When consulta, crea, edita o elimina una entrada FOL LOE
Then pot mantindre el codi, els dos noms del cicle i el nivell
And el codi es valida com a identificador numèric de longitud variable i conserva literalment els zeros inicials si n'hi ha
And no es guarden dues entrades el codi de les quals represente el mateix nombre natural, encara que una tinga zeros inicials
And una entrada incompleta o amb un codi duplicat no es guarda
And l'accés a la gestió queda restringit al perfil de Direcció

### Escenari 3: identificar un FOL LOE superat al propi centre

Given l'alumne selecciona estudis cursats al propi centre
And té un mòdul FOL aprovat en un XML privat
When el servei processa el resultat acadèmic
Then normalitza el codi de l'XML i el del catàleg com a nombres naturals, llevant els espais exteriors i els zeros inicials
And els codis de longitud diferent que representen el mateix nombre natural coincideixen
And conserva el valor literal rebut de l'XML en les dades d'origen de la petició
And conserva la nota i les dades del resultat d'origen ja previstes en la petició
And no exposa el nom ni la ubicació del fitxer XML a l'alumne
But un codi absent del catàleg no s'interpreta com a FOL LOE per semblança de nom

### Escenari 4: aplicar FOL LOE a IPE I sense restricció de grau

Given una petició presentada per a IPE I pendent de resolució
And el mòdul d'origen és un FOL LOE aprovat identificat pel catàleg
When Direcció previsualitza o aplica les convalidacions automàtiques
Then la regla és elegible per a IPE I de grau mitjà i de grau superior, sense exigir que els nivells d'origen i destí coincidisquen
And en aplicar-la, la petició passa a `Acceptada` amb resultat `CO` i conserva la nota numèrica d'origen
And el registre guarda la regla i la base normativa `art. 127.5`
And consultar la previsualització no canvia cap estat

### Escenari 5: revisió manual quan l'origen no està identificat en la versió actual

Given l'alumne aporta un certificat d'un altre centre o no ha pogut seleccionar el mòdul d'origen
And la versió actual no pot extraure i validar automàticament el codi FOL del document
When s'avalua la regla FOL LOE → IPE I
Then la petició continua per a revisió manual per limitació tècnica, no perquè els codis d'altres centres estiguen exclosos de la regla
But si una futura eina OCR identifica i valida inequívocament un codi del catàleg, el mateix criteri de convalidació es pot aplicar sense una nova equivalència
And un codi absent del catàleg no s'interpreta com a FOL LOE per semblança del nom
And la regla no modifica ni elimina els documents, la declaració LOGSE ni les regles específiques de FOL LOGSE

### Escenari 6: no alterar les regles d'EIE

Given el YAML conté regles d'EIE
When s'actualitza la regla de FOL LOE → IPE I
Then les regles d'EIE romanen sense canvis en esta funcionalitat
And qualsevol canvi futur d'EIE requerix una especificació i una decisió pròpies

## Regles de negoci

- El catàleg LOE és una font de dades editable per a identificar mòduls FOL. Emmagatzema els codis numèrics com a text de longitud variable i no pressuposa que tinguen quatre xifres; conserva literalment els zeros inicials i els codis més llargs tal com s'introduïxen. Per comparar, els dos valors es normalitzen a la representació decimal del nombre natural corresponent, eliminant espais exteriors i tots els zeros inicials (un valor format només per zeros es normalitza a `0`). Així, `0021`, `21` i `00021` representen el mateix codi a efectes de coincidència. No es fan coincidències per nom. El valor literal de l'XML es conserva per a traçabilitat.
- La correspondència FOL LOE → IPE I s'aplica independentment del grau d'origen i del grau de destí.
- En l'aplicació automàtica, el resultat és `CO`, es conserva la nota numèrica disponible i es registra l'article 127.5 del RD 659/2023.
- En la versió actual, només s'automatitzen peticions presentades amb dades d'origen verificades com a resultats aprovats de l'XML. Els certificats externs o els casos sense mòdul d'origen seleccionable queden en revisió manual perquè encara no hi ha extracció automàtica del codi.
- L'origen extern no queda exclòs permanentment: si una futura eina OCR identifica i valida exactament un codi present al catàleg LOE, podrà usar-se la mateixa regla. El desenvolupament OCR no forma part d'esta proposta.
- Les dades del nou catàleg no substituïxen ni alteren el catàleg FOL LOGSE, la declaració LOGSE o la documentació PRL existent.
- No s'introduïx cap canvi a EIE en esta proposta.
- La relació inicial conté els codis aportats per l'usuari més `0218` i `0755`, descoberts en els XML del centre i contrastats amb els reials decrets dels títols corresponents. `0106` queda identificat com a Farmàcia i Parafarmàcia, GM.

## Traça i punt d'entrada

- Domini: Convalidacions.
- Punt d'entrada actual: `ResultatsAcademicsXmlService`, `ConvalidacioAutomaticaService`, `DireccionConvalidacioController@configuracio` i `ConvalidacioReglesManager`.
- Docs/specs llegits: `AGENTS.md`, `docs/agents/openspec.md`, `docs/agents/conventions.md`, `specs/convalidacions.md`, `specs/convalidacions-documents-i-fol-catalog.md` i `specs/convalidacions-resolucions-automatiques.md`.
- Abast: proposta de catàleg FOL LOE gestionable per Direcció i regla automàtica amb origen propi verificat per a IPE I; sense canvis a EIE.

## Fitxers previstos per a una fase d'implementació posterior

- `database/migrations/` i `app/Entities/` — persistència i model del catàleg FOL LOE.
- `routes/direccion.php`, controladors de Direcció i `resources/views/intranet/convalidacions/direccion/configuracio.blade.php` — operacions CRUD protegides i accés des de Configuració.
- `app/Application/Convalidacio/ResultatsAcademicsXmlService.php` — identificació exacta dels codis FOL LOE.
- `app/Application/Convalidacio/ConvalidacioAutomaticaService.php` i `resources/convalidacions/regles-lfp.yaml` — connexió del catàleg amb la regla IPE I i resolució `CO` amb nota.
- `tests/Feature/` i `tests/Unit/` — CRUD, permisos, identificació exacta, nivells d'origen/destí i resolució automàtica.
- `specs/convalidacions.md` — incorporar els escenaris aprovats en arxivar la implementació.

## Riscos i punts a validar

- Els dos XML disponibles confirmen la relació local, però no proven que siga exhaustiva per a tots els cicles LOE impartits històricament a la Comunitat Valenciana.
- L'article 127.5 del RD 659/2023 estableix la resolució automàtica i la conservació de la qualificació per a les convalidacions d'IPE I i II aportant FOL/EIE d'estudis previs. La [disposició addicional sexta del RD 1085/2020](https://www.boe.es/buscar/act.php?id=BOE-A-2020-17274) diu que, quan el mòdul destí siga FOL, la relació s'entén també feta a IPE I, i quan siga EIE, a IPE II. Esta proposta, seguint l'abast demanat, modifica només FOL → IPE I; no reinterpreta EIE.
- La migració inicial del catàleg i les edicions CRUD poden afectar quines peticions són elegibles en execucions futures; les instantànies de resolucions ja aplicades han de continuar intactes.
- La lectura OCR de certificats externs queda fora d'esta proposta; fins que existisca, eixes peticions i les que no tinguen un mòdul d'origen seleccionable requerixen revisió manual.
