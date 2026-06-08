# Flux d'importació

Este document descriu l'arquitectura actual del bloc d'importació.

## Components

- `app/Application/Import/ImportService.php`
  - Valida el fitxer (`XML`) i gestiona el timeout de l'execució.
  - Exposa `isFirstImport()` per a decisions de flux.

- `app/Application/Import/ImportWorkflowService.php`
  - Orquestra el recorregut de les taules XML.
  - Dona dos pipelines:
    - `executeXmlImportWithHooks()` per a `pre/in/post` (import general).
    - `executeXmlImportSimple()` per a import de professorat.

- `app/Application/Import/ImportSchemaProvider.php`
  - Centralitza els mapatges XML -> BD.
  - `forGeneralImport()` i `forTeacherImport()`.

- `app/Application/Import/ImportXmlHelperService.php`
  - Parseig de camps (`extractField`), filtres i required.
  - Invoca funcions auxiliars del context (controlador) per compatibilitat amb mapatges existents.

- `app/Application/Import/GeneralImportExecutionService.php`
  - Conté la lògica d'execució gran de l'import general:
    - `pre` per classe (`Alumno`, `Profesor`, `Grupo`, `AlumnoGrupo`, `Horario`).
    - inserció/actualització de registres.
    - `post` (neteja de baixes, assignació departaments, restauració taules temporals, etc.).

- `app/Application/Import/TeacherImportExecutionService.php`
  - Conté la lògica d'execució de l'import de professorat:
    - neteja d'horaris del professor (`clearTeacherHorarios`).
    - inserció/actualització de `Profesor` i `Horario` filtrada per `idProfesor`.

## Responsabilitat dels controladors

- `app/Http/Controllers/ImportController.php`
  - Coordinador del cas d'ús.
  - No conté ja la lògica pesada de `pre/in/post`.
  - Manté només mètodes auxiliars de transformació necessaris pels mapatges (`getFechaFormatoIngles`, `hazDNI`, etc.).

- `app/Http/Controllers/TeacherImportController.php`
  - Coordinador del cas d'ús de professorat.
  - Delega la lògica de persistència i neteja al servei d'execució.

## Regla de codi recomanada

- Evitar lògica de persistència en controladors.
- El patró objectiu és:
  - controlador = entrada/sortida + coordinació
  - workflow/execution service = lògica de negoci d'importació
  - schema provider = configuració de mapatge
