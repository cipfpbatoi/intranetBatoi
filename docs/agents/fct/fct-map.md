# Mapa FCT

## Rutes Principals

- `routes/profesor.php`
  - `/documentacionFCT/{id}/{documento}`: documents FCT.
  - `/documentacionFCT/{documento}`: correus/documentació FCT.
  - `/signatura`: panell de signatures del professorat.
  - `/signatura/{id}/send`: enviament individual a instructor.
  - `/signatura/{tipus}/send`: enviament múltiple a alumnat o instructors.
  - `/signatura/a5`: generació/gestió Annex V.
- `routes/todos.php`
  - `/signatura/{id}/upload`: pujada de document signat.
  - `/signatura/{id}/pdf`: PDF d'una signatura.
- `routes/api.php`
  - `/documentacionFCT/{documento}` i `/signatura*`.
- `routes/direccion.php`
  - `/signatures`: signatures des de direcció.

## Controladors I Serveis

- `app/Http/Controllers/FctAlumnoController.php`: accions sobre alumnat FCT.
- `app/Http/Controllers/FctController.php`: FCT, instructors i col·laboradors.
- `app/Http/Controllers/SignaturaController.php`: flux de signatures i enviaments.
- `app/Http/Controllers/PanelFctAvalController.php`: avaluació FCT, actes, projecte, estadístiques.
- `app/Services/Document/*`: generació/resposta de documents.
- `app/Sao/*`: descàrrega i processament d'annexos des de SAO.

## Vistes I Correus

- `resources/views/email/fct/*.blade.php`: correus FCT.
- `resources/views/email/fct/anexes.blade.php`: instruccions generals d'annexos a l'instructor.
- `resources/views/email/fct/a5.blade.php`: correu quan només s'envia Annex V.
- `resources/views/email/signaturaA3.blade.php`: correu a alumnat per Annex III.
- `resources/views/pdf/fct/*.blade.php`: PDFs FCT.

## Models Habituals

- `AlumnoFct`, `Fct`, `Signatura`, `Instructor`, `Centro`, `Empresa`, `Colaboracion`.
- Revisar relacions abans d'usar cadenes com `$signatura->Fct->Fct->Instructor`.
