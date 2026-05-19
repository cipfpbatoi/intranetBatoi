# Signatures FCT

## Controlador

El flux principal està en `app/Http/Controllers/SignaturaController.php`.

- `index()`: panell `/signatura`.
- `store()`: registra una nova signatura manual i ajusta el tipus real d'annex.
- `sendUnique($id)`: envia un document individual a l'instructor.
- `sendMultiple(Request $request, $tipus)`: envia seleccionats a alumnat (`A3`) o instructors (`All`).
- `upload($id, Request $request)`: puja el document signat i reinicia `sendTo`.
- `mailViewForInstructorSignatures($signatures)`: tria plantilla de correu.
- `isAnnexOne()` i `isAnnexFive()`: detecten Annex I i Annex V amb variants.

## Semàntica Observada

- `sendTo < 2`: pendent d'enviar a instructor.
- En enviar a instructor, es marca sumant o assignant estat de tramés.
- `signed` reflecteix el progrés de signatures i s'usa en finders/recursos.
- `EmailPostSendService::handleAnnexeIndividual()` actualitza signatures després de l'enviament real.

## Plantilles

- Si tots els documents seleccionats són Annex V, `mailViewForInstructorSignatures()` retorna `email.fct.a5`.
- Si hi ha altres annexos, retorna `email.fct.anexes`.
- `email.fct.a5` ha de dir que l'instructor ha de signar i tornar l'Annex V, no que és informatiu.
- Mantindre textos bilingües quan la plantilla ja inclou Valencià i Castellà.

## Precaucions

- No canviar `sendTo` o `signed` sense revisar `Finders/A1Finder.php`, `A2Finder.php`, `A3Finder.php`, `Finders/MailFinders/*`, `SignaturaStatusService`, i `EmailPostSendService`.
- Abans d'enviar a instructor, comprovar que existeixen `Fct`, `Instructor`, `email` i `nombre`.
- Els canvis de text de correu solen requerir només Blade, però els canvis de comportament requereixen test o prova manual.
