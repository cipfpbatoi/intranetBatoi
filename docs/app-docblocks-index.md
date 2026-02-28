# Index Doc-Blocks de l aplicacio

Fitxer generat automaticament des dels doc-blocks de `app/`.

## Controladors

### `app/Http/Controllers/API/ActividadController.php`

#### `Intranet\Http\Controllers\API\ActividadController`
- Metodes:
  - `getFiles`


### `app/Http/Controllers/API/ActivityController.php`

#### `Intranet\Http\Controllers\API\ActivityController`
- Metodes:
  - `move`


### `app/Http/Controllers/API/AlumnoController.php`

#### `Intranet\Http\Controllers\API\AlumnoController`
- Metodes:
  - `putImage`
  - `putDades`


### `app/Http/Controllers/API/AlumnoFctController.php`

#### `Intranet\Http\Controllers\API\AlumnoFctController`
- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\API\grupoService`
- Metodes:
  - `alumnoFcts`

#### `Intranet\Http\Controllers\API\alumnoFctService`
- Metodes:
  - `indice`
  - `dual`
  - `update`
  - `show`


### `app/Http/Controllers/API/AlumnoGrupoController.php`

#### `Intranet\Http\Controllers\API\AlumnoGrupoController`
- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\API\grupoService`
- Metodes:
  - `moduloGrupos`

#### `Intranet\Http\Controllers\API\moduloGrupoService`
- Metodes:
  - `alumnos`
  - `show`
  - `getModulo`


### `app/Http/Controllers/API/AlumnoResultadoContoller.php`

#### `Intranet\Http\Controllers\API\AlumnoResultadoContoller`
- Metodes: cap


### `app/Http/Controllers/API/AlumnoReunionController.php`

#### `Intranet\Http\Controllers\API\AlumnoReunionController`
- Metodes:
  - `getDades`
  - `getDadesMatricula`
  - `generaToken`
  - `sendMatricula`
  - `getTestMatricula`


### `app/Http/Controllers/API/ApiBaseController.php`

#### `Intranet\Http\Controllers\API\ApiBaseController`
- Metodes:
  - `ApiUser`
    Resol usuari API en mode coexistència (`sanctum`/`api` + token legacy).
  - `show`
  - `fields`
  - `sendFail`
  - `isLegacyFilterExpression`
  - `queryLegacy`
  - `applyLegacyCondition`


### `app/Http/Controllers/API/ApiResourceController.php`

#### `Intranet\Http\Controllers\API\ApiResourceController`
- Metodes:
  - `__construct`
  - `index`
  - `destroy`
  - `store`
  - `update`
  - `show`
  - `edit`
  - `resolveClass`

#### `Intranet\Http\Controllers\API\ltrim`
- Metodes:
  - `hasResource`
  - `validatedPayloadForStore`
    /
  - `validatedPayloadForUpdate`
    /
  - `storeRules`
    Sobrescriu en controladors concrets quan necessites validació en create.
  - `updateRules`
    Sobrescriu en controladors concrets quan necessites validació en update.
  - `mutableFields`
    Permet limitar camps mutables per endpoint sense tocar el model.
  - `filterMutationPayload`
    /
  - `sendResponse`
  - `sendError`
  - `sendNotFound`
  - `sendFail`
  - `ApiUser`
  - `markLegacyUsage`
    Marca resposta d'endpoint legacy per facilitar deprecació controlada.


### `app/Http/Controllers/API/ArticuloController.php`

#### `Intranet\Http\Controllers\API\ArticuloController`
- Metodes:
  - `index`


### `app/Http/Controllers/API/ArticuloLoteController.php`

#### `Intranet\Http\Controllers\API\ArticuloLoteController`
- Metodes:
  - `store`
  - `getMateriales`


### `app/Http/Controllers/API/AsistenciaController.php`

#### `Intranet\Http\Controllers\API\AsistenciaController`
- Metodes:
  - `cambiar`


### `app/Http/Controllers/API/AuthTokenController.php`

#### `Intranet\Http\Controllers\API\AuthTokenController`
Gestió de tokens d'accés API en fase de coexistència legacy + Sanctum.

- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - `exchange`
  - `me`
  - `logout`


### `app/Http/Controllers/API/CentroController.php`

#### `Intranet\Http\Controllers\API\CentroController`
- Metodes:
  - `fusionar`
  - `fusion`
    /
  - `fusionCenter`
    /
  - `fusionColaboration`
    /


### `app/Http/Controllers/API/CicloController.php`

#### `Intranet\Http\Controllers\API\CicloController`
- Metodes: cap


### `app/Http/Controllers/API/ColaboracionController.php`

#### `Intranet\Http\Controllers\API\ColaboracionController`
- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - `instructores`
  - `resolve`
  - `refuse`
  - `unauthorize`
  - `telefon`
  - `alumnat`
  - `book`
  - `changeState`
  - `upsertDailyActivity`


### `app/Http/Controllers/API/ComisionController.php`

#### `Intranet\Http\Controllers\API\ComisionController`
- Metodes:
  - `__construct`
  - `autorizar`
  - `prePay`


### `app/Http/Controllers/API/CotxeController.php`

#### `Intranet\Http\Controllers\API\Direccio`
- Metodes: cap

#### `Intranet\Http\Controllers\API\CotxeController`
- Metodes:
  - `__construct`
  - `eventEntrada`
  - `obrirAutomatica`
  - `eventSortida`
  - `obrirTest`
    Obertura manual per proves: no necessita matrícula.
  - `handleEvent`
  - `normalizePayload`
    Accepta payloads heterogenis (Milesight, etc.)


### `app/Http/Controllers/API/CursoController.php`

#### `Intranet\Http\Controllers\API\CursoController`
- Metodes: cap


### `app/Http/Controllers/API/DepartamentoController.php`

#### `Intranet\Http\Controllers\API\DepartamentoController`
- Metodes:
  - `index`


### `app/Http/Controllers/API/DocumentacionFCTController.php`

#### `Intranet\Http\Controllers\API\DocumentacionFCTController`
- Metodes:
  - `exec`
  - `signatura`
  - `signaturaA1`
  - `signaturaDirector`


### `app/Http/Controllers/API/DocumentoController.php`

#### `Intranet\Http\Controllers\API\DocumentoController`
- Metodes: cap


### `app/Http/Controllers/API/DropZoneController.php`

#### `Intranet\Http\Controllers\API\DropZoneController`
- Metodes:
  - `getAttached`
  - `getNameAttached`
  - `removeAttached`
  - `attachFile`


### `app/Http/Controllers/API/DualController.php`

#### `Intranet\Http\Controllers\API\DualController`
Només es manté per lectura/compatibilitat temporal.

- Metodes:
  - `store`


### `app/Http/Controllers/API/EmpresaController.php`

#### `Intranet\Http\Controllers\API\EmpresaController`
- Metodes:
  - `__construct`
  - `empreses`

#### `Intranet\Http\Controllers\API\empresaService`
- Metodes:
  - `indexConvenio`


### `app/Http/Controllers/API/EspacioController.php`

#### `Intranet\Http\Controllers\API\EspacioController`
- Metodes: cap


### `app/Http/Controllers/API/ExpedienteController.php`

#### `Intranet\Http\Controllers\API\ExpedienteController`
- Metodes: cap


### `app/Http/Controllers/API/FaltaController.php`

#### `Intranet\Http\Controllers\API\FaltaController`
- Metodes: cap


### `app/Http/Controllers/API/FaltaItacaController.php`

#### `Intranet\Http\Controllers\API\FaltaItacaController`
- Metodes:
  - `horarios`

#### `Intranet\Http\Controllers\API\horarioService`
- Metodes:
  - `fitxatge`

#### `Intranet\Http\Controllers\API\fitxatgeService`
- Metodes:
  - `potencial`
  - `guarda`

#### `Intranet\Http\Controllers\API\send`
- Metodes: cap


### `app/Http/Controllers/API/FaltaProfesorController.php`

#### `Intranet\Http\Controllers\API\FaltaProfesorController`
- Metodes:
  - `index`
  - `show`
  - `horas`
  - `queryByLegacyConditions`
  - `queryByRequestFilters`
  - `extractQueryFilters`
  - `isLegacyFilterExpression`
  - `applyLegacyCondition`


### `app/Http/Controllers/API/FctController.php`

#### `Intranet\Http\Controllers\API\FctController`
- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - `llist`
  - `seguimiento`


### `app/Http/Controllers/API/FicharController.php`

#### `Intranet\Http\Controllers\API\FicharController`
Endpoints API de fitxatge amb compatibilitat legacy i auth per header.

- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - `fichar`
    Registra entrada/eixida de fitxatge.
  - `entrefechas`

#### `Intranet\Http\Controllers\API\registrosEntreFechas`
- Metodes: cap


### `app/Http/Controllers/API/GrupoController.php`

#### `Intranet\Http\Controllers\API\GrupoController`
- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\API\grupoService`
- Metodes: cap


### `app/Http/Controllers/API/GrupoTrabajoController.php`

#### `Intranet\Http\Controllers\API\GrupoTrabajoController`
- Metodes: cap


### `app/Http/Controllers/API/GuardiaController.php`

#### `Intranet\Http\Controllers\API\GuardiaController`
- Metodes:
  - `show`
  - `range`
  - `getServerTime`
  - `queryByDiaRange`
  - `isLegacyFilterExpression`
  - `queryLegacy`
  - `applyLegacyCondition`


### `app/Http/Controllers/API/HoraController.php`

#### `Intranet\Http\Controllers\API\HoraController`
- Metodes: cap


### `app/Http/Controllers/API/HorarioController.php`

#### `Intranet\Http\Controllers\API\HorarioController`
- Metodes:
  - `horarios`

#### `Intranet\Http\Controllers\API\horarioService`
- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - `show`
  - `index`
  - `guardia`
  - `HorariosDia`
  - `getChange`
  - `Change`
  - `isLegacyFilterExpression`
  - `queryLegacy`
  - `extractQueryFilters`
  - `queryByRequestFilters`
  - `applyLegacyCondition`


### `app/Http/Controllers/API/IPController.php`

#### `Intranet\Http\Controllers\API\IPController`
- Metodes:
  - `miIP`


### `app/Http/Controllers/API/IncidenciaController.php`

#### `Intranet\Http\Controllers\API\IncidenciaController`
- Metodes: cap


### `app/Http/Controllers/API/InstructorController.php`

#### `Intranet\Http\Controllers\API\InstructorController`
- Metodes: cap


### `app/Http/Controllers/API/IpGuardiaController.php`

#### `Intranet\Http\Controllers\API\IpGuardiaController`
- Metodes:
  - `arrayIps`


### `app/Http/Controllers/API/LoteController.php`

#### `Intranet\Http\Controllers\API\LoteController`
- Metodes:
  - `destroy`
  - `index`
  - `getArticulos`
  - `putArticulos`


### `app/Http/Controllers/API/MaterialBajaController.php`

#### `Intranet\Http\Controllers\API\MaterialBajaController`
- Metodes:
  - `show`


### `app/Http/Controllers/API/MaterialController.php`

#### `Intranet\Http\Controllers\API\MaterialController`
- Metodes:
  - `getMaterial`
  - `getInventario`
  - `espai`
  - `inventario`
  - `index`
  - `put`
  - `putUnidades`
  - `putUbicacion`
  - `putEstado`
  - `resolveApiUser`
  - `putInventario`


### `app/Http/Controllers/API/ModuloController.php`

#### `Intranet\Http\Controllers\API\ModuloController`
- Metodes: cap


### `app/Http/Controllers/API/Modulo_cicloController.php`

#### `Intranet\Http\Controllers\API\Modulo_cicloController`
- Metodes: cap


### `app/Http/Controllers/API/NotificationController.php`

#### `Intranet\Http\Controllers\API\NotificationController`
- Metodes:
  - `leer`

#### `Intranet\Http\Controllers\API\markAsRead`
- Metodes: cap


### `app/Http/Controllers/API/OrdenReunionController.php`

#### `Intranet\Http\Controllers\API\OrdenReunionController`
- Metodes: cap


### `app/Http/Controllers/API/OrdenTrabajoController.php`

#### `Intranet\Http\Controllers\API\OrdenTrabajoController`
- Metodes: cap


### `app/Http/Controllers/API/PPollController.php`

#### `Intranet\Http\Controllers\API\PPollController`
- Metodes: cap


### `app/Http/Controllers/API/PresenciaResumenController.php`

#### `Intranet\Http\Controllers\API\PresenciaResumenController`
- Metodes:
  - `rango`


### `app/Http/Controllers/API/ProfesorController.php`

#### `Intranet\Http\Controllers\API\ProfesorController`
- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - `rol`
  - `getRol`


### `app/Http/Controllers/API/ProgramacionController.php`

#### `Intranet\Http\Controllers\API\ProgramacionController`
- Metodes: cap


### `app/Http/Controllers/API/ProjecteController.php`

#### `Intranet\Http\Controllers\API\ProjecteController`
- Metodes: cap


### `app/Http/Controllers/API/ReservaController.php`

#### `Intranet\Http\Controllers\API\ReservaController`
- Metodes:
  - `index`
  - `profesores`

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - `show`
  - `unsecure`
  - `getJson`
  - `action`
  - `checkSecuredStatus`
  - `isLegacyFilterExpression`
  - `queryLegacy`
  - `extractQueryFilters`
  - `queryByRequestFilters`
  - `applyLegacyCondition`


### `app/Http/Controllers/API/ResultadoController.php`

#### `Intranet\Http\Controllers\API\ResultadoController`
- Metodes: cap


### `app/Http/Controllers/API/ReunionController.php`

#### `Intranet\Http\Controllers\API\ReunionController`
- Metodes:
  - `putAlumno`


### `app/Http/Controllers/API/SettingController.php`

#### `Intranet\Http\Controllers\API\SettingController`
- Metodes: cap


### `app/Http/Controllers/API/SignaturaController.php`

#### `Intranet\Http\Controllers\API\SignaturaController`
- Metodes: cap


### `app/Http/Controllers/API/SolicitudController.php`

#### `Intranet\Http\Controllers\API\SolicitudController`
- Metodes: cap


### `app/Http/Controllers/API/TaskController.php`

#### `Intranet\Http\Controllers\API\TaskController`
- Metodes: cap


### `app/Http/Controllers/API/TipoActividadController.php`

#### `Intranet\Http\Controllers\API\TipoActividadController`
- Metodes: cap


### `app/Http/Controllers/API/TipoExpedienteController.php`

#### `Intranet\Http\Controllers\API\TipoExpedienteController`
- Metodes: cap


### `app/Http/Controllers/API/TipoIncidenciaController.php`

#### `Intranet\Http\Controllers\API\TipoIncidenciaController`
- Metodes: cap


### `app/Http/Controllers/API/TipoReunionController.php`

#### `Intranet\Http\Controllers\API\TipoReunionController`
- Metodes:
  - `show`


### `app/Http/Controllers/API/TutoriaGrupoController.php`

#### `Intranet\Http\Controllers\API\TutoriaGrupoController`
- Metodes: cap


### `app/Http/Controllers/ActividadController.php`

#### `Intranet\Http\Controllers\ActividadController`
Controlador d'activitats extraescolars i complementàries.

- Metodes:
  - `search`
  - `createWithDefaultValues`
  - `store`
    Guarda una activitat i aplica els participants per defecte.

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `valoracion`
  - `showValue`
    Mostra la pantalla de valoració d'una activitat.
  - `value`
    Mostra el formulari per omplir la valoració.
  - `printValue`
    Genera el PDF de la valoració d'una activitat.

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - `autorize`
    Mostra la pantalla de control d'autoritzacions de menors.
  - `iniBotones`
    Inicialitza la botonera del grid i perfil.
  - `autorizar`
    Autoritza activitats en estat 1 i, si hi ha credencials, les exporta a calendari.
  - `accept`
    Accepta l'activitat incrementant estat i sincronitzant calendari extern.
  - `printAutoritzats`
    Imprimeix el llistat d'autoritzats.
  - `itaca`
    Marca l'activitat com a tramitada en ITACA.
  - `menorAuth`
    Alterna l'estat d'autorització d'un alumne menor.
  - `gestor`
    Renderitza el document associat a l'activitat amb GestorService.

#### `Intranet\Http\Controllers\grupos`
- Metodes:
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `detalle`
    Mostra el detall d'una activitat amb professors i grups associats.

#### `Intranet\Http\Controllers\activosOrdered`
- Metodes:
  - `altaGrupo`
    Afig un grup a una activitat sense esborrar els existents.
  - `borrarGrupo`
    Esborra un grup assignat a l'activitat.
  - `altaProfesor`
    Afig un professor participant a l'activitat.
  - `borrarProfesor`
    Esborra un professor participant.
  - `coordinador`
    Assigna el coordinador de l'activitat.
  - `notify`
    Notifica a professorat afectat i tutors dels grups de l'activitat.

#### `Intranet\Http\Controllers\find`
- Metodes: cap

#### `Intranet\Http\Controllers\notifyActivity`
- Metodes:
  - `autorizacion`
    Genera/mostra l'autorització de menors i crea registres si encara no existixen.


### `app/Http/Controllers/ActualizacionController.php`

#### `Intranet\Http\Controllers\ActualizacionController`
Class ActualizacionController

- Metodes:
  - `actualizacion`
    /
  - `runShell`
  - `gitEnv`
  - `markRepoAsSafe`


### `app/Http/Controllers/AdministracionController.php`

#### `Intranet\Http\Controllers\AdministracionController`
Class AdministracionController

- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `alumnoFcts`

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - `lang`
    /
  - `allApiToken`
    /

#### `Intranet\Http\Controllers\activos`
- Metodes:
  - `cleanCache`
  - `nuevoCursoIndex`
    /
  - `esborrarProgramacions`
  - `esborrarEnquestes`
  - `ferVotsPermanents`
  - `nuevoCurso`
    /

#### `Intranet\Http\Controllers\clearFechaBaja`
- Metodes:
  - `help`
    /
  - `exe_actualizacion`
    /
  - `v3_00`
  - `v3_01`

#### `Intranet\Http\Controllers\all`
- Metodes:
  - `consulta`
  - `v2_01`
  - `importaAnexoI`
  - `centres_amb_mateixa_adreça`
  - `showDoor`
  - `secure`


### `app/Http/Controllers/AlumnoController.php`

#### `Intranet\Http\Controllers\AlumnoController`
Class AlumnoController

- Metodes:
  - `update`
    /
  - `carnet`
    /

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - `checkFol`
  - `equipo`
    /

#### `Intranet\Http\Controllers\byGrupo`
- Metodes:
  - `iniBotones`
    /
  - `alerta`
    /


### `app/Http/Controllers/AlumnoCursoController.php`

#### `Intranet\Http\Controllers\AlumnoCursoController`
- Metodes:
  - `search`
  - `active`
  - `destroy`
  - `iniBotones`
  - `pdf`

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - `registerGrup`
  - `registerAlumn`
  - `register`
  - `getRegister`
  - `unregister`


### `app/Http/Controllers/AlumnoGrupoController.php`

#### `Intranet\Http\Controllers\AlumnoGrupoController`
- Metodes:
  - `indice`
    Punt d'entrada legacy per a rutes que passen el grup en URL.
  - `search`
  - `redirect`
  - `updateModal`
  - `realStore`
  - `update`
  - `iniBotones`


### `app/Http/Controllers/ArticuloController.php`

#### `Intranet\Http\Controllers\ArticuloController`
Class MaterialController

- Metodes:
  - `iniBotones`
  - `detalle`
    /
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina un article amb autorització explícita.
  - `borrarFichero`


### `app/Http/Controllers/ArticuloLoteController.php`

#### `Intranet\Http\Controllers\ArticuloLoteController`
Class MaterialController

- Metodes:
  - `search`
  - `iniBotones`


### `app/Http/Controllers/Auth/Alumno/HomeController.php`

#### `Intranet\Http\Controllers\Auth\Alumno\HomeController`
Description of HomeIdentifyController

- Metodes: cap


### `app/Http/Controllers/Auth/Alumno/LoginController.php`

#### `Intranet\Http\Controllers\Auth\Alumno\LoginController`
- Metodes:
  - `username`
  - `credentials`
  - `guard`
  - `showLoginForm`
  - `logout`
  - `plogin`


### `app/Http/Controllers/Auth/Alumno/PerfilController.php`

#### `Intranet\Http\Controllers\Auth\Alumno\PerfilController`
- Metodes:
  - `editar`
  - `update`


### `app/Http/Controllers/Auth/ExternLoginController.php`

#### `Intranet\Http\Controllers\Auth\ExternLoginController`
- Metodes:
  - `username`
  - `authenticated`

#### `Intranet\Http\Controllers\Auth\apiSessionTokenService`
- Metodes:
  - `showExternLoginForm`

#### `Intranet\Http\Controllers\Auth\profesorService`
- Metodes: cap


### `app/Http/Controllers/Auth/ForgotPasswordController.php`

#### `Intranet\Http\Controllers\Auth\ForgotPasswordController`
- Metodes:
  - `__construct`
    Create a new controller instance.


### `app/Http/Controllers/Auth/HomeController.php`

#### `Intranet\Http\Controllers\Auth\HomeController`
- Metodes:
  - `__construct`
  - `index`
  - `legal`


### `app/Http/Controllers/Auth/LoginController.php`

#### `Intranet\Http\Controllers\Auth\LoginController`
- Metodes:
  - `login`


### `app/Http/Controllers/Auth/PerfilController.php`

#### `Intranet\Http\Controllers\Auth\PerfilController`
- Metodes:
  - `update`


### `app/Http/Controllers/Auth/Profesor/HomeController.php`

#### `Intranet\Http\Controllers\Auth\Profesor\HomeController`
Description of HomeIdentifyController

- Metodes: cap


### `app/Http/Controllers/Auth/Profesor/LoginController.php`

#### `Intranet\Http\Controllers\Auth\Profesor\LoginController`
- Metodes:
  - `username`
  - `profesores`

#### `Intranet\Http\Controllers\Auth\Profesor\profesorService`
- Metodes:
  - `apiSessionTokens`

#### `Intranet\Http\Controllers\Auth\Profesor\apiSessionTokenService`
- Metodes:
  - `authenticated`
    Hook del trait AuthenticatesUsers després de login satisfactori.
  - `credentials`
  - `guard`
  - `showLoginForm`
  - `logout`
  - `plogin`
  - `firstLogin`


### `app/Http/Controllers/Auth/Profesor/PerfilController.php`

#### `Intranet\Http\Controllers\Auth\Profesor\PerfilController`
- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\Auth\Profesor\profesorService`
- Metodes:
  - `editar`
  - `files`
  - `updateFiles`
  - `updatePhoto`
    Processa la pujada de foto de perfil i garanteix persistència en BBDD.
  - `cleanupAndRelinkProfileAssets`
    Elimina/relaciona fitxers antics lligats a la foto anterior.
  - `moveProfileAsset`
    Mou un fitxer d'asset de perfil si existeix amb el nom antic.
  - `updateSignature`
  - `updatePeu`
  - `deleteCertificate`
  - `updateDigitalCertificate`
  - `update`


### `app/Http/Controllers/Auth/Profesor/ResetProfesorController.php`

#### `Intranet\Http\Controllers\Auth\Profesor\ResetProfesorController`
- Metodes:
  - `__construct`


### `app/Http/Controllers/Auth/RegisterController.php`

#### `Intranet\Http\Controllers\Auth\RegisterController`
- Metodes:
  - `__construct`
    Create a new controller instance.
  - `validator`
    Get a validator for an incoming registration request.
  - `create`
    Create a new user instance after a valid registration.


### `app/Http/Controllers/Auth/ResetPasswordController.php`

#### `Intranet\Http\Controllers\Auth\ResetPasswordController`
- Metodes:
  - `__construct`
    Create a new controller instance.
  - `resetPassword`


### `app/Http/Controllers/Auth/Social/SocialController.php`

#### `Intranet\Http\Controllers\Auth\Social\SocialController`
- Metodes:
  - `__construct`
  - `profesores`

#### `Intranet\Http\Controllers\Auth\Social\profesorService`
- Metodes:
  - `apiSessionTokens`

#### `Intranet\Http\Controllers\Auth\Social\apiSessionTokenService`
- Metodes:
  - `getSocialAuth`
  - `checkTokenAndRedirect`
  - `successloginProfesor`
  - `getSocialAuthCallback`


### `app/Http/Controllers/CalendariFctController.php`

#### `Intranet\Http\Controllers\CalendariFctController`
- Metodes:
  - `search`
  - `iniBotones`
  - `days`


### `app/Http/Controllers/CentroController.php`

#### `Intranet\Http\Controllers\CentroController`
Class CentroController

- Metodes:
  - `update`
    /
  - `showEmpresa`
  - `store`
    /
  - `destroy`
    /
  - `empresaCreateCentro`


### `app/Http/Controllers/CicloController.php`

#### `Intranet\Http\Controllers\CicloController`
Class CicloController

- Metodes:
  - `iniBotones`
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina un cicle amb autorització explícita.


### `app/Http/Controllers/CicloDualController.php`

#### `Intranet\Http\Controllers\CicloDualController`
Class CicloController

- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `edit`
  - `update`


### `app/Http/Controllers/ColaboracionAlumnoController.php`

#### `Intranet\Http\Controllers\ColaboracionAlumnoController`
Class PanelColaboracionController

- Metodes:
  - `index`
    /
  - `search`
    /


### `app/Http/Controllers/ColaboracionController.php`

#### `Intranet\Http\Controllers\ColaboracionController`
Class ColaboracionController

- Metodes:
  - `iniBotones`
    /
  - `search`
    /
  - `update`
    Actualitza una col·laboració des del formulari específic de panell.
  - `show`
    /
  - `printAnexeIV`

#### `Intranet\Http\Controllers\fillAndSave`
- Metodes:
  - `makeArrayPdfAnexoIV`
  - `makeArrayPdfConveni`
  - `deleteDir`


### `app/Http/Controllers/ComisionController.php`

#### `Intranet\Http\Controllers\ComisionController`
Class ComisionController

- Metodes:
  - `__construct`
  - `comisionService`

#### `Intranet\Http\Controllers\comisionService`
- Metodes:
  - `store`

#### `Intranet\Http\Controllers\merge`
- Metodes:
  - `update`
  - `confirm`
  - `iniBotones`
    /
  - `createWithDefaultValues`
  - `enviarCorreos`
  - `sendEmail`
  - `init`
  - `payment`
    /
  - `printAutoritzats`
  - `paid`
    /
  - `unpaid`
    /
  - `autorizar`
    /

#### `Intranet\Http\Controllers\StateService`
- Metodes:
  - `detalle`
  - `createFct`
  - `deleteFct`
  - `setEstado`
  - `buildFctOptions`
    Retorna opcions de FCT per al selector de detall:


### `app/Http/Controllers/Controller.php`

#### `Intranet\Http\Controllers\Controller`
- Metodes:
  - `__construct`


### `app/Http/Controllers/Core/BaseController.php`

#### `Intranet\Http\Controllers\Core\BaseController`
- Metodes:
  - `__construct`
  - `chooseView`
    Resol la vista a utilitzar per a una acció.
  - `grid`
    Renderitza el grid amb o sense formulari modal.
  - `parametres`
    Extensió per a paràmetres addicionals en classes filles.
  - `index`
    Acció index estàndard.
  - `confirm`
    Mostra modal de confirmació per a un registre.
  - `indice`
    Variante d'index amb filtre extern.
  - `search`
    Cerca per defecte del controlador base.
  - `llist`
    Renderitza una vista de llistat sobre un panell concret.
  - `iniBotones`
    Punt d'extensió per inicialitzar botons en classes filles.
  - `iniPestanas`
    Inicialitza pestanyes per defecte.
  - `resolveModelClass`
    Resol la classe de model actual del controlador.

#### `Intranet\Http\Controllers\Core\ltrim`
- Metodes:
  - `hasModelColumn`
    Comprova si un model té una columna, amb cache en memòria.


### `app/Http/Controllers/Core/IntranetController.php`

#### `Intranet\Http\Controllers\Core\IntranetController`
- Metodes:
  - `redirect`
    Calcula la redirecció de retorn després de store/update/destroy.
  - `destroy`
    Elimina un registre i fitxers associats si escau.
  - `borrarFichero`
    Esborra un fitxer del `public/` o `storage/app/` si la ruta és segura.
  - `store`
    Guarda un nou registre.
  - `realStore`
    Crea o actualitza un registre i retorna la clau primària resultant.
  - `persist`
    Alias semàntic de persistència per compatibilitat amb flux modal.
  - `update`
    Actualitza un registre existent.
  - `active`
    Alterna l'estat `activo` d'un registre.
  - `document`
    Retorna el document físic associat al registre.
  - `gestor`
    Redirigeix al gestor documental del registre si està enllaçat.
  - `validateAll`
    Valida el request segons les regles del model.
  - `manageCheckBox`
    Normalitza camps checkbox en el request abans de validar/guardar.


### `app/Http/Controllers/Core/ModalController.php`

#### `Intranet\Http\Controllers\Core\ModalController`
- Metodes:
  - `__construct`
  - `index`
  - `grid`
    Renderitza la vista modal amb grid i formulari d'alta.
  - `resolveIndexView`
    Resol la vista d'index del modal.
  - `search`
    Cerca per defecte del modal:
  - `hasModelColumn`
    Comprova si la taula del model té una columna.
  - `resolveModelClass`
    Resol la classe de model del controlador modal.
  - `create`
    Per a recursos amb vista modal, la ruta create redirigeix a l'índex
  - `edit`
  - `persist`
    Persistix un model del controlador modal.
  - `createWithDefaultValues`
    Crea una instància buida del model per al formulari modal.
  - `destroy`
  - `confirm`
    Retorna la vista de confirmació per al model.
  - `iniBotones`
  - `iniPestanas`
    Inicialitza pestanyes per defecte.
  - `redirect`
    Resol redirecció de retorn en fluxos modal.


### `app/Http/Controllers/CotxeController.php`

#### `Intranet\Http\Controllers\CotxeController`
- Metodes:
  - `store`

#### `Intranet\Http\Controllers\merge`
- Metodes:
  - `update`
  - `destroy`
    Elimina un cotxe amb autorització explícita.
  - `iniBotones`


### `app/Http/Controllers/CursoController.php`

#### `Intranet\Http\Controllers\CursoController`
Class CursoController

- Metodes:
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `detalle`
    /
  - `indexAlumno`
    /
  - `iniAluBotones`
    /
  - `iniBotones`
    /
  - `saveFile`
    /
  - `makeReport`
    /
  - `document`
  - `pdf`
    /
  - `email`
  - `active`
  - `destroy`
    Elimina un curs amb autorització explícita.


### `app/Http/Controllers/DepartamentoController.php`

#### `Intranet\Http\Controllers\DepartamentoController`
Class CicloController

- Metodes:
  - `iniBotones`
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina un departament amb autorització explícita.
  - `search`


### `app/Http/Controllers/Deprecated/DualController.php`

#### `Intranet\Http\Controllers\Deprecated\DualController`
Class DualAlumnoController

- Metodes:
  - `search`
    /
  - `grupos`

#### `Intranet\Http\Controllers\Deprecated\grupoService`
- Metodes:
  - `iniBotones`
    /
  - `show`
    /
  - `update`
    /
  - `create`
    /
  - `store`
    /
  - `destroy`
  - `informe`
    /
  - `getGestor`
  - `chooseAction`
  - `certificado`
  - `getInforme`
  - `deleteDir`
  - `putInforme`
  - `printAnexeXII`

#### `Intranet\Http\Controllers\Deprecated\fillAndSave`
- Metodes:
  - `makeArrayPdfAnexoVII`
  - `printAnexeVI`

#### `Intranet\Http\Controllers\Deprecated\semanalByGrupo`
- Metodes:
  - `makeArrayPdfAnexoXII`
    /
  - `printAnexeXIII`

#### `Intranet\Http\Controllers\Deprecated\fillAndSend`
- Metodes:
  - `makeArrayPdfAnexoXIV`
    /


### `app/Http/Controllers/Deprecated/ImportEmailController.php`

#### `Intranet\Http\Controllers\Deprecated\ImportEmailController`
/

- Metodes:
  - `create`
    /
  - `hazDNI`
  - `store`
    /
  - `modifica`

#### `Intranet\Http\Controllers\Deprecated\find`
- Metodes: cap


### `app/Http/Controllers/DocumentoController.php`

#### `Intranet\Http\Controllers\DocumentoController`
Controlador de gestió de documents i fluxos associats de FCT/qualitat.

- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `alumnoFcts`

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - `documentos`

#### `Intranet\Http\Controllers\documentoLifecycleService`
- Metodes:
  - `forms`

#### `Intranet\Http\Controllers\documentoFormService`
- Metodes:
  - `redirect`
  - `store`

#### `Intranet\Http\Controllers\validate`
- Metodes:
  - `createWithDefaultValues`
  - `project`
  - `qualitatUpload`

#### `Intranet\Http\Controllers\app`
- Metodes: cap

#### `Intranet\Http\Controllers\findOrFail`
- Metodes:
  - `qualitat`

#### `Intranet\Http\Controllers\grupos`
- Metodes:
  - `edit`
  - `show`
  - `destroy`
  - `readFile`


### `app/Http/Controllers/EmpresaController.php`

#### `Intranet\Http\Controllers\EmpresaController`
- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `empreses`

#### `Intranet\Http\Controllers\empresaService`
- Metodes:
  - `search`
  - `create`
  - `show`
  - `iniBotones`
  - `store`

#### `Intranet\Http\Controllers\empreses`
- Metodes:
  - `update`
  - `document`
  - `A1`


### `app/Http/Controllers/EspacioController.php`

#### `Intranet\Http\Controllers\EspacioController`
Class EspacioController

- Metodes:
  - `search`
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina un espai amb autorització explícita.
  - `detalle`
    /
  - `iniBotones`
    /
  - `barcode`


### `app/Http/Controllers/ExpedienteController.php`

#### `Intranet\Http\Controllers\ExpedienteController`
Class ExpedienteController

- Metodes:
  - `__construct`
  - `expedients`

#### `Intranet\Http\Controllers\expedienteService`
- Metodes:
  - `store`

#### `Intranet\Http\Controllers\expedients`
- Metodes:
  - `update`
  - `iniBotones`
    /
  - `autorizar`
    /

#### `Intranet\Http\Controllers\authorizePending`
- Metodes:
  - `init`
    /

#### `Intranet\Http\Controllers\init`
- Metodes:
  - `createWithDefaultValues`
  - `pasaOrientacion`
    /

#### `Intranet\Http\Controllers\passToOrientation`
- Metodes:
  - `assigna`

#### `Intranet\Http\Controllers\assignCompanion`
- Metodes:
  - `pdf`
    /
  - `imprimir`
    /
  - `show`
  - `destroy`
    Elimina un expedient amb autorització explícita.


### `app/Http/Controllers/FaltaController.php`

#### `Intranet\Http\Controllers\FaltaController`
Class FaltaController

- Metodes:
  - `__construct`
  - `faltas`

#### `Intranet\Http\Controllers\faltaService`
- Metodes:
  - `iniBotones`
    /
  - `store`
    /

#### `Intranet\Http\Controllers\validate`
- Metodes:
  - `update`
    /
  - `createWithDefaultValues`
  - `init`
    /
  - `alta`
    /
  - `findFaltaOrFail`
    Recupera la falta per aplicar autorització explícita.


### `app/Http/Controllers/FaltaItacaController.php`

#### `Intranet\Http\Controllers\FaltaItacaController`
- Metodes:
  - `horarios`

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - `faltes`

#### `Intranet\Http\Controllers\faltaItacaWorkflowService`
- Metodes:
  - `index`
  - `printReport`

#### `Intranet\Http\Controllers\findElements`
- Metodes:
  - `resolve`
  - `refuse`


### `app/Http/Controllers/FctAlumnoController.php`

#### `Intranet\Http\Controllers\FctAlumnoController`
- Metodes:
  - `__construct`
  - `alumnoFcts`

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - `search`
  - `iniBotones`
  - `setQualityB`
    /
  - `days`
  - `nuevaConvalidacion`
  - `unlink`
  - `storeConvalidacion`
  - `update`
  - `show`
  - `pdf`
  - `Signatura`
  - `Valoratiu`
  - `AVI`
  - `AEng`
  - `auth`
  - `AutDual`
  - `preparePdf`

#### `Intranet\Http\Controllers\findOrFail`
- Metodes: cap

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - `pg0301`
    public function email($id)
  - `email`
  - `importa`


### `app/Http/Controllers/FctController.php`

#### `Intranet\Http\Controllers\FctController`
Class FctController

- Metodes:
  - `__construct`
  - `fcts`

#### `Intranet\Http\Controllers\fctService`
- Metodes:
  - `certificates`

#### `Intranet\Http\Controllers\fctCertificateService`
- Metodes:
  - `edit`
  - `update`
    /
  - `certificat`
  - `certificatColaboradores`

#### `Intranet\Http\Controllers\findOrFail`
- Metodes: cap

#### `Intranet\Http\Controllers\streamColaboradorCertificate`
- Metodes:
  - `store`
    /

#### `Intranet\Http\Controllers\validate`
- Metodes:
  - `show`
    /
  - `destroy`
    /
  - `nouAlumno`
    /
  - `nouFctAlumno`
    /
  - `nouInstructor`
    /
  - `deleteInstructor`
    /
  - `alumnoDelete`
    /
  - `modificaHoras`
    /
  - `cotutor`


### `app/Http/Controllers/FctMailController.php`

#### `Intranet\Http\Controllers\FctMailController`
- Metodes:
  - `__construct`
  - `showMailById`
  - `showMailByRequest`


### `app/Http/Controllers/FicharController.php`

#### `Intranet\Http\Controllers\FicharController`
- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\profesorService`
- Metodes:
  - `horarios`

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - `ficha`
  - `search`
  - `store`

#### `Intranet\Http\Controllers\profesores`
- Metodes:
  - `control`
  - `controlDia`
  - `loadHoraries`
  - `loadHorary`
  - `resumenRango`


### `app/Http/Controllers/GrupoController.php`

#### `Intranet\Http\Controllers\GrupoController`
Class GrupoController

- Metodes:
  - `horarios`

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `workflows`

#### `Intranet\Http\Controllers\grupoWorkflowService`
- Metodes:
  - `search`
    /
  - `detalle`
    /
  - `iniBotones`
    /
  - `horario`
    /
  - `asigna`
    /
  - `pdf`
    /
  - `carnet`
    /
  - `certificados`
    /
  - `certificado`
    /
  - `checkFol`


### `app/Http/Controllers/GrupoTrabajoController.php`

#### `Intranet\Http\Controllers\GrupoTrabajoController`
Class GrupoTrabajoController

- Metodes:
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `seach`
    /
  - `detalle`
    /

#### `Intranet\Http\Controllers\allOrderedBySurname`
- Metodes:
  - `altaProfesor`
    /
  - `borrarProfesor`
    /
  - `coordinador`
    /
  - `removeCoord`
    /
  - `addCoord`
    /
  - `iniBotones`
    /
  - `destroy`
    Elimina un grup de treball amb autorització explícita.


### `app/Http/Controllers/GuardiaController.php`

#### `Intranet\Http\Controllers\GuardiaController`
Class GuardiaController

- Metodes:
  - `index`
    /


### `app/Http/Controllers/HorarioController.php`

#### `Intranet\Http\Controllers\HorarioController`
- Metodes:
  - `horarios`

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\profesorService`
- Metodes:
  - `getJsonFromFile`
  - `changeHorary`
  - `saveCopy`
  - `changeTable`
  - `changeTableAll`
    /
  - `changeIndex`
    /
  - `propuestas`
  - `aceptarPropuesta`

#### `Intranet\Http\Controllers\send`
- Metodes:
  - `esborrarProposta`
  - `sendAcceptationEmail`
  - `horarioCambiar`
    /
  - `iniBotones`
    /
  - `index`
    /
  - `update`
  - `modificarHorario`
    /


### `app/Http/Controllers/ImportController.php`

#### `Intranet\Http\Controllers\ImportController`
- Metodes:
  - `create`
  - `store`
  - `storeAsync`
  - `history`
  - `status`
  - `asignarTutores`
  - `run`
  - `sacaCampos`
  - `filtro`
  - `required`
  - `imports`

#### `Intranet\Http\Controllers\importService`
- Metodes:
  - `workflows`

#### `Intranet\Http\Controllers\importWorkflowService`
- Metodes:
  - `schemas`

#### `Intranet\Http\Controllers\importSchemaProvider`
- Metodes:
  - `xmlHelper`

#### `Intranet\Http\Controllers\importXmlHelperService`
- Metodes:
  - `executions`

#### `Intranet\Http\Controllers\generalImportExecutionService`
- Metodes:
  - `camposBdXml`
    /
  - `executeSyncImport`
  - `resolveImportMode`
  - `authorizeImportManagement`


### `app/Http/Controllers/IncidenciaController.php`

#### `Intranet\Http\Controllers\IncidenciaController`
Class IncidenciaController

- Metodes:
  - `search`
  - `generarOrden`
    /
  - `generateOrder`
    /
  - `removeOrden`
    /
  - `edit`
    /
  - `store`

#### `Intranet\Http\Controllers\merge`
- Metodes:
  - `update`
  - `storeImagen`
  - `createWithDefaultValues`
  - `show`
  - `notify`
    /
  - `iniBotones`
    /
  - `destroy`
  - `currentProfesorDni`


### `app/Http/Controllers/InstructorController.php`

#### `Intranet\Http\Controllers\InstructorController`
Class InstructorController

- Metodes:
  - `instructors`

#### `Intranet\Http\Controllers\instructorWorkflowService`
- Metodes:
  - `iniBotones`
    /
  - `search`
    /
  - `show`
    /
  - `crea`
    /
  - `edita`
    /
  - `guarda`
    /
  - `showEmpresa`
  - `almacena`
    /
  - `delete`
    /
  - `copy`
    /
  - `toCopy`
    /
  - `pdf`
    /


### `app/Http/Controllers/InventarioController.php`

#### `Intranet\Http\Controllers\InventarioController`
Class MaterialController

- Metodes:
  - `barcode`
  - `edit`
  - `espacio`
    /


### `app/Http/Controllers/IpGuardiaController.php`

#### `Intranet\Http\Controllers\IpGuardiaController`
Class LoteController

- Metodes:
  - `search`
  - `iniBotones`
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina una IP de guàrdia amb autorització explícita.


### `app/Http/Controllers/ItacaController.php`

#### `Intranet\Http\Controllers\ItacaController`
- Metodes:
  - `extraescolars`
  - `birret`
  - `faltes`
  - `tryOne`


### `app/Http/Controllers/LoteController.php`

#### `Intranet\Http\Controllers\LoteController`
Class LoteController

- Metodes:
  - `search`
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina un lot amb autorització explícita.
  - `iniBotones`
  - `capture`
  - `postCapture`


### `app/Http/Controllers/MaterialBajaController.php`

#### `Intranet\Http\Controllers\MaterialBajaController`
Class MaterialController

- Metodes:
  - `search`
    /
  - `iniBotones`
  - `delete`
  - `active`
  - `recover`


### `app/Http/Controllers/MaterialController.php`

#### `Intranet\Http\Controllers\MaterialController`
Class MaterialController

- Metodes:
  - `__construct`
    MaterialController constructor.
  - `iniBotones`
    /
  - `copy`
    /
  - `incidencia`
    /


### `app/Http/Controllers/MaterialModController.php`

#### `Intranet\Http\Controllers\MaterialModController`
Class MaterialController

- Metodes:
  - `search`
    /
  - `iniBotones`
  - `refuse`
  - `resolve`


### `app/Http/Controllers/MensualController.php`

#### `Intranet\Http\Controllers\MensualController`
- Metodes:
  - `vistaImpresion`
  - `imprimir`
  - `printFaltaReport`


### `app/Http/Controllers/MenuController.php`

#### `Intranet\Http\Controllers\MenuController`
Class MenuController

- Metodes:
  - `search`
    /
  - `realStore`
  - `copy`
    /
  - `up`
    /
  - `down`
    /
  - `store`
    Guarda un nou menú amb autorització explícita.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `update`
    Actualitza un menú amb autorització explícita.
  - `active`
  - `destroy`
  - `menus`

#### `Intranet\Http\Controllers\menuService`
- Metodes:
  - `iniBotones`
    /


### `app/Http/Controllers/ModuloController.php`

#### `Intranet\Http\Controllers\ModuloController`
Class ModuloController

- Metodes:
  - `iniBotones`
    /
  - `update`


### `app/Http/Controllers/ModuloGrupoController.php`

#### `Intranet\Http\Controllers\ModuloGrupoController`
Class Modulo_cicloController

- Metodes:
  - `iniBotones`
    /
  - `search`

#### `Intranet\Http\Controllers\misModulos`
- Metodes:
  - `link`


### `app/Http/Controllers/Modulo_cicloController.php`

#### `Intranet\Http\Controllers\Modulo_cicloController`
Class Modulo_cicloController

- Metodes:
  - `iniBotones`
    /
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina un enllaç mòdul-cicle amb autorització explícita.


### `app/Http/Controllers/MyMailController.php`

#### `Intranet\Http\Controllers\MyMailController`
Class AdministracionController

- Metodes:
  - `send`
  - `store`
  - `create`


### `app/Http/Controllers/NotificationController.php`

#### `Intranet\Http\Controllers\NotificationController`
Class NotificationController

- Metodes:
  - `inbox`

#### `Intranet\Http\Controllers\notificationInboxService`
- Metodes:
  - `search`
    /
  - `read`
    /
  - `readAll`
    /
  - `deleteAll`
    /
  - `destroy`
    /
  - `iniBotones`
    /
  - `show`
    /


### `app/Http/Controllers/OcrController.php`

#### `Intranet\Http\Controllers\OcrController`
- Metodes:
  - `index`


### `app/Http/Controllers/OptionController.php`

#### `Intranet\Http\Controllers\OptionController`
Class OptionController

- Metodes:
  - `store`
    /

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `destroy`
    /


### `app/Http/Controllers/OrdenTrabajoController.php`

#### `Intranet\Http\Controllers\OrdenTrabajoController`
Class OrdenTrabajoController

- Metodes:
  - `iniBotones`
    /
  - `destroy`
    /
  - `store`
  - `update`
  - `imprime`
    /
  - `resolve`
    /
  - `open`
    /


### `app/Http/Controllers/PPollController.php`

#### `Intranet\Http\Controllers\PPollController`
- Metodes:
  - `iniBotones`
  - `show`
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina una plantilla de poll amb autorització explícita.


### `app/Http/Controllers/PanelActaController.php`

#### `Intranet\Http\Controllers\PanelActaController`
Class PanelActaController

- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\profesorService`
- Metodes:
  - `index`
    /

#### `Intranet\Http\Controllers\Session`
- Metodes:
  - `search`
    /

#### `Intranet\Http\Controllers\RolesUser`
- Metodes:
  - `createGrupsPestana`
    /
  - `iniPestanas`
    /


### `app/Http/Controllers/PanelActasController.php`

#### `Intranet\Http\Controllers\PanelActasController`
Class PanelActasController

- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `indice`
    Mostra l'acta pendent del grup indicat amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `iniBotones`
    /

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap

#### `Intranet\Http\Controllers\send`
- Metodes: cap


### `app/Http/Controllers/PanelActividadController.php`

#### `Intranet\Http\Controllers\PanelActividadController`
Class PanelActividadController

- Metodes:
  - `iniBotones`
    /


### `app/Http/Controllers/PanelActividadOrientacionController.php`

#### `Intranet\Http\Controllers\PanelActividadOrientacionController`
Class PanelActividadOrientacionController

- Metodes:
  - `index`
    Mostra el panell d'activitats d'orientació amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `iniBotones`
    /

#### `Intranet\Http\Controllers\panel`
- Metodes:
  - `search`
    /

#### `Intranet\Http\Controllers\Actividad`
- Metodes:
  - `createWithDefaultValues`
    /


### `app/Http/Controllers/PanelAlumnoCursoController.php`

#### `Intranet\Http\Controllers\PanelAlumnoCursoController`
Class PanelAlumnoCursoController

- Metodes:
  - `index`
    Mostra el llistat de cursos d'alumne amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `search`
    /

#### `Intranet\Http\Controllers\Curso`
- Metodes:
  - `iniBotones`
    /

#### `Intranet\Http\Controllers\panel`
- Metodes: cap


### `app/Http/Controllers/PanelColaboracionController.php`

#### `Intranet\Http\Controllers\PanelColaboracionController`
Class PanelColaboracionController

- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `colaboraciones`

#### `Intranet\Http\Controllers\colaboracionService`
- Metodes:
  - `index`
    /
  - `iniBotones`
    /
  - `search`
    Carrega les col·laboracions del tutor i les relacionades per centre/departament.
  - `update`
    /
  - `store`
    /

#### `Intranet\Http\Controllers\validate`
- Metodes:
  - `showEmpresa`
  - `copy`
    /
  - `destroy`
    /
  - `live`


### `app/Http/Controllers/PanelComisionController.php`

#### `Intranet\Http\Controllers\PanelComisionController`
Class PanelComisionController

- Metodes:
  - `iniBotones`
    /


### `app/Http/Controllers/PanelControlProgramacionController.php`

#### `Intranet\Http\Controllers\PanelControlProgramacionController`
Class PanelControlProgramacionController

- Metodes:
  - `search`
    /
  - `iniBotones`


### `app/Http/Controllers/PanelCursoController.php`

#### `Intranet\Http\Controllers\PanelCursoController`
Class PanelCursoController

- Metodes:
  - `search`
    /
  - `iniBotones`
    /


### `app/Http/Controllers/PanelDocAgrupadosController.php`

#### `Intranet\Http\Controllers\PanelDocAgrupadosController`
Class PanelDocAgrupadosController

- Metodes:
  - `index`
    /

#### `Intranet\Http\Controllers\iniPestanas`
- Metodes:
  - `search`
    /

#### `Intranet\Http\Controllers\Documento`
- Metodes:
  - `iniPestanas`
    /


### `app/Http/Controllers/PanelDocumentoController.php`

#### `Intranet\Http\Controllers\PanelDocumentoController`
Class PanelDocumentoController

- Metodes:
  - `index`
    /

#### `Intranet\Http\Controllers\Session`
- Metodes:
  - `iniBotones`
    /
  - `search`
    /

#### `Intranet\Http\Controllers\Documento`
- Metodes: cap


### `app/Http/Controllers/PanelDualController.php`

#### `Intranet\Http\Controllers\PanelDualController`
- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `index`
    Mostra el panell de control dual amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `iniBotones`

#### `Intranet\Http\Controllers\panel`
- Metodes:
  - `search`

#### `Intranet\Http\Controllers\Fct`
- Metodes:
  - `show`

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap


### `app/Http/Controllers/PanelEmpresaSCController.php`

#### `Intranet\Http\Controllers\PanelEmpresaSCController`
Class PanelEmpresaSCController

- Metodes:
  - `__construct`
  - `empreses`

#### `Intranet\Http\Controllers\empresaService`
- Metodes:
  - `index`
    Mostra el panell d'empreses amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `search`
    /

#### `Intranet\Http\Controllers\empreses`
- Metodes:
  - `iniBotones`
    /


### `app/Http/Controllers/PanelErasmusController.php`

#### `Intranet\Http\Controllers\PanelErasmusController`
Class PanelEmpresaSCController

- Metodes:
  - `search`
    /

#### `Intranet\Http\Controllers\empreses`
- Metodes:
  - `iniBotones`
    /

#### `Intranet\Http\Controllers\panel`
- Metodes: cap


### `app/Http/Controllers/PanelExpedienteController.php`

#### `Intranet\Http\Controllers\PanelExpedienteController`
Class PanelExpedienteController

- Metodes:
  - `iniBotones`
    /
  - `search`
    /


### `app/Http/Controllers/PanelFaltaController.php`

#### `Intranet\Http\Controllers\PanelFaltaController`
Class PanelFaltaController

- Metodes:
  - `search`
  - `iniBotones`
    /


### `app/Http/Controllers/PanelFaltaItacaController.php`

#### `Intranet\Http\Controllers\PanelFaltaItacaController`
Class PanelFaltaItacaController

- Metodes:
  - `iniBotones`
    /


### `app/Http/Controllers/PanelFctAvalController.php`

#### `Intranet\Http\Controllers\PanelFctAvalController`
Class PanelFctAvalController

- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `alumnoFcts`

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - `avals`

#### `Intranet\Http\Controllers\alumnoFctAvalService`
- Metodes:
  - `search`
    /
  - `iniBotones`
    /
  - `apte`
    /

#### `Intranet\Http\Controllers\avals`
- Metodes:
  - `demanarActa`
    /

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap

#### `Intranet\Http\Controllers\findOrFail`
- Metodes:
  - `send`
  - `estadistiques`


### `app/Http/Controllers/PanelFctController.php`

#### `Intranet\Http\Controllers\PanelFctController`
Class FctController

- Metodes:
  - `__construct`
  - `fcts`

#### `Intranet\Http\Controllers\fctService`
- Metodes:
  - `index`
    /

#### `Intranet\Http\Controllers\search`
- Metodes:
  - `iniBotones`
    /
  - `search`
    /

#### `Intranet\Http\Controllers\fcts`
- Metodes: cap


### `app/Http/Controllers/PanelFinCursoController.php`

#### `Intranet\Http\Controllers\PanelFinCursoController`
Class PanelActaController

- Metodes:
  - `index`
    /

#### `Intranet\Http\Controllers\find`
- Metodes:
  - `profesor`
  - `mantenimiento`
  - `direccion`
  - `jefe_dpto`
  - `tutor`
  - `lookForCheckFol`

#### `Intranet\Http\Controllers\misGrupos`
- Metodes:
  - `lookForIssues`
  - `lookForActesPendents`

#### `Intranet\Http\Controllers\withActaPendiente`
- Metodes:
  - `lookforInformsDepartment`
  - `lookAtQualitatUpload`
  - `lookAtActasUpload`
  - `lookAtPollsTutor`
  - `lookAtFctsProjects`

#### `Intranet\Http\Controllers\firstByTutor`
- Metodes:
  - `loadPreviousVotes`
  - `lookForMyResults`

#### `Intranet\Http\Controllers\misModulos`
- Metodes:
  - `lookforMyPrograms`
  - `lookUnPaidBills`

#### `Intranet\Http\Controllers\hasPendingUnpaidByProfesor`
- Metodes: cap


### `app/Http/Controllers/PanelGuardiaController.php`

#### `Intranet\Http\Controllers\PanelGuardiaController`
- Metodes:
  - `comisions`

#### `Intranet\Http\Controllers\profesores`
- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\horarios`
- Metodes:
  - `horarios`

#### `Intranet\Http\Controllers\fitxatge`
- Metodes:
  - `fitxatge`

#### `Intranet\Http\Controllers\index`
- Metodes:
  - `index`
    Mostra el panell de guàrdies amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `iniBotones`

#### `Intranet\Http\Controllers\panel`
- Metodes:
  - `search`

#### `Intranet\Http\Controllers\sesion`
- Metodes:
  - `coincideHorario`
  - `getHorasAfectas`


### `app/Http/Controllers/PanelIncidenciaController.php`

#### `Intranet\Http\Controllers\PanelIncidenciaController`
- Metodes:
  - `index`

#### `Intranet\Http\Controllers\panel`
- Metodes: cap


### `app/Http/Controllers/PanelInfDptoController.php`

#### `Intranet\Http\Controllers\PanelInfDptoController`
- Metodes:
  - `search`
  - `iniPestanas`


### `app/Http/Controllers/PanelListadoEntregasController.php`

#### `Intranet\Http\Controllers\PanelListadoEntregasController`
- Metodes:
  - `index`
    Mostra el llistat d'entregues amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `search`

#### `Intranet\Http\Controllers\hazArray`
- Metodes: cap

#### `Intranet\Http\Controllers\distinctModulos`
- Metodes:
  - `iniBotones`
  - `hazInformeTrimestral`

#### `Intranet\Http\Controllers\hazPdfInforme`
- Metodes:
  - `avisaTodos`
  - `avisaFaltaEntrega`

#### `Intranet\Http\Controllers\Modulo_grupo`
- Metodes: cap

#### `Intranet\Http\Controllers\profesorIds`
- Metodes:
  - `pdf`

#### `Intranet\Http\Controllers\response`
- Metodes:
  - `existeInforme`
  - `hazPdfInforme`

#### `Intranet\Http\Controllers\byDepartamento`
- Metodes:
  - `faltan`


### `app/Http/Controllers/PanelLoteController.php`

#### `Intranet\Http\Controllers\PanelLoteController`
Class LoteController

- Metodes:
  - `search`
  - `iniBotones`
  - `barcode`


### `app/Http/Controllers/PanelModuloGrupoController.php`

#### `Intranet\Http\Controllers\PanelModuloGrupoController`
- Metodes:
  - `search`
  - `iniBotones`
  - `pdf`


### `app/Http/Controllers/PanelOrdenTrabajoController.php`

#### `Intranet\Http\Controllers\PanelOrdenTrabajoController`
- Metodes:
  - `indice`
    Mostra el detall d'orde de treball amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `search`

#### `Intranet\Http\Controllers\Incidencia`
- Metodes:
  - `iniBotones`

#### `Intranet\Http\Controllers\panel`
- Metodes:
  - `iniPestanas`


### `app/Http/Controllers/PanelPG0301Controller.php`

#### `Intranet\Http\Controllers\PanelPG0301Controller`
- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `indice`
    Mostra el llistat per grup amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `iniBotones`

#### `Intranet\Http\Controllers\Session`
- Metodes:
  - `search`

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap


### `app/Http/Controllers/PanelPGDualController.php`

#### `Intranet\Http\Controllers\PanelPGDualController`
- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `indice`
    Mostra el llistat dual per grup amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `iniBotones`

#### `Intranet\Http\Controllers\Session`
- Metodes:
  - `search`

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap


### `app/Http/Controllers/PanelPollResponseController.php`

#### `Intranet\Http\Controllers\PanelPollResponseController`
- Metodes:
  - `iniBotones`
  - `search`


### `app/Http/Controllers/PanelPollResultController.php`

#### `Intranet\Http\Controllers\PanelPollResultController`
- Metodes:
  - `iniBotones`
  - `search`


### `app/Http/Controllers/PanelPracticasController.php`

#### `Intranet\Http\Controllers\PanelPracticasController`
- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `index`
    Mostra el panell de control FCT amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `iniBotones`

#### `Intranet\Http\Controllers\panel`
- Metodes:
  - `search`

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap


### `app/Http/Controllers/PanelPresenciaController.php`

#### `Intranet\Http\Controllers\PanelPresenciaController`
- Metodes:
  - `comisions`

#### `Intranet\Http\Controllers\profesores`
- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\horarios`
- Metodes:
  - `horarios`

#### `Intranet\Http\Controllers\indice`
- Metodes:
  - `indice`

#### `Intranet\Http\Controllers\Session`
- Metodes:
  - `email`

#### `Intranet\Http\Controllers\self`
- Metodes: cap

#### `Intranet\Http\Controllers\fitxaDiaManual`
- Metodes:
  - `noHanFichado`


### `app/Http/Controllers/PanelProcedimientoAcompanyantController.php`

#### `Intranet\Http\Controllers\PanelProcedimientoAcompanyantController`
Class PanelExpedienteOrientacionController

- Metodes:
  - `iniPestanas`
  - `iniBotones`
    /
  - `search`
    /


### `app/Http/Controllers/PanelProcedimientoController.php`

#### `Intranet\Http\Controllers\PanelProcedimientoController`
Class PanelExpedienteOrientacionController

- Metodes:
  - `index`

#### `Intranet\Http\Controllers\search`
- Metodes:
  - `iniBotones`
    /
  - `search`
    /

#### `Intranet\Http\Controllers\Expediente`
- Metodes: cap


### `app/Http/Controllers/PanelProjecteController.php`

#### `Intranet\Http\Controllers\PanelProjecteController`
Class PanelProjecteController

- Metodes:
  - `profesores`

#### `Intranet\Http\Controllers\profesorService`
- Metodes:
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `myTutorGroup`
  - `search`
    Recupera els projectes del grup del tutor autenticat.
  - `store`

#### `Intranet\Http\Controllers\myTutorGroup`
- Metodes:
  - `pdf`

#### `Intranet\Http\Controllers\hazZip`
- Metodes:
  - `acta`
    Genera l'acta de valoració de propostes del grup.

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - `iniBotones`


### `app/Http/Controllers/PanelProyectoController.php`

#### `Intranet\Http\Controllers\PanelProyectoController`
- Metodes:
  - `index`
    Mostra el panell de projectes documentals amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `iniPestanas`
  - `search`
  - `iniBotones`


### `app/Http/Controllers/PanelSeguimientoAlumnosController.php`

#### `Intranet\Http\Controllers\PanelSeguimientoAlumnosController`
- Metodes:
  - `indice`
  - `store`
  - `destroy`


### `app/Http/Controllers/PanelSignaturaController.php`

#### `Intranet\Http\Controllers\PanelSignaturaController`
Class PanelExpedienteController

- Metodes:
  - `index`
    Mostra el panell de signatures de direcció amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - `iniBotones`
    /
  - `search`
    /

#### `Intranet\Http\Controllers\Signatura`
- Metodes:
  - `sign`

#### `Intranet\Http\Controllers\array_keys`
- Metodes: cap

#### `Intranet\Http\Controllers\send`
- Metodes: cap


### `app/Http/Controllers/PanelSolicitudOrientacionController.php`

#### `Intranet\Http\Controllers\PanelSolicitudOrientacionController`
Class PanelSolicitudOrientacionController

- Metodes:
  - `iniBotones`
    /
  - `active`
    Activa una sol·licitud d'orientació pendent.
  - `resolve`
    Resol una sol·licitud d'orientació activa.
  - `search`
    Recupera les sol·licituds visibles per a l'orientador autenticat.


### `app/Http/Controllers/PdfController.php`

#### `Intranet\Http\Controllers\PdfController`
- Metodes:
  - `index`


### `app/Http/Controllers/PollController.php`

#### `Intranet\Http\Controllers\PollController`
- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `polls`

#### `Intranet\Http\Controllers\pollWorkflowService`
- Metodes:
  - `iniBotones`
  - `preparaEnquesta`
  - `guardaEnquesta`
  - `lookAtMyVotes`
  - `lookAtAllVotes`


### `app/Http/Controllers/ProfesorController.php`

#### `Intranet\Http\Controllers\ProfesorController`
- Metodes:
  - `__construct`
  - `profesores`

#### `Intranet\Http\Controllers\profesorService`
- Metodes:
  - `horarios`

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `index`
  - `departamento`
  - `fse`
  - `equipoDirectivo`
  - `comissio`
  - `rol`
  - `equipo`
  - `update`
  - `miApiToken`
  - `avisaColectivo`
  - `alerta`
  - `carnet`
  - `tarjeta`
  - `iniBotones`
  - `iniProfileBotones`
  - `horario`
  - `imprimirHorarios`
  - `change`
  - `backChange`


### `app/Http/Controllers/ProgramacionController.php`

#### `Intranet\Http\Controllers\ProgramacionController`
- Metodes:
  - `horarios`

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - `search`
  - `init`
  - `seguimiento`
  - `avisaFaltaEntrega`

#### `Intranet\Http\Controllers\profesorIds`
- Metodes:
  - `advise`
  - `updateSeguimiento`
  - `link`
  - `iniBotones`


### `app/Http/Controllers/ProjecteController.php`

#### `Intranet\Http\Controllers\ProjecteController`
Class EspacioController

- Metodes:
  - `search`
  - `store`
  - `update`
  - `email`

#### `Intranet\Http\Controllers\send`
- Metodes:
  - `pdf`

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - `iniBotones`


### `app/Http/Controllers/QualitatDocumentoController.php`

#### `Intranet\Http\Controllers\QualitatDocumentoController`
Class PanelDocumentoController

- Metodes:
  - `index`
    /
  - `iniBotones`
    /
  - `search`
    /


### `app/Http/Controllers/RedirectAfterAuthenticationController.php`

#### `Intranet\Http\Controllers\RedirectAfterAuthenticationController`
Orquestra l'execució d'accions SAO després de validar la contrasenya.

- Metodes:
  - `__construct`

#### `Intranet\Http\Controllers\__invoke`
- Metodes:
  - `__invoke`

#### `Intranet\Http\Controllers\method_exists`
- Metodes: cap


### `app/Http/Controllers/ReservaController.php`

#### `Intranet\Http\Controllers\ReservaController`
Class ReservaController

- Metodes:
  - `index`
    /


### `app/Http/Controllers/ResultadoController.php`

#### `Intranet\Http\Controllers\ResultadoController`
Class ResultadoController

- Metodes:
  - `iniBotones`
    /
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `rellenaPropuestasMejora`
  - `store`
  - `update`
  - `destroy`
    Elimina un resultat amb autorització explícita.
  - `search`
    /

#### `Intranet\Http\Controllers\misModulos`
- Metodes:
  - `listado`
    /
  - `createWithDefaultValues`


### `app/Http/Controllers/ReunionController.php`

#### `Intranet\Http\Controllers\ReunionController`
Controlador de gestió de reunions i assistències.

- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `reunionService`

#### `Intranet\Http\Controllers\reunionService`
- Metodes:
  - `search`
  - `createWithDefaultValues`
  - `store`

#### `Intranet\Http\Controllers\DB`
- Metodes:
  - `edit`

#### `Intranet\Http\Controllers\activosOrdered`
- Metodes:
  - `update`
  - `tAlumnos`
  - `altaProfesor`
  - `borrarProfesor`
  - `borrarAlumno`
  - `altaAlumno`
  - `altaOrden`
  - `borrarOrden`
  - `notify`
  - `email`
  - `iniBotones`
  - `pdf`
  - `actaCompleta`
  - `saveFile`
  - `deleteFile`
  - `listado`
  - `avisaFaltaActa`
  - `construye_pdf`
  - `informe`
  - `preparePdf`


### `app/Http/Controllers/SendAvaluacioEmailController.php`

#### `Intranet\Http\Controllers\SendAvaluacioEmailController`
Class ImportController

- Metodes:
  - `create`
    /
  - `generaToken`
  - `store`
    /
  - `getToken`
  - `obtenToken`
  - `sendMatricula`


### `app/Http/Controllers/SettingController.php`

#### `Intranet\Http\Controllers\SettingController`
Controlador de manteniment de settings de sistema.

- Metodes:
  - `search`
  - `iniBotones`
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina un setting existent.


### `app/Http/Controllers/SignaturaAlumneController.php`

#### `Intranet\Http\Controllers\SignaturaAlumneController`
Class PanelExpedienteController

- Metodes:
  - `__construct`
  - `alumnoFcts`

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - `index`
  - `grid`
  - `pdf`
    /
  - `iniBotones`


### `app/Http/Controllers/SignaturaController.php`

#### `Intranet\Http\Controllers\SignaturaController`
Class PanelExpedienteController

- Metodes:
  - `__construct`
  - `alumnoFcts`

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - `store`

#### `Intranet\Http\Controllers\file`
- Metodes:
  - `iniBotones`
    /
  - `deleteAll`

#### `Intranet\Http\Controllers\search`
- Metodes:
  - `pdf`
  - `destroy`
  - `sendUnique`
  - `sendMultiple`
  - `upload`
  - `a5`

#### `Intranet\Http\Controllers\Signatura`
- Metodes: cap


### `app/Http/Controllers/SolicitudController.php`

#### `Intranet\Http\Controllers\SolicitudController`
Class ExpedienteController

- Metodes:
  - `store`

#### `Intranet\Http\Controllers\merge`
- Metodes:
  - `update`
  - `confirm`
  - `iniBotones`
    /
  - `init`
    /

#### `Intranet\Http\Controllers\send`
- Metodes:
  - `createWithDefaultValues`
  - `show`
  - `destroy`
    Elimina una sol·licitud amb autorització explícita.


### `app/Http/Controllers/TaskController.php`

#### `Intranet\Http\Controllers\TaskController`
Controlador de manteniment i validació de tasques.

- Metodes:
  - `__construct`
  - `validationService`

#### `Intranet\Http\Controllers\taskValidationService`
- Metodes:
  - `iniBotones`
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `check`


### `app/Http/Controllers/TeacherImportController.php`

#### `Intranet\Http\Controllers\TeacherImportController`
- Metodes:
  - `create`
  - `store`
  - `storeAsync`
  - `run`
  - `sacaCampos`
  - `filtro`
  - `required`
  - `imports`

#### `Intranet\Http\Controllers\importService`
- Metodes:
  - `workflows`

#### `Intranet\Http\Controllers\importWorkflowService`
- Metodes:
  - `schemas`

#### `Intranet\Http\Controllers\importSchemaProvider`
- Metodes:
  - `xmlHelper`

#### `Intranet\Http\Controllers\importXmlHelperService`
- Metodes:
  - `executions`

#### `Intranet\Http\Controllers\teacherImportExecutionService`
- Metodes:
  - `camposBdXml`
    /
  - `executeSyncImport`
  - `resolveImportMode`
  - `authorizeImportManagement`


### `app/Http/Controllers/TipoActividadController.php`

#### `Intranet\Http\Controllers\TipoActividadController`
Class LoteController

- Metodes:
  - `store`
  - `update`
  - `destroy`
    Elimina un tipus d'activitat amb autorització explícita.
  - `search`
  - `iniBotones`


### `app/Http/Controllers/TipoIncidenciaController.php`

#### `Intranet\Http\Controllers\TipoIncidenciaController`
Class ComisionController

- Metodes:
  - `iniBotones`
    /
  - `search`
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina un tipus d'incidència amb autorització explícita.


### `app/Http/Controllers/TutoriaController.php`

#### `Intranet\Http\Controllers\TutoriaController`
- Metodes:
  - `__construct`
  - `grupos`

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - `index`
  - `search`
  - `detalle`
  - `indexTutoria`
  - `anexo`
  - `iniTutBotones`
  - `iniBotones`


### `app/Http/Controllers/TutoriaGrupoController.php`

#### `Intranet\Http\Controllers\TutoriaGrupoController`
- Metodes:
  - `createfrom`
  - `create`

#### `Intranet\Http\Controllers\FormBuilder`
- Metodes:
  - `edit`
  - `store`

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - `update`
  - `destroy`
    Elimina una relació tutoria-grup amb autorització explícita.
  - `search`
  - `iniBotones`


### `app/Http/Controllers/VotesController.php`

#### `Intranet\Http\Controllers\VotesController`
- Metodes:
  - `showColaboracion`



## Models

### `app/Entities/Actividad.php`

#### `Intranet\Entities\Actividad`
Model d'activitats extraescolars/complementàries.

- Metodes: cap

#### `Intranet\Entities\grupos`
- Metodes:
  - `grupos`

#### `Intranet\Entities\profesores`
- Metodes:
  - `profesores`

#### `Intranet\Entities\withPivot`
- Metodes:
  - `tipoActividad`

#### `Intranet\Entities\Creador`
- Metodes:
  - `Creador`
    Devueld el id del Coordinador
  - `scopeProfesor`
  - `getDesdeAttribute`
    Accessor de `desde` en format de visualització.
  - `getHastaAttribute`
    Accessor de `hasta` en format de visualització.
  - `scopeNext`
    Filtra activitats futures.
  - `scopeAuth`
    Filtra activitats autoritzades o no extraescolars.
  - `scopeDia`
    Filtra activitats que cauen en un dia concret.
  - `scopeDepartamento`
    Filtra per departament a través dels grups de l'activitat.
  - `Tutor`
    Relació de professor coordinador de l'activitat.

#### `Intranet\Entities\wherePivot`
- Metodes:
  - `getcoordAttribute`
    Accessor booleà per saber si l'usuari autenticat és coordinador.
  - `getsituacionAttribute`
    Accessor de text de situació segons estat.
  - `loadPoll`
    Carrega activitats de poll dels grups de l'usuari.
  - `getRecomendadaAttribute`
    Accessor de "recomanada" en format Sí/No.
  - `getTipoActividadIdOptions`
    Opcions de tipus d'activitat segons departament de l'usuari.


### `app/Entities/ActividadGrupo.php`

#### `Intranet\Entities\ActividadGrupo`
- Metodes:
  - `scopeDepartamento`

#### `Intranet\Entities\byDepartamento`
- Metodes: cap


### `app/Entities/ActividadProfesor.php`

#### `Intranet\Entities\ActividadProfesor`
- Metodes:
  - `scopeTutor`


### `app/Entities/Activity.php`

#### `Intranet\Entities\Activity`
- Metodes:
  - `record`
    Manté API estàtica legacy delegant en el servei d'aplicació.

#### `Intranet\Entities\record`
- Metodes:
  - `scopeProfesor`
  - `scopeModelo`
    Filtra per classe de model emmagatzemada en format FQCN.
  - `scopeNotUpdate`
  - `scopeMail`
  - `scopeId`
  - `scopeIds`
  - `scopeRelationId`
  - `propietario`

#### `Intranet\Entities\getUpdatedAtAttribute`
- Metodes:
  - `getUpdatedAtAttribute`


### `app/Entities/Adjunto.php`

#### `Intranet\Entities\Adjunto`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\scopeFindByName`
- Metodes:
  - `scopeFindByName`
  - `scopeGetByPath`
  - `getPathAttribute`
  - `getFileAttribute`
  - `getDirectoryAttribute`
  - `getModeloAttribute`
  - `getModeloIdAttribute`


### `app/Entities/Alumno.php`

#### `Intranet\Entities\Alumno`
- Metodes:
  - `Curso`

#### `Intranet\Entities\withPivot`
- Metodes:
  - `AlumnoFct`

#### `Intranet\Entities\Grupo`
- Metodes:
  - `Grupo`

#### `Intranet\Entities\Fcts`
- Metodes:
  - `Fcts`

#### `Intranet\Entities\FctsColaboracion`
- Metodes:
  - `FctsColaboracion`
  - `AlumnoResultado`

#### `Intranet\Entities\Provincia`
- Metodes:
  - `Provincia`

#### `Intranet\Entities\Municipio`
- Metodes:
  - `Municipio`

#### `Intranet\Entities\where`
- Metodes:
  - `Projecte`

#### `Intranet\Entities\scopeQGrupo`
- Metodes:
  - `scopeQGrupo`
  - `scopeMenor`
  - `scopeMisAlumnos`

#### `Intranet\Entities\qTutor`
- Metodes: cap


### `app/Entities/AlumnoCurso.php`

#### `Intranet\Entities\AlumnoCurso`
- Metodes:
  - `scopeCurso`
  - `scopeFinalizado`
  - `Alumno`

#### `Intranet\Entities\Curso`
- Metodes:
  - `Curso`

#### `Intranet\Entities\getNombreAttribute`
- Metodes:
  - `getNombreAttribute`
  - `getSexoAttribute`
  - `getFullNameAttribute`
  - `getDniAttribute`


### `app/Entities/AlumnoFct.php`

#### `Intranet\Entities\AlumnoFct`
- Metodes: cap

#### `Intranet\Entities\null`
- Metodes:
  - `Alumno`

#### `Intranet\Entities\Fct`
- Metodes:
  - `Fct`

#### `Intranet\Entities\Dual`
- Metodes:
  - `Dual`

#### `Intranet\Entities\Signatures`
- Metodes:
  - `Signatures`

#### `Intranet\Entities\Tutor`
- Metodes:
  - `Tutor`

#### `Intranet\Entities\Contactos`
- Metodes:
  - `Contactos`

#### `Intranet\Entities\mail`
- Metodes: cap

#### `Intranet\Entities\scopeMisFcts`
- Metodes:
  - `scopeMisFcts`
  - `scopeTotesFcts`
  - `scopeMisProyectos`
  - `scopeEsFct`
  - `scopeEsAval`
  - `scopeEsDual`
  - `scopeMisDual`
  - `scopeMisConvalidados`
  - `scopeNoAval`
  - `scopePendiente`
  - `scopeAval`
  - `scopePendienteNotificar`
  - `scopeCalificados`
  - `scopeAprobados`
  - `scopeTitulan`
  - `scopeRealFcts`
  - `scopeAvaluables`
  - `scopeMisErasmus`
  - `scopeEsErasmus`
  - `scopeEsExempt`
  - `scopeEstaSao`
  - `scopeActiva`
  - `scopeHaEmpezado`
  - `scopeNoHaAcabado`
  - `getEmailAttribute`
  - `getCentroAttribute`
  - `getNombreAttribute`
  - `getNomEdatAttribute`
  - `getQualificacioAttribute`
  - `getDesdeAttribute`
  - `getHastaAttribute`
  - `getFinPracticasAttribute`
  - `getClassAttribute`
  - `presenter`
  - `getAdjuntosAttribute`
  - `routeFile`
  - `getSignAttribute`
  - `getContactoAttribute`
  - `getFullNameAttribute`
  - `getHorasRealizadasAttribute`
  - `getHorasTotalAttribute`
  - `getPeriodeAttribute`
  - `getProjecteAttribute`
  - `getAsociacionAttribute`
  - `getMiniCentroAttribute`
  - `getInstructorAttribute`
  - `getGrupAttribute`
  - `scopeGrupo`
  - `getQuienAttribute`
  - `getSaoAnnexesAttribute`
  - `getA2Attribute`
  - `getA1Attribute`
  - `getA3Attribute`
  - `getIdPrintAttribute`
  - `getAnnexesCollection`
  - `signatureService`


### `app/Entities/AlumnoGrupo.php`

#### `Intranet\Entities\AlumnoGrupo`
- Metodes:
  - `find`
    Troba un registre d'alumne-grup.
  - `Alumno`

#### `Intranet\Entities\Grupo`
- Metodes:
  - `Grupo`

#### `Intranet\Entities\getNombreAttribute`
- Metodes:
  - `getNombreAttribute`
  - `getPoblacionAttribute`
  - `getEmailAttribute`
  - `getTelef2Attribute`
  - `getTelef1Attribute`
  - `getFolAttribute`
  - `getFotoAttribute`
  - `getDretsAttribute`
  - `getExtraescolarsAttribute`
  - `getDAAttribute`


### `app/Entities/AlumnoResultado.php`

#### `Intranet\Entities\AlumnoResultado`
- Metodes:
  - `Alumno`

#### `Intranet\Entities\ModuloGrupo`
- Metodes:
  - `ModuloGrupo`

#### `Intranet\Entities\getNombreAttribute`
- Metodes:
  - `getNombreAttribute`
  - `getidAlumnoOptions`
  - `getValoracionAttribute`
  - `getModuloAttribute`


### `app/Entities/AlumnoReunion.php`

#### `Intranet\Entities\AlumnoReunion`
- Metodes:
  - `Alumno`

#### `Intranet\Entities\Reunion`
- Metodes:
  - `Reunion`


### `app/Entities/Articulo.php`

#### `Intranet\Entities\Articulo`
- Metodes:
  - `Lote`
    Lots on apareix l'article a través de la taula pivot `articulos_lote`.

#### `Intranet\Entities\getMiniaturaAttribute`
- Metodes:
  - `getMiniaturaAttribute`
  - `fillFile`
    Guarda la imatge de l'article en `public/Articulos` i retorna la ruta relativa.
  - `setDescripcionAttribute`


### `app/Entities/ArticuloLote.php`

#### `Intranet\Entities\ArticuloLote`
- Metodes:
  - `Articulo`

#### `Intranet\Entities\Lote`
- Metodes:
  - `Lote`

#### `Intranet\Entities\Materiales`
- Metodes:
  - `Materiales`

#### `Intranet\Entities\getDescripcionAttribute`
- Metodes:
  - `getDescripcionAttribute`


### `app/Entities/Asistencia.php`

#### `Intranet\Entities\Asistencia`
- Metodes:
  - `Profesor`


### `app/Entities/BustiaVioleta.php`

#### `Intranet\Entities\BustiaVioleta`
- Metodes:
  - `getAutorDisplayNameAttribute`
  - `scopePendents`
  - `scopeAmbCategoria`
  - `scopeDeTipus`


### `app/Entities/CalendariEscolar.php`

#### `Intranet\Entities\CalendariEscolar`
- Metodes:
  - `esNoLectiu`
  - `esFestiu`


### `app/Entities/Centro.php`

#### `Intranet\Entities\Centro`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Empresa`
- Metodes:
  - `Empresa`

#### `Intranet\Entities\scopeEmpresa`
- Metodes:
  - `scopeEmpresa`
  - `colaboraciones`

#### `Intranet\Entities\instructores`
- Metodes:
  - `instructores`

#### `Intranet\Entities\getIdiomaOptions`
- Metodes:
  - `getIdiomaOptions`


### `app/Entities/Ciclo.php`

#### `Intranet\Entities\Ciclo`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Grupos`
- Metodes:
  - `Grupos`

#### `Intranet\Entities\Departament`
- Metodes:
  - `Departament`

#### `Intranet\Entities\TutoresFct`
- Metodes:
  - `TutoresFct`

#### `Intranet\Entities\Grupo`
- Metodes: cap

#### `Intranet\Entities\where`
- Metodes:
  - `colaboraciones`

#### `Intranet\Entities\fcts`
- Metodes:
  - `fcts`

#### `Intranet\Entities\Colaboracion`
- Metodes: cap

#### `Intranet\Entities\getTipoOptions`
- Metodes:
  - `getTipoOptions`
  - `getDepartamentoOptions`
  - `getXtipoAttribute`
  - `getCtipoAttribute`
  - `getXdepartamentoAttribute`
  - `getLiteralAttribute`
  - `getCompleteDualAttribute`


### `app/Entities/Colaboracion.php`

#### `Intranet\Entities\Colaboracion`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Centro`
- Metodes:
  - `Centro`

#### `Intranet\Entities\Ciclo`
- Metodes:
  - `Ciclo`

#### `Intranet\Entities\fcts`
- Metodes:
  - `fcts`

#### `Intranet\Entities\incidencias`
- Metodes:
  - `incidencias`

#### `Intranet\Entities\Propietario`
- Metodes:
  - `Propietario`

#### `Intranet\Entities\votes`
- Metodes:
  - `votes`

#### `Intranet\Entities\scopeCiclo`
- Metodes:
  - `scopeCiclo`
  - `scopeEmpresa`
  - `scopeMiColaboracion`

#### `Intranet\Entities\qTutor`
- Metodes:
  - `getEmpresaAttribute`
  - `getShortAttribute`
  - `getXCicloAttribute`
  - `getXEstadoAttribute`
  - `getLocalidadAttribute`
  - `getHorariAttribute`
  - `getEstadoOptions`
  - `getAnotacioAttribute`
  - `getProfesorAttribute`
  - `getUltimoAttribute`
  - `getSituationAttribute`


### `app/Entities/Colaborador.php`

#### `Intranet\Entities\Colaborador`
- Metodes:
  - `Fct`


### `app/Entities/Comision.php`

#### `Intranet\Entities\Comision`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Creador`
- Metodes:
  - `Creador`
  - `scopeActual`
  - `scopeNext`
  - `getDesdeAttribute`
  - `getHastaAttribute`
  - `Profesor`

#### `Intranet\Entities\Fcts`
- Metodes:
  - `Fcts`

#### `Intranet\Entities\withPivot`
- Metodes:
  - `getMedioOptions`
  - `getEstadoOptions`
  - `getIdProfesorOptions`
  - `scopeDia`
  - `getnombreAttribute`
  - `getsituacionAttribute`
  - `getTotalAttribute`
  - `getDescripcionAttribute`
  - `getTipoVehiculoAttribute`
  - `showConfirm`


### `app/Entities/Concerns/BatoiModels.php`

#### `Intranet\Entities\Concerns\BatoiModels`
Utilitats comunes de model per a formularis, validació i càrrega de fitxers.

- Metodes:
  - `getDateFormat`
    /
  - `getRules`
    /
  - `isRequired`
    /
  - `setInputType`
    /
  - `deleteInputType`
    /
  - `addFillable`
    /
  - `setRule`
    /
  - `getRule`
    /
  - `getInputType`
    /
  - `getInputTypes`
    Retorna la definició completa de tipus d'input del model.
  - `existsDatepicker`
    /
  - `isTypeDate`
    /
  - `fillAll`
    Emplena i persisteix els camps `fillable` des d'un request.
  - `fillField`
    Normalitza i transforma el valor d'un camp segons el seu tipus d'input.
  - `fillFile`
    Valida i guarda un fitxer annex retornant la ruta final.
  - `getDirectory`
    Construeix el directori de destí del fitxer segons curs i classe.
  - `getFileName`
    Construeix el nom final del fitxer pujat.
  - `has`
    /
  - `getLinkAttribute`
    /
  - `showConfirm`
    Retorna el model serialitzat per a pantalles de confirmació.


### `app/Entities/Cotxe.php`

#### `Intranet\Entities\Cotxe`
- Metodes:
  - `professor`

#### `Intranet\Entities\scopePlateHamming1`
- Metodes:
  - `scopePlateHamming1`


### `app/Entities/CotxeAcces.php`

#### `Intranet\Entities\CotxeAcces`
- Metodes: cap


### `app/Entities/Counter.php`

#### `Intranet\Entities\Counter`
- Metodes: cap


### `app/Entities/Curso.php`

#### `Intranet\Entities\Curso`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Alumnos`
- Metodes:
  - `Alumnos`

#### `Intranet\Entities\withPivot`
- Metodes:
  - `Registrado`
  - `getFechaInicioAttribute`
  - `getFechaFinAttribute`
  - `getHorainiAttribute`
  - `getHorafinAttribute`
  - `getNAlumnosAttribute`
  - `getEstadoAttribute`
  - `scopeActivo`


### `app/Entities/Departamento.php`

#### `Intranet\Entities\Departamento`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\Modulo`
- Metodes:
  - `Modulo`

#### `Intranet\Entities\Jefe`
- Metodes:
  - `Jefe`

#### `Intranet\Entities\getLiteralAttribute`
- Metodes:
  - `getLiteralAttribute`
  - `getidProfesorOptions`


### `app/Entities/Documento.php`

#### `Intranet\Entities\Documento`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\getCreatedAtAttribute`
- Metodes:
  - `getCreatedAtAttribute`
  - `getGrupoOptions`
  - `getTipoDocumentoOptions`
  - `getExistAttribute`
  - `getSituacionAttribute`
  - `getLinkAttribute`
  - `deleteDoc`

#### `Intranet\Entities\delete`
- Metodes: cap


### `app/Entities/Dual.php`

#### `Intranet\Entities\Dual`
Mantingut temporalment per compatibilitat amb fluxos antics.

- Metodes:
  - `getIdAlumnoOptions`
  - `getIdColaboracionOptions`

#### `Intranet\Entities\firstByTutor`
- Metodes: cap


### `app/Entities/Empresa.php`

#### `Intranet\Entities\Empresa`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\null`
- Metodes:
  - `centros`

#### `Intranet\Entities\colaboraciones`
- Metodes:
  - `colaboraciones`

#### `Intranet\Entities\Centro`
- Metodes: cap

#### `Intranet\Entities\scopeCiclo`
- Metodes:
  - `scopeCiclo`

#### `Intranet\Entities\firstByTutor`
- Metodes:
  - `scopeMenor`
  - `getConveniNouAttribute`
  - `getConveniRenovatAttribute`
  - `getRenovatConveniAttribute`
  - `getConveniCaducatAttribute`
  - `getDataSignaturaAttribute`
  - `getCiclesAttribute`
  - `convenioFileMtime`


### `app/Entities/Espacio.php`

#### `Intranet\Entities\Espacio`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Departamento`
- Metodes:
  - `Departamento`

#### `Intranet\Entities\GruposMati`
- Metodes:
  - `GruposMati`

#### `Intranet\Entities\GruposVesprada`
- Metodes:
  - `GruposVesprada`

#### `Intranet\Entities\getIdDepartamentoOptions`
- Metodes:
  - `getIdDepartamentoOptions`
  - `getGMatiOptions`

#### `Intranet\Entities\all`
- Metodes:
  - `getGVespradaOptions`
  - `getXDepartamentoAttribute`
  - `Materiales`

#### `Intranet\Entities\where`
- Metodes: cap


### `app/Entities/Expediente.php`

#### `Intranet\Entities\Expediente`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\tipoExpediente`
- Metodes:
  - `tipoExpediente`

#### `Intranet\Entities\getfechaAttribute`
- Metodes:
  - `getfechaAttribute`
  - `getfechasolucionAttribute`
  - `getfechatramiteAttribute`
  - `getTipoOptions`
  - `getIdModuloOptions`
  - `getIdAlumnoOptions`

#### `Intranet\Entities\misGrupos`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\Acompanyant`
- Metodes:
  - `Acompanyant`

#### `Intranet\Entities\Alumno`
- Metodes:
  - `Alumno`

#### `Intranet\Entities\Modulo`
- Metodes:
  - `Modulo`

#### `Intranet\Entities\getNomAlumAttribute`
- Metodes:
  - `getNomAlumAttribute`
  - `getNomProfeAttribute`
  - `getSituacionAttribute`
  - `getXtipoAttribute`
  - `getXmoduloAttribute`
  - `getShortAttribute`
  - `getEsInformeAttribute`
  - `getQuienAttribute`
  - `scopeListos`
  - `getAnnexoAttribute`


### `app/Entities/Falta.php`

#### `Intranet\Entities\Falta`
Model de faltes.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\profesor`
- Metodes:
  - `profesor`

#### `Intranet\Entities\getDesdeAttribute`
- Metodes:
  - `getDesdeAttribute`
  - `getHastaAttribute`
  - `getHorainiAttribute`
  - `getHorafinAttribute`
  - `getDesdeHoraAttribute`
  - `getMotivosOptions`
  - `getIdProfesorOptions`
  - `scopeDia`
  - `getNombreAttribute`
  - `getSituacionAttribute`
  - `getMotivoAttribute`
  - `showConfirm`
  - `getHoraIniOptions`
  - `getHoraFinOptions`


### `app/Entities/Falta_itaca.php`

#### `Intranet\Entities\Falta_itaca`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\Hora`
- Metodes:
  - `Hora`

#### `Intranet\Entities\Grupo`
- Metodes:
  - `Grupo`

#### `Intranet\Entities\getNombreAttribute`
- Metodes:
  - `getNombreAttribute`
  - `getHorasAttribute`
  - `getXGrupoAttribute`
  - `getFichajeAttribute`
  - `getXestadoAttribute`
  - `getDiaAttribute`
  - `putEstado`


### `app/Entities/Falta_profesor.php`

#### `Intranet\Entities\Falta_profesor`
Model de fitxatges de professorat.

- Metodes:
  - `Profesor`

#### `Intranet\Entities\scopeHoy`
- Metodes:
  - `scopeHoy`
  - `scopehaFichado`
  - `fichar`
    /

#### `Intranet\Entities\fitxar`
- Metodes:
  - `fichaDia`
    /

#### `Intranet\Entities\fitxaDiaManual`
- Metodes: cap


### `app/Entities/Fct.php`

#### `Intranet\Entities\Fct`
- Metodes: cap

#### `Intranet\Entities\FctCreated`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Comision`
- Metodes:
  - `Comision`

#### `Intranet\Entities\withPivot`
- Metodes:
  - `AlFct`

#### `Intranet\Entities\Instructor`
- Metodes:
  - `Instructor`

#### `Intranet\Entities\Contactos`
- Metodes:
  - `Contactos`

#### `Intranet\Entities\mail`
- Metodes:
  - `Colaboradores`

#### `Intranet\Entities\Alumnos`
- Metodes:
  - `Alumnos`

#### `Intranet\Entities\votes`
- Metodes:
  - `votes`

#### `Intranet\Entities\cotutor`
- Metodes:
  - `cotutor`

#### `Intranet\Entities\hasSignatures`
- Metodes:
  - `hasSignatures`
  - `tutor`

#### `Intranet\Entities\Colaboracion`
- Metodes: cap

#### `Intranet\Entities\scopeCentro`
- Metodes:
  - `scopeCentro`
  - `scopeEmpresa`
  - `scopeMisFcts`
  - `scopeWithCotutor`
  - `getEncarregatAttribute`
  - `scopeMisFctsColaboracion`
  - `scopeEsExempt`
  - `scopeEsErasmus`
  - `scopeEsFct`
  - `scopeEsAval`
  - `scopeEsDual`
  - `scopeNoAval`
  - `getIdColaboracionOptions`

#### `Intranet\Entities\firstByTutor`
- Metodes:
  - `getIdAlumnoOptions`
  - `getIdInstructorOptions`
  - `getTipusAttribute`
  - `getDesdeAttribute`
  - `getDualAttribute`
  - `getExentoAttribute`
  - `getCentroAttribute`
  - `getCicloAttribute`
  - `getQuantsAttribute`
  - `getNalumnesAttribute`
  - `getLalumnesAttribute`
  - `getEmailAttribute`
  - `getContactoAttribute`
  - `getXinstructorAttribute`
  - `getSendCorreoAttribute`


### `app/Entities/FctColaborador.php`

#### `Intranet\Entities\FctColaborador`
- Metodes: cap


### `app/Entities/FctConvalidacion.php`

#### `Intranet\Entities\FctConvalidacion`
- Metodes: cap


### `app/Entities/FctDay.php`

#### `Intranet\Entities\FctDay`
- Metodes:
  - `Colaboracion`

#### `Intranet\Entities\getHorariAttribute`
- Metodes:
  - `getHorariAttribute`
  - `setColaboracionIdAttribute`
    Normalitza valors buits perquè la BBDD no reba '' en una FK integer nullable.


### `app/Entities/Grupo.php`

#### `Intranet\Entities\Grupo`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Alumnos`
- Metodes:
  - `Alumnos`

#### `Intranet\Entities\Actividades`
- Metodes:
  - `Actividades`

#### `Intranet\Entities\Tutor`
- Metodes:
  - `Tutor`

#### `Intranet\Entities\Ciclo`
- Metodes:
  - `Ciclo`

#### `Intranet\Entities\Horario`
- Metodes:
  - `Horario`

#### `Intranet\Entities\Modulos`
- Metodes:
  - `Modulos`

#### `Intranet\Entities\getTodosOptions`
- Metodes:
  - `getTodosOptions`

#### `Intranet\Entities\all`
- Metodes:
  - `getIdCicloOptions`
  - `getTutorOptions`
  - `scopeQTutor`
  - `scopeLargestByAlumnes`
  - `scopeMisGrupos`
  - `scopeMiGrupoModulo`
  - `scopeMatriculado`
  - `scopeDepartamento`
  - `scopeCurso`
  - `getProyectoAttribute`
  - `getXcicloAttribute`
  - `getXtutorAttribute`
  - `getActaAttribute`
  - `getCalidadAttribute`
  - `getMatriculadosAttribute`
  - `getAvalFctAttribute`
  - `getEnDualAttribute`
  - `getAprobFctAttribute`
  - `getAvalProAttribute`
  - `getAprobProAttribute`
  - `getColocadosAttribute`
  - `getExentosAttribute`
  - `getResfctAttribute`
  - `getResempresaAttribute`
  - `getResproAttribute`
  - `getIsSemiAttribute`
  - `getTornAttribute`


### `app/Entities/GrupoTrabajo.php`

#### `Intranet\Entities\GrupoTrabajo`
Model de grup de treball.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\GrupoCreated`
- Metodes: cap

#### `Intranet\Entities\profesores`
- Metodes:
  - `profesores`

#### `Intranet\Entities\Miembro`
- Metodes: cap

#### `Intranet\Entities\Creador`
- Metodes:
  - `Creador`
  - `scopeMisGruposTrabajo`


### `app/Entities/Guardia.php`

#### `Intranet\Entities\Guardia`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Profesor`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\scopeProfesor`
- Metodes:
  - `scopeProfesor`
  - `scopeDiaHora`


### `app/Entities/Hora.php`

#### `Intranet\Entities\Hora`
- Metodes:
  - `Horario`

#### `Intranet\Entities\horasAfectadas`
- Metodes:
  - `horasAfectadas`


### `app/Entities/Horario.php`

#### `Intranet\Entities\Horario`
- Metodes:
  - `Modulo`

#### `Intranet\Entities\Ocupacion`
- Metodes:
  - `Ocupacion`

#### `Intranet\Entities\Grupo`
- Metodes:
  - `Grupo`

#### `Intranet\Entities\Hora`
- Metodes:
  - `Hora`

#### `Intranet\Entities\Mestre`
- Metodes:
  - `Mestre`

#### `Intranet\Entities\scopeProfesor`
- Metodes:
  - `scopeProfesor`
  - `scopeGrup`
  - `scopeDia`
  - `scopeOrden`
  - `scopeGuardia`
  - `scopeGuardiaBiblio`
  - `scopeGuardiaAll`
  - `scopeLectivos`
  - `scopePrimera`
  - `HorarioSemanal`
  - `HorarioGrupo`
  - `getProfesorAttribute`
  - `getXGrupoAttribute`
  - `getXModuloAttribute`
  - `getXOcupacionAttribute`
  - `getDesdeAttribute`
  - `getHastaAttribute`
  - `getModuloOptions`
  - `getIdGrupoOptions`

#### `Intranet\Entities\all`
- Metodes:
  - `getOcupacionOptions`


### `app/Entities/ImportRun.php`

#### `Intranet\Entities\ImportRun`
- Metodes: cap


### `app/Entities/Incidencia.php`

#### `Intranet\Entities\Incidencia`
Model d'incidencies.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\null`
- Metodes:
  - `Creador`

#### `Intranet\Entities\Responsables`
- Metodes:
  - `Responsables`

#### `Intranet\Entities\Tipos`
- Metodes:
  - `Tipos`

#### `Intranet\Entities\Materiales`
- Metodes:
  - `Materiales`

#### `Intranet\Entities\Espacios`
- Metodes:
  - `Espacios`

#### `Intranet\Entities\getEspacioOptions`
- Metodes:
  - `getEspacioOptions`
  - `getTipoOptions`
  - `getEstadoOptions`
  - `getPrioridadOptions`
  - `getFechasolucionAttribute`
  - `getXestadoAttribute`
  - `getXcreadorAttribute`
  - `getXespacioAttribute`
  - `getXresponsableAttribute`
  - `getXtipoAttribute`
  - `getDesCurtaAttribute`
  - `putEstado`
  - `getSubTipoAttribute`


### `app/Entities/Instructor.php`

#### `Intranet\Entities\Instructor`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Fcts`
- Metodes:
  - `Fcts`

#### `Intranet\Entities\Centros`
- Metodes:
  - `Centros`

#### `Intranet\Entities\getXcentrosAttribute`
- Metodes:
  - `getXcentrosAttribute`
  - `getXNcentrosAttribute`
  - `getNfctsAttribute`
  - `getNombreAttribute`
  - `getContactoAttribute`
  - `getIdAttribute`


### `app/Entities/Inventario.php`

#### `Intranet\Entities\Inventario`
- Metodes:
  - `getEspaiAttribute`
  - `getDescripcioAttribute`
  - `getEstatAttribute`
  - `getOrigeAttribute`


### `app/Entities/IpGuardia.php`

#### `Intranet\Entities\IpGuardia`
- Metodes: cap


### `app/Entities/Lote.php`

#### `Intranet\Entities\Lote`
- Metodes:
  - `ArticuloLote`

#### `Intranet\Entities\Departamento`
- Metodes:
  - `Departamento`

#### `Intranet\Entities\getProcedenciaOptions`
- Metodes:
  - `getProcedenciaOptions`
  - `getDepartamentoIdOptions`
  - `Materiales`

#### `Intranet\Entities\ArticuloLote`
- Metodes: cap

#### `Intranet\Entities\getOrigenAttribute`
- Metodes:
  - `getOrigenAttribute`
  - `getEstadoAttribute`
  - `resolveArticuloLoteCount`
  - `resolveMaterialesStats`
    /
  - `getEstatAttribute`
  - `getDepartamentAttribute`


### `app/Entities/Material.php`

#### `Intranet\Entities\Material`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Espacios`
- Metodes:
  - `Espacios`

#### `Intranet\Entities\LoteArticulo`
- Metodes:
  - `LoteArticulo`

#### `Intranet\Entities\getEstadoOptions`
- Metodes:
  - `getEstadoOptions`
  - `getStateAttribute`
  - `getEspacioOptions`
  - `getEspaiAttribute`
  - `getProcedenciaOptions`


### `app/Entities/MaterialBaja.php`

#### `Intranet\Entities\MaterialBaja`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\Material`
- Metodes:
  - `Material`

#### `Intranet\Entities\getDescripcionAttribute`
- Metodes:
  - `getDescripcionAttribute`
  - `getSolicitanteAttribute`
  - `getEspacioAttribute`
  - `getFechaBajaAttribute`
  - `getStateAttribute`
  - `getTipusAttribute`
  - `getNuevoAttribute`


### `app/Entities/Menu.php`

#### `Intranet\Entities\Menu`
- Metodes:
  - `getXrolAttribute`
  - `getXactivoAttribute`
  - `getCategoriaAttribute`
  - `getDescripcionAttribute`
  - `getXajudaAttribute`
    Versió segura de l'ajuda per al grid (sense HTML).


### `app/Entities/Miembro.php`

#### `Intranet\Entities\Miembro`
- Metodes: cap


### `app/Entities/Modulo.php`

#### `Intranet\Entities\Modulo`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Horario`
- Metodes:
  - `Horario`

#### `Intranet\Entities\Grupos`
- Metodes:
  - `Grupos`

#### `Intranet\Entities\scopeMisModulos`
- Metodes:
  - `scopeMisModulos`
  - `scopeModulosGrupo`
  - `scopeLectivos`
  - `getliteralAttribute`


### `app/Entities/Modulo_ciclo.php`

#### `Intranet\Entities\Modulo_ciclo`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Ciclo`
- Metodes:
  - `Ciclo`

#### `Intranet\Entities\Modulo`
- Metodes:
  - `Modulo`

#### `Intranet\Entities\Departamento`
- Metodes:
  - `Departamento`

#### `Intranet\Entities\Programacion`
- Metodes: cap

#### `Intranet\Entities\Profesor`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\getXmoduloAttribute`
- Metodes:
  - `getXmoduloAttribute`
  - `getXdepartamentoAttribute`
  - `getXcicloAttribute`
  - `getAcicloAttribute`
  - `getNombreAttribute`
  - `getIdCicloOptions`
  - `getIdModuloOptions`
  - `getIdDepartamentoOptions`
  - `getEstadoAttribute`
  - `getSituacionAttribute`


### `app/Entities/Modulo_grupo.php`

#### `Intranet\Entities\Modulo_grupo`
- Metodes:
  - `Grupo`

#### `Intranet\Entities\ModuloCiclo`
- Metodes:
  - `ModuloCiclo`

#### `Intranet\Entities\resultados`
- Metodes:
  - `resultados`

#### `Intranet\Entities\scopeCurso`
- Metodes:
  - `scopeCurso`

#### `Intranet\Entities\byCurso`
- Metodes:
  - `getXGrupoAttribute`
  - `getXModuloAttribute`
  - `getXcicloAttribute`
  - `getXdepartamentoAttribute`
  - `getXtornAttribute`
  - `getliteralAttribute`
  - `getseguimientoAttribute`

#### `Intranet\Entities\hasSeguimiento`
- Metodes:
  - `getprofesorAttribute`

#### `Intranet\Entities\profesorNombres`
- Metodes:
  - `getProgramacioLinkAttribute`

#### `Intranet\Entities\programacioLink`
- Metodes: cap


### `app/Entities/Municipio.php`

#### `Intranet\Entities\Municipio`
- Metodes:
  - `Provincia`


### `app/Entities/Notification.php`

#### `Intranet\Entities\Notification`
- Metodes:
  - `getMotivoAttribute`
  - `getEmisorAttribute`
  - `getFechaAttribute`
  - `getLeidoAttribute`
  - `decodedData`


### `app/Entities/Ocupacion.php`

#### `Intranet\Entities\Ocupacion`
- Metodes:
  - `Ocupacion`
  - `getliteralAttribute`


### `app/Entities/OrdenReunion.php`

#### `Intranet\Entities\OrdenReunion`
- Metodes:
  - `Reunion`

#### `Intranet\Entities\scopeForReunion`
- Metodes:
  - `scopeForReunion`
  - `scopeOrderNumber`
  - `firstByReunionAndOrder`
  - `resumenByReunionAndOrder`


### `app/Entities/OrdenTrabajo.php`

#### `Intranet\Entities\OrdenTrabajo`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\Tipos`
- Metodes:
  - `Tipos`

#### `Intranet\Entities\getTipoOptions`
- Metodes:
  - `getTipoOptions`
  - `getEstadoOptions`
  - `getCreatedAtAttribute`
  - `getXestadoAttribute`
  - `getXtipoAttribute`


### `app/Entities/Poll/Actividad.php`

#### `Intranet\Entities\Poll\Actividad`
- Metodes:
  - `loadPoll`


### `app/Entities/Poll/AlumnoFct.php`

#### `Intranet\Entities\Poll\AlumnoFct`
- Metodes:
  - `loadPoll`
  - `loadVotes`
  - `aggregate`
  - `loadGroupVotes`
  - `vista`
  - `has`


### `app/Entities/Poll/Fct.php`

#### `Intranet\Entities\Poll\Fct`
- Metodes:
  - `loadPoll`
  - `interviewed`
  - `keyInterviewed`
  - `loadVotes`
  - `aggregate`
  - `loadGroupVotes`
  - `has`


### `app/Entities/Poll/ModelPoll.php`

#### `Intranet\Entities\Poll\ModelPoll`
- Metodes:
  - `loadPoll`
  - `loadVotes`
  - `loadGroupVotes`
  - `interviewed`
  - `keyInterviewed`
  - `vista`

#### `Intranet\Entities\Poll\aggregate`
- Metodes:
  - `aggregate`
  - `has`


### `app/Entities/Poll/Option.php`

#### `Intranet\Entities\Poll\Option`
- Metodes:
  - `poll`
    An option belongs to one poll

#### `Intranet\Entities\Poll\isPollClosed`
- Metodes:
  - `isPollClosed`
    Check if the option is Closed


### `app/Entities/Poll/PPoll.php`

#### `Intranet\Entities\Poll\PPoll`
- Metodes:
  - `polls`

#### `Intranet\Entities\Poll\options`
- Metodes:
  - `options`
    A poll has many options related to

#### `Intranet\Entities\Poll\getWhatOptions`
- Metodes:
  - `getWhatOptions`


### `app/Entities/Poll/Poll.php`

#### `Intranet\Entities\Poll\Poll`
- Metodes:
  - `Plantilla`
    A poll has many options related to

#### `Intranet\Entities\Poll\getStateAttribute`
- Metodes:
  - `getStateAttribute`
  - `getKeyUserAttribute`
  - `getAnonymousAttribute`
  - `getQueAttribute`
  - `getRemainsAttribute`
  - `getModeloAttribute`
  - `getVistaAttribute`
  - `getIdPPollOptions`
  - `getDesdeAttribute`
  - `getHastaAttribute`


### `app/Entities/Poll/Profesor.php`

#### `Intranet\Entities\Poll\Profesor`
- Metodes:
  - `loadPoll`
  - `loadVotes`

#### `Intranet\Entities\Poll\misModulos`
- Metodes:
  - `loadGroupVotes`

#### `Intranet\Entities\Poll\misGrupos`
- Metodes:
  - `aggregate`
  - `has`
  - `aggregateGrupo`
    /

#### `Intranet\Entities\Poll\all`
- Metodes:
  - `aggregateDepartamento`
    /


### `app/Entities/Poll/Vote.php`

#### `Intranet\Entities\Poll\Vote`
- Metodes:
  - `Option`

#### `Intranet\Entities\Poll\ModuloGrupo`
- Metodes:
  - `ModuloGrupo`

#### `Intranet\Entities\Poll\Actividad`
- Metodes:
  - `Actividad`

#### `Intranet\Entities\Poll\Fct`
- Metodes:
  - `Fct`

#### `Intranet\Entities\Poll\Profesor`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\Poll\Poll`
- Metodes:
  - `Poll`

#### `Intranet\Entities\Poll\getIsValueAttribute`
- Metodes:
  - `getIsValueAttribute`
  - `optionsPoll`
  - `optionsNumericPoll`
  - `scopeMyVotes`
  - `scopeGetVotes`
  - `scopeMyGroupVotes`
  - `scopeAllNumericVotes`
  - `getGrupoAttribute`
  - `getDepartmentoAttribute`
  - `getCicloAttribute`
  - `getQuestionAttribute`
  - `getAnswerAttribute`
  - `getYearAttribute`
  - `getInstructorAttribute`
  - `scopeTipusEnquesta`


### `app/Entities/Poll/VoteAnt.php`

#### `Intranet\Entities\Poll\VoteAnt`
- Metodes:
  - `Option`

#### `Intranet\Entities\Poll\Colaboracion`
- Metodes:
  - `Colaboracion`

#### `Intranet\Entities\Poll\getIsValueAttribute`
- Metodes:
  - `getIsValueAttribute`
  - `getQuestionAttribute`
  - `getAnswerAttribute`


### `app/Entities/Profesor.php`

#### `Intranet\Entities\Profesor`
Model de professor.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Notifiable`
- Metodes:
  - `Comision`

#### `Intranet\Entities\Faltas`
- Metodes:
  - `Faltas`

#### `Intranet\Entities\Actividad`
- Metodes:
  - `Actividad`

#### `Intranet\Entities\Departamento`
- Metodes:
  - `Departamento`

#### `Intranet\Entities\Sustituye`
- Metodes:
  - `Sustituye`

#### `Intranet\Entities\Reserva`
- Metodes:
  - `Reserva`
  - `Horari`

#### `Intranet\Entities\Cotxes`
- Metodes:
  - `Cotxes`

#### `Intranet\Entities\grupos`
- Metodes:
  - `grupos`
  - `Activity`

#### `Intranet\Entities\scopeActivo`
- Metodes:
  - `scopeActivo`
  - `getRol`
  - `scopePlantilla`
  - `scopeTutoresFCT`

#### `Intranet\Entities\byCurso`
- Metodes:
  - `scopeGrupo`
  - `scopeGrupoT`
  - `scopeApiToken`
  - `getfechaIngresoAttribute`
  - `getFechaNacAttribute`
  - `getFechaBajaAttribute`
  - `getIdiomaOptions`
  - `getIdAttribute`
  - `getDepartamentoOptions`
  - `sendPasswordResetNotification`
  - `getXrolAttribute`
  - `getXdepartamentoAttribute`
  - `getLdepartamentoAttribute`
  - `getEntradaAttribute`
  - `getSalidaAttribute`
  - `getHorarioAttribute`
  - `getFullNameAttribute`
  - `getNameFullAttribute`
  - `getSurNamesAttribute`
  - `getShortNameAttribute`
  - `getAhoraAttribute`
  - `getMiJefeAttribute`
  - `getQualitatFile`
  - `getGrupoTutoriaAttribute`

#### `Intranet\Entities\byTutorOrSubstitute`
- Metodes: cap

#### `Intranet\Entities\firstByTutorDual`
- Metodes:
  - `getFileNameAttribute`
  - `getSubstitutAttribute`
  - `getSustituidosAttribute`
  - `getSubstituts`
  - `getHasCertificateAttribute`
  - `getPathCertificateAttribute`


### `app/Entities/Programacion.php`

#### `Intranet\Entities\Programacion`
Model de programacio.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\ModuloCiclo`
- Metodes:
  - `ModuloCiclo`

#### `Intranet\Entities\Departament`
- Metodes:
  - `Departament`

#### `Intranet\Entities\Modulo_ciclo`
- Metodes: cap

#### `Intranet\Entities\Ciclo`
- Metodes:
  - `Ciclo`

#### `Intranet\Entities\Modulo`
- Metodes:
  - `Modulo`

#### `Intranet\Entities\Profesor`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\Grupo`
- Metodes:
  - `Grupo`

#### `Intranet\Entities\Modulo_grupo`
- Metodes: cap

#### `Intranet\Entities\getidModuloCicloOptions`
- Metodes:
  - `getidModuloCicloOptions`
  - `scopeMisProgramaciones`
  - `scopeDepartamento`
  - `nomFichero`
  - `getXdepartamentoAttribute`
  - `getXModuloAttribute`
  - `getXCicloAttribute`
  - `getDescripcionAttribute`
  - `getXnombreAttribute`
  - `getSituacionAttribute`
  - `resolve`


### `app/Entities/Projecte.php`

#### `Intranet\Entities\Projecte`
- Metodes:
  - `Alumno`

#### `Intranet\Entities\Grupo`
- Metodes:
  - `Grupo`

#### `Intranet\Entities\getStatusAttribute`
- Metodes:
  - `getStatusAttribute`
  - `getAlumneAttribute`
  - `getGrupOptions`
  - `getIdAlumneOptions`

#### `Intranet\Entities\byTutorOrSubstitute`
- Metodes:
  - `getDefensaAttribute`


### `app/Entities/Provincia.php`

#### `Intranet\Entities\Provincia`
- Metodes:
  - `Municipio`


### `app/Entities/Recurso.php`

#### `Intranet\Entities\Recurso`
- Metodes:
  - `Reserva`


### `app/Entities/Reserva.php`

#### `Intranet\Entities\Reserva`
- Metodes:
  - `Profesor`


### `app/Entities/Resultado.php`

#### `Intranet\Entities\Resultado`
Model de resultats.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\getEvaluacionOptions`
- Metodes:
  - `getEvaluacionOptions`
  - `getIdModuloGrupoOptions`

#### `Intranet\Entities\misModulos`
- Metodes:
  - `scopeQGrupo`
  - `scopeDepartamento`
  - `scopeTrimestreCurso`
  - `Grupo`

#### `Intranet\Entities\Profesor`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\ModuloGrupo`
- Metodes:
  - `ModuloGrupo`

#### `Intranet\Entities\getModuloAttribute`
- Metodes:
  - `getModuloAttribute`
  - `getXEvaluacionAttribute`
  - `getXProfesorAttribute`


### `app/Entities/Reunion.php`

#### `Intranet\Entities\Reunion`
Model de reunions.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\ReunionCreated`
- Metodes: cap

#### `Intranet\Entities\Creador`
- Metodes:
  - `Creador`

#### `Intranet\Entities\profesores`
- Metodes:
  - `profesores`

#### `Intranet\Entities\withPivot`
- Metodes:
  - `Departament`

#### `Intranet\Entities\alumnos`
- Metodes:
  - `alumnos`

#### `Intranet\Entities\Profesor`
- Metodes: cap

#### `Intranet\Entities\scopeMisReuniones`
- Metodes:
  - `scopeMisReuniones`
  - `scopeConvocante`
  - `scopeTipo`
  - `scopeNumero`
  - `scopeArchivada`
  - `scopeActaFinal`
  - `getTipoOptions`
  - `getIdEspacioOptions`
  - `getNumeroOptions`
  - `getGrupoOptions`
  - `getDepartamentoAttribute`
  - `getAvaluacioAttribute`
  - `getModificableAttribute`
  - `getFechaAttribute`
  - `getUpdatedAtAttribute`
  - `Tipos`
  - `Grupos`

#### `Intranet\Entities\Espacio`
- Metodes:
  - `Espacio`

#### `Intranet\Entities\Responsable`
- Metodes:
  - `Responsable`

#### `Intranet\Entities\getXgrupoAttribute`
- Metodes:
  - `getXgrupoAttribute`

#### `Intranet\Entities\largestByTutor`
- Metodes:
  - `getInformeAttribute`
  - `getIsSemiAttribute`
  - `scopeNext`

#### `Intranet\Entities\firstByTutor`
- Metodes:
  - `getXtipoAttribute`
  - `getXnumeroAttribute`
  - `getAvaluacioFinalAttribute`
  - `getExtraOrdinariaAttribute`
  - `getGrupoClaseAttribute`


### `app/Entities/Setting.php`

#### `Intranet\Entities\Setting`
- Metodes: cap


### `app/Entities/Signatura.php`

#### `Intranet\Entities\Signatura`
- Metodes:
  - `Fct`

#### `Intranet\Entities\Teacher`
- Metodes:
  - `Teacher`

#### `Intranet\Entities\Alumno`
- Metodes:
  - `Alumno`

#### `Intranet\Entities\AlumnoFct`
- Metodes: cap

#### `Intranet\Entities\deleteFile`
- Metodes:
  - `deleteFile`
  - `saveIfNotExists`
  - `getProfesorAttribute`
  - `getAlumneAttribute`
  - `getCentreAttribute`
  - `getPathAttribute`
  - `getFileNameAttribute`
  - `getRouteFileAttribute`
  - `getSimpleRouteFileAttribute`
  - `getEmailAttribute`
  - `getContactoAttribute`
  - `getSignAttribute`
  - `getSendAttribute`
  - `getEstatAttribute`
  - `getClassAttribute`
  - `getFctOptions`
  - `getTipusOptions`
  - `statusService`


### `app/Entities/Solicitud.php`

#### `Intranet\Entities\Solicitud`
- Metodes:
  - `getfechaAttribute`
  - `getfechasolucionAttribute`
  - `getIdOrientadorOptions`
  - `getIdAlumnoOptions`

#### `Intranet\Entities\misGrupos`
- Metodes:
  - `Profesor`

#### `Intranet\Entities\Orientador`
- Metodes:
  - `Orientador`

#### `Intranet\Entities\Alumno`
- Metodes:
  - `Alumno`

#### `Intranet\Entities\getNomAlumAttribute`
- Metodes:
  - `getNomAlumAttribute`
  - `getSituacionAttribute`
  - `getMotiuAttribute`
  - `getQuienAttribute`
  - `scopeListos`


### `app/Entities/Task.php`

#### `Intranet\Entities\Task`
- Metodes:
  - `Profesores`

#### `Intranet\Entities\withPivot`
- Metodes:
  - `scopeMisTareas`
  - `getmyDetailsAttribute`
  - `getValidAttribute`
  - `getLinkAttribute`
  - `getVencimientoAttribute`
  - `getImageAttribute`
  - `getDestinoAttribute`
  - `getDestinatarioOptions`
  - `getActionOptions`
  - `getAccioAttribute`
  - `fillFile`

#### `Intranet\Entities\store`
- Metodes: cap


### `app/Entities/TipoActividad.php`

#### `Intranet\Entities\TipoActividad`
- Metodes:
  - `actividades`

#### `Intranet\Entities\departament`
- Metodes:
  - `departament`

#### `Intranet\Entities\getDepartamentoAttribute`
- Metodes:
  - `getDepartamentoAttribute`


### `app/Entities/TipoExpediente.php`

#### `Intranet\Entities\TipoExpediente`
- Metodes:
  - `expedientes`


### `app/Entities/TipoIncidencia.php`

#### `Intranet\Entities\TipoIncidencia`
- Metodes:
  - `getLiteralAttribute`
  - `getTipoAttribute`
  - `Responsable`

#### `Intranet\Entities\getIdProfesorOptions`
- Metodes:
  - `getIdProfesorOptions`
  - `getTipusOptions`
  - `Rol`
  - `getProfesorAttribute`


### `app/Entities/Tutoria.php`

#### `Intranet\Entities\Tutoria`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Grupos`
- Metodes:
  - `Grupos`

#### `Intranet\Entities\TutoriaGrupo`
- Metodes: cap

#### `Intranet\Entities\getDesdeAttribute`
- Metodes:
  - `getDesdeAttribute`
  - `getHastaAttribute`
  - `getGruposOptions`
  - `getTipoOptions`
  - `getXobligatoriaAttribute`
  - `getGrupoAttribute`
  - `getTiposAttribute`
  - `getEstatAttribute`
  - `getFeedBackAttribute`


### `app/Entities/TutoriaGrupo.php`

#### `Intranet\Entities\TutoriaGrupo`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\getFechaAttribute`
- Metodes:
  - `getFechaAttribute`
  - `getNombreAttribute`
  - `Grupo`



## Serveis

### `app/Application/Activity/ActivityService.php`

#### `Intranet\Application\Activity\ActivityService`
Servei d'aplicació per al registre d'activitat d'usuari.

- Metodes:
  - `record`
    Crea un registre d'activitat i, si hi ha usuari autenticat, el persistix associat a l'autor.
  - `notifyUser`
    Mostra una alerta de confirmació quan el registre està vinculat a un model.


### `app/Application/AlumnoFct/AlumnoFctAvalService.php`

#### `Intranet\Application\AlumnoFct\AlumnoFctAvalService`
- Metodes:
  - `__construct`
  - `latestByProfesor`
  - `apte`
  - `noApte`
  - `noAval`
  - `noProyecto`
  - `nullProyecto`
  - `nuevoProyecto`
  - `toggleInsercion`
  - `requestActaForTutor`
  - `estadistiques`
  - `markStudentsAsActaPending`


### `app/Application/AlumnoFct/AlumnoFctService.php`

#### `Intranet\Application\AlumnoFct\AlumnoFctService`
Servei d'aplicació per a casos d'ús d'AlumnoFct.

- Metodes:
  - `__construct`
  - `all`
    Recupera tots els registres d'alumnat en FCT.
  - `totesFcts`
    Recupera les FCT visibles per al tutor indicat.
  - `find`
    Cerca un registre per identificador.
  - `findOrFail`
    Cerca un registre per identificador o llança excepció.
  - `firstByIdSao`
    Recupera el primer registre associat a un id SAO.
  - `byAlumno`
    Llista tots els registres d'un alumne.
  - `byAlumnoWithA56`
    Llista registres d'un alumne amb annex A56 en curs.
  - `byGrupoEsFct`
    Llista registres d'un grup que són FCT.
  - `byGrupoEsDual`
    Llista registres d'un grup que són dual.
  - `reassignProfesor`
    Reassigna en bloc el tutor responsable.
  - `avalDistinctAlumnoIdsByProfesor`
    Recupera identificadors d'alumnes amb FCT avaluable del tutor.
  - `latestAvalByAlumnoAndProfesor`
    Recupera l'últim registre avaluable d'un alumne per tutor.
  - `avaluablesNoAval`
    Recupera registres avaluables no tancats en acta.


### `app/Application/AlumnoFct/AlumnoFctSignatureService.php`

#### `Intranet\Application\AlumnoFct\AlumnoFctSignatureService`
Casos d'ús de signatures vinculades a AlumnoFct.

- Metodes:
  - `hasAnySignature`
    Determina si el registre té alguna signatura associada.
  - `findByType`
    Cerca la signatura per tipus i estat de signatura.
  - `routeFile`
    Construeix la ruta física de l'annex per al registre.


### `app/Application/Colaboracion/ColaboracionQueryService.php`

#### `Intranet\Application\Colaboracion\ColaboracionQueryService`
Consultes de lectura per al domini de col·laboracions.

- Metodes:
  - `myColaboraciones`
    /
  - `relatedByCenterDepartment`
    /
  - `groupedActivitiesByColaboracion`
    /
  - `attachRelatedAndContacts`
    /


### `app/Application/Colaboracion/ColaboracionService.php`

#### `Intranet\Application\Colaboracion\ColaboracionService`
Casos d'ús d'aplicació per al panell de col·laboracions.

- Metodes:
  - `__construct`
  - `panelListingByTutor`
    /
  - `resolvePanelTitle`
    /


### `app/Application/Comision/ComisionService.php`

#### `Intranet\Application\Comision\ComisionService`
Casos d'ús d'aplicació per al domini de comissions.

- Metodes:
  - `__construct`
  - `pendingAuthorization`
    /
  - `find`
  - `findOrFail`
  - `byDay`
    /
  - `withProfesorByDay`
    /
  - `authorizationApiList`
    /
  - `prePayByProfesor`
    /
  - `hasPendingUnpaidByProfesor`
  - `setEstado`
  - `attachFct`
  - `detachFct`


### `app/Application/Documento/DocumentoFormService.php`

#### `Intranet\Application\Documento\DocumentoFormService`
- Metodes:
  - `updateNota`
  - `projectDefaults`
  - `qualitatDefaults`


### `app/Application/Documento/DocumentoLifecycleService.php`

#### `Intranet\Application\Documento\DocumentoLifecycleService`
Servei de cicle de vida per a Documento.

- Metodes:
  - `delete`
    Esborra un document i, si aplica, també el fitxer físic associat.
  - `mustDeleteFile`


### `app/Application/Empresa/EmpresaService.php`

#### `Intranet\Application\Empresa\EmpresaService`
Casos d'ús d'aplicació per al domini d'empreses.

- Metodes:
  - `__construct`
  - `listForGrid`
    /
  - `findForShow`
  - `colaboracionIdsForTutorCycle`
    /
  - `departmentCycles`
    /
  - `convenioList`
    /
  - `socialConcertList`
    /
  - `erasmusList`
    /
  - `saveFromRequest`
    Persisteix una empresa (alta o edició) amb validació i normalització.
  - `createCenter`
    Crea el centre inicial d'una empresa.
  - `createColaboration`
    Crea col·laboració inicial associada al cicle del tutor.
  - `fillMissingCenterData`
    Propaga camps bàsics d'empresa a centres incomplets.
  - `normalizeRequest`
    Normalitza checkbox i CIF en l'entrada del formulari d'empresa.


### `app/Application/Expediente/ExpedienteService.php`

#### `Intranet\Application\Expediente\ExpedienteService`
Casos d'ús d'aplicació per al domini d'expedients.

- Metodes:
  - `__construct`
  - `find`
  - `findOrFail`
  - `createFromRequest`
  - `updateFromRequest`
  - `pendingAuthorization`
    /
  - `readyToPrint`
    /
  - `allTypes`
    /


### `app/Application/Falta/FaltaService.php`

#### `Intranet\Application\Falta\FaltaService`
Casos d'ús d'aplicació per al domini de faltes de professorat.

- Metodes:
  - `create`

#### `Intranet\Application\Falta\markLeave`
- Metodes:
  - `update`
  - `init`

#### `Intranet\Application\Falta\sendTutorEmail`
- Metodes:
  - `alta`

#### `Intranet\Application\Falta\reactivate`
- Metodes: cap


### `app/Application/FaltaItaca/FaltaItacaWorkflowService.php`

#### `Intranet\Application\FaltaItaca\FaltaItacaWorkflowService`
- Metodes:
  - `findElements`
  - `monthlyReportFileName`
  - `deletePreviousMonthlyReport`
  - `resolveByAbsenceId`
  - `refuseByAbsenceId`


### `app/Application/Fct/FctCertificateService.php`

#### `Intranet\Application\Fct\FctCertificateService`
- Metodes:
  - `colaboradorCertificateData`
  - `streamColaboradorCertificate`

#### `Intranet\Application\Fct\hazPdf`
- Metodes: cap


### `app/Application/Fct/FctService.php`

#### `Intranet\Application\Fct\FctService`
Casos d'ús d'aplicació per al domini FCT.

- Metodes:
  - `__construct`
  - `find`
  - `findOrFail`
  - `panelListingByProfesor`
    /
  - `setInstructor`
  - `findBySignature`
  - `createFromRequest`
  - `attachAlumnoFromStoreRequest`
  - `attachAlumnoSimple`
  - `detachAlumno`
  - `addColaborador`
  - `deleteColaborador`
  - `updateColaboradorHoras`
  - `setCotutor`
  - `empresaIdByFct`
  - `deleteFct`


### `app/Application/Grupo/GrupoService.php`

#### `Intranet\Application\Grupo\GrupoService`
Servei d'aplicació per a casos d'ús relacionats amb grups.

- Metodes:
  - `__construct`
  - `create`
    /
  - `find`
  - `all`
    /
  - `qTutor`
    /
  - `firstByTutor`
  - `largestByTutor`
  - `byCurso`
    /
  - `byDepartamento`
    /
  - `tutoresDniList`
    /
  - `reassignTutor`
  - `misGrupos`
    /
  - `misGruposByProfesor`
    /
  - `withActaPendiente`
    /
  - `byTutorOrSubstitute`
    Retorna el primer grup on el professor és tutor o substitueix al tutor.
  - `withStudents`
    /
  - `firstByTutorDual`
  - `byCodes`
    /
  - `allWithTutorAndCiclo`
    /
  - `misGruposWithCiclo`
    Retorna els grups del professor amb la relació de cicle carregada.


### `app/Application/Grupo/GrupoWorkflowService.php`

#### `Intranet\Application\Grupo\GrupoWorkflowService`
- Metodes:
  - `assignMissingCiclo`
  - `selectedStudentsPlainText`
  - `sendFolCertificates`
    /


### `app/Application/Horario/HorarioService.php`

#### `Intranet\Application\Horario\HorarioService`
Casos d'ús d'aplicació per al domini d'horaris.

- Metodes:
  - `__construct`
  - `semanalByProfesor`
    /
  - `semanalByGrupo`
    /
  - `lectivosByDayAndSesion`
    /
  - `countByProfesorAndDay`
  - `guardiaAllByDia`
    /
  - `guardiaAllByProfesorAndDiaAndSesiones`
    /
  - `guardiaAllByProfesorAndDia`
    /
  - `guardiaAllByProfesor`
    /
  - `firstByProfesorDiaSesion`
  - `byProfesor`
    /
  - `byProfesorWithRelations`
    /
  - `lectivasByProfesorAndDayOrdered`
    /
  - `reassignProfesor`
  - `deleteByProfesor`
  - `gruposByProfesor`
    /
  - `gruposByProfesorDiaAndSesiones`
    /
  - `profesoresByGruposExcept`
    /
  - `primeraByProfesorAndDateOrdered`
    /
  - `firstByModulo`
  - `byProfesorDiaOrdered`
    /
  - `distinctModulos`
    /
  - `create`
    /
  - `forProgramacionImport`
    /
  - `firstForDepartamentoAsignacion`
  - `situacionAhora`
    Retorna la situació actual del professor segons el seu horari.


### `app/Application/Import/Concerns/SharedImportFieldTransformers.php`

#### `Intranet\Application\Import\Concerns\SharedImportFieldTransformers`
- Metodes:
  - `emailConselleriaImport`
  - `emailProfesorImport`
  - `aleatorio`
  - `hazDNI`
  - `getFechaFormatoIngles`
    /
  - `cifrar`
  - `digitos`
  - `hazDomicilio`
  - `creaCodigoProfesor`

#### `Intranet\Application\Import\Concerns\usedCodigosBetween`
- Metodes:
  - `crea_codigo_profesor`


### `app/Application/Import/GeneralImportExecutionService.php`

#### `Intranet\Application\Import\GeneralImportExecutionService`
- Metodes:
  - `__construct`
  - `handlePreImport`
  - `handlePostImport`
  - `importTable`
    /
  - `createRecordByClass`
    /
  - `createHorario`
    /
  - `createModuloCicloAndGrupoFromHorarios`
  - `createModuloCiclo`
  - `markAllAlumnosAsBaja`
  - `cleanupSustituciones`
  - `assignDepartamentoByHorario`
  - `disableProfesores`
  - `removeBajaAlumnosFromGroups`
  - `markAllGruposWithoutTutor`
  - `deleteBajaGrupos`
  - `normalizeEmptyTutor`
  - `truncateTables`
    /
  - `cloneTable`
  - `deleteBlankRecords`
  - `restoreAlumnosGrupoCopy`
  - `keepLatestHorarioPlantilla`
  - `setForeignKeys`
  - `preloadExistingRecords`
    /
  - `normalizeCacheKey`
  - `loadEstadoFromHorarioJson`


### `app/Application/Import/ImportSchemaProvider.php`

#### `Intranet\Application\Import\ImportSchemaProvider`
Proveeix l'esquema de mapatge XML -> camps de BD per als imports.

- Metodes:
  - `forGeneralImport`
    /
  - `forTeacherImport`
    /


### `app/Application/Import/ImportService.php`

#### `Intranet\Application\Import\ImportService`
Servei d'aplicació per a operacions comunes d'importació.

- Metodes:
  - `resolveXmlFile`
    Valida i retorna el fitxer XML d'importació.
  - `runWithExtendedTimeout`
    Executa una importació amb timeout ampliat.
  - `isFirstImport`


### `app/Application/Import/ImportWorkflowService.php`

#### `Intranet\Application\Import\ImportWorkflowService`
Servei d'orquestració del flux d'importació.

- Metodes:
  - `__construct`
  - `executeXmlImport`
    Executa el recorregut de taules d'un XML d'importació.
  - `resolveXmlPath`
  - `executeXmlImportWithHooks`
    Executa el recorregut amb pipeline pre/in/post.
  - `executeXmlImportSimple`
    Executa el recorregut amb pipeline simple.
  - `assignTutores`
  - `applyTutorRoleRules`


### `app/Application/Import/ImportXmlHelperService.php`

#### `Intranet\Application\Import\ImportXmlHelperService`
Utilitats compartides per a parseig i validació de camps XML.

- Metodes:
  - `extractField`
  - `passesFilter`
    /
  - `findMissingRequired`
    /
  - `invokeContextMethod`
    /


### `app/Application/Import/TeacherImportExecutionService.php`

#### `Intranet\Application\Import\TeacherImportExecutionService`
- Metodes:
  - `__construct`
  - `clearTeacherHorarios`
  - `importTable`
    /
  - `createRecordByClass`
    /
  - `preloadExistingRecords`
    /
  - `normalizeCacheKey`


### `app/Application/Instructor/InstructorWorkflowService.php`

#### `Intranet\Application\Instructor\InstructorWorkflowService`
- Metodes:
  - `searchForTutorFcts`
  - `empresaIdFromInstructor`
  - `upsertAndAttachToCentro`
  - `detachFromCentroAndDeleteIfOrphan`
  - `copyInstructorToCentro`
  - `ultimaFecha`


### `app/Application/Menu/MenuService.php`

#### `Intranet\Application\Menu\MenuService`
Servei d'aplicació per construir i cachejar menús de navegació.

- Metodes:
  - `make`
    Construeix el menú per nom i usuari.
  - `clearCache`
    Neteja el cache de menú (global o filtrat per nom/dni).
  - `listForGrid`
    Retorna menús ordenats per al grid, normalitzant ordres abans.
  - `saveFromRequest`
    Persisteix un menú des del request.
  - `copy`
    Duplica un menú dins del mateix grup/submenú.
  - `moveUp`
    Mou un menú cap amunt dins del bloc actual.
  - `moveDown`
    Mou un menú cap avall dins del bloc actual.
  - `build`
    Construeix l'estructura de menú a partir dels registres actius.
  - `tipoUrl`
    Determina si una URL és externa o interna per a StydeMenu.
  - `cacheKey`
    Compon la clau de cache per menú i usuari.
  - `registerCacheKey`
    Registra la clau en l'índex global per permetre invalidació selectiva.
  - `isAdminUser`
    Comprovació local de rol admin sobre l'usuari rebut.
  - `renderMenu`
    Renderitza l'arbre de menú amb el markup legacy del tema bootstrap.
  - `translateMenuTitle`
    Resol la traducció d'un ítem de menú amb fallback compatible amb legacy.
  - `sortForGrid`
    Reordena pares i fills per mantindre seqüència contínua.


### `app/Application/Notification/NotificationInboxService.php`

#### `Intranet\Application\Notification\NotificationInboxService`
Casos d'ús per a la safata de notificacions d'usuari.

- Metodes:
  - `__construct`
  - `listForUser`
    /
  - `markAsRead`
  - `markAllAsRead`
  - `deleteAll`
  - `deleteById`
  - `findForShow`
  - `profesores`

#### `Intranet\Application\Notification\profesorService`
- Metodes:
  - `resolveNotifiable`
  - `hydratePayload`


### `app/Application/Poll/PollWorkflowService.php`

#### `Intranet\Application\Poll\PollWorkflowService`
- Metodes:
  - `prepareSurvey`
  - `saveSurvey`
  - `myVotes`
  - `allVotes`
  - `userKey`
  - `loadPreviousVotes`
  - `saveVote`
  - `initValues`


### `app/Application/Profesor/ProfesorService.php`

#### `Intranet\Application\Profesor\ProfesorService`
Casos d'ús d'aplicació per al domini de professorat.

- Metodes:
  - `__construct`
  - `plantillaOrderedWithDepartamento`
    /
  - `activosByDepartamentosWithHorario`
    /
  - `activosOrdered`
    /
  - `all`
    /
  - `plantilla`
    /
  - `plantillaByDepartamento`
    /
  - `activos`
    /
  - `byDepartamento`
    /
  - `byGrupo`
    /
  - `byGrupoTrabajo`
    /
  - `byDnis`
    /
  - `find`
  - `findOrFail`
  - `findBySustituyeA`
  - `findByCodigo`
  - `findByApiToken`
  - `findByEmail`
  - `plantillaOrderedByDepartamento`
    /
  - `plantillaForResumen`
    /
  - `allOrderedBySurname`
    /
  - `clearFechaBaja`
  - `countByCodigo`
  - `usedCodigosBetween`
    /
  - `create`
    /
  - `withSustituyeAssigned`
    /


### `app/Domain/AlumnoFct/AlumnoFctRepositoryInterface.php`

#### `Intranet\Domain\AlumnoFct\AlumnoFctRepositoryInterface`
Contracte d'accés a dades d'AlumnoFct.

- Metodes:
  - `all`
    Recupera tots els registres d'alumnat en FCT.
  - `totesFcts`
    Recupera les FCT visibles per a un tutor (incloent substitucions).
  - `find`
    Cerca un registre per identificador.
  - `findOrFail`
    Cerca un registre per identificador o llança excepció.
  - `firstByIdSao`
    Recupera el primer registre associat a un id SAO.
  - `byAlumno`
    Llista tots els registres d'un alumne.
  - `byAlumnoWithA56`
    Llista registres d'un alumne amb annex A56 en curs.
  - `byGrupoEsFct`
    Llista registres d'un grup que són FCT (no dual).
  - `byGrupoEsDual`
    Llista registres d'un grup que són dual.
  - `reassignProfesor`
    Reassigna tutor responsable en bloc.
  - `avalDistinctAlumnoIdsByProfesor`
    Recupera els identificadors d'alumnat amb FCT avaluable del tutor.
  - `latestAvalByAlumnoAndProfesor`
    Recupera l'últim registre avaluable d'un alumne per tutor.
  - `avaluablesNoAval`
    Recupera registres avaluables que encara no estan tancats en acta.


### `app/Domain/Comision/ComisionRepositoryInterface.php`

#### `Intranet\Domain\Comision\ComisionRepositoryInterface`
Contracte de persistència per al domini de Comissió.

- Metodes:
  - `find`
  - `findOrFail`
  - `byDay`
    /
  - `withProfesorByDay`
    /
  - `pendingAuthorization`
    /
  - `authorizationApiList`
    Llistat per a l'API d'autorització (inclou nom concatenat de professor/a).
  - `authorizeAllPending`
  - `prePayByProfesor`
    /
  - `setEstado`
  - `hasPendingUnpaidByProfesor`
  - `attachFct`
  - `detachFct`


### `app/Domain/Empresa/EmpresaRepositoryInterface.php`

#### `Intranet\Domain\Empresa\EmpresaRepositoryInterface`
Contracte de persistència per al domini d'empreses.

- Metodes:
  - `listForGrid`
    /
  - `findForShow`
  - `colaboracionIdsByCycleAndCenters`
    /
  - `cyclesByDepartment`
    /
  - `convenioList`
    /
  - `socialConcertList`
    /
  - `erasmusList`
    /


### `app/Domain/Expediente/ExpedienteRepositoryInterface.php`

#### `Intranet\Domain\Expediente\ExpedienteRepositoryInterface`
Contracte de persistència per al domini d'expedients.

- Metodes:
  - `find`
  - `findOrFail`
  - `createFromRequest`
  - `updateFromRequest`
  - `pendingAuthorization`
    /
  - `readyToPrint`
    /
  - `allTypes`
    /


### `app/Domain/FaltaProfesor/FaltaProfesorRepositoryInterface.php`

#### `Intranet\Domain\FaltaProfesor\FaltaProfesorRepositoryInterface`
Contracte de persistència per al domini de fitxatges de professorat.

- Metodes:
  - `lastTodayByProfesor`
  - `hasFichadoOnDay`
  - `createEntry`
  - `closeExit`
  - `byDayAndProfesor`
    /
  - `rangeByProfesor`
    /


### `app/Domain/Fct/FctRepositoryInterface.php`

#### `Intranet\Domain\Fct\FctRepositoryInterface`
Contracte de persistència per al domini FCT.

- Metodes:
  - `find`
  - `findOrFail`
  - `firstByColaboracionAsociacionInstructor`
  - `panelListingByProfesor`
    /
  - `save`
  - `create`
  - `attachAlumno`
  - `detachAlumno`
  - `saveColaborador`
  - `deleteColaborador`
  - `updateColaboradorHoras`
  - `setCotutor`
  - `empresaIdByFct`


### `app/Domain/Grupo/GrupoRepositoryInterface.php`

#### `Intranet\Domain\Grupo\GrupoRepositoryInterface`
Contracte d'accés a dades de l'agregat Grupo.

- Metodes:
  - `create`
    /
  - `find`
  - `all`
    /
  - `qTutor`
    /
  - `firstByTutor`
  - `largestByTutor`
  - `byCurso`
    /
  - `byDepartamento`
    /
  - `tutoresDniList`
    /
  - `reassignTutor`
  - `misGrupos`
    /
  - `misGruposByProfesor`
    /
  - `withActaPendiente`
    /
  - `byTutorOrSubstitute`
    Retorna el primer grup on el professor és tutor o tutor substituït.
  - `withStudents`
    /
  - `firstByTutorDual`
  - `byCodes`
    /
  - `allWithTutorAndCiclo`
    /
  - `misGruposWithCiclo`
    /


### `app/Domain/Horario/HorarioRepositoryInterface.php`

#### `Intranet\Domain\Horario\HorarioRepositoryInterface`
Contracte de persistència per a l'agregat Horario.

- Metodes:
  - `semanalByProfesor`
    /
  - `semanalByGrupo`
    /
  - `lectivosByDayAndSesion`
    /
  - `countByProfesorAndDay`
  - `guardiaAllByDia`
    /
  - `guardiaAllByProfesorAndDiaAndSesiones`
    /
  - `guardiaAllByProfesorAndDia`
    /
  - `guardiaAllByProfesor`
    /
  - `firstByProfesorDiaSesion`
  - `byProfesor`
    /
  - `byProfesorWithRelations`
    /
  - `lectivasByProfesorAndDayOrdered`
    /
  - `reassignProfesor`
  - `deleteByProfesor`
  - `gruposByProfesor`
    /
  - `gruposByProfesorDiaAndSesiones`
    /
  - `profesoresByGruposExcept`
    /
  - `primeraByProfesorAndDateOrdered`
    /
  - `firstByModulo`
  - `byProfesorDiaOrdered`
    /
  - `distinctModulos`
    /
  - `create`
    /
  - `forProgramacionImport`
    /
  - `firstForDepartamentoAsignacion`


### `app/Domain/Profesor/ProfesorRepositoryInterface.php`

#### `Intranet\Domain\Profesor\ProfesorRepositoryInterface`
Contracte de persistència per a l'agregat Profesor.

- Metodes:
  - `plantillaOrderedWithDepartamento`
    /
  - `activosByDepartamentosWithHorario`
    /
  - `activosOrdered`
    /
  - `all`
    /
  - `plantilla`
    /
  - `plantillaByDepartamento`
    /
  - `activos`
    /
  - `byDepartamento`
    /
  - `byGrupo`
    /
  - `byGrupoTrabajo`
    /
  - `byDnis`
    /
  - `find`
  - `findOrFail`
  - `findBySustituyeA`
  - `findByCodigo`
  - `findByApiToken`
  - `findByEmail`
  - `plantillaOrderedByDepartamento`
    /
  - `plantillaForResumen`
    /
  - `allOrderedBySurname`
    /
  - `clearFechaBaja`
  - `countByCodigo`
  - `usedCodigosBetween`
    /
  - `create`
    /
  - `withSustituyeAssigned`
    /


### `app/Services/Auth/ApiSessionTokenService.php`

#### `Intranet\Services\Auth\ApiSessionTokenService`
Gestiona el token Sanctum de sessió web per al professorat.

- Metodes:
  - `issueForProfesor`
    Emet un token Sanctum i el guarda en sessió per a ús del client web.
  - `revokeCurrentFromSession`
    Revoca el token actual emmagatzemat en sessió i neteja claus de sessió.
  - `currentToken`
    Retorna el token de sessió actual, si existeix.


### `app/Services/Auth/JWTTokenService.php`

#### `Intranet\Services\Auth\JWTTokenService`
- Metodes:
  - `__construct`

#### `Intranet\Services\Auth\createTokenProgramacio`
- Metodes:
  - `createTokenProgramacio`
  - `getTokenLink`
  - `role`
  - `turno`


### `app/Services/Auth/PerfilService.php`

#### `Intranet\Services\Auth\PerfilService`
- Metodes:
  - `__construct`
  - `carregarDadesProfessor`
  - `carregarDadesAlumne`


### `app/Services/Auth/RemoteLoginService.php`

#### `Intranet\Services\Auth\RemoteLoginService`
- Metodes:
  - `login`


### `app/Services/Auth/VioletHasher.php`

#### `Intranet\Services\Auth\VioletHasher`
- Metodes:
  - `dniHash`


### `app/Services/Automation/SeleniumService.php`

#### `Intranet\Services\Automation\SeleniumService`
- Metodes:
  - `__construct`
  - `getDriverSelenium`
    /
  - `getDriver`
    /
  - `quit`
  - `loginSAO`
    /
  - `loginItaca`
    /
  - `restartSelenium`
  - `fill`
  - `waitAndClick`
  - `gTPersonalLlist`
  - `closeNoticias`


### `app/Services/Calendar/CalendarService.php`

#### `Intranet\Services\Calendar\CalendarService`
- Metodes:
  - `build`
    /
  - `render`


### `app/Services/Calendar/GoogleCalendarService.php`

#### `Intranet\Services\Calendar\GoogleCalendarService`
- Metodes:
  - `__construct`
  - `getClient`
  - `getCalendar`
  - `addEvent`
  - `dateToGoogle`
  - `saveEvents`


### `app/Services/Calendar/MeetingOrderGenerateService.php`

#### `Intranet\Services\Calendar\MeetingOrderGenerateService`
- Metodes:
  - `__construct`
  - `exec`
  - `isOrderAdvanced`
  - `storeAdvancedItems`
  - `getResumenAdvanced`
  - `storeItem`


### `app/Services/Document/AttachedFileService.php`

#### `Intranet\Services\Document\AttachedFileService`
- Metodes:
  - `safeFile`
  - `saveLink`
  - `save`
  - `delete`
  - `saveExistingFile`
  - `moveAndPreserveDualFiles`
  - `deleteNonDualFiles`


### `app/Services/Document/CreateOrUpdateDocumentAction.php`

#### `Intranet\Services\Document\CreateOrUpdateDocumentAction`
- Metodes:
  - `fromRequest`
  - `fromArray`
  - `build`
  - `applyDefaults`
  - `firstAvailable`
  - `resolveElementoId`


### `app/Services/Document/DocumentAccessChecker.php`

#### `Intranet\Services\Document\DocumentAccessChecker`
- Metodes:
  - `isAllowed`


### `app/Services/Document/DocumentContext.php`

#### `Intranet\Services\Document\DocumentContext`
- Metodes:
  - `__construct`
  - `document`
  - `link`
  - `isFile`


### `app/Services/Document/DocumentPathService.php`

#### `Intranet\Services\Document\DocumentPathService`
- Metodes:
  - `resolvePath`
  - `exists`
  - `mimeType`
  - `responseFile`
  - `existsPath`
  - `responseFromPath`


### `app/Services/Document/DocumentResolver.php`

#### `Intranet\Services\Document\DocumentResolver`
- Metodes:
  - `resolve`
  - `findDocument`
  - `getFileIfExistFromModel`


### `app/Services/Document/DocumentResponder.php`

#### `Intranet\Services\Document\DocumentResponder`
- Metodes:
  - `__construct`
  - `respond`


### `app/Services/Document/DocumentService.php`

#### `Intranet\Services\Document\DocumentService`
Servei per generar documents (PDF, ZIP o correus) a partir de la configuració

- Metodes:
  - `__construct`
    DocumentService constructor.
  - `__get`
  - `load`
    Retorna els elements carregats pel Finder.
  - `render`
    Renderitza el document segons la configuració (email o impressió).
  - `mail`
    Envia el document per correu utilitzant la configuració del Finder.
  - `generatePdfFromView`
    Genera un PDF a partir d'una vista Blade.

#### `Intranet\Services\Document\hazZip`
- Metodes: cap

#### `Intranet\Services\Document\hazPdf`
- Metodes:
  - `generatePdfFromTemplate`
    Genera un PDF a partir d'una plantilla (`printResource`).
  - `generateSignedPdf`
    Genera un PDF signat si està activada la signatura digital.
  - `generateMultiplePdfs`
    Genera diversos PDFs i els empaqueta si cal.

#### `Intranet\Services\Document\merge`
- Metodes:
  - `generateZip`
    Genera un ZIP amb els PDFs indicats i retorna una resposta de fitxer.
  - `normalizePdfPaths`
    Normalitza un conjunt d'entrades a rutes de fitxers PDF existents.


### `app/Services/Document/ExcelService.php`

#### `Intranet\Services\Document\ExcelService`
- Metodes:
  - `__construct`
    /
  - `render`


### `app/Services/Document/FDFPrepareService.php`

#### `Intranet\Services\Document\FDFPrepareService`
Servei per preparar PDFs de plantilles FDF i concatenar fitxers resultants.

- Metodes:
  - `exec`
    Genera un PDF a partir d'un recurs imprimible i retorna la ruta absoluta.

#### `Intranet\Services\Document\fillForResource`
- Metodes:
  - `joinPDFs`
    Concatena diversos PDFs i retorna la ruta relativa del resultat.

#### `Intranet\Services\Document\merge`
- Metodes: cap


### `app/Services/Document/PdfFormService.php`

#### `Intranet\Services\Document\PdfFormService`
Encapsula les operacions de formularis PDF basades en pdftk via CLI.

- Metodes:
  - `fillAndSave`
    Emplena una plantilla PDF i desa el resultat en un fitxer.
  - `fillAndSend`
    Emplena una plantilla PDF i l'envia al navegador.
  - `fillForResource`
    Emplena una plantilla i aplica el flux de preparació utilitzat pels recursos FDF.
  - `runCommand`
    Executa un comandament de procés i valida l'eixida.
  - `createTempFdf`
    Crea un fitxer temporal FDF amb les dades del formulari.
  - `escapeFdfString`
    Escapa valors per a cadenes literals en FDF.
  - `resolveTemplatePath`
    Resol una ruta de plantilla relativa o absoluta.
  - `binary`
    Retorna el binari pdftk configurat.
  - `ensureOutputDirectory`
    Crea el directori de destí si no existix.


### `app/Services/Document/PdfMergeService.php`

#### `Intranet\Services\Document\PdfMergeService`
Servei per concatenar múltiples PDFs en un únic document amb FPDI.

- Metodes:
  - `merge`
    Concatena els fitxers PDF indicats i guarda el resultat en la ruta de destí.


### `app/Services/Document/PdfService.php`

#### `Intranet\Services\Document\PdfService`
Servei de generació de PDFs i ZIPs.

- Metodes:
  - `footerText`
    Calcula el text del peu segons el document.
  - `hazPdf`
    Genera un PDF amb el driver indicat.
  - `hazZip`
    Genera un ZIP amb PDFs per a cada element.
  - `hazSnappyPdf`
    Genera un PDF amb Snappy.
  - `hazDomPdf`
    Genera un PDF amb DomPDF.


### `app/Services/Document/TipoDocumentoService.php`

#### `Intranet\Services\Document\TipoDocumentoService`
- Metodes:
  - `allPestana`
  - `allDocuments`
  - `allRol`
  - `rol`
  - `all`
  - `get`


### `app/Services/Document/TipoReunionService.php`

#### `Intranet\Services\Document\TipoReunionService`
- Metodes:
  - `__construct`
  - `__get`
  - `__isset`
  - `allSelect`
  - `find`
  - `all`
  - `literal`
  - `get`


### `app/Services/Document/ZipService.php`

#### `Intranet\Services\Document\ZipService`
- Metodes:
  - `exec`
    Crea un fitxer ZIP amb els paths indicats i retorna el path relatiu dins de storage/tmp.


### `app/Services/General/AutorizacionPrintService.php`

#### `Intranet\Services\General\AutorizacionPrintService`
Servei d'impressió en lot per a fluxos d'autorització.

- Metodes:
  - `__construct`
  - `imprimir`
    Executa la generació de document i canvi d'estat en lot.


### `app/Services/General/AutorizacionStateService.php`

#### `Intranet\Services\General\AutorizacionStateService`
Servei d'aplicació per a transicions d'estat en fluxos d'autorització.

- Metodes:
  - `__construct`
  - `cancel`
    Mou l'element a estat de cancel·lació.
  - `init`
    Inicialitza l'element a l'estat configurat pel caller.
  - `resolve`
    /
  - `accept`
    /
  - `resign`
    /
  - `refuse`
    /
  - `setState`
    Assigna un estat concret i retorna si l'operació és correcta.
  - `transitionWithResult`
    Executa una transició i retorna els estats per a la capa de presentació.


### `app/Services/General/GestorService.php`

#### `Intranet\Services\General\GestorService`
- Metodes:
  - `__construct`
  - `save`
  - `render`
  - `saveDocument`


### `app/Services/General/StateService.php`

#### `Intranet\Services\General\StateService`
Servei per gestionar canvis d'estat d'un model i accions associades.

- Metodes:
  - `__construct`
    Crea el servei amb un model o una classe.
  - `putEstado`
    Canvia l'estat i executa accions associades.
  - `makeDocument`
    Guarda el document associat si hi ha fitxer.
  - `dateResolve`
    Assigna la data de resolucio i el missatge al camp configurat.
  - `resolve`
    Resol l'element segons la configuracio del model.
  - `refuse`
    Rebutja l'element segons la configuracio del model.
  - `_print`
    Marca l'element com a imprimit segons la configuracio del model.
  - `getEstado`
    Retorna l'estat actual de l'element.
  - `normalizeStatesElement`
    Normalitza la configuracio del model.
  - `getConfiguredState`
    Retorna un estat configurat o null si falta.
  - `makeAll`
    Modifica l'estat d'un conjunt d'elements
  - `makeLink`
    Enllaça múltiples elements a un document.


### `app/Services/HR/FitxatgeService.php`

#### `Intranet\Services\HR\FitxatgeService`
Servei de gestio de fitxatges.

- Metodes:
  - `__construct`
  - `fitxar`
  - `fitxaDiaManual`
  - `hasFichado`
  - `isInside`
  - `sessionEntry`
  - `sessionExit`
  - `wasInsideAt`
  - `registrosEntreFechas`
    /


### `app/Services/HR/PresenciaResumenService.php`

#### `Intranet\Services\HR\PresenciaResumenService`
- Metodes:
  - `__construct`
  - `resumenDia`
    Resum d'un dia per a un conjunt de professors.
  - `firstEntry`
  - `weekdayLetter`
  - `buildPlannedSlotsFromDbRows`
  - `sanitizeFichajes`
    El professor a vegades marca una "entrada" quan en realitat està eixint.
  - `lastPlannedEnd`
  - `hasOpenStay`
  - `buildStayIntervals`
  - `buildExceptionIntervals`
  - `computeCoverage`
  - `decideStatus`
  - `clampToDay`
  - `overlapMinutes`
  - `overlapWithGrace`
  - `mergeOverlappingOrClose`
  - `mergeTouchingByType`
  - `mergeByType`


### `app/Services/Mail/EmailPostSendService.php`

#### `Intranet\Services\Mail\EmailPostSendService`
Accions post-enviament per a correus.

- Metodes:
  - `handleAnnexeIndividual`
    Actualitza l'estat d'enviament d'annexos individuals.
  - `markFctEmailSent`
    Marca el correu enviat per a FCT.
  - `updateAlumnoFct`
    /
  - `updateSignatura`
    /


### `app/Services/Mail/FctMailService.php`

#### `Intranet\Services\Mail\FctMailService`
- Metodes:
  - `getMailById`
    Obté un correu per ID.
  - `getMailByRequest`
    Obté un correu a partir d'una petició.
  - `generateMail`
    Genera el correu a partir d'un Finder.


### `app/Services/Mail/MailSender.php`

#### `Intranet\Services\Mail\MailSender`
Envia correus a partir d'un MyMail.

- Metodes:
  - `send`
    Envia el correu a tots els receptors.
  - `sendMail`
    Envia un correu a un receptor.
  - `handlePostSend`
    Lança l'esdeveniment definit en sessió, si existeix.

#### `Intranet\Services\Mail\handleAnnexeIndividual`
- Metodes: cap


### `app/Services/Mail/MyMail.php`

#### `Intranet\Services\Mail\MyMail`
Correu compost a partir de dades de configuració i receptors.

- Metodes:
  - `__get`
    Retorna propietats internes o del mapa de característiques.
  - `__set`
    Assigna propietats internes o del mapa de característiques.
  - `__construct`
    /
  - `render`
    Renderitza la vista d'edició del correu.
  - `send`
    Envia el correu a tots els receptors.
  - `resolveViewForSend`
    Resol la vista a enviar (carrega el fitxer si cal).
  - `getTo`
    Retorna la col·lecció o element(s) a qui s'enviarà el correu.


### `app/Services/Mail/RecipientResolver.php`

#### `Intranet\Services\Mail\RecipientResolver`
Resol i formata receptors per a MyMail.

- Metodes:
  - `resolveElements`
    Converteix una llista d'elements en col·lecció d'objectes.
  - `resolveElement`
    Resol un element a objecte, si cal.
  - `formatReceivers`
    Dona format a la llista de receptors per a la vista.
  - `formatReceiver`
    Dona format a un receptor: id(mail;contacte).


### `app/Services/Media/ImageService.php`

#### `Intranet\Services\Media\ImageService`
- Metodes:
  - `openGdImage`
    Obri una imatge GD des d'un UploadedFile o path, detectant el tipus real.
  - `imagetypeFromMime`
  - `convertHeicToPng`
  - `transform`
    Redimensiona a 68x90 mantenint proporció i farcint amb transparent (PNG).
  - `updatePhotoCarnet`
  - `newPhotoCarnet`
  - `toPng`


### `app/Services/Notifications/ActividadNotificationService.php`

#### `Intranet\Services\Notifications\ActividadNotificationService`
Servei d'enviament de notificacions relacionades amb activitats.

- Metodes:
  - `__construct`

#### `Intranet\Services\Notifications\app`
- Metodes: cap

#### `Intranet\Services\Notifications\groupTeachersResolver`
- Metodes: cap

#### `Intranet\Services\Notifications\advise`
- Metodes:
  - `notifyActivity`
    Envia notificacions a professorat de grups i participants.
  - `notifyGroups`
    Envia missatge als professors dels grups inclosos en l'activitat.
  - `notifyParticipants`
    Envia avís als professors participants de la pròpia activitat.


### `app/Services/Notifications/AdviseService.php`

#### `Intranet\Services\Notifications\AdviseService`
- Metodes:
  - `exec`
  - `__construct`
  - `file`
  - `getAdvises`
  - `addDescriptionToMessage`
  - `advise`

#### `Intranet\Services\Notifications\send`
- Metodes:
  - `setExplanation`
  - `setLink`
  - `resolveRecipients`
  - `buildMessage`
  - `send`


### `app/Services/Notifications/AdviseTeacher.php`

#### `Intranet\Services\Notifications\AdviseTeacher`
- Metodes:
  - `__construct`

#### `Intranet\Services\Notifications\profesorService`
- Metodes: cap

#### `Intranet\Services\Notifications\horarioService`
- Metodes: cap

#### `Intranet\Services\Notifications\grupoService`
- Metodes: cap

#### `Intranet\Services\Notifications\advise`
- Metodes:
  - `advise`
    API nova injectable.
  - `affectedGroups`
  - `sendTutorEmail`
  - `horarioAltreGrup`
  - `teachersAffected`
  - `hoursAffected`


### `app/Services/Notifications/ConfirmAndSend.php`

#### `Intranet\Services\Notifications\ConfirmAndSend`
- Metodes:
  - `render`


### `app/Services/Notifications/NotificationService.php`

#### `Intranet\Services\Notifications\NotificationService`
- Metodes:
  - `__construct`

#### `Intranet\Services\Notifications\findAlumno`
- Metodes:
  - `receptor`
  - `emisor`
  - `send`


### `app/Services/School/ActividadParticipantsService.php`

#### `Intranet\Services\School\ActividadParticipantsService`
Gestiona participants i coordinació d'activitats.

- Metodes:
  - `assignInitialParticipants`
    Assigna coordinador i grup per defecte en crear l'activitat.

#### `Intranet\Services\School\largestByTutor`
- Metodes:
  - `addGroup`
    Afig un grup sense desassignar els existents.
  - `removeGroup`
    Esborra un grup del pivot.
  - `addProfesor`
    Afig un professor sense duplicar pivots.
  - `removeProfesor`
    Esborra un professor i, si era coordinador, en reassigna un de nou.
  - `assignCoordinator`
    Marca un únic coordinador per a l'activitat.


### `app/Services/School/CotxeAccessService.php`

#### `Intranet\Services\School\CotxeAccessService`
- Metodes:
  - `recentAccessWithin`
    Comprova si hi ha hagut un accés recent d'una matrícula.
  - `registrarAcces`
    Registra un nou accés al pàrquing.
  - `obrirIPorta`
    Envia les ordres d'obrir i tancar la porta al dispositiu IoT.


### `app/Services/School/ExpedienteWorkflowService.php`

#### `Intranet\Services\School\ExpedienteWorkflowService`
Fluxos de negoci d'estat per a expedients.

- Metodes:
  - `__construct`
  - `expedients`

#### `Intranet\Services\School\expedienteService`
- Metodes:
  - `authorizePending`
    Autoritza en lot tots els expedients pendents (estat 1 -> 2).
  - `init`
    Inicialitza un expedient.
  - `passToOrientation`
    Passa l'expedient a orientació tancada (estat 5) i fixa data de solució.
  - `assignCompanion`
    Assigna professor acompanyant i passa l'expedient a estat 5.


### `app/Services/School/FaltaReportService.php`

#### `Intranet\Services\School\FaltaReportService`
- Metodes:
  - `getComunicacioElements`
  - `getMensualElements`
  - `markPrinted`
  - `nameFile`
  - `buildQuery`


### `app/Services/School/ItacaService.php`

#### `Intranet\Services\School\ItacaService`
- Metodes:
  - `__construct`
  - `close`
  - `goToLlist`
  - `processActivitat`
  - `processFalta`
  - `closeNoticias`


### `app/Services/School/ModuloGrupoService.php`

#### `Intranet\Services\School\ModuloGrupoService`
- Metodes:
  - `hasSeguimiento`
  - `profesorNombres`
  - `programacioLink`
  - `profesorIds`
  - `misModulos`
  - `buildProgramacioUrl`


### `app/Services/School/ReunionService.php`

#### `Intranet\Services\School\ReunionService`
- Metodes:
  - `makeMessage`
  - `addProfesor`
  - `removeProfesor`
  - `addAlumno`
  - `removeAlumno`
  - `notify`


### `app/Services/School/SecretariaService.php`

#### `Intranet\Services\School\SecretariaService`
- Metodes:
  - `__construct`
  - `uploadFile`
  - `error`


### `app/Services/School/SignaturaStatusService.php`

#### `Intranet\Services\School\SignaturaStatusService`
- Metodes:
  - `estat`
  - `cssClass`
  - `yesNo`
  - `estatA1`
  - `estatA2`
  - `estatA3`


### `app/Services/School/TaskFileService.php`

#### `Intranet\Services\School\TaskFileService`
- Metodes:
  - `store`


### `app/Services/School/TaskValidationService.php`

#### `Intranet\Services\School\TaskValidationService`
- Metodes:
  - `resolve`
  - `avalPrg`
  - `entrPrg`
  - `segAval`

#### `Intranet\Services\School\misModulos`
- Metodes:
  - `actAval`
  - `actaDel`
  - `actaFse`
  - `infDept`


### `app/Services/School/TeacherSubstitutionService.php`

#### `Intranet\Services\School\TeacherSubstitutionService`
Gestiona lògica d'alta/baixa de professorat amb cadena de substitucions.

- Metodes:
  - `__construct`

#### `Intranet\Services\School\horarioService`
- Metodes: cap

#### `Intranet\Services\School\grupoService`
- Metodes: cap

#### `Intranet\Services\School\alumnoFctService`
- Metodes: cap

#### `Intranet\Services\School\markLeave`
- Metodes:
  - `markLeave`
    Marca un professor com de baixa en una data.
  - `reactivate`
    Reactiva un professor i reverteix canvis dels substituts en cadena.
  - `changeWithSubstitute`
    Mou càrrega docent/administrativa del substitut al professor original.
  - `markAssistenceMeetings`
    Marca assistència pendent del professor reactiu a una reunió.


### `app/Services/Signature/DigitalSignatureService.php`

#### `Intranet\Services\Signature\DigitalSignatureService`
- Metodes:
  - `readCertificat`
    Llig i valida un certificat PKCS#12 amb OpenSSL.
  - `readCertificate`
    Llig i valida un certificat PKCS#12 amb OpenSSL.
  - `cryptCertificate`
  - `encryptCertificate`
  - `decryptCertificate`
  - `decryptUserCertificate`
  - `decryptCertificateUser`
  - `decryptUserCertificateInstance`
  - `deleteCertificate`
  - `removeCertificate`
  - `validateUserSign`
  - `validateUserSignature`
  - `sign`
  - `signDocument`
  - `signWithJSignPdf`
  - `buildJSignPdfCommand`
  - `resolveJSignPdfOutputFile`
  - `stringifyCommand`
  - `prepareBackgroundImage`
  - `composeLogoBackground`
  - `getLastPageNumber`
  - `buildVisibleSignatureText`
  - `normalizePdf`
  - `getEncrypter`
  - `fileNameCrypt`
  - `fileNameDeCrypt`


### `app/Services/Signature/SignaturaService.php`

#### `Intranet\Services\Signature\SignaturaService`
- Metodes:
  - `exec`

#### `Intranet\Services\Signature\find`
- Metodes:
  - `getFile`


### `app/Services/UI/AlertLogger.php`

#### `Intranet\Services\UI\AlertLogger`
- Metodes:
  - `info`
  - `warning`
  - `error`
  - `log`


### `app/Services/UI/AppAlert.php`

#### `Intranet\Services\UI\AppAlert`
Façana pròpia d'alertes de la intranet.

- Metodes:
  - `info`
    Mostra un missatge informatiu.
  - `warning`
    Mostra un missatge d'avís.
  - `danger`
    Mostra un missatge d'error.
  - `success`
    Mostra un missatge d'èxit.
  - `error`
    Mostra un missatge d'error (alias de danger per compatibilitat).
  - `message`
    Mostra un missatge amb nivell explícit.
  - `render`
    Renderitza i buida les alertes pendents de la sessió.
  - `send`
    Encapsula l'enviament real de l'alerta.


### `app/Services/UI/FieldBuilder.php`

#### `Intranet\Services\UI\FieldBuilder`
Builder compatible amb `Field::*` per desacoblar Styde Html.

- Metodes:
  - `__construct`
    /
  - `setAbbreviations`
    /
  - `setCssClasses`
    /
  - `setTemplates`
    /
  - `__call`
    Redirigix qualsevol mètode desconegut a un build dinàmic per tipus.
  - `text`
    /
  - `textarea`
    /
  - `hidden`
    /
  - `file`
    /
  - `select`
    /
  - `checkbox`
    /
  - `checkboxes`
    /
  - `radios`
    /
  - `build`
    /
  - `doBuild`
    /
  - `resolveFieldTemplate`
    /
  - `getDefaultTemplate`
    /
  - `getCustomTemplate`
    /
  - `getHtmlName`
    /
  - `getHtmlId`
    /
  - `getRequired`
    /
  - `getLabel`
    /
  - `getDefaultClasses`
    /
  - `getClasses`
    /
  - `getControlErrors`
    /
  - `getHtmlAttributes`
    /
  - `replaceAttributes`
    /
  - `checkAccess`
    /
  - `buildControl`
    /
  - `normalizeOptionsArray`
    /
  - `getOptionsList`
    /
  - `getOptionsFromModel`
    /
  - `addEmptyOption`
    /
  - `getEmptyOption`
    /
  - `renderRadioCollection`
    /
  - `renderCheckboxCollection`
    /


### `app/Services/UI/FormBuilder.php`

#### `Intranet\Services\UI\FormBuilder`
- Metodes:
  - `__construct`
  - `getElemento`
    /
  - `getDefault`
    /
  - `render`
  - `modal`
  - `fillDefaultOptionsToForm`
  - `translate`
  - `aspect`
  - `fillDefaultOptionsFromModel`
    /


### `app/Services/UI/NavigationService.php`

#### `Intranet\Services\UI\NavigationService`
- Metodes:
  - `customBack`
  - `dropFromHistory`
  - `addToHistory`
  - `getPreviousUrl`



## Requests

### `app/Http/Requests/ActividadRequest.php`

#### `Intranet\Http\Requests\ActividadRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/AlumnoFctUpdateRequest.php`

#### `Intranet\Http\Requests\AlumnoFctUpdateRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/AlumnoGrupoUpdateRequest.php`

#### `Intranet\Http\Requests\AlumnoGrupoUpdateRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/AlumnoPerfilUpdateRequest.php`

#### `Intranet\Http\Requests\AlumnoPerfilUpdateRequest`
- Metodes:
  - `rules`


### `app/Http/Requests/AlumnoResultadoStoreRequest.php`

#### `Intranet\Http\Requests\AlumnoResultadoStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/AlumnoUpdateRequest.php`

#### `Intranet\Http\Requests\AlumnoUpdateRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/ArticuloLoteRequest.php`

#### `Intranet\Http\Requests\ArticuloLoteRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/ArticuloRequest.php`

#### `Intranet\Http\Requests\ArticuloRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/AuthPerfilUpdateRequest.php`

#### `Intranet\Http\Requests\AuthPerfilUpdateRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/CentroRequest.php`

#### `Intranet\Http\Requests\CentroRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/CicloDualRequest.php`

#### `Intranet\Http\Requests\CicloDualRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/CicloRequest.php`

#### `Intranet\Http\Requests\CicloRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/ColaboracionRequest.php`

#### `Intranet\Http\Requests\ColaboracionRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.
  - `messages`
    Missatges curts per al formulari d'edició de col·laboració.


### `app/Http/Requests/ColaboradorRequest.php`

#### `Intranet\Http\Requests\ColaboradorRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/ComisionRequest.php`

#### `Intranet\Http\Requests\ComisionRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/CotxeRequest.php`

#### `Intranet\Http\Requests\CotxeRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/CursoRequest.php`

#### `Intranet\Http\Requests\CursoRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/DepartamentoRequest.php`

#### `Intranet\Http\Requests\DepartamentoRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/DesdeHastaRequest.php`

#### `Intranet\Http\Requests\DesdeHastaRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/DocumentoStoreRequest.php`

#### `Intranet\Http\Requests\DocumentoStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/DualRequest.php`

#### `Intranet\Http\Requests\DualRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/EmpresaCentroRequest.php`

#### `Intranet\Http\Requests\EmpresaCentroRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/EmpresaRequest.php`

#### `Intranet\Http\Requests\EmpresaRequest`
- Metodes:
  - `authorize`
    Determina si l'usuari està autoritzat a fer la petició.
  - `rules`
    Regles de validació del formulari d'empresa.
  - `prepareForValidation`
    Normalitza dades abans de validar/guardar.


### `app/Http/Requests/EspacioRequest.php`

#### `Intranet\Http\Requests\EspacioRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/ExpedienteRequest.php`

#### `Intranet\Http\Requests\ExpedienteRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/FaltaRequest.php`

#### `Intranet\Http\Requests\FaltaRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/FctConvalidacionStoreRequest.php`

#### `Intranet\Http\Requests\FctConvalidacionStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/FctStoreRequest.php`

#### `Intranet\Http\Requests\FctStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/FctUpdateRequest.php`

#### `Intranet\Http\Requests\FctUpdateRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/FicharStoreRequest.php`

#### `Intranet\Http\Requests\FicharStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/GTProfesorRequest.php`

#### `Intranet\Http\Requests\GTProfesorRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.
  - `messages`


### `app/Http/Requests/GrupoTrabajoRequest.php`

#### `Intranet\Http\Requests\GrupoTrabajoRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/HorarioUpdateRequest.php`

#### `Intranet\Http\Requests\HorarioUpdateRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/ImportStoreRequest.php`

#### `Intranet\Http\Requests\ImportStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/IncidenciaRequest.php`

#### `Intranet\Http\Requests\IncidenciaRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/IpGuardiaRequest.php`

#### `Intranet\Http\Requests\IpGuardiaRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/LoteRequest.php`

#### `Intranet\Http\Requests\LoteRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/ModuloCicloRequest.php`

#### `Intranet\Http\Requests\ModuloCicloRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/ModuloRequest.php`

#### `Intranet\Http\Requests\ModuloRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/MyMailStoreRequest.php`

#### `Intranet\Http\Requests\MyMailStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/OptionStoreRequest.php`

#### `Intranet\Http\Requests\OptionStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/OrdenReunionStoreRequest.php`

#### `Intranet\Http\Requests\OrdenReunionStoreRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/OrdenTrabajoRequest.php`

#### `Intranet\Http\Requests\OrdenTrabajoRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/PPollRequest.php`

#### `Intranet\Http\Requests\PPollRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/PasswordRequest.php`

#### `Intranet\Http\Requests\PasswordRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/PerfilFilesRequest.php`

#### `Intranet\Http\Requests\PerfilFilesRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.
  - `messages`


### `app/Http/Requests/ProfesorPerfilUpdateRequest.php`

#### `Intranet\Http\Requests\ProfesorPerfilUpdateRequest`
- Metodes:
  - `rules`


### `app/Http/Requests/ProfesorUpdateRequest.php`

#### `Intranet\Http\Requests\ProfesorUpdateRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/ProyectoRequest.php`

#### `Intranet\Http\Requests\ProyectoRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/ResultadoStoreRequest.php`

#### `Intranet\Http\Requests\ResultadoStoreRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/ResultadoUpdateRequest.php`

#### `Intranet\Http\Requests\ResultadoUpdateRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/ReunionStoreRequest.php`

#### `Intranet\Http\Requests\ReunionStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/ReunionUpdateRequest.php`

#### `Intranet\Http\Requests\ReunionUpdateRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/SendAvaluacioEmailStoreRequest.php`

#### `Intranet\Http\Requests\SendAvaluacioEmailStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/SettingRequest.php`

#### `Intranet\Http\Requests\SettingRequest`
- Metodes:
  - `authorize`
    Determina si l'usuari autenticat pot modificar settings.
  - `rules`
    Retorna les regles de validació del formulari de settings.


### `app/Http/Requests/SignaturaStoreRequest.php`

#### `Intranet\Http\Requests\SignaturaStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/SolicitudRequest.php`

#### `Intranet\Http\Requests\SolicitudRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/StoreBustiaRequest.php`

#### `Intranet\Http\Requests\StoreBustiaRequest`
- Metodes:
  - `authorize`
  - `rules`
  - `messages`


### `app/Http/Requests/TaskRequest.php`

#### `Intranet\Http\Requests\TaskRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/TeacherImportStoreRequest.php`

#### `Intranet\Http\Requests\TeacherImportStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/TipoActividadRequest.php`

#### `Intranet\Http\Requests\TipoActividadRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/TipoActividadUpdateRequest.php`

#### `Intranet\Http\Requests\TipoActividadUpdateRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/TipoIncidenciaRequest.php`

#### `Intranet\Http\Requests\TipoIncidenciaRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.


### `app/Http/Requests/TutoriaGrupoStoreRequest.php`

#### `Intranet\Http\Requests\TutoriaGrupoStoreRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/TutoriaGrupoUpdateRequest.php`

#### `Intranet\Http\Requests\TutoriaGrupoUpdateRequest`
- Metodes:
  - `authorize`
  - `rules`


### `app/Http/Requests/ValoracionRequest.php`

#### `Intranet\Http\Requests\ValoracionRequest`
- Metodes:
  - `authorize`
    Determine if the user is authorized to make this request.
  - `rules`
    Get the validation rules that apply to the request.



## Policies

### `app/Policies/ActividadPolicy.php`

#### `Intranet\Policies\ActividadPolicy`
Policy d'autorització per al flux d'activitats.

- Metodes:
  - `viewAny`
    /
  - `create`
    /
  - `view`
    /
  - `update`
    /
  - `manageParticipants`
    /
  - `notify`
    /
  - `canManage`
    Regla de gestió: coordinador de l'activitat o rol elevat.


### `app/Policies/ArticuloPolicy.php`

#### `Intranet\Policies\ArticuloPolicy`
Policy d'autorització per a catàleg d'articles.

- Metodes:
  - `view`
    Determina si l'usuari pot veure articles.
  - `create`
    Determina si l'usuari pot crear articles.
  - `update`
    Determina si l'usuari pot actualitzar articles.
  - `delete`
    Determina si l'usuari pot eliminar articles.


### `app/Policies/CicloPolicy.php`

#### `Intranet\Policies\CicloPolicy`
Policy d'autorització per a cicles.

- Metodes:
  - `create`
    Determina si l'usuari pot crear cicles.
  - `update`
    Determina si l'usuari pot actualitzar cicles.
  - `delete`
    Determina si l'usuari pot eliminar cicles.


### `app/Policies/ColaboracionPolicy.php`

#### `Intranet\Policies\ColaboracionPolicy`
- Metodes:
  - `create`
  - `update`
  - `isTutor`


### `app/Policies/ComisionPolicy.php`

#### `Intranet\Policies\ComisionPolicy`
Policy d'autorització per a comissions de servei.

- Metodes:
  - `create`
    /
  - `update`
    /
  - `view`
    /
  - `manageFct`
    /
  - `isOwner`
    Regla de propietat de la comissió.


### `app/Policies/Concerns/InteractsWithProfesorOwnership.php`

#### `Intranet\Policies\Concerns\InteractsWithProfesorOwnership`
Utilitats de policy per a regles basades en propietari professor i rols elevats.

- Metodes:
  - `hasProfesorIdentity`
    Comprova que l'usuari tinga identitat de professor.
  - `hasRole`
    Comprova si l'usuari té un rol concret (bitmask de rols).
  - `isDirectionOrAdmin`
    Comprova si l'usuari és direcció o administració.
  - `ownsOrIsDirectionOrAdmin`
    Regla genèrica: propietari professor o rols elevats.


### `app/Policies/CotxePolicy.php`

#### `Intranet\Policies\CotxePolicy`
Policy d'autorització per a vehicles de professorat.

- Metodes:
  - `create`
    Determina si l'usuari pot crear vehicles.
  - `view`
    Determina si l'usuari pot veure vehicles.
  - `update`
    Determina si l'usuari pot actualitzar vehicles.
  - `delete`
    Determina si l'usuari pot eliminar vehicles.


### `app/Policies/CursoPolicy.php`

#### `Intranet\Policies\CursoPolicy`
Policy d'autorització per a cursos.

- Metodes:
  - `viewAny`
    Determina si l'usuari pot accedir als llistats de cursos.
  - `create`
    Determina si l'usuari pot crear cursos.
  - `update`
    Determina si l'usuari pot actualitzar cursos.
  - `delete`
    Determina si l'usuari pot eliminar cursos.


### `app/Policies/DepartamentoPolicy.php`

#### `Intranet\Policies\DepartamentoPolicy`
Policy d'autorització per a departaments.

- Metodes:
  - `create`
    Determina si l'usuari pot crear departaments.
  - `update`
    Determina si l'usuari pot actualitzar departaments.
  - `delete`
    Determina si l'usuari pot eliminar departaments.


### `app/Policies/DocumentoPolicy.php`

#### `Intranet\Policies\DocumentoPolicy`
Policy d'autorització per a la gestió de documents.

- Metodes:
  - `viewAny`
    Determina si l'usuari pot accedir als panells de llistat documental.
  - `create`
    Determina si l'usuari pot crear documents.
  - `view`
    Determina si l'usuari pot veure documents.
  - `update`
    Determina si l'usuari pot actualitzar documents.
  - `delete`
    Determina si l'usuari pot eliminar documents.
  - `hasIdentity`
    /


### `app/Policies/EmpresaPolicy.php`

#### `Intranet\Policies\EmpresaPolicy`
- Metodes:
  - `viewAny`
    Determina si l'usuari pot accedir als llistats d'empreses.
  - `create`
  - `update`
  - `canMutate`


### `app/Policies/EspacioPolicy.php`

#### `Intranet\Policies\EspacioPolicy`
Policy d'autorització per a espais.

- Metodes:
  - `create`
    Determina si l'usuari pot crear espais.
  - `update`
    Determina si l'usuari pot actualitzar espais.
  - `delete`
    Determina si l'usuari pot eliminar espais.
  - `printBarcode`
    Determina si l'usuari pot imprimir codis de barres de l'espai.


### `app/Policies/ExpedientePolicy.php`

#### `Intranet\Policies\ExpedientePolicy`
Policy d'autorització per a expedients.

- Metodes:
  - `create`
    Determina si l'usuari pot crear expedients.
  - `view`
    Determina si l'usuari pot veure expedients.
  - `update`
    Determina si l'usuari pot actualitzar expedients.
  - `delete`
    Determina si l'usuari pot eliminar expedients.


### `app/Policies/FaltaPolicy.php`

#### `Intranet\Policies\FaltaPolicy`
Policy d'autorització per a la gestió de faltes.

- Metodes:
  - `create`
    Determina si l'usuari pot crear una falta.
  - `view`
    Determina si l'usuari pot veure una falta.
  - `update`
    Determina si l'usuari pot actualitzar una falta.
  - `delete`
    Determina si l'usuari pot eliminar una falta.


### `app/Policies/FctPolicy.php`

#### `Intranet\Policies\FctPolicy`
Policy d'autorització per a les operacions de FCT.

- Metodes:
  - `viewAny`
    Determina si l'usuari pot accedir al panell general de FCT.
  - `create`
    Determina si l'usuari pot crear una FCT.
  - `update`
    Determina si l'usuari pot actualitzar una FCT.
  - `delete`
    Determina si l'usuari pot eliminar una FCT.
  - `manageAval`
    Determina si l'usuari pot gestionar avaluacions FCT (apte/no apte/projecte/inserció).
  - `requestActa`
    Determina si l'usuari pot demanar actes d'avaluació.
  - `sendA56`
    Determina si l'usuari pot enviar annexos A56 a secretaria.
  - `viewStats`
    Determina si l'usuari pot consultar estadístiques d'avaluació FCT.
  - `managePendingActa`
    Determina si l'usuari pot validar/rebutjar actes pendents de FCT.
  - `manageFctControl`
    Determina si l'usuari pot gestionar el panell de control de dual.
  - `canMutate`
    Regla comuna de permisos per a mutacions de FCT.


### `app/Policies/GrupoTrabajoPolicy.php`

#### `Intranet\Policies\GrupoTrabajoPolicy`
Policy d'autorització per a grups de treball.

- Metodes:
  - `create`
    Determina si l'usuari pot crear grups de treball.
  - `update`
    Determina si l'usuari pot actualitzar un grup de treball.
  - `delete`
    Determina si l'usuari pot eliminar un grup de treball.
  - `manageMembers`
    Determina si l'usuari pot gestionar membres/coordinador del grup.
  - `isOwner`
    /


### `app/Policies/ImportRunPolicy.php`

#### `Intranet\Policies\ImportRunPolicy`
- Metodes:
  - `manage`
  - `viewAny`
  - `view`


### `app/Policies/IncidenciaPolicy.php`

#### `Intranet\Policies\IncidenciaPolicy`
Policy d'autorització per a incidències.

- Metodes:
  - `viewAny`
    Determina si l'usuari pot accedir als llistats d'incidències.
  - `create`
    Determina si l'usuari pot crear incidències.
  - `view`
    Determina si l'usuari pot veure una incidència.
  - `update`
    Determina si l'usuari pot actualitzar una incidència.
  - `delete`
    Determina si l'usuari pot eliminar una incidència.
  - `ownsOrIsResponsible`
    Regla: creador o responsable.


### `app/Policies/IpGuardiaPolicy.php`

#### `Intranet\Policies\IpGuardiaPolicy`
Policy d'autorització per a IPs de guàrdia.

- Metodes:
  - `create`
    Determina si l'usuari pot crear IPs.
  - `update`
    Determina si l'usuari pot actualitzar IPs.
  - `delete`
    Determina si l'usuari pot eliminar IPs.


### `app/Policies/LotePolicy.php`

#### `Intranet\Policies\LotePolicy`
Policy d'autorització per a lots d'inventari.

- Metodes:
  - `create`
    Determina si l'usuari pot crear lots.
  - `update`
    Determina si l'usuari pot actualitzar lots.
  - `delete`
    Determina si l'usuari pot eliminar lots.


### `app/Policies/MaterialBajaPolicy.php`

#### `Intranet\Policies\MaterialBajaPolicy`
Policy d'autorització per a gestió de baixes de material.

- Metodes:
  - `update`
    Determina si l'usuari pot actualitzar una baixa de material.
  - `delete`
    Determina si l'usuari pot eliminar una baixa de material.
  - `recover`
    Determina si l'usuari pot recuperar material des de baixa.


### `app/Policies/MenuPolicy.php`

#### `Intranet\Policies\MenuPolicy`
Policy d'autorització per a opcions de menú.

- Metodes:
  - `create`
    Determina si l'usuari pot crear menús.
  - `update`
    Determina si l'usuari pot actualitzar menús.
  - `delete`
    Determina si l'usuari pot eliminar menús.


### `app/Policies/ModuloCicloPolicy.php`

#### `Intranet\Policies\ModuloCicloPolicy`
Policy d'autorització per a l'enllaç mòdul-cicle.

- Metodes:
  - `create`
    Determina si l'usuari pot crear enllaços mòdul-cicle.
  - `update`
    Determina si l'usuari pot actualitzar enllaços mòdul-cicle.
  - `delete`
    Determina si l'usuari pot eliminar enllaços mòdul-cicle.


### `app/Policies/OptionPolicy.php`

#### `Intranet\Policies\OptionPolicy`
Policy d'autorització per a opcions de polls.

- Metodes:
  - `create`
    Determina si l'usuari pot crear opcions.
  - `delete`
    Determina si l'usuari pot eliminar opcions.


### `app/Policies/PPollPolicy.php`

#### `Intranet\Policies\PPollPolicy`
Policy d'autorització per a plantilles de polls.

- Metodes:
  - `view`
    Determina si l'usuari pot veure la plantilla.
  - `create`
    Determina si l'usuari pot crear plantilles.
  - `update`
    Determina si l'usuari pot actualitzar plantilles.
  - `delete`
    Determina si l'usuari pot eliminar plantilles.


### `app/Policies/ProfesorPolicy.php`

#### `Intranet\Policies\ProfesorPolicy`
Policy d'autorització per a professorat.

- Metodes:
  - `update`
    Determina si l'usuari pot actualitzar el perfil d'un professor.
  - `manageQualityFinal`
    Determina si l'usuari pot gestionar la qualitat final (cap de pràctiques).
  - `manageAttendance`
    Determina si l'usuari pot gestionar incidències de fitxatge/presència.


### `app/Policies/ProjectePolicy.php`

#### `Intranet\Policies\ProjectePolicy`
Policy d'autorització per al flux de propostes de projecte.

- Metodes:
  - `create`
    Determina si l'usuari pot crear propostes dins del seu grup de tutoria.
  - `view`
    Determina si l'usuari pot vore una proposta del seu grup de tutoria.
  - `update`
    Determina si l'usuari pot actualitzar una proposta del seu grup.
  - `delete`
    Determina si l'usuari pot eliminar una proposta del seu grup.
  - `check`
    Determina si l'usuari pot validar una proposta del seu grup.
  - `send`
    Determina si l'usuari pot enviar projectes del seu grup.
  - `createActa`
    Determina si l'usuari pot crear l'acta de valoració del seu grup.
  - `createDefenseActa`
    Determina si l'usuari pot crear l'acta de defenses del seu grup.
  - `isTutorOfAnyGroup`
    /

#### `Intranet\Policies\byTutorOrSubstitute`
- Metodes: cap


### `app/Policies/ResultadoPolicy.php`

#### `Intranet\Policies\ResultadoPolicy`
Policy d'autorització per a resultats acadèmics.

- Metodes:
  - `create`
    Determina si l'usuari pot crear resultats.
  - `view`
    Determina si l'usuari pot veure resultats.
  - `update`
    Determina si l'usuari pot actualitzar resultats.
  - `delete`
    Determina si l'usuari pot eliminar resultats.


### `app/Policies/ReunionPolicy.php`

#### `Intranet\Policies\ReunionPolicy`
Policy d'autorització per a la gestió de reunions.

- Metodes:
  - `create`
    Determina si l'usuari pot crear reunions.
  - `update`
    Determina si l'usuari pot veure/editar la reunió.
  - `manageParticipants`
    Determina si l'usuari pot modificar participants de la reunió.
  - `manageOrder`
    Determina si l'usuari pot gestionar l'orde de reunió.
  - `notify`
    Determina si l'usuari pot notificar o enviar correu de la reunió.
  - `manageDepartmentReport`
    Determina si l'usuari pot gestionar l'informe trimestral de departament.
  - `isOwner`
    /


### `app/Policies/SettingPolicy.php`

#### `Intranet\Policies\SettingPolicy`
Policy d'autorització per a la gestió de settings.

- Metodes:
  - `create`
    Determina si l'usuari pot crear settings (rol administrador).
  - `update`
    Determina si l'usuari pot actualitzar settings (rol administrador).
  - `delete`
    Determina si l'usuari pot eliminar settings (rol administrador).
  - `isAdministrador`
    /


### `app/Policies/SignaturaPolicy.php`

#### `Intranet\Policies\SignaturaPolicy`
Policy d'autorització per a signatures de FCT.

- Metodes:
  - `manageDirectionPanel`
    Determina si l'usuari pot accedir al panell de signatures de direcció.
  - `manage`
    Determina si l'usuari pot gestionar fluxos globals de signatures.
  - `create`
    Determina si l'usuari pot crear signatures.
  - `view`
    Determina si l'usuari pot veure signatures.
  - `update`
    Determina si l'usuari pot actualitzar signatures.
  - `delete`
    Determina si l'usuari pot eliminar signatures.


### `app/Policies/SolicitudPolicy.php`

#### `Intranet\Policies\SolicitudPolicy`
Policy d'autorització per a sol·licituds.

- Metodes:
  - `create`
    Determina si l'usuari pot crear sol·licituds.
  - `view`
    Determina si l'usuari pot veure sol·licituds.
  - `update`
    Determina si l'usuari pot actualitzar sol·licituds.
  - `activate`
    Determina si l'usuari pot activar una sol·licitud d'orientació.
  - `resolve`
    Determina si l'usuari pot resoldre una sol·licitud d'orientació.
  - `delete`
    Determina si l'usuari pot eliminar sol·licituds.


### `app/Policies/TaskPolicy.php`

#### `Intranet\Policies\TaskPolicy`
Policy d'autorització per a tasques.

- Metodes:
  - `create`
    Determina si l'usuari pot crear tasques (rol administrador).
  - `update`
    Determina si l'usuari pot actualitzar tasques (rol administrador).
  - `check`
    Determina si l'usuari pot marcar/desmarcar una tasca pròpia.
  - `isAdministrador`
    /


### `app/Policies/TipoActividadPolicy.php`

#### `Intranet\Policies\TipoActividadPolicy`
Policy d'autorització per a tipus d'activitat.

- Metodes:
  - `create`
    Determina si l'usuari pot crear tipus d'activitat.
  - `update`
    Determina si l'usuari pot actualitzar un tipus d'activitat.
  - `delete`
    Determina si l'usuari pot eliminar un tipus d'activitat.
  - `isDirectionOrHeadOfDepartment`
    Comprova si l'usuari té rol de direcció/admin o cap de departament.


### `app/Policies/TipoIncidenciaPolicy.php`

#### `Intranet\Policies\TipoIncidenciaPolicy`
Policy d'autorització per a tipus d'incidència.

- Metodes:
  - `create`
    Determina si l'usuari pot crear tipus d'incidència.
  - `update`
    Determina si l'usuari pot actualitzar tipus d'incidència.
  - `delete`
    Determina si l'usuari pot eliminar tipus d'incidència.


### `app/Policies/TutoriaGrupoPolicy.php`

#### `Intranet\Policies\TutoriaGrupoPolicy`
Policy d'autorització per a tutories de grup.

- Metodes:
  - `create`
    Determina si l'usuari pot crear registres de tutoria-grup.
  - `view`
    Determina si l'usuari pot veure registres de tutoria-grup.
  - `update`
    Determina si l'usuari pot actualitzar registres de tutoria-grup.
  - `delete`
    Determina si l'usuari pot eliminar registres de tutoria-grup.



## Events

### `app/Events/ActivityReport.php`

#### `Intranet\Events\ActivityReport`
Event de registre d'activitat.

- Metodes:
  - `__construct`
    /


### `app/Events/FctAlDeleted.php`

#### `Intranet\Events\FctAlDeleted`
Event de baixa d'alumne en FCT.

- Metodes:
  - `__construct`
    /


### `app/Events/FctCreated.php`

#### `Intranet\Events\FctCreated`
Event de FCT creada.

- Metodes:
  - `__construct`
    /


### `app/Events/GrupoCreated.php`

#### `Intranet\Events\GrupoCreated`
Event de grup creat.

- Metodes:
  - `__construct`
    /


### `app/Events/ReunionCreated.php`

#### `Intranet\Events\ReunionCreated`
Event de reunio creada.

- Metodes:
  - `__construct`
    /



## Listeners

### `app/Listeners/AsistentesCreate.php`

#### `Intranet\Listeners\AsistentesCreate`
- Metodes:
  - `__construct`
    Create the event listener.

#### `Intranet\Listeners\queAlumnes`
- Metodes:
  - `queAlumnes`
    Handle the event.
  - `assignaAlumnes`
    /
  - `handle`
    Handle the event.
  - `esJefe`
    /
  - `asignaProfeReunion`
    /


### `app/Listeners/ColaboracionColabora.php`

#### `Intranet\Listeners\ColaboracionColabora`
Marca la col·laboració com a finalitzada en crear una FCT.

- Metodes:
  - `__construct`
    Create the event listener.
  - `handle`
    Handle the event.


### `app/Listeners/CoordinadorCreate.php`

#### `Intranet\Listeners\CoordinadorCreate`
- Metodes:
  - `__construct`
    Create the event listener.
  - `handle`
    Handle the event.


### `app/Listeners/FctDelete.php`

#### `Intranet\Listeners\FctDelete`
Elimina la FCT si es queda sense alumnes.

- Metodes:
  - `__construct`
    Create the event listener.
  - `handle`
    Handle the event.


### `app/Listeners/LogLastLogin.php`

#### `Intranet\Listeners\LogLastLogin`
- Metodes:
  - `__construct`
    Create the event listener.
  - `handle`
    Handle the event.


### `app/Listeners/RegisterActivity.php`

#### `Intranet\Listeners\RegisterActivity`
- Metodes:
  - `__construct`
    Create the event listener.
  - `handle`
    Handle the event.


### `app/Listeners/UpdateLastLoggedAt.php`

#### `Intranet\Listeners\UpdateLastLoggedAt`
- Metodes:
  - `__construct`
    Create the event listener.
  - `handle`
    Handle the event.



## Jobs

### `app/Jobs/RunImportJob.php`

#### `Intranet\Jobs\RunImportJob`
- Metodes:
  - `__construct`
  - `handle`

#### `Intranet\Jobs\run`
- Metodes: cap


### `app/Jobs/SendEmail.php`

#### `Intranet\Jobs\SendEmail`
- Metodes:
  - `__construct`
  - `handle`
    Execute the job.

#### `Intranet\Jobs\markFctEmailSent`
- Metodes: cap



## Comandes

### `app/Console/Commands/CreateDailyGuards.php`

#### `Intranet\Console\Commands\CreateDailyGuards`
- Metodes:
  - `__construct`

#### `Intranet\Console\Commands\profesorService`
- Metodes: cap

#### `Intranet\Console\Commands\horarioService`
- Metodes: cap

#### `Intranet\Console\Commands\substitutoActual`
- Metodes:
  - `substitutoActual`
    Execute the console command.
  - `handle`
  - `creaGuardia`
  - `saveGuardia`
  - `createGuardias`
    /


### `app/Console/Commands/DeleteOldCotxeAccessos.php`

#### `Intranet\Console\Commands\DeleteOldCotxeAccessos`
- Metodes:
  - `handle`


### `app/Console/Commands/NotifyDailyFaults.php`

#### `Intranet\Console\Commands\NotifyDailyFaults`
- Metodes:
  - `__construct`

#### `Intranet\Console\Commands\profesorService`
- Metodes: cap

#### `Intranet\Console\Commands\horarioService`
- Metodes: cap

#### `Intranet\Console\Commands\handle`
- Metodes:
  - `handle`
    Execute the console command.
  - `noHanFichado`
  - `profeSinFichar`
    /
  - `profesoresEnActividad`
    /
  - `profesoresDeComision`
    /
  - `profesoresDeBaja`
    /


### `app/Console/Commands/SaoAnnexes.php`

#### `Intranet\Console\Commands\SaoAnnexes`
- Metodes:
  - `handle`
    Execute the console command.


### `app/Console/Commands/SaoConnect.php`

#### `Intranet\Console\Commands\SaoConnect`
- Metodes:
  - `handle`


### `app/Console/Commands/SendAvaluacioEmails.php`

#### `Intranet\Console\Commands\SendAvaluacioEmails`
- Metodes:
  - `generaToken`
  - `obtenToken`
  - `sendMatricula`
  - `handle`
    Execute the console command.


### `app/Console/Commands/SendDailyEmails.php`

#### `Intranet\Console\Commands\SendDailyEmails`
- Metodes:
  - `__construct`
  - `handle`
    Execute the console command.


### `app/Console/Commands/SendFctEmails.php`

#### `Intranet\Console\Commands\SendFctEmails`
- Metodes:
  - `handle`
    Execute the console command.
  - `correuInstructor`
    /


### `app/Console/Commands/UploadAnexes.php`

#### `Intranet\Console\Commands\UploadAnexes`
- Metodes:
  - `handle`
    Execute the console command.
  - `buscaDocuments`
    /



## Altres

### `app/Console/Kernel.php`

#### `Intranet\Console\Kernel`
- Metodes: cap

#### `Intranet\Console\CreateDailyGuards`
- Metodes: cap

#### `Intranet\Console\NotifyDailyFaults`
- Metodes: cap

#### `Intranet\Console\SendFctEmails`
- Metodes: cap

#### `Intranet\Console\UploadAnexes`
- Metodes: cap

#### `Intranet\Console\SaoConnect`
- Metodes: cap

#### `Intranet\Console\SaoAnnexes`
- Metodes: cap

#### `Intranet\Console\DeleteOldCotxeAccessos`
- Metodes: cap

#### `Intranet\Console\schedule`
- Metodes:
  - `schedule`
    Define the application's command schedule.
  - `commands`
    Register the commands for the application.


### `app/Exceptions/CertException.php`

#### `Intranet\Exceptions\CertException`
- Metodes: cap


### `app/Exceptions/Handler.php`

#### `Intranet\Exceptions\Handler`
- Metodes: cap

#### `Intranet\Exceptions\AuthorizationException`
- Metodes: cap

#### `Intranet\Exceptions\ModelNotFoundException`
- Metodes: cap

#### `Intranet\Exceptions\ValidationException`
- Metodes: cap

#### `Intranet\Exceptions\render`
- Metodes:
  - `render`

#### `Intranet\Exceptions\send`
- Metodes: cap


### `app/Exceptions/IntranetException.php`

#### `Intranet\Exceptions\IntranetException`
- Metodes: cap


### `app/Exceptions/SeleniumException.php`

#### `Intranet\Exceptions\SeleniumException`
- Metodes:
  - `__construct`
  - `incrementCounter`


### `app/Exports/PollResultsExport.php`

#### `Intranet\Exports\PollResultsExport`
- Metodes:
  - `__construct`
  - `sheets`


### `app/Exports/PollResultsSheet.php`

#### `Intranet\Exports\PollResultsSheet`
- Metodes:
  - `__construct`
  - `view`
  - `title`


### `app/Finders/A1Finder.php`

#### `Intranet\Finders\A1Finder`
- Metodes:
  - `exec`


### `app/Finders/A2Finder.php`

#### `Intranet\Finders\A2Finder`
- Metodes:
  - `exec`


### `app/Finders/A3Finder.php`

#### `Intranet\Finders\A3Finder`
- Metodes:
  - `exec`


### `app/Finders/AlumnoEnFctFinder.php`

#### `Intranet\Finders\AlumnoEnFctFinder`
- Metodes:
  - `exec`
  - `filter`


### `app/Finders/AlumnoFctFinder.php`

#### `Intranet\Finders\AlumnoFctFinder`
- Metodes:
  - `exec`
  - `filter`


### `app/Finders/AlumnoFctNoFinder.php`

#### `Intranet\Finders\AlumnoFctNoFinder`
- Metodes:
  - `exec`
  - `filter`


### `app/Finders/AlumnoNoFctFinder.php`

#### `Intranet\Finders\AlumnoNoFctFinder`
- Metodes:
  - `exec`
  - `filter`


### `app/Finders/ColaboracionFinder.php`

#### `Intranet\Finders\ColaboracionFinder`
- Metodes:
  - `exec`
  - `filter`
  - `checkFcts`


### `app/Finders/FctActivaFinder.php`

#### `Intranet\Finders\FctActivaFinder`
- Metodes:
  - `exec`
  - `filter`


### `app/Finders/FctFinder.php`

#### `Intranet\Finders\FctFinder`
- Metodes:
  - `exec`
  - `filter`


### `app/Finders/Finder.php`

#### `Intranet\Finders\Finder`
- Metodes:
  - `__construct`
  - `existsActivity`
  - `getDocument`
  - `getZip`


### `app/Finders/MailFinders/AlumnosAllFinder.php`

#### `Intranet\Finders\MailFinders\AlumnosAllFinder`
- Metodes:
  - `__construct`


### `app/Finders/MailFinders/Finder.php`

#### `Intranet\Finders\MailFinders\Finder`
- Metodes:
  - `getElements`


### `app/Finders/MailFinders/InstructoresAllFinder.php`

#### `Intranet\Finders\MailFinders\InstructoresAllFinder`
- Metodes:
  - `__construct`


### `app/Finders/MailFinders/MyA1Finder.php`

#### `Intranet\Finders\MailFinders\MyA1Finder`
- Metodes:
  - `__construct`


### `app/Finders/MailFinders/MySignaturesFinder.php`

#### `Intranet\Finders\MailFinders\MySignaturesFinder`
- Metodes:
  - `__construct`


### `app/Finders/MailFinders/SignaturesFinder.php`

#### `Intranet\Finders\MailFinders\SignaturesFinder`
- Metodes:
  - `__construct`


### `app/Finders/ModelInStateFinder.php`

#### `Intranet\Finders\ModelInStateFinder`
- Metodes:
  - `exec`


### `app/Finders/RequestFinder.php`

#### `Intranet\Finders\RequestFinder`
- Metodes:
  - `__construct`
    RequestFinder constructor.
  - `exec`
  - `getZip`
  - `getRequest`


### `app/Finders/SignedFinder.php`

#### `Intranet\Finders\SignedFinder`
- Metodes:
  - `exec`
  - `filter`


### `app/Finders/UniqueFinder.php`

#### `Intranet\Finders\UniqueFinder`
- Metodes:
  - `__construct`
  - `exec`


### `app/Http/Kernel.php`

#### `Intranet\Http\Kernel`
- Metodes: cap


### `app/Http/Middleware/ApiTokenToBearer.php`

#### `Intranet\Http\Middleware\ApiTokenToBearer`
Compatibilitat temporal:

- Metodes:
  - `handle`
    /


### `app/Http/Middleware/CustomBackMiddleware.php`

#### `Intranet\Http\Middleware\CustomBackMiddleware`
- Metodes:
  - `handle`


### `app/Http/Middleware/EncryptCookies.php`

#### `Intranet\Http\Middleware\EncryptCookies`
- Metodes: cap


### `app/Http/Middleware/LangMiddleware.php`

#### `Intranet\Http\Middleware\LangMiddleware`
- Metodes:
  - `handle`
    Handle an incoming request.


### `app/Http/Middleware/LegacyApiTokenDeprecation.php`

#### `Intranet\Http\Middleware\LegacyApiTokenDeprecation`
Marca ús legacy de `api_token` en query/body per facilitar retirada gradual.

- Metodes:
  - `handle`
    /


### `app/Http/Middleware/OwnerMiddleware.php`

#### `Intranet\Http\Middleware\OwnerMiddleware`
- Metodes:
  - `handle`
    Handle an incoming request.
  - `owner`


### `app/Http/Middleware/RedirectIfAuthenticated.php`

#### `Intranet\Http\Middleware\RedirectIfAuthenticated`
- Metodes:
  - `handle`
    Handle an incoming request.


### `app/Http/Middleware/RoleMiddleware.php`

#### `Intranet\Http\Middleware\RoleMiddleware`
- Metodes:
  - `handle`
  - `normalizeRedirector`


### `app/Http/Middleware/SessionTimeout.php`

#### `Intranet\Http\Middleware\SessionTimeout`
- Metodes:
  - `__construct`
  - `apiSessionTokens`

#### `Intranet\Http\Middleware\apiSessionTokenService`
- Metodes:
  - `handle`
    Handle an incoming request.


### `app/Http/Middleware/TrimStrings.php`

#### `Intranet\Http\Middleware\TrimStrings`
- Metodes: cap


### `app/Http/Middleware/TrustProxies.php`

#### `Intranet\Http\Middleware\TrustProxies`
- Metodes: cap


### `app/Http/Middleware/VerifyCsrfToken.php`

#### `Intranet\Http\Middleware\VerifyCsrfToken`
- Metodes: cap


### `app/Http/PrintResources/A1ENResource.php`

#### `Intranet\Http\PrintResources\A1ENResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.


### `app/Http/PrintResources/A1Resource.php`

#### `Intranet\Http\PrintResources\A1Resource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.
  - `dataSig`


### `app/Http/PrintResources/A2ENResource.php`

#### `Intranet\Http\PrintResources\A2ENResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/A3ENResource.php`

#### `Intranet\Http\PrintResources\A3ENResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/A5Resource.php`

#### `Intranet\Http\PrintResources\A5Resource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\firstByTutor`
- Metodes: cap


### `app/Http/PrintResources/AVIIAResource.php`

#### `Intranet\Http\PrintResources\AVIIAResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.


### `app/Http/PrintResources/AVIIBResource.php`

#### `Intranet\Http\PrintResources\AVIIBResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.


### `app/Http/PrintResources/AVIIIResource.php`

#### `Intranet\Http\PrintResources\AVIIIResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/AVIResource.php`

#### `Intranet\Http\PrintResources\AVIResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/AutorizacionDireccionGrupoResource.php`

#### `Intranet\Http\PrintResources\AutorizacionDireccionGrupoResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/AutorizacionDireccionResource.php`

#### `Intranet\Http\PrintResources\AutorizacionDireccionResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/AutorizacionGrupoResource.php`

#### `Intranet\Http\PrintResources\AutorizacionGrupoResource`
- Metodes:
  - `__construct`
  - `setFlatten`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/CertificatInstructorResource.php`

#### `Intranet\Http\PrintResources\CertificatInstructorResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.


### `app/Http/PrintResources/ConformidadAlumnadoGrupoResource.php`

#### `Intranet\Http\PrintResources\ConformidadAlumnadoGrupoResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/ConformidadAlumnadoResource.php`

#### `Intranet\Http\PrintResources\ConformidadAlumnadoResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/ConformidadTutoriaResource.php`

#### `Intranet\Http\PrintResources\ConformidadTutoriaResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/ExempcioFCTResource.php`

#### `Intranet\Http\PrintResources\ExempcioFCTResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/ExempcioResource.php`

#### `Intranet\Http\PrintResources\ExempcioResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/NotificacioInspeccioResource.php`

#### `Intranet\Http\PrintResources\NotificacioInspeccioResource`
- Metodes:
  - `__construct`
  - `toArray`
    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/PrintResource.php`

#### `Intranet\Http\PrintResources\PrintResource`
- Metodes:
  - `build`
  - `__construct`
  - `getElements`
    /
  - `getFlatten`
    /
  - `getStamp`
    /
  - `getFile`
    /


### `app/Http/Resources/AlumnoFctControlResource.php`

#### `Intranet\Http\Resources\AlumnoFctControlResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/AlumnoFctResource.php`

#### `Intranet\Http\Resources\AlumnoFctResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/ArticuloLoteResource.php`

#### `Intranet\Http\Resources\ArticuloLoteResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/DualResource.php`

#### `Intranet\Http\Resources\DualResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/EmpresaResource.php`

#### `Intranet\Http\Resources\EmpresaResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/FaltaConfirmResource.php`

#### `Intranet\Http\Resources\FaltaConfirmResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/InventariableResource.php`

#### `Intranet\Http\Resources\InventariableResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/JDepartamentoResource.php`

#### `Intranet\Http\Resources\JDepartamentoResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/LoteResource.php`

#### `Intranet\Http\Resources\LoteResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/MaterialBajaResource.php`

#### `Intranet\Http\Resources\MaterialBajaResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.
  - `descripcion`


### `app/Http/Resources/MaterialResource.php`

#### `Intranet\Http\Resources\MaterialResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.
  - `descripcion`


### `app/Http/Resources/SelectAlumnoFctResource.php`

#### `Intranet\Http\Resources\SelectAlumnoFctResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/SelectAlumnoResource.php`

#### `Intranet\Http\Resources\SelectAlumnoResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/SelectColaboracionResource.php`

#### `Intranet\Http\Resources\SelectColaboracionResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/SelectFctResource.php`

#### `Intranet\Http\Resources\SelectFctResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/SelectSignaturaResource.php`

#### `Intranet\Http\Resources\SelectSignaturaResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/SignaturaDireccionResource.php`

#### `Intranet\Http\Resources\SignaturaDireccionResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/SignaturaResource.php`

#### `Intranet\Http\Resources\SignaturaResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Resources/SolicitudResource.php`

#### `Intranet\Http\Resources\SolicitudResource`
- Metodes:
  - `toArray`
    Transform the resource into an array.


### `app/Http/Traits/Autorizacion.php`

#### `Intranet\Http\Traits\Autorizacion`
Trait de suport per a controllers amb fluxos d'autorització per estats.

- Metodes:
  - `getAutorizacionStateService`
    Resol i memoitza el servei de transicions d'estat per al model actual.

#### `Intranet\Http\Traits\class`
- Metodes:
  - `getAutorizacionPrintService`
    Resol i memoitza el servei d'impressió en lot.

#### `Intranet\Http\Traits\autorizacionPrintService`
- Metodes:
  - `cancel`
    Mou un element a estat de cancel·lació (`-1`).
  - `init`
    Inicialitza un element a l'estat definit en `$this->init`.
  - `_print`
    Aplica la transició `_print` a un element.
  - `resolve`
    Resol l'element i opcionalment redirigeix a la pestanya d'estat resultant.
  - `accept`
    Incrementa en una unitat l'estat actual de l'element.
  - `resign`
    Decrementa en una unitat l'estat actual de l'element.
  - `refuse`
    Refusa l'element amb explicació opcional.
  - `follow`
    Tria la pestanya de retorn segons `notFollow`.
  - `imprimir`
    Genera un PDF en lot per als elements en estat inicial i aplica transició.
  - `guardAutorizacionContract`
    Valida que el controller definisca el contracte mínim del trait.


### `app/Http/Traits/Core/DropZone.php`

#### `Intranet\Http\Traits\Core\DropZone`
Trait per gestionar la vista DropZone i la neteja d'adjunts associats.

- Metodes:
  - `deleteAttached`
    Elimina tots els adjunts vinculats al path `{model}/{id}`.
  - `link`
    Mostra la pantalla d'adjunts DropZone per a un registre.


### `app/Http/Traits/Core/Imprimir.php`

#### `Intranet\Http\Traits\Core\Imprimir`
Trait de suport per a funcionalitats d'impressió i calendari en controllers.

- Metodes:
  - `notify`
    Envia notificació de recordatori al professor responsable del registre.

#### `Intranet\Http\Traits\Core\advise`
- Metodes:
  - `hazPdf`
    Fa de façana del servei de PDF per mantindre compatibilitat als controllers.

#### `Intranet\Http\Traits\Core\hazPdf`
- Metodes:
  - `ics`
    Genera la resposta iCalendar d'un registre.
  - `gestor`
    Mostra o descarrega el document vinculat al registre en el gestor documental.
  - `guardPrintableContract`
    Valida el contracte mínim requerit pel trait.
  - `hasField`
    Comprova si un camp existeix encara que el valor siga `null`.


### `app/Http/Traits/Core/Panel.php`

#### `Intranet\Http\Traits\Core\Panel`
Trait de suport per a controllers tipus panell.

- Metodes:
  - `index`
    Mostra la llista d'elements del panell.
  - `search`
    Retorna els elements filtrats segons el seu estat i data.
  - `setAuthBotonera`
    Configura la botónera segons els permisos i estats disponibles.
  - `getActiveTab`
    Retorna la pestanya activa actual.
  - `setTabs`
    Configura les pestanyes del panell.
  - `guardPanelContract`
    Valida els atributs mínims que necessita el trait.


### `app/Http/Traits/Core/SCRUD.php`

#### `Intranet\Http\Traits\Core\SCRUD`
Trait de suport per a operacions bàsiques de tipus SCRUD en controllers.

- Metodes:
  - `modelClass`
    Resol la FQCN del model i la guarda en `$this->class`.

#### `Intranet\Http\Traits\Core\ltrim`
- Metodes:
  - `show`
    Mostra el detall d'un registre.
  - `create`
    Mostra el formulari de creació.
  - `edit`
    Mostra el formulari d'edició.
  - `createWithDefaultValues`
    Crea una nova instància del model amb valors per defecte.
  - `chooseView`
    Retorna la vista per a una acció CRUD concreta.


### `app/Infrastructure/Persistence/Eloquent/AlumnoFct/EloquentAlumnoFctRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\AlumnoFct\EloquentAlumnoFctRepository`
Implementació Eloquent del repositori d'AlumnoFct.

- Metodes:
  - `all`
    {@inheritdoc}
  - `totesFcts`
    {@inheritdoc}
  - `find`
    {@inheritdoc}
  - `findOrFail`
    {@inheritdoc}
  - `firstByIdSao`
    {@inheritdoc}
  - `byAlumno`
    {@inheritdoc}
  - `byAlumnoWithA56`
    {@inheritdoc}
  - `byGrupoEsFct`
    {@inheritdoc}
  - `byGrupoEsDual`
    {@inheritdoc}
  - `reassignProfesor`
    {@inheritdoc}
  - `avalDistinctAlumnoIdsByProfesor`
    {@inheritdoc}
  - `latestAvalByAlumnoAndProfesor`
    {@inheritdoc}
  - `avaluablesNoAval`
    {@inheritdoc}


### `app/Infrastructure/Persistence/Eloquent/Comision/EloquentComisionRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Comision\EloquentComisionRepository`
Implementació Eloquent del repositori de comissions.

- Metodes:
  - `find`
  - `findOrFail`
  - `byDay`
  - `withProfesorByDay`
  - `pendingAuthorization`
  - `authorizationApiList`
  - `authorizeAllPending`
  - `prePayByProfesor`
  - `setEstado`
  - `hasPendingUnpaidByProfesor`
  - `attachFct`
  - `detachFct`


### `app/Infrastructure/Persistence/Eloquent/Empresa/EloquentEmpresaRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Empresa\EloquentEmpresaRepository`
Implementació Eloquent del repositori d'empreses.

- Metodes:
  - `listForGrid`
  - `findForShow`
  - `colaboracionIdsByCycleAndCenters`
  - `cyclesByDepartment`
  - `convenioList`
  - `socialConcertList`
  - `erasmusList`


### `app/Infrastructure/Persistence/Eloquent/Expediente/EloquentExpedienteRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Expediente\EloquentExpedienteRepository`
Implementació Eloquent del repositori d'expedients.

- Metodes:
  - `find`
  - `findOrFail`
  - `createFromRequest`
  - `updateFromRequest`
  - `pendingAuthorization`
  - `readyToPrint`
  - `allTypes`


### `app/Infrastructure/Persistence/Eloquent/FaltaProfesor/EloquentFaltaProfesorRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\FaltaProfesor\EloquentFaltaProfesorRepository`
Implementació Eloquent del repositori de fitxatges de professorat.

- Metodes:
  - `lastTodayByProfesor`
  - `hasFichadoOnDay`
  - `createEntry`
  - `closeExit`
  - `byDayAndProfesor`
  - `rangeByProfesor`


### `app/Infrastructure/Persistence/Eloquent/Fct/EloquentFctRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Fct\EloquentFctRepository`
Implementació Eloquent del repositori FCT.

- Metodes:
  - `find`
  - `findOrFail`
  - `firstByColaboracionAsociacionInstructor`
  - `panelListingByProfesor`
  - `save`
  - `create`
  - `attachAlumno`
  - `detachAlumno`
  - `saveColaborador`
  - `deleteColaborador`
  - `updateColaboradorHoras`
  - `setCotutor`
  - `empresaIdByFct`


### `app/Infrastructure/Persistence/Eloquent/Grupo/EloquentGrupoRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Grupo\EloquentGrupoRepository`
Implementació Eloquent del repositori de Grupo.

- Metodes:
  - `create`
  - `find`
  - `all`
  - `qTutor`
  - `firstByTutor`
  - `largestByTutor`
  - `byCurso`
  - `byDepartamento`
  - `tutoresDniList`
  - `reassignTutor`
  - `misGrupos`
  - `misGruposByProfesor`
  - `withActaPendiente`
  - `byTutorOrSubstitute`
    Cerca el primer grup associat al tutor o al professor substituït.
  - `withStudents`
  - `firstByTutorDual`
  - `byCodes`
  - `allWithTutorAndCiclo`
    Retorna tots els grups amb relacions bàsiques per a llistats de direcció.
  - `misGruposWithCiclo`
    Retorna els grups del professor amb la relació de cicle carregada.


### `app/Infrastructure/Persistence/Eloquent/Horario/EloquentHorarioRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Horario\EloquentHorarioRepository`
Implementació Eloquent del repositori d'horaris.

- Metodes:
  - `semanalByProfesor`
  - `semanalByGrupo`
  - `lectivosByDayAndSesion`
  - `countByProfesorAndDay`
  - `guardiaAllByDia`
  - `guardiaAllByProfesorAndDiaAndSesiones`
  - `guardiaAllByProfesorAndDia`
  - `guardiaAllByProfesor`
  - `firstByProfesorDiaSesion`
  - `byProfesor`
  - `byProfesorWithRelations`
  - `lectivasByProfesorAndDayOrdered`
  - `reassignProfesor`
  - `deleteByProfesor`
  - `gruposByProfesor`
  - `gruposByProfesorDiaAndSesiones`
  - `profesoresByGruposExcept`
  - `primeraByProfesorAndDateOrdered`
  - `firstByModulo`
  - `byProfesorDiaOrdered`
  - `distinctModulos`
  - `create`
  - `forProgramacionImport`
  - `firstForDepartamentoAsignacion`


### `app/Infrastructure/Persistence/Eloquent/Profesor/EloquentProfesorRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Profesor\EloquentProfesorRepository`
Implementació Eloquent del repositori de professorat.

- Metodes:
  - `plantillaOrderedWithDepartamento`
  - `activosByDepartamentosWithHorario`
  - `activosOrdered`
  - `all`
  - `plantilla`
  - `plantillaByDepartamento`
  - `activos`
  - `byDepartamento`
  - `byGrupo`
  - `byGrupoTrabajo`
  - `byDnis`
  - `find`
  - `findOrFail`
  - `findBySustituyeA`
  - `findByCodigo`
  - `findByApiToken`
  - `findByEmail`
  - `plantillaOrderedByDepartamento`
  - `plantillaForResumen`
  - `allOrderedBySurname`
  - `clearFechaBaja`
  - `countByCodigo`
  - `usedCodigosBetween`
    /
  - `create`
  - `withSustituyeAssigned`


### `app/Livewire/BustiaVioleta/AdminList.php`

#### `Intranet\Livewire\BustiaVioleta\AdminList`
- Metodes:
  - `mount`
  - `updating`
  - `viewContact`

#### `Intranet\Livewire\BustiaVioleta\find`
- Metodes:
  - `closeContact`
  - `viewMessage`
  - `closeMessage`
  - `setEstado`
  - `togglePublicable`
  - `delete`
  - `render`


### `app/Livewire/BustiaVioleta/Form.php`

#### `Intranet\Livewire\BustiaVioleta\Form`
- Metodes:
  - `confirmAndSubmit`
  - `mount`
  - `updatedTipus`
  - `reloadCategories`
  - `rules`
  - `updatedFinalitat`
  - `submit`
  - `render`


### `app/Livewire/CalendariComponent.php`

#### `Intranet\Livewire\CalendariComponent`
- Metodes:
  - `dataCompletada`
  - `mount`
  - `updatedMes`
  - `canviarMes`
  - `carregarDies`
  - `seleccionarDia`
  - `guardarCanvis`
  - `resetSeleccionat`
  - `cancelarEdicio`
  - `render`


### `app/Livewire/Controlguardia.php`

#### `Intranet\Livewire\Controlguardia`
- Metodes:
  - `mount`
  - `weekBefore`
  - `weekAfter`
  - `render`


### `app/Livewire/DocumentoTable.php`

#### `Intranet\Livewire\DocumentoTable`
- Metodes:
  - `mount`
  - `updating`
  - `render`
  - `searchableFields`
  - `sortBy`
  - `sanitizeSortField`
  - `sanitizeSortDirection`
  - `isDireccion`


### `app/Livewire/FctCalendar.php`

#### `Intranet\Livewire\FctCalendar`
- Metodes:
  - `mount`
  - `alumno`
  - `addTram`
  - `removeTram`
  - `createCalendar`
  - `deleteCalendar`
  - `loadCalendar`
  - `updateDay`
  - `normalizeHours`
  - `normalizeNullableInt`
  - `exportCalendarPdf`
  - `sendCalendarEmails`
    Enviar calendaris per correu des de la vista (botó).
  - `mapDaysToMonthlyCalendar`
    Retorna el calendari agrupat per mes amb any inclòs per evitar desquadres.
  - `renderPdfContent`
    Genera el contingut PDF per a un calendari concret.
  - `buildLegend`
  - `createZipFromDocuments`
  - `buildDocuments`
    Genera tots els PDFs (alumne + col·laboracions).
  - `dispatchCalendarEmails`
    Envia cada PDF de forma separada (alumne i una per empresa) amb còpia a l'usuari actual.
  - `getTutorContact`
  - `render`


### `app/Livewire/FicharControlDia.php`

#### `Intranet\Livewire\FicharControlDia`
- Metodes:
  - `mount`
  - `updatedFecha`
  - `diaAnterior`
  - `diaSeguent`
  - `render`
  - `profesores`

#### `Intranet\Livewire\profesorService`
- Metodes:
  - `horarios`

#### `Intranet\Livewire\horarioService`
- Metodes:
  - `refreshData`
  - `loadProfesoresForControlDia`


### `app/Livewire/HorariProfessorCanvi.php`

#### `Intranet\Livewire\HorariProfessorCanvi`
- Metodes:
  - `mount`
  - `loadPropuestasDisponibles`
  - `updatedSelectedPropuestaId`
  - `loadHoras`
  - `loadHorario`
  - `loadCambios`
  - `loadPropuestaById`
  - `applyPropuestaData`
  - `applyCambios`
  - `forceMove`
  - `cellClicked`
  - `moveFromTo`
  - `moveSelectedTo`
  - `resetCanvis`
  - `novaProposta`
  - `esborrarProposta`
  - `guardarProposta`
  - `downloadJson`
  - `getCambiosCountProperty`
  - `buildCambios`
  - `latestPropuestaByEstado`
  - `generatePropuestaId`
  - `datesOverlapExisting`
  - `cellHasGuardia`
  - `itemIsGuardia`
  - `render`
  - `profesores`

#### `Intranet\Livewire\profesorService`
- Metodes:
  - `horarios`

#### `Intranet\Livewire\horarioService`
- Metodes: cap


### `app/Mail/AvalAlumne.php`

#### `Intranet\Mail\AvalAlumne`
- Metodes:
  - `__construct`
    Create a new message instance.
  - `build`
    Build the message.


### `app/Mail/AvalFct.php`

#### `Intranet\Mail\AvalFct`
- Metodes:
  - `__construct`
    Create a new message instance.
  - `build`
    Build the message.


### `app/Mail/CertificatAlumneFct.php`

#### `Intranet\Mail\CertificatAlumneFct`
- Metodes:
  - `__construct`
    Create a new message instance.
  - `build`
    Build the message.


### `app/Mail/CertificatInstructorFct.php`

#### `Intranet\Mail\CertificatInstructorFct`
- Metodes:
  - `__construct`
    Create a new message instance.
  - `build`
    Build the message.
  - `certificatColaboradors`

#### `Intranet\Mail\hazPdf`
- Metodes: cap


### `app/Mail/Comunicado.php`

#### `Intranet\Mail\Comunicado`
- Metodes:
  - `getmodel`
  - `__construct`
  - `build`


### `app/Mail/DocumentRequest.php`

#### `Intranet\Mail\DocumentRequest`
Class DocumentRequest

- Metodes:
  - `__construct`
    Create a new message instance.
  - `build`
    Build the message.


### `app/Mail/MatriculaAlumne.php`

#### `Intranet\Mail\MatriculaAlumne`
- Metodes:
  - `__construct`
    Create a new message instance.
  - `build`
    Build the message.


### `app/Mail/ResumenDiario.php`

#### `Intranet\Mail\ResumenDiario`
- Metodes:
  - `__construct`
    Create a new message instance.
  - `build`
    Build the message.


### `app/Mail/TitolAlumne.php`

#### `Intranet\Mail\TitolAlumne`
- Metodes:
  - `__construct`
    Create a new message instance.
  - `build`
    Build the message.


### `app/Notifications/MyResetPassword.php`

#### `Intranet\Notifications\MyResetPassword`
- Metodes:
  - `toMail`
    Get the mail representation of the notification.


### `app/Notifications/mensajePanel.php`

#### `Intranet\Notifications\mensajePanel`
- Metodes:
  - `__construct`
  - `via`
    Get the notification's delivery channels.
  - `toMail`
    Get the mail representation of the notification.
  - `toArray`
    Get the array representation of the notification.


### `app/OpenApi/ApiCustomEndpoints.php`

#### `Intranet\OpenApi\ApiCustomEndpoints`
Documentacio OpenAPI per a endpoints custom (no REST resource) definits en routes/api.php.

- Metodes:
  - `alumnofct_grupo_grupo_get`
  - `convenio_get`
  - `miIp_get`
  - `actividad_actividad_getFiles_get`
  - `server_time_get`
  - `porta_obrir_get`
  - `porta_obrir_automatica_post`
  - `eventPortaSortida_post`
  - `eventPorta_post`
  - `presencia_resumen_rango_get`
  - `grupo_list_id_get`
  - `alumnofct_grupo_dual_get`
  - `fct_id_alFct_get`
  - `fct_id_alFct_post`
  - `comision_dni_prePay_put`
  - `autorizar_comision_get`
  - `notification_id_get`
  - `profesor_dni_rol_get`
  - `profesor_rol_rol_get`
  - `doficha_get`
  - `ipGuardias_get`
  - `verficha_get`
  - `itaca_dia_idProfesor_get`
  - `itaca_post`
  - `aula_get`
  - `faltaProfesor_horas_condicion_get`
  - `material_cambiarUbicacion_put`
  - `material_cambiarEstado_put`
  - `material_cambiarUnidad_put`
  - `material_cambiarInventario_put`
  - `material_espacio_espacio_get`
  - `inventario_get`
  - `inventario_espai_get`
  - `guardia_range_get`
  - `alumnoGrupoModulo_dni_modulo_get`
  - `horario_idProfesor_guardia_get`
  - `horariosDia_fecha_get`
  - `asistencia_cambiar_put`
  - `reunion_idReunion_alumno_idAlumno_put`
  - `tiporeunion_id_get`
  - `modulo_id_get`
  - `horarioChange_dni_get`
  - `horarioChange_dni_post`
  - `centro_fusionar_post`
  - `colaboracion_instructores_id_get`
  - `colaboracion_colaboracion_resolve_get`
  - `colaboracion_colaboracion_refuse_get`
  - `colaboracion_colaboracion_unauthorize_get`
  - `colaboracion_colaboracion_switch_get`
  - `colaboracion_colaboracion_telefonico_post`
  - `colaboracion_colaboracion_book_post`
  - `documentacionFCT_documento_get`
  - `signatura_get`
  - `signatura_director_get`
  - `signatura_a1_get`
  - `matricula_token_get`
  - `test_matricula_token_get`
  - `alumno_dni_foto_post`
  - `alumno_dni_dades_post`
  - `matricula_send_post`
  - `lote_id_articulos_get`
  - `lote_id_articulos_put`
  - `articuloLote_id_materiales_get`
  - `attachFile_post`
  - `getAttached_modelo_id_get`
  - `getNameAttached_modelo_id_filename_get`
  - `removeAttached_modelo_id_file_get`
  - `activity_id_move_fct_get`
  - `tutoriagrupo_id_get`


### `app/OpenApi/ApiDomainSchemas.php`

#### `Intranet\OpenApi\ApiDomainSchemas`
Esquemes de domini reutilitzables per a la documentacio OpenAPI.

- Metodes: cap


### `app/OpenApi/ApiResourceDocumentation.php`

#### `Intranet\OpenApi\ApiResourceDocumentation`
Documentacio OpenAPI de rutes REST definides amb Route::resource.

- Metodes:
  - `alumnofctResourceEndpoints`
  - `projecteResourceEndpoints`
  - `actividadResourceEndpoints`
  - `programacionResourceEndpoints`
  - `reunionResourceEndpoints`
  - `faltaResourceEndpoints`
  - `documentoResourceEndpoints`
  - `modulo_cicloResourceEndpoints`
  - `resultadoResourceEndpoints`
  - `comisionResourceEndpoints`
  - `instructorResourceEndpoints`
  - `ipguardiaResourceEndpoints`
  - `settingResourceEndpoints`
  - `ppollResourceEndpoints`
  - `profesorResourceEndpoints`
  - `faltaprofesorResourceEndpoints`
  - `materialResourceEndpoints`
  - `materialbajaResourceEndpoints`
  - `espacioResourceEndpoints`
  - `guardiaResourceEndpoints`
  - `departamentoResourceEndpoints`
  - `reservaResourceEndpoints`
  - `ordenreunionResourceEndpoints`
  - `colaboracionResourceEndpoints`
  - `centroResourceEndpoints`
  - `grupotrabajoResourceEndpoints`
  - `empresaResourceEndpoints`
  - `ordentrabajoResourceEndpoints`
  - `incidenciaResourceEndpoints`
  - `tipoincidenciaResourceEndpoints`
  - `expedienteResourceEndpoints`
  - `solicitudResourceEndpoints`
  - `tipoexpedienteResourceEndpoints`
  - `alumnogrupoResourceEndpoints`
  - `activityResourceEndpoints`
  - `cursoResourceEndpoints`
  - `cicloResourceEndpoints`
  - `taskResourceEndpoints`
  - `horarioResourceEndpoints`
  - `horaResourceEndpoints`
  - `alumnoresultadoResourceEndpoints`
  - `loteResourceEndpoints`
  - `articuloloteResourceEndpoints`
  - `articuloResourceEndpoints`
  - `cotxeResourceEndpoints`
  - `tipoactividadResourceEndpoints`


### `app/OpenApi/OpenApiSpec.php`

#### `Intranet\OpenApi\OpenApiSpec`
Especificacio global OpenAPI per a la API del projecte.

- Metodes: cap


### `app/Presentation/AlumnoFct/AlumnoFctPresenter.php`

#### `Intranet\Presentation\AlumnoFct\AlumnoFctPresenter`
- Metodes:
  - `__construct`
  - `cssClass`
    Retorna la classe CSS de fons segons estat/temporalitat del registre.
  - `centerName`
  - `studentShortName`
  - `studentNameWithMinorIcon`
  - `remainingPracticeTimeLabel`
  - `contactName`
  - `fullName`
  - `completedHoursLabel`
  - `instructorName`
  - `printableId`
  - `backgroundByDates`


### `app/Presentation/Crud/ActividadCrudSchema.php`

#### `Intranet\Presentation\Crud\ActividadCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/AlumnoFctAvalCrudSchema.php`

#### `Intranet\Presentation\Crud\AlumnoFctAvalCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/AlumnoFctCrudSchema.php`

#### `Intranet\Presentation\Crud\AlumnoFctCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/ColaboracionCrudSchema.php`

#### `Intranet\Presentation\Crud\ColaboracionCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/ComisionCrudSchema.php`

#### `Intranet\Presentation\Crud\ComisionCrudSchema`
- Metodes:
  - `requestRules`
    Regles del formulari principal de comissió.


### `app/Presentation/Crud/CotxeCrudSchema.php`

#### `Intranet\Presentation\Crud\CotxeCrudSchema`
- Metodes:
  - `requestRules`
    Regles de validació del formulari de cotxe.


### `app/Presentation/Crud/CursoCrudSchema.php`

#### `Intranet\Presentation\Crud\CursoCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/DocumentoCrudSchema.php`

#### `Intranet\Presentation\Crud\DocumentoCrudSchema`
- Metodes:
  - `projectFormFields`
    Formulari del flux de projecte.
  - `qualitatFormFields`
    Formulari del flux de qualitat.
  - `editFormFields`
    Formulari d'edició segons siga enllaç o fitxer.


### `app/Presentation/Crud/EmpresaCrudSchema.php`

#### `Intranet\Presentation\Crud\EmpresaCrudSchema`
- Metodes:
  - `requestRules`
    Regles de validació per al formulari d'empresa.


### `app/Presentation/Crud/ExpedienteCrudSchema.php`

#### `Intranet\Presentation\Crud\ExpedienteCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/FaltaCrudSchema.php`

#### `Intranet\Presentation\Crud\FaltaCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/FctCrudSchema.php`

#### `Intranet\Presentation\Crud\FctCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/GrupoCrudSchema.php`

#### `Intranet\Presentation\Crud\GrupoCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/HorarioCrudSchema.php`

#### `Intranet\Presentation\Crud\HorarioCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/IncidenciaCrudSchema.php`

#### `Intranet\Presentation\Crud\IncidenciaCrudSchema`
- Metodes:
  - `requestRules`
    Regles de request afegint validació de fitxer d'imatge.
  - `editFormFields`
    Configuració de formulari d'edició.


### `app/Presentation/Crud/InstructorCrudSchema.php`

#### `Intranet\Presentation\Crud\InstructorCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/MaterialCrudSchema.php`

#### `Intranet\Presentation\Crud\MaterialCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/ReunionCrudSchema.php`

#### `Intranet\Presentation\Crud\ReunionCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/SolicitudCrudSchema.php`

#### `Intranet\Presentation\Crud\SolicitudCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/TaskCrudSchema.php`

#### `Intranet\Presentation\Crud\TaskCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/TipoActividadCrudSchema.php`

#### `Intranet\Presentation\Crud\TipoActividadCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/TipoIncidenciaCrudSchema.php`

#### `Intranet\Presentation\Crud\TipoIncidenciaCrudSchema`
- Metodes:
  - `requestRules`
    Regles de validacio per a create/update.


### `app/Presentation/Crud/TutoriaCrudSchema.php`

#### `Intranet\Presentation\Crud\TutoriaCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/TutoriaGrupoCrudSchema.php`

#### `Intranet\Presentation\Crud\TutoriaGrupoCrudSchema`
- Metodes: cap


### `app/Providers/AppServiceProvider.php`

#### `Intranet\Providers\AppServiceProvider`
Proveidor principal de serveis de l'aplicació.

- Metodes:
  - `boot`
    Bootstrap any application services.
  - `register`
    Register any application services.

#### `Intranet\Providers\config`
- Metodes: cap

#### `Intranet\Providers\app`
- Metodes: cap

#### `Intranet\Providers\EloquentAlumnoFctRepository`
- Metodes: cap

#### `Intranet\Providers\EloquentComisionRepository`
- Metodes: cap

#### `Intranet\Providers\EloquentProfesorRepository`
- Metodes: cap

#### `Intranet\Providers\EloquentHorarioRepository`
- Metodes: cap

#### `Intranet\Providers\EloquentGrupoRepository`
- Metodes: cap

#### `Intranet\Providers\EloquentEmpresaRepository`
- Metodes: cap

#### `Intranet\Providers\EloquentExpedienteRepository`
- Metodes: cap

#### `Intranet\Providers\EloquentFaltaProfesorRepository`
- Metodes: cap

#### `Intranet\Providers\EloquentFctRepository`
- Metodes: cap


### `app/Providers/AuthServiceProvider.php`

#### `Intranet\Providers\AuthServiceProvider`
- Metodes: cap

#### `Intranet\Providers\EmpresaPolicy`
- Metodes: cap

#### `Intranet\Providers\Fct`
- Metodes: cap

#### `Intranet\Providers\FctPolicy`
- Metodes: cap

#### `Intranet\Providers\Falta`
- Metodes: cap

#### `Intranet\Providers\FaltaPolicy`
- Metodes: cap

#### `Intranet\Providers\Ciclo`
- Metodes: cap

#### `Intranet\Providers\CicloPolicy`
- Metodes: cap

#### `Intranet\Providers\Departamento`
- Metodes: cap

#### `Intranet\Providers\DepartamentoPolicy`
- Metodes: cap

#### `Intranet\Providers\Curso`
- Metodes: cap

#### `Intranet\Providers\CursoPolicy`
- Metodes: cap

#### `Intranet\Providers\Articulo`
- Metodes: cap

#### `Intranet\Providers\ArticuloPolicy`
- Metodes: cap

#### `Intranet\Providers\IpGuardia`
- Metodes: cap

#### `Intranet\Providers\IpGuardiaPolicy`
- Metodes: cap

#### `Intranet\Providers\Lote`
- Metodes: cap

#### `Intranet\Providers\LotePolicy`
- Metodes: cap

#### `Intranet\Providers\Resultado`
- Metodes: cap

#### `Intranet\Providers\ResultadoPolicy`
- Metodes: cap

#### `Intranet\Providers\Solicitud`
- Metodes: cap

#### `Intranet\Providers\SolicitudPolicy`
- Metodes: cap

#### `Intranet\Providers\Cotxe`
- Metodes: cap

#### `Intranet\Providers\CotxePolicy`
- Metodes: cap

#### `Intranet\Providers\Expediente`
- Metodes: cap

#### `Intranet\Providers\ExpedientePolicy`
- Metodes: cap

#### `Intranet\Providers\Signatura`
- Metodes: cap

#### `Intranet\Providers\SignaturaPolicy`
- Metodes: cap

#### `Intranet\Providers\TutoriaGrupo`
- Metodes: cap

#### `Intranet\Providers\TutoriaGrupoPolicy`
- Metodes: cap

#### `Intranet\Providers\GrupoTrabajo`
- Metodes: cap

#### `Intranet\Providers\GrupoTrabajoPolicy`
- Metodes: cap

#### `Intranet\Providers\Menu`
- Metodes: cap

#### `Intranet\Providers\MenuPolicy`
- Metodes: cap

#### `Intranet\Providers\Modulo_ciclo`
- Metodes: cap

#### `Intranet\Providers\ModuloCicloPolicy`
- Metodes: cap

#### `Intranet\Providers\PPoll`
- Metodes: cap

#### `Intranet\Providers\PPollPolicy`
- Metodes: cap

#### `Intranet\Providers\Option`
- Metodes: cap

#### `Intranet\Providers\OptionPolicy`
- Metodes: cap

#### `Intranet\Providers\Colaboracion`
- Metodes: cap

#### `Intranet\Providers\ColaboracionPolicy`
- Metodes: cap

#### `Intranet\Providers\Projecte`
- Metodes: cap

#### `Intranet\Providers\ProjectePolicy`
- Metodes: cap

#### `Intranet\Providers\ImportRun`
- Metodes: cap

#### `Intranet\Providers\ImportRunPolicy`
- Metodes: cap

#### `Intranet\Providers\Incidencia`
- Metodes: cap

#### `Intranet\Providers\IncidenciaPolicy`
- Metodes: cap

#### `Intranet\Providers\MaterialBaja`
- Metodes: cap

#### `Intranet\Providers\MaterialBajaPolicy`
- Metodes: cap

#### `Intranet\Providers\Profesor`
- Metodes: cap

#### `Intranet\Providers\ProfesorPolicy`
- Metodes: cap

#### `Intranet\Providers\Reunion`
- Metodes: cap

#### `Intranet\Providers\ReunionPolicy`
- Metodes: cap

#### `Intranet\Providers\Espacio`
- Metodes: cap

#### `Intranet\Providers\EspacioPolicy`
- Metodes: cap

#### `Intranet\Providers\Documento`
- Metodes: cap

#### `Intranet\Providers\DocumentoPolicy`
- Metodes: cap

#### `Intranet\Providers\TipoActividad`
- Metodes: cap

#### `Intranet\Providers\TipoActividadPolicy`
- Metodes: cap

#### `Intranet\Providers\TipoIncidencia`
- Metodes: cap

#### `Intranet\Providers\TipoIncidenciaPolicy`
- Metodes: cap

#### `Intranet\Providers\Actividad`
- Metodes: cap

#### `Intranet\Providers\ActividadPolicy`
- Metodes: cap

#### `Intranet\Providers\Comision`
- Metodes: cap

#### `Intranet\Providers\ComisionPolicy`
- Metodes: cap

#### `Intranet\Providers\Task`
- Metodes: cap

#### `Intranet\Providers\TaskPolicy`
- Metodes: cap

#### `Intranet\Providers\Setting`
- Metodes: cap

#### `Intranet\Providers\SettingPolicy`
- Metodes: cap

#### `Intranet\Providers\boot`
- Metodes:
  - `boot`
    Register any authentication / authorization services.


### `app/Providers/BroadcastServiceProvider.php`

#### `Intranet\Providers\BroadcastServiceProvider`
- Metodes:
  - `boot`
    Bootstrap any application services.


### `app/Providers/EventServiceProvider.php`

#### `Intranet\Providers\EventServiceProvider`
Registre d'esdeveniments de l'aplicacio.

- Metodes:
  - `boot`
    Register any events for your application.


### `app/Providers/HelperServiceProvider.php`

#### `Intranet\Providers\HelperServiceProvider`
- Metodes:
  - `boot`
    Bootstrap the application services.
  - `register`
    Register the application services.


### `app/Providers/RouteServiceProvider.php`

#### `Intranet\Providers\RouteServiceProvider`
- Metodes:
  - `boot`
    Define your route model bindings, pattern filters, etc.
  - `map`
    Define the routes for the application.
  - `mapWebRoutes`
    Define the "web" routes for the application.
  - `mapApiRoutes`
    Define the "api" routes for the application.
  - `profesorRoutes`
    Define the "auth" routes for the application.
  - `adminRoutes`
  - `todosRoutes`
  - `consergeRoutes`
  - `direccionRoutes`
  - `alumnoRoutes`
  - `mantenimientoRoutes`
  - `jefeRoutes`


### `app/Providers/SettingsProvider.php`

#### `Intranet\Providers\SettingsProvider`
- Metodes:
  - `register`
    Register services.
  - `boot`
    Bootstrap services.


### `app/Providers/TelescopeServiceProvider.php`

#### `Intranet\Providers\TelescopeServiceProvider`
- Metodes:
  - `register`
    Register any application services.
  - `hideSensitiveRequestDetails`
    Prevent sensitive request details from being logged by Telescope.
  - `gate`
    Register the Telescope gate.


### `app/Providers/ValidationServiceProvider.php`

#### `Intranet\Providers\ValidationServiceProvider`
- Metodes:
  - `boot`
    Bootstrap the application services.
  - `register`
    Register the application services.


### `app/Providers/ViewComposerServiceProvider.php`

#### `Intranet\Providers\ViewComposerServiceProvider`
- Metodes:
  - `boot`

#### `Intranet\Providers\isInside`
- Metodes: cap


### `app/Sao/Actions/SAOAction.php`

#### `Intranet\Sao\Actions\SAOAction`
Entrypoint unificat per a les operacions SAO.

- Metodes:
  - `__construct`

#### `Intranet\Sao\Actions\setFireFoxCapabilities`
- Metodes:
  - `setFireFoxCapabilities`
    Retorna les capacitats Firefox necessàries per a descàrregues SAO.
  - `index`
    /
  - `executeLegacyAction`
    Manté compatibilitat amb accions SAO legacy no migrades.


### `app/Sao/Actions/SaoActionInterface.php`

#### `Intranet\Sao\Actions\SaoActionInterface`
Contracte base per a una acció SAO executable amb un driver Selenium.

- Metodes:
  - `index`
    Executa l'acció SAO.


### `app/Sao/Documents/A1DocumentService.php`

#### `Intranet\Sao\Documents\A1DocumentService`
Gestiona la descàrrega de l'annex A1/A1DUAL.

- Metodes:
  - `__construct`
  - `download`
    Descarrega l'annex A1/A1DUAL.


### `app/Sao/Documents/A2DocumentService.php`

#### `Intranet\Sao\Documents\A2DocumentService`
Gestiona la descàrrega i signatura dels annexes A2 i A3.

- Metodes:
  - `__construct`
  - `download`
    Descarrega i, si cal, firma digitalment l'annex A2/A3.


### `app/Sao/Documents/A5DocumentService.php`

#### `Intranet\Sao\Documents\A5DocumentService`
Gestiona la descàrrega i processat de l'annex A5.

- Metodes:
  - `__construct`
  - `download`
    Descarrega, processa i opcionalment firma l'annex A5.


### `app/Sao/SaoAnnexesAction.php`

#### `Intranet\Sao\SaoAnnexesAction`
Acció SAO per descarregar i enllaçar annexos.

- Metodes:
  - `__construct`
  - `execute`
  - `index`
  - `processFcts`
  - `getValidFcts`
  - `isAnnexDownloaded`
  - `downloadAnnex`
  - `saveAnnex`
  - `deleteSignatures`
  - `closePopup`


### `app/Sao/SaoComparaAction.php`

#### `Intranet\Sao\SaoComparaAction`
Acció SAO per comparar dades Intranet vs SAO.

- Metodes:
  - `compara`
  - `igual`
  - `index`
  - `descomposaClau`


### `app/Sao/SaoDocumentsAction.php`

#### `Intranet\Sao\SaoDocumentsAction`
Gestió de documents SAO (A1, A2 i A5).

- Metodes:
  - `__construct`
  - `setFireFoxCapabilities`
  - `index`

#### `Intranet\Sao\send`
- Metodes: cap


### `app/Sao/SaoImportaAction.php`

#### `Intranet\Sao\SaoImportaAction`
Acció SAO per importar dades de FCT des de la plataforma externa.

- Metodes:
  - `buscaCentro`
  - `extractFromModal`
  - `extractFromEdit`
    /
  - `deepMerge`
    Funció per fusionar profundament dos arrays
  - `selectDirectorFct`
  - `index`

#### `Intranet\Sao\firstByTutor`
- Metodes:
  - `importa`
  - `getCentro`
  - `getColaboracion`
  - `getAlumno`
    /
  - `getEmpresa`
    /
  - `getIdSao`
    /
  - `getPeriode`
    /
  - `altaInstructor`
    /
  - `getDni`
    /
  - `getFct`
    /
  - `saveFctAl`
    /
  - `extractPage`
    /


### `app/Sao/SaoSyncAction.php`

#### `Intranet\Sao\SaoSyncAction`
Acció SAO per sincronitzar dades d'alumnat FCT.

- Metodes:
  - `__construct`
  - `execute`
  - `index`
  - `processFcts`
  - `getValidFcts`
  - `obtenirHoresFct`
  - `actualitzarFct`
  - `consultaDiario`


### `app/Sao/Support/SaoDownloadManager.php`

#### `Intranet\Sao\Support\SaoDownloadManager`
Operacions comunes de fitxers temporals en processos SAO.

- Metodes:
  - `tempDirectory`
    Retorna el directori temporal compartit per SAO.
  - `waitForFile`
    Espera a l'existència d'un fitxer dins del timeout indicat.
  - `unlinkIfExists`
    Esborra un fitxer si existeix.


### `app/Sao/Support/SaoNavigator.php`

#### `Intranet\Sao\Support\SaoNavigator`
Utilitats bàsiques de navegació per al flux SAO.

- Metodes:
  - `backToMain`
    Torna a la pantalla principal de SAO i aplica una xicoteta espera.


### `app/Sao/Support/SaoRunner.php`

#### `Intranet\Sao\Support\SaoRunner`
Gestiona el cicle de vida de Selenium per a accions SAO.

- Metodes:
  - `run`
    Executa una acció SAO amb login previ i tancament garantit de sessió.
  - `executeAction`
    Resol i executa el mètode `index` de l'acció SAO.


### `app/Support/Concerns/DatesTranslator.php`

#### `Intranet\Support\Concerns\DatesTranslator`
- Metodes:
  - `getCreatedAttribute`
  - `getUpdatedAttribute`
  - `getSalidaAttribute`
  - `getEntradaAttribute`


### `app/Support/Facades/Field.php`

#### `Intranet\Support\Facades\Field`
Façana de compatibilitat per a l'API `Field::*`.

- Metodes:
  - `getFacadeAccessor`
    /


### `app/Support/Fct/DocumentoFctConfig.php`

#### `Intranet\Support\Fct\DocumentoFctConfig`
- Metodes:
  - `__construct`
  - `__get`
  - `__isset`
  - `__set`
  - `getFinder`
  - `getResource`


### `app/Support/Helpers/MyHelpers.php`

#### `asset_nocache`
- Metodes:
  - `asset_nocache`
    Genera una URL d'asset amb versió basada en `filemtime` per evitar caché antic.
  - `profile_photo_url`
    Retorna la URL de la foto de perfil o un placeholder si no existeix.
  - `emailConselleria`
    Genera un correu institucional de Conselleria a partir del nom i cognoms.
  - `eliminarTildes`
    Elimina espais i accents d'una cadena.
  - `genre`
    /
  - `voteValue`
    Ajusta aleatòriament el valor d'una votació per a un DNI concret.
  - `evaluacion`
    Retorna l'avaluació actual segons les dates configurades en `curso.evaluaciones`.
  - `curso`
    Retorna el curs acadèmic actual (`YYYY-YYYY+1`).
  - `cursoAnterior`
    Retorna el curs acadèmic anterior.
  - `fullDireccion`
    Devuelve la direccion completa
  - `cargo`
    Retorna el professor associat a un càrrec configurat.
  - `signatura`
    Retorna la forma textual de signatura adequada al document i gènere de qui signa.
  - `imgSig`
    Retorna el codi d'imatge de signatura per a un document.
  - `userIsNameAllow`
    Mira si al usuario actual le esta permitido el nombre de rol
  - `authUser`
    Retorna l'usuari autenticat de `profesor` o, en defecte, d'`alumno`.
  - `apiAuthUser`
    Resol l'usuari professor per context API.
  - `isProfesor`
    Comprova si l'usuari autenticat és professor.
  - `userIsAllow`
    Mira si al usuario actual le esta permitido el  rol
  - `roleIsInArray`
    /
  - `nameRolesUser`
    Devuelve todos los roles de un usuario
  - `rolesUser`
    Devuelve todos los roles de un usuario
  - `esRol`
    Comprova si un rol concret està inclòs dins del rol compost de l'usuari.
  - `isAdmin`
    Comprova si l'usuari autenticat té rol d'administració (11).
  - `usersWithRol`
    Retorna els DNI dels professors actius que compleixen un rol determinat.
  - `rol`
    Devuelve el rol de un conjunto de roles
  - `blankTrans`
    /
  - `isblankTrans`
    Indica si una clau de traducció no existeix.
  - `valorReal`
    Resol una propietat simple o anidada (`foo->bar`) d'un element.
  - `hazArray`
    Construeix un array associatiu a partir d'una col·lecció/lista d'elements.
  - `extrauValor`
    /
  - `getClase`
    Retorna el nom curt d'una classe o entitat.
  - `getClass`
    Retorna el nom curt d'una FQCN d'entitat.
  - `avisa`
    Envia notificació interna a alumne/professor.
  - `primryKey`
    Retorna el valor de la clau primària de l'element.
  - `subsRequest`
    Substitueix valors en un Request i retorna una còpia.
  - `mdFind`
    Retalla un fragment de documentació markdown des d'un enllaç concret.
  - `existsHelp`
    Retorna si hi ha ajuda associada a una URL de menú.
  - `inRol`
    Prepara una estructura `['roles' => [...]]` per passar-la a components/polítiques de UI.
  - `existsTranslate`
    Retorna la traducció o `null` si no existeix.
  - `firstWord`
    Retorna la primera paraula d'una cadena separada per espais.
  - `cargaDatosCertificado`
    /
  - `getClientIpAddress`
    Obté l'adreça IP client des de capçaleres comunes o `REMOTE_ADDR`.
  - `isPrivateAddress`
    Comprova si una IP pertany a rangs privats/predefinits de confiança.
  - `mbUcfirst`
    Capitalitza el primer caràcter d'una cadena multibyte.
  - `nomAmbTitol`
    Afig tractament (`en`, `na`, `n'`) a un nom segons sexe i vocal inicial.
  - `deleteDir`
    Elimina tots els fitxers d'una carpeta i, després, la carpeta.
  - `provincia`
    Retorna el nom de província a partir del codi postal espanyol.
  - `replaceCachitos`
    Substitueix tokens `[nom]` per `@include('email.fct.cachitos.nom')` de manera recursiva.
  - `in_substr`
    Retalla valors llargs de forma segura, normalitzant tipus comuns (array, bool, dates...).
  - `array_depth`
    Calcula la profunditat màxima d'un array multidimensional.
  - `asociacion_fct`
    Retorna la clau associada a un tipus FCT en configuració.


### `app/UI/Botones/Boton.php`

#### `Intranet\UI\Botones\Boton`
- Metodes:
  - `translateText`
    Resol el text del botó amb traduccions i textos per defecte.
  - `translateExistingText`
    Tradueix un text ja proporcionat si hi ha clau existent.
  - `__construct`
    /
  - `__set`
    Assigna atributs dinàmics.
  - `__get`
    Llig atributs dinàmics.
  - `show`
    Retorna el botó renderitzat perquè el caller el puga imprimir.
  - `render`
    Retorna el botó renderitzat si l'usuari té permís.
  - `html`
  - `split`
    Separa model/acció a partir del `href`.
  - `cleanAttr`
    Neteja valors per a atributs HTML (classes, id, etc.).
  - `isDisabled`
    Indica si el botó està deshabilitat.
  - `clase`
    Retorna la classe CSS final del botó.
  - `id`
    Retorna l'ID HTML del botó.
  - `disabledAttr`
    Retorna atributs per a desactivar el botó segons el tipus.
  - `data`
    Retorna els atributs `data-*` en format HTML.
  - `href`
    Construeix l'URL final del botó.
  - `getPrefix`
    Obté el prefix de ruta segons el mode `relative`.
  - `getPostfix`
    Obté el sufix de ruta si està definit.
  - `getAdress`
    Construeix l'adreça final a partir de prefix, clau i sufix.


### `app/UI/Botones/BotonBasico.php`

#### `Intranet\UI\Botones\BotonBasico`
Botó bàsic amb renderització d'enllaç i icona opcional.

- Metodes:
  - `html`
    Genera el HTML del botó bàsic.


### `app/UI/Botones/BotonConfirmacion.php`

#### `Intranet\UI\Botones\BotonConfirmacion`
Botó bàsic amb classe de confirmació.

- Metodes: cap


### `app/UI/Botones/BotonElemento.php`

#### `Intranet\UI\Botones\BotonElemento`
- Metodes:
  - `show`
    Mostra el botó si compleix les condicions de visibilitat.
  - `render`
    Retorna el botó renderitzat si compleix les condicions.
  - `isVisible`
    Avalua si l'element compleix les condicions de visibilitat.
  - `extractConditions`
    Extreu i avalua les condicions configurades.
  - `avalAndConditions`
    Avalua condicions amb AND.
  - `avalOrConditions`
    Avalua condicions amb OR.
  - `avalCondition`
    Avalua una condició individual.


### `app/UI/Botones/BotonIcon.php`

#### `Intranet\UI\Botones\BotonIcon`
Botó amb icona (font-awesome).

- Metodes:
  - `html`
    Genera el HTML del botó amb icona.


### `app/UI/Botones/BotonImg.php`

#### `Intranet\UI\Botones\BotonImg`
Botó amb icona en format imatge/font-awesome.

- Metodes:
  - `__construct`
    /
  - `html`
    Genera el HTML del botó amb imatge.


### `app/UI/Botones/BotonPost.php`

#### `Intranet\UI\Botones\BotonPost`
Botó per a enviament de formulari (submit).

- Metodes:
  - `html`
    Genera el HTML del botó tipus submit.


### `app/UI/Panels/Panel.php`

#### `Intranet\UI\Panels\Panel`
Contenidor de pestanyes, botons i dades de vista per als panells CRUD.

- Metodes:
  - `__construct`
    /
  - `render`
    Ompli el panell i retorna la vista final.
  - `setBotonera`
    Crea una botonera estàndard a partir de noms d'accions.
  - `setBoton`
    Afig un botó al grup indicat.
  - `setBothBoton`
    Afig el mateix botó a `grid` i `profile`.
  - `setPestana`
    Afig una pestanya o substituïx la primera.
  - `countPestana`
    Retorna el nombre de pestanyes disponibles.
  - `setTitulo`
    /
  - `desactivaAll`
  - `getModel`
    Retorna el nom del model associat al panell.
  - `getPestanas`
    Retorna totes les pestanyes del panell.
  - `getRejilla`
  - `setRejilla`
  - `getBotones`
    /
  - `countBotones`
    Retorna quants botons hi ha en un grup.
  - `getTitulo`
    Resol el títol traduït segons el model i l'acció.
  - `setElementos`
    /
  - `getElemento`
    /
  - `getElementos`
  - `getPaginator`
    Retorna el paginador si la cerca original era paginada.
  - `activaPestana`
    Activa una pestanya pel nom i desactiva la resta.
  - `getView`
  - `__set`
  - `__get`
    /
  - `ensureValidBotonType`
    Valida que el tipus pertany a la botonera coneguda.
  - `feedPanel`
    /
  - `getLastPestanaWithModals`


### `app/UI/Panels/Pestana.php`

#### `Intranet\UI\Panels\Pestana`
- Metodes:
  - `__construct`
    /
  - `setVista`
    /
  - `getVista`
    /
  - `getNombre`
    /
  - `getActiva`
    /
  - `getInclude`
    /
  - `setInclude`
    /
  - `setActiva`
    /
  - `getFiltro`
    /
  - `getRejilla`
    /
  - `setRejilla`
    /
  - `getLabel`
    Retorna l'etiqueta traduïda; si no hi ha traducció, usa el nom original.


### `app/View/Components/Activity.php`

#### `Intranet\View\Components\Activity`
- Metodes:
  - `__construct`
  - `render`
  - `getClass`
  - `getAction`


### `app/View/Components/Botones.php`

#### `Intranet\View\Components\Botones`
- Metodes:
  - `__construct`
  - `render`


### `app/View/Components/Form/DynamicFieldRenderer.php`

#### `Intranet\View\Components\Form\DynamicFieldRenderer`
- Metodes:
  - `__construct`
  - `render`


### `app/View/Components/Form/FileInput.php`

#### `Intranet\View\Components\Form\FileInput`
- Metodes:
  - `__construct`
  - `render`


### `app/View/Components/Form/GenericField.php`

#### `Intranet\View\Components\Form\GenericField`
- Metodes:
  - `__construct`
  - `render`


### `app/View/Components/Form/TagInput.php`

#### `Intranet\View\Components\Form\TagInput`
- Metodes:
  - `__construct`
  - `render`


### `app/View/Components/Grid/Header.php`

#### `Intranet\View\Components\Grid\Header`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Grid/Row.php`

#### `Intranet\View\Components\Grid\Row`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Grid/Table.php`

#### `Intranet\View\Components\Grid\Table`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Horari.php`

#### `Intranet\View\Components\Horari`
- Metodes:
  - `__construct`
  - `render`


### `app/View/Components/Label.php`

#### `Intranet\View\Components\Label`
- Metodes:
  - `__construct`
    /
  - `render`


### `app/View/Components/Layouts/App.php`

#### `Intranet\View\Components\Layouts\App`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Footer.php`

#### `Intranet\View\Components\Layouts\Footer`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Leftside.php`

#### `Intranet\View\Components\Layouts\Leftside`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Meta.php`

#### `Intranet\View\Components\Layouts\Meta`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Page.php`

#### `Intranet\View\Components\Layouts\Page`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Panel.php`

#### `Intranet\View\Components\Layouts\Panel`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Pestanas.php`

#### `Intranet\View\Components\Layouts\Pestanas`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Titlebar.php`

#### `Intranet\View\Components\Layouts\Titlebar`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Topmenu.php`

#### `Intranet\View\Components\Layouts\Topmenu`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Topnav.php`

#### `Intranet\View\Components\Layouts\Topnav`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/Llist.php`

#### `Intranet\View\Components\Llist`
- Metodes:
  - `__construct`
    /
  - `render`


### `app/View/Components/Modal.php`

#### `Intranet\View\Components\Modal`
- Metodes:
  - `__construct`
    Modal constructor.
  - `render`


### `app/View/Components/Note.php`

#### `Intranet\View\Components\Note`
- Metodes:
  - `__construct`
    /
  - `render`


### `app/View/Components/ReunionItem.php`

#### `Intranet\View\Components\ReunionItem`
- Metodes:
  - `__construct`
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/UserProfile.php`

#### `Intranet\View\Components\UserProfile`
- Metodes:
  - `__construct`
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/UserTabs.php`

#### `Intranet\View\Components\UserTabs`
- Metodes:
  - `__construct`
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/ui/Errors.php`

#### `Intranet\View\Components\ui\Errors`
- Metodes:
  - `__construct`
    Create a new component instance.
  - `render`
    Get the view / contents that represent the component.


### `app/View/Components/ui/Tabs.php`

#### `Intranet\View\Components\ui\Tabs`
- Metodes:
  - `__construct`
  - `render`



