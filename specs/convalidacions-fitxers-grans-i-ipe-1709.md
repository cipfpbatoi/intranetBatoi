# Fitxers massa grans i regles de mateix codi IPE I / IPE II

Domini: Convalidacions  
Punt d'entrada: formulari de sol·licitud de l'alumnat i motor de regles de Direcció.  
Docs/specs llegits: `docs/agents/fct/fct-map.md`, `docs/agents/conventions.md`, `specs/convalidacions.md`, `specs/convalidacions-resolucions-automatiques.md`, `specs/convalidacions-fol-logse-ipe.md`.  
Abast: detectar els fitxers massa grans abans d'incorporar-los a la sol·licitud i revisar les regles d'IPE I i IPE II quan el codi d'origen i el de destí coincidixen, inclòs el pas de GM a GS.

## Escenaris

**Escenari 1: avisar d'un fitxer massa gran abans d'afegir-lo**

Given l'alumne està preparant una sol·licitud i selecciona un fitxer que supera el límit configurat  
When intenta afegir el mòdul a la sol·licitud  
Then veu un avís clar al formulari d'afegir mòdul abans de tramitar la sol·licitud  
And el fitxer no s'incorpora a la sol·licitud  
And els altres mòduls i documents ja afegits continuen visibles i intactes.

**Escenari 2: conservar la validació del servidor per als fitxers**

Given l'alumne presenta una sol·licitud amb un fitxer que supera el límit configurat malgrat la validació del formulari  
When el servidor rep la sol·licitud  
Then rebutja el fitxer sense guardar cap petició incompleta  
And manté la validació de tipus i mida al servidor com a protecció d'integritat.

**Escenari 3: aplicar la regla de mateix codi en IPE I entre GM i GS**

Given una sol·licitud presentada demana convalidar IPE I del cicle de matrícula de grau superior  
And l'acreditació d'origen del mateix centre identifica IPE I amb el mateix codi 1709, superat en un cicle de grau mitjà  
When Direcció aplica les convalidacions automàtiques  
Then el motor troba i aplica la regla de mateix codi sense exigir una regla FOL LOGSE ni una coincidència de nivell d'origen i destí  
And registra el resultat, la qualificació conservada i la base normativa definida per la regla.

**Escenari 4: aplicar també la regla de mateix codi en IPE II**

Given una sol·licitud demana convalidar IPE II  
And l'acreditació d'origen del mateix centre identifica IPE II amb el mateix codi 1710, superat en un altre nivell de cicle formatiu  
When Direcció aplica les convalidacions automàtiques  
Then el motor aplica la regla de mateix codi per a IPE II  
And registra el resultat, la qualificació conservada i la base normativa definida per la regla.

**Escenari 5: no aplicar una regla de mateix codi quan els codis són diferents**

Given una sol·licitud demana convalidar IPE I o IPE II  
And l'acreditació d'origen no té, respectivament, el codi 1709 o 1710 del mòdul destí  
When Direcció aplica les convalidacions automàtiques  
Then la regla de mateix codi no s'aplica  
And la sol·licitud continua disponible per a revisió manual o per a una altra regla que sí que coincidisca.

## Regles de negoci

- La mida màxima es valida en seleccionar/incorporar el fitxer i també al servidor; la validació del navegador no substitueix la del servidor.
- Un fitxer rebutjat no ha de descartar ni ocultar la resta de la sol·licitud que l'alumne encara està preparant.
- En el cas de la captura, l'origen és IPE I 1709 de grau mitjà i el destí IPE I de grau superior; coincidix el codi 1709. La regla es basa en la coincidència exacta de codi, sense exigir que coincidisca el nivell.
- El Reial decret 659/2023 declara IPE I i IPE II comuns als cicles de grau mitjà i superior, amb codis 1709 i 1710, respectivament. Per tant, cal revisar i representar en YAML tant 1709 → 1709 com 1710 → 1710.
- La regla només s'aplica quan el codi d'origen coincideix exactament amb el codi de destí corresponent. No amplia ni altera les regles específiques de FOL LOGSE.
- El resultat és `AA` i es conserva la nota d'origen. La regla registra com a base normativa els articles 126.5 i 127.1.a del Reial decret 659/2023: convalidació d'IPE I/II entre ofertes, qualificació de l'expedient d'origen i resolució automàtica pel centre.

## Fitxers afectats

- `resources/views/intranet/convalidacions/alumno/create.blade.php` — avís de mida abans d'incorporar el document i preservació de la composició en curs.
- `app/Http/Controllers/AlumnoConvalidacioController.php` — mantindre la validació de servidor existent; només modificar-la si la implementació ho exigeix per a retornar errors sense perdre dades persistibles.
- `resources/convalidacions/regles-lfp.yaml` — regles de mateix codi per a IPE I (1709) i IPE II (1710), aplicables entre GM i GS.
- Tests de `tests/Feature/` per a la validació de mida i l'aplicació/no aplicació de la regla.
- `specs/convalidacions.md` i/o `specs/convalidacions-resolucions-automatiques.md` — incorporar els escenaris aprovats durant l'arxivat.

## Riscos

- El límit de pujada del servidor web/PHP pot ser inferior al límit de l'aplicació i rebutjar la petició abans que Laravel puga conservar cap estat; cal comprovar la configuració efectiva durant la implementació.
- La normativa permet establir formació complementària per a IPE I/II; qualsevol requisit o pràctica addicional establida per la Comunitat Valenciana queda fora d'esta regla i s'ha de revisar abans del desplegament.
- Les regles es poden haver modificat a l'entorn desplegat; després de la implementació caldrà importar/publicar el YAML actualitzat perquè el comportament siga efectiu allí.

Status: ready_for_review
