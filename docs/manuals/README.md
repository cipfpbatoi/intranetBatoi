# Manuals per al professorat

Este és el punt d'entrada de la documentació funcional de la intranet. Els
manuals expliquen què veu el professorat, per a què serveix cada pantalla i
quines accions pot realitzar segons els seus rols.

> La disponibilitat exacta d'una opció depén dels rols assignats a l'usuari i
> de la configuració activa de la taula `menus`.

## Per on començar

| Perfil o necessitat | Manual |
|---|---|
| Professorat | [Manual general del professorat](manual-profe.md) |
| Tutoria de grup | [Manual de tutoria](manual-tutor.md) |
| Cap de departament | [Manual del cap de departament](manual-cap.md) |
| Direcció | [Manual de direcció](manual-dir.md) |
| Tutoria de pràctiques | [Manual d'FCT](manual-fct.md) |
| Empreses i centres de treball | [Empreses d'FCT](manual-fct-empreses.md) |
| Contactes d'FCT | [Gestió de contactes](manual-fct-gestio-contactes.md) |
| Col·laboracions assignades | [Les meues col·laboracions](manual-fct-meues-colaboracions.md) |
| Panell d'FCT | [Panell d'FCT](manual-fct-panel.md) |
| Avaluació d'FCT | [Avaluació d'FCT](manual-fct-avaluacio.md) |
| Pantalla de l'alumnat en FCT | [FCT de l'alumnat](manual-fct-alumno.md) |

## Com està organitzada la documentació

El [manual general del professorat](manual-profe.md) descriu els elements
comuns —entrada, inici, notificacions, perfil i menús generals— i les tasques
que pot fer qualsevol docent. Els manuals de perfil només documenten les
opcions addicionals que apareixen quan l'usuari és tutor, cap de departament,
membre de direcció o tutor de pràctiques.

Per a evitar contradiccions, una mateixa pantalla s'explica en un sol manual.
Els altres manuals han d'enllaçar l'explicació principal.

## Criteri per a les captures

Cada pantalla documentada ha d'incloure una captura actual quan la imatge
aporte informació que no quede clara amb el text. Les captures han de:

- mostrar el menú o el camí necessari per arribar a la pantalla;
- utilitzar dades de prova representatives;
- ocultar DNI, telèfons, correus, signatures i qualsevol altra dada personal;
- conservar només la zona de pantalla necessària per entendre l'acció;
- guardar-se en `docs/img/ajuda/` o en una subcarpeta específica del domini;
- tindre un nom estable i descriptiu, per exemple
  `activitats-01-llistat.png`.

Quan una operació té diversos estats, cal capturar almenys l'estat inicial i
el resultat final. No s'han d'usar captures de producció sense anonimitzar.

## Plantilla d'una pantalla

Cada apartat nou o revisat ha de respondre, en este ordre, a les preguntes
següents:

1. **Per a què serveix?** Objectiu de la pantalla.
2. **Com s'hi arriba?** Menú i opció que cal seleccionar.
3. **Què mostra?** Camps, filtres, estats i indicadors importants.
4. **Què puc fer?** Accions disponibles i condicions per executar-les.
5. **Què passa després?** Resultat, notificacions o canvi d'estat.

La relació tècnica entre pantalles, rutes i controladors es manté en el
[mapa de pantalles del professorat](../architecture/mapa-pantalles-professorat.md).

## Regenerar les captures actuals

Les captures de `docs/img/ajuda/actual/` es poden regenerar amb Selenium i una
base de dades local que continga dades representatives i anonimitzades:

```bash
DOCS_BASE_URL=http://laravel.test \
DOCS_SELENIUM_URL=http://127.0.0.1:4444 \
DOCS_LOGIN=usuari-de-prova \
DOCS_PASSWORD=contrasenya-de-prova \
php tools/docs/capture-professorat.php
```

Per regenerar només algunes pantalles, es pot afegir una llista amb els noms
de `$pages` de l'script:

```bash
DOCS_PAGES=tutories,panell-fct,direccio-activitats \
DOCS_BASE_URL=http://laravel.test \
DOCS_SELENIUM_URL=http://127.0.0.1:4444 \
DOCS_LOGIN=usuari-de-prova \
DOCS_PASSWORD=contrasenya-de-prova \
php tools/docs/capture-professorat.php
```

L'script oculta fotografies i substituïx correus, telèfons i identificadors
personals que encara siguen visibles. Esta protecció és complementària: abans
de publicar, cal revisar visualment totes les imatges i no s'han d'utilitzar
mai credencials ni dades de producció.
