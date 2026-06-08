# Mapa Activitats

## Rutes Principals

- Professor: `routes/profesor.php`.
- Direcció: `routes/direccion.php`.
- API edició/recursos: `routes/api.php`.

## Fitxers Clau

- `app/Http/Controllers/ActividadController.php`.
- `app/Livewire/ActividadDireccionPanel.php` (panell direcció).
- `app/Entities/Actividad.php` i `app/Entities/ActividadProfesor.php`.
- `app/Presentation/Crud/ActividadCrudSchema.php`.
- `app/Http/Requests/ActividadRequest.php`.

## Camps Llegats (no canviar sense migració explícita)

- `complementaria`: separa complementària d'extraescolar al formulari d'activitat.
- `extraescolar`: flag llegat/mòdul que inclou activitats en el flux d'extraescolars; **no** és el mateix que la tria d'usuari complementària/extraescolar.
- `fueraCentro`: si l'activitat és fora del centre.
- `transport`: només té sentit quan `fueraCentro = 1`.

## Copy UI (Valencià, conceptes separats)

- Tipus: `Complementària` / `Extraescolar`.
- Ubicació: `Dins del centre` / `Fora del centre`.
- Transport: només aplica fora del centre.

## Participants i Coordinador

- El coordinador real és la fila d'`actividad_profesor` amb `coordinador = 1`.
- No usar el primer participant com a coordinador.
- Usar `Actividad::Creador()` o el pivot `coordinador` segons calga.

## PDFs i Vistes

- `resources/views/pdf/extraescolars.blade.php`.
- `resources/views/pdf/valoracionActividad.blade.php`.
- `resources/views/extraescolares/showValue.blade.php`.

## Notes de Domini

- Complementàries tenen RA (Resultats d'Aprenentatge); les extraescolars no.
- Si un camp és justificació de RA, mostrar-lo només per a complementàries.
- La combinació `fueraCentro = 0, transport = 1` és incoherent: evitar o normalitzar.
- Afegir tests acotats quan canvia el mapping d'estats, la selecció de coordinador, o l'eixida renderitzada.
