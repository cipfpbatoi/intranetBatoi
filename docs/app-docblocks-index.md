# Index Doc-Blocks de l aplicacio

Fitxer generat automaticament des dels doc-blocks de `app/`.

## Controladors

### `app/Http/Controllers/API/ActividadController.php`

#### `Intranet\Http\Controllers\API\ActividadController`
- Metodes:
  - **`getFiles`**($id)


### `app/Http/Controllers/API/ActivityController.php`

#### `Intranet\Http\Controllers\API\ActivityController`
- Metodes:
  - **`move`**($id, $fct)


### `app/Http/Controllers/API/AlumnoController.php`

#### `Intranet\Http\Controllers\API\AlumnoController`
- Metodes:
  - **`putImage`**(Request $request, $id)
  - **`putDades`**(Request $request, $id)


### `app/Http/Controllers/API/AlumnoFctController.php`

#### `Intranet\Http\Controllers\API\AlumnoFctController`
- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null, ?AlumnoFctService $alumnoFctService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\API\grupoService`
- Metodes:
  - **`alumnoFcts`**(): AlumnoFctService

#### `Intranet\Http\Controllers\API\alumnoFctService`
- Metodes:
  - **`indice`**($grupo)
  - **`dual`**($grupo)
  - **`update`**(Request $request, $id)
  - **`show`**($id)


### `app/Http/Controllers/API/AlumnoGrupoController.php`

#### `Intranet\Http\Controllers\API\AlumnoGrupoController`
- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null, ?ModuloGrupoService $moduloGrupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\API\grupoService`
- Metodes:
  - **`moduloGrupos`**(): ModuloGrupoService

#### `Intranet\Http\Controllers\API\moduloGrupoService`
- Metodes:
  - **`alumnos`**($misgrupos)
  - **`show`**($id)
  - **`getModulo`**($dni, $modulo)


### `app/Http/Controllers/API/AlumnoResultadoContoller.php`

#### `Intranet\Http\Controllers\API\AlumnoResultadoContoller`
- Metodes: cap


### `app/Http/Controllers/API/AlumnoReunionController.php`

#### `Intranet\Http\Controllers\API\AlumnoReunionController`
- Metodes:
  - **`getDades`**($nia)
  - **`getDadesMatricula`**($token)
  - **`generaToken`**()
  - **`sendMatricula`**(Request $request)
  - **`getTestMatricula`**($token)


### `app/Http/Controllers/API/ApiBaseController.php`

#### `Intranet\Http\Controllers\API\ApiBaseController`
- Metodes:
  - **`ApiUser`**(Request $request)

    Resol usuari API en mode coexistència (`sanctum`/`api` + token legacy).
  - **`show`**($cadena, $send = true)
  - **`fields`**($fields)
  - **`sendFail`**($error, $code = 400)
  - **`isLegacyFilterExpression`**(string $cadena): bool
  - **`queryLegacy`**(string $cadena): Collection
  - **`applyLegacyCondition`**($query, string $filter): void


### `app/Http/Controllers/API/ApiResourceController.php`

#### `Intranet\Http\Controllers\API\ApiResourceController`
- Metodes:
  - **`__construct`**()
  - **`index`**()
  - **`destroy`**($id)
  - **`store`**(Request $request)
  - **`update`**(Request $request, $id)
  - **`show`**($id)
  - **`edit`**($id)
  - **`resolveClass`**(): string

#### `Intranet\Http\Controllers\API\ltrim`
- Metodes:
  - **`hasResource`**(): bool
  - **`validatedPayloadForStore`**(Request $request): array

    /
  - **`validatedPayloadForUpdate`**(Request $request): array

    /
  - **`storeRules`**(): array

    Sobrescriu en controladors concrets quan necessites validació en create.
  - **`updateRules`**(): array

    Sobrescriu en controladors concrets quan necessites validació en update.
  - **`mutableFields`**(): ?array

    Permet limitar camps mutables per endpoint sense tocar el model.
  - **`filterMutationPayload`**(Request $request): array

    /
  - **`sendResponse`**($result, $message = null)
  - **`sendError`**($error, $code = 400)
  - **`sendNotFound`**(string $error = 'Not found')
  - **`sendFail`**($error, $code = 400)
  - **`ApiUser`**(Request $request)
  - **`markLegacyUsage`**(JsonResponse $response, string $legacyContract, ?string $replacementHint = null): JsonResponse

    Marca resposta d'endpoint legacy per facilitar deprecació controlada.


### `app/Http/Controllers/API/ArticuloController.php`

#### `Intranet\Http\Controllers\API\ArticuloController`
- Metodes:
  - **`index`**()


### `app/Http/Controllers/API/ArticuloLoteController.php`

#### `Intranet\Http\Controllers\API\ArticuloLoteController`
- Metodes:
  - **`store`**(Request $request)
  - **`getMateriales`**($articulo)


### `app/Http/Controllers/API/AsistenciaController.php`

#### `Intranet\Http\Controllers\API\AsistenciaController`
- Metodes:
  - **`cambiar`**(Request $request)


### `app/Http/Controllers/API/AuthTokenController.php`

#### `Intranet\Http\Controllers\API\AuthTokenController`
Gestió de tokens d'accés API en fase de coexistència legacy + Sanctum.

- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - **`exchange`**(Request $request): JsonResponse
  - **`me`**(Request $request): JsonResponse
  - **`logout`**(Request $request): JsonResponse


### `app/Http/Controllers/API/CentroController.php`

#### `Intranet\Http\Controllers\API\CentroController`
- Metodes:
  - **`fusionar`**(Request $request)
  - **`fusion`**($codiCentre, &$centroQ): void

    /
  - **`fusionCenter`**($codiCentre, &$centroQ): mixed

    /
  - **`fusionColaboration`**($colaboracion, $colaboracionQ): void

    /


### `app/Http/Controllers/API/CicloController.php`

#### `Intranet\Http\Controllers\API\CicloController`
- Metodes: cap


### `app/Http/Controllers/API/ColaboracionController.php`

#### `Intranet\Http\Controllers\API\ColaboracionController`
- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - **`instructores`**($id)
  - **`resolve`**($id)
  - **`refuse`**($id)
  - **`unauthorize`**($id)
  - **`telefon`**($id, Request $request)
  - **`alumnat`**($id, Request $request)
  - **`book`**($id, Request $request)
  - **`changeState`**(string|int $id, string $action)
  - **`upsertDailyActivity`**(string $action, string|int $modelId, string $explicacion, callable $modelResolver, string $document): Activity


### `app/Http/Controllers/API/ComisionController.php`

#### `Intranet\Http\Controllers\API\ComisionController`
- Metodes:
  - **`__construct`**(ComisionService $comisionService)
  - **`autorizar`**()
  - **`prePay`**($dni)


### `app/Http/Controllers/API/CotxeController.php`

#### `Intranet\Http\Controllers\API\Direccio`
- Metodes: cap

#### `Intranet\Http\Controllers\API\CotxeController`
- Metodes:
  - **`__construct`**(private CotxeAccessService $access, private FitxatgeService $fitxatge, )
  - **`eventEntrada`**(Request $request)
  - **`obrirAutomatica`**(Request $request)
  - **`eventSortida`**(Request $request)
  - **`obrirTest`**()

    Obertura manual per proves: no necessita matrícula.
  - **`handleEvent`**(Request $request, Direccio $direccio)
  - **`normalizePayload`**(Request $request, Direccio $direccio): array

    Accepta payloads heterogenis (Milesight, etc.)


### `app/Http/Controllers/API/CursoController.php`

#### `Intranet\Http\Controllers\API\CursoController`
- Metodes: cap


### `app/Http/Controllers/API/DepartamentoController.php`

#### `Intranet\Http\Controllers\API\DepartamentoController`
- Metodes:
  - **`index`**()


### `app/Http/Controllers/API/DocumentacionFCTController.php`

#### `Intranet\Http\Controllers\API\DocumentacionFCTController`
- Metodes:
  - **`exec`**($documento)
  - **`signatura`**()
  - **`signaturaA1`**()
  - **`signaturaDirector`**()


### `app/Http/Controllers/API/DocumentoController.php`

#### `Intranet\Http\Controllers\API\DocumentoController`
- Metodes: cap


### `app/Http/Controllers/API/DropZoneController.php`

#### `Intranet\Http\Controllers\API\DropZoneController`
- Metodes:
  - **`getAttached`**($modelo, $id)
  - **`getNameAttached`**($modelo, $id, $name)
  - **`removeAttached`**($modelo, $id, $file)
  - **`attachFile`**(Request $request)


### `app/Http/Controllers/API/DualController.php`

#### `Intranet\Http\Controllers\API\DualController`
Només es manté per lectura/compatibilitat temporal.

- Metodes:
  - **`store`**(Request $request)


### `app/Http/Controllers/API/EmpresaController.php`

#### `Intranet\Http\Controllers\API\EmpresaController`
- Metodes:
  - **`__construct`**(?EmpresaService $empresaService = null)
  - **`empreses`**(): EmpresaService

#### `Intranet\Http\Controllers\API\empresaService`
- Metodes:
  - **`indexConvenio`**()


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
  - **`horarios`**(): HorarioService

#### `Intranet\Http\Controllers\API\horarioService`
- Metodes:
  - **`fitxatge`**(): FitxatgeService

#### `Intranet\Http\Controllers\API\fitxatgeService`
- Metodes:
  - **`potencial`**($dia, $idProfesor)
  - **`guarda`**(Request $request)

#### `Intranet\Http\Controllers\API\send`
- Metodes: cap


### `app/Http/Controllers/API/FaltaProfesorController.php`

#### `Intranet\Http\Controllers\API\FaltaProfesorController`
- Metodes:
  - **`index`**()
  - **`show`**($id)
  - **`horas`**($cadena)
  - **`queryByLegacyConditions`**(string $cadena)
  - **`queryByRequestFilters`**(array $filters)
  - **`extractQueryFilters`**(Request $request): array
  - **`isLegacyFilterExpression`**(string $cadena): bool
  - **`applyLegacyCondition`**($query, string $filter): void


### `app/Http/Controllers/API/FctController.php`

#### `Intranet\Http\Controllers\API\FctController`
- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - **`llist`**($id)
  - **`seguimiento`**($id, Request $request)


### `app/Http/Controllers/API/FicharController.php`

#### `Intranet\Http\Controllers\API\FicharController`
Endpoints API de fitxatge amb compatibilitat legacy i auth per header.

- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - **`fichar`**(Request $request, FitxatgeService $fitxatgeService)

    Registra entrada/eixida de fitxatge.
  - **`entrefechas`**(Request $datos)

#### `Intranet\Http\Controllers\API\registrosEntreFechas`
- Metodes: cap


### `app/Http/Controllers/API/GrupoController.php`

#### `Intranet\Http\Controllers\API\GrupoController`
- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\API\grupoService`
- Metodes: cap


### `app/Http/Controllers/API/GrupoTrabajoController.php`

#### `Intranet\Http\Controllers\API\GrupoTrabajoController`
- Metodes: cap


### `app/Http/Controllers/API/GuardiaController.php`

#### `Intranet\Http\Controllers\API\GuardiaController`
- Metodes:
  - **`show`**($id)
  - **`range`**(Request $request)
  - **`getServerTime`**()
  - **`queryByDiaRange`**(string $desde, string $hasta)
  - **`isLegacyFilterExpression`**(string $cadena): bool
  - **`queryLegacy`**(string $cadena)
  - **`applyLegacyCondition`**($query, string $filter): void


### `app/Http/Controllers/API/HoraController.php`

#### `Intranet\Http\Controllers\API\HoraController`
- Metodes: cap


### `app/Http/Controllers/API/HorarioController.php`

#### `Intranet\Http\Controllers\API\HorarioController`
- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Http\Controllers\API\horarioService`
- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - **`show`**($id)
  - **`index`**()
  - **`guardia`**($idProfesor)
  - **`HorariosDia`**($fecha)
  - **`getChange`**($dni)
  - **`Change`**(Request $request, $dni)
  - **`isLegacyFilterExpression`**(string $cadena): bool
  - **`queryLegacy`**(string $cadena)
  - **`extractQueryFilters`**(Request $request): array
  - **`queryByRequestFilters`**(array $filters, $fields = null)
  - **`applyLegacyCondition`**($query, string $filter): void


### `app/Http/Controllers/API/IPController.php`

#### `Intranet\Http\Controllers\API\IPController`
- Metodes:
  - **`miIP`**()


### `app/Http/Controllers/API/IncidenciaController.php`

#### `Intranet\Http\Controllers\API\IncidenciaController`
- Metodes: cap


### `app/Http/Controllers/API/InstructorController.php`

#### `Intranet\Http\Controllers\API\InstructorController`
- Metodes: cap


### `app/Http/Controllers/API/IpGuardiaController.php`

#### `Intranet\Http\Controllers\API\IpGuardiaController`
- Metodes:
  - **`arrayIps`**()


### `app/Http/Controllers/API/LoteController.php`

#### `Intranet\Http\Controllers\API\LoteController`
- Metodes:
  - **`destroy`**($id)
  - **`index`**()
  - **`getArticulos`**($lote)
  - **`putArticulos`**(Request $request, $lote)


### `app/Http/Controllers/API/MaterialBajaController.php`

#### `Intranet\Http\Controllers\API\MaterialBajaController`
- Metodes:
  - **`show`**($id)


### `app/Http/Controllers/API/MaterialController.php`

#### `Intranet\Http\Controllers\API\MaterialController`
- Metodes:
  - **`getMaterial`**($espacio)
  - **`getInventario`**(Request $request, $espai = null)
  - **`espai`**(Request $request, $espai)
  - **`inventario`**(Request $request)
  - **`index`**()
  - **`put`**(Request $request)
  - **`putUnidades`**(Request $request)
  - **`putUbicacion`**(Request $request)
  - **`putEstado`**(Request $request)
  - **`resolveApiUser`**(Request $request)
  - **`putInventario`**(Request $request)


### `app/Http/Controllers/API/ModuloController.php`

#### `Intranet\Http\Controllers\API\ModuloController`
- Metodes: cap


### `app/Http/Controllers/API/Modulo_cicloController.php`

#### `Intranet\Http\Controllers\API\Modulo_cicloController`
- Metodes: cap


### `app/Http/Controllers/API/NotificationController.php`

#### `Intranet\Http\Controllers\API\NotificationController`
- Metodes:
  - **`leer`**($id)

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
  - **`rango`**(Request $request, PresenciaResumenService $svc)


### `app/Http/Controllers/API/ProfesorController.php`

#### `Intranet\Http\Controllers\API\ProfesorController`
- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - **`rol`**($dni)
  - **`getRol`**($rol)


### `app/Http/Controllers/API/ProgramacionController.php`

#### `Intranet\Http\Controllers\API\ProgramacionController`
- Metodes: cap


### `app/Http/Controllers/API/ProjecteController.php`

#### `Intranet\Http\Controllers\API\ProjecteController`
- Metodes: cap


### `app/Http/Controllers/API/ReservaController.php`

#### `Intranet\Http\Controllers\API\ReservaController`
- Metodes:
  - **`index`**()
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\API\profesorService`
- Metodes:
  - **`show`**($id)
  - **`unsecure`**(Request $datosProfesor)
  - **`getJson`**($dispositivo)
  - **`action`**($action, $espacio): bool
  - **`checkSecuredStatus`**($data): bool
  - **`isLegacyFilterExpression`**(string $cadena): bool
  - **`queryLegacy`**(string $cadena)
  - **`extractQueryFilters`**(Request $request): array
  - **`queryByRequestFilters`**(array $filters, $fields = null)
  - **`applyLegacyCondition`**($query, string $filter): void


### `app/Http/Controllers/API/ResultadoController.php`

#### `Intranet\Http\Controllers\API\ResultadoController`
- Metodes: cap


### `app/Http/Controllers/API/ReunionController.php`

#### `Intranet\Http\Controllers\API\ReunionController`
- Metodes:
  - **`putAlumno`**($idReunion, $idAlumno, Request $request)


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
  - **`show`**($id)


### `app/Http/Controllers/API/TutoriaGrupoController.php`

#### `Intranet\Http\Controllers\API\TutoriaGrupoController`
- Metodes: cap


### `app/Http/Controllers/ActividadController.php`

#### `Intranet\Http\Controllers\ActividadController`
Controlador d'activitats extraescolars i complementàries.

- Metodes:
  - **`search`**()
  - **`createWithDefaultValues`**($default=[])
  - **`store`**(ActividadRequest $request): \Illuminate\Http\RedirectResponse

    Guarda una activitat i aplica els participants per defecte.

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(ActividadRequest $request, $id)
  - **`valoracion`**(ValoracionRequest $request)
  - **`showValue`**($id): \Illuminate\Contracts\View\View

    Mostra la pantalla de valoració d'una activitat.
  - **`value`**($id): \Illuminate\Contracts\View\View

    Mostra el formulari per omplir la valoració.
  - **`printValue`**($id): \Symfony\Component\HttpFoundation\StreamedResponse

    Genera el PDF de la valoració d'una activitat.

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - **`autorize`**($id): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse

    Mostra la pantalla de control d'autoritzacions de menors.
  - **`iniBotones`**(): void

    Inicialitza la botonera del grid i perfil.
  - **`autorizar`**(): \Illuminate\Http\RedirectResponse

    Autoritza activitats en estat 1 i, si hi ha credencials, les exporta a calendari.
  - **`accept`**($id, $redirect = true): mixed

    Accepta l'activitat incrementant estat i sincronitzant calendari extern.
  - **`printAutoritzats`**(): mixed

    Imprimeix el llistat d'autoritzats.
  - **`itaca`**($id): \Illuminate\Http\RedirectResponse

    Marca l'activitat com a tramitada en ITACA.
  - **`menorAuth`**($nia, $id): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse

    Alterna l'estat d'autorització d'un alumne menor.
  - **`gestor`**($id): mixed

    Renderitza el document associat a l'activitat amb GestorService.

#### `Intranet\Http\Controllers\grupos`
- Metodes:
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`detalle`**($id): \Illuminate\Contracts\View\View

    Mostra el detall d'una activitat amb professors i grups associats.

#### `Intranet\Http\Controllers\activosOrdered`
- Metodes:
  - **`altaGrupo`**(Request $request, $actividad_id): \Illuminate\Http\RedirectResponse

    Afig un grup a una activitat sense esborrar els existents.
  - **`borrarGrupo`**($actividad_id, $grupo_id): \Illuminate\Http\RedirectResponse

    Esborra un grup assignat a l'activitat.
  - **`altaProfesor`**(Request $request, $actividad_id): \Illuminate\Http\RedirectResponse

    Afig un professor participant a l'activitat.
  - **`borrarProfesor`**($actividad_id, $profesor_id): \Illuminate\Http\RedirectResponse

    Esborra un professor participant.
  - **`coordinador`**($actividad_id, $profesor_id): \Illuminate\Http\RedirectResponse

    Assigna el coordinador de l'activitat.
  - **`notify`**($id): \Illuminate\Http\RedirectResponse

    Notifica a professorat afectat i tutors dels grups de l'activitat.

#### `Intranet\Http\Controllers\find`
- Metodes: cap

#### `Intranet\Http\Controllers\notifyActivity`
- Metodes:
  - **`autorizacion`**($id): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\StreamedResponse

    Genera/mostra l'autorització de menors i crea registres si encara no existixen.


### `app/Http/Controllers/ActualizacionController.php`

#### `Intranet\Http\Controllers\ActualizacionController`
Class ActualizacionController

- Metodes:
  - **`actualizacion`**(): \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

    /
  - **`runShell`**(string $command, string $label): void
  - **`gitEnv`**(): array
  - **`markRepoAsSafe`**(): void


### `app/Http/Controllers/AdministracionController.php`

#### `Intranet\Http\Controllers\AdministracionController`
Class AdministracionController

- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null, ?AlumnoFctService $alumnoFctService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`alumnoFcts`**(): AlumnoFctService

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - **`lang`**($lang): \Illuminate\Http\RedirectResponse

    /
  - **`allApiToken`**(): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\activos`
- Metodes:
  - **`cleanCache`**()
  - **`nuevoCursoIndex`**(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`esborrarProgramacions`**()
  - **`esborrarEnquestes`**()
  - **`ferVotsPermanents`**()
  - **`nuevoCurso`**(Request $request): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\clearFechaBaja`
- Metodes:
  - **`help`**($fichero, $enlace): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`exe_actualizacion`**($VersionAntigua)

    /
  - **`v3_00`**()
  - **`v3_01`**()

#### `Intranet\Http\Controllers\all`
- Metodes:
  - **`consulta`**()
  - **`v2_01`**()
  - **`importaAnexoI`**()
  - **`centres_amb_mateixa_adreça`**()
  - **`showDoor`**()
  - **`secure`**(Request $request)


### `app/Http/Controllers/AlumnoController.php`

#### `Intranet\Http\Controllers\AlumnoController`
Class AlumnoController

- Metodes:
  - **`update`**(Request $request, $id): \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void

    /
  - **`carnet`**($alumno): mixed

    /

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - **`checkFol`**($id)
  - **`equipo`**(): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /

#### `Intranet\Http\Controllers\byGrupo`
- Metodes:
  - **`iniBotones`**()

    /
  - **`alerta`**(Request $request, $id): \Illuminate\Http\RedirectResponse

    /


### `app/Http/Controllers/AlumnoCursoController.php`

#### `Intranet\Http\Controllers\AlumnoCursoController`
- Metodes:
  - **`search`**()
  - **`active`**($id)
  - **`destroy`**($id)
  - **`iniBotones`**()
  - **`pdf`**($id)

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - **`registerGrup`**($grupo, $id)
  - **`registerAlumn`**($alumno, $id)
  - **`register`**($id, $alumno = null)
  - **`getRegister`**($alumno, $curso)
  - **`unregister`**($id, $alumno = null, $redirect = 1)


### `app/Http/Controllers/AlumnoGrupoController.php`

#### `Intranet\Http\Controllers\AlumnoGrupoController`
- Metodes:
  - **`indice`**($grupo): \Illuminate\Contracts\View\View

    Punt d'entrada legacy per a rutes que passen el grup en URL.
  - **`search`**()
  - **`redirect`**()
  - **`updateModal`**(AlumnoGrupoUpdateRequest $request, $grupo, $alumno)
  - **`realStore`**(AlumnoGrupoUpdateRequest $request, $id = null)
  - **`update`**(AlumnoGrupoUpdateRequest $request, $id)
  - **`iniBotones`**()


### `app/Http/Controllers/ArticuloController.php`

#### `Intranet\Http\Controllers\ArticuloController`
Class MaterialController

- Metodes:
  - **`iniBotones`**()
  - **`detalle`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`store`**(ArticuloRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(ArticuloRequest $request, $id)
  - **`destroy`**($id)

    Elimina un article amb autorització explícita.
  - **`borrarFichero`**($fichero)


### `app/Http/Controllers/ArticuloLoteController.php`

#### `Intranet\Http\Controllers\ArticuloLoteController`
Class MaterialController

- Metodes:
  - **`search`**()
  - **`iniBotones`**()


### `app/Http/Controllers/Auth/Alumno/HomeController.php`

#### `Intranet\Http\Controllers\Auth\Alumno\HomeController`
Description of HomeIdentifyController

- Metodes: cap


### `app/Http/Controllers/Auth/Alumno/LoginController.php`

#### `Intranet\Http\Controllers\Auth\Alumno\LoginController`
- Metodes:
  - **`username`**()
  - **`credentials`**(Request $request)
  - **`guard`**()
  - **`showLoginForm`**()
  - **`logout`**()
  - **`plogin`**(Request $request)


### `app/Http/Controllers/Auth/Alumno/PerfilController.php`

#### `Intranet\Http\Controllers\Auth\Alumno\PerfilController`
- Metodes:
  - **`editar`**()
  - **`update`**(Request $request, $id = null)


### `app/Http/Controllers/Auth/ExternLoginController.php`

#### `Intranet\Http\Controllers\Auth\ExternLoginController`
- Metodes:
  - **`username`**()
  - **`authenticated`**(Request $request, $user)

#### `Intranet\Http\Controllers\Auth\apiSessionTokenService`
- Metodes:
  - **`showExternLoginForm`**($token)

#### `Intranet\Http\Controllers\Auth\profesorService`
- Metodes: cap


### `app/Http/Controllers/Auth/ForgotPasswordController.php`

#### `Intranet\Http\Controllers\Auth\ForgotPasswordController`
- Metodes:
  - **`__construct`**(): void

    Create a new controller instance.


### `app/Http/Controllers/Auth/HomeController.php`

#### `Intranet\Http\Controllers\Auth\HomeController`
- Metodes:
  - **`__construct`**()
  - **`index`**(FitxatgeService $fitxatgeService, PerfilService $perfilService)
  - **`legal`**()


### `app/Http/Controllers/Auth/LoginController.php`

#### `Intranet\Http\Controllers\Auth\LoginController`
- Metodes:
  - **`login`**()


### `app/Http/Controllers/Auth/PerfilController.php`

#### `Intranet\Http\Controllers\Auth\PerfilController`
- Metodes:
  - **`update`**(Request $request, $new)


### `app/Http/Controllers/Auth/Profesor/HomeController.php`

#### `Intranet\Http\Controllers\Auth\Profesor\HomeController`
Description of HomeIdentifyController

- Metodes: cap


### `app/Http/Controllers/Auth/Profesor/LoginController.php`

#### `Intranet\Http\Controllers\Auth\Profesor\LoginController`
- Metodes:
  - **`username`**()
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\Auth\Profesor\profesorService`
- Metodes:
  - **`apiSessionTokens`**(): ApiSessionTokenService

#### `Intranet\Http\Controllers\Auth\Profesor\apiSessionTokenService`
- Metodes:
  - **`authenticated`**(Request $request, $user): void

    Hook del trait AuthenticatesUsers després de login satisfactori.
  - **`credentials`**(Request $request)
  - **`guard`**()
  - **`showLoginForm`**()
  - **`logout`**()
  - **`plogin`**(Request $request)
  - **`firstLogin`**(Request $request)


### `app/Http/Controllers/Auth/Profesor/PerfilController.php`

#### `Intranet\Http\Controllers\Auth\Profesor\PerfilController`
- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\Auth\Profesor\profesorService`
- Metodes:
  - **`editar`**()
  - **`files`**()
  - **`updateFiles`**(PerfilFilesRequest $request)
  - **`updatePhoto`**(PerfilFilesRequest $request, Profesor $profesor): void

    Processa la pujada de foto de perfil i garanteix persistència en BBDD.
  - **`cleanupAndRelinkProfileAssets`**(?string $oldPhoto, string $newPhoto): void

    Elimina/relaciona fitxers antics lligats a la foto anterior.
  - **`moveProfileAsset`**(string $folder, string $oldPhoto, string $newPhoto): void

    Mou un fitxer d'asset de perfil si existeix amb el nom antic.
  - **`updateSignature`**(PerfilFilesRequest $request, Profesor $profesor)
  - **`updatePeu`**(PerfilFilesRequest $request, Profesor $profesor)
  - **`deleteCertificate`**(PerfilFilesRequest $request, Profesor $profesor)
  - **`updateDigitalCertificate`**(PerfilFilesRequest $request, Profesor $profesor)
  - **`update`**(Request $request, $id=null)


### `app/Http/Controllers/Auth/Profesor/ResetProfesorController.php`

#### `Intranet\Http\Controllers\Auth\Profesor\ResetProfesorController`
- Metodes:
  - **`__construct`**()


### `app/Http/Controllers/Auth/RegisterController.php`

#### `Intranet\Http\Controllers\Auth\RegisterController`
- Metodes:
  - **`__construct`**(): void

    Create a new controller instance.
  - **`validator`**(array $data): \Illuminate\Contracts\Validation\Validator

    Get a validator for an incoming registration request.
  - **`create`**(array $data): \Intranet\User

    Create a new user instance after a valid registration.


### `app/Http/Controllers/Auth/ResetPasswordController.php`

#### `Intranet\Http\Controllers\Auth\ResetPasswordController`
- Metodes:
  - **`__construct`**(): void

    Create a new controller instance.
  - **`resetPassword`**($user, $password)


### `app/Http/Controllers/Auth/Social/SocialController.php`

#### `Intranet\Http\Controllers\Auth\Social\SocialController`
- Metodes:
  - **`__construct`**()
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\Auth\Social\profesorService`
- Metodes:
  - **`apiSessionTokens`**(): ApiSessionTokenService

#### `Intranet\Http\Controllers\Auth\Social\apiSessionTokenService`
- Metodes:
  - **`getSocialAuth`**($token=null)
  - **`checkTokenAndRedirect`**(Request $request, $user)
  - **`successloginProfesor`**($user)
  - **`getSocialAuthCallback`**(Request $request)


### `app/Http/Controllers/CalendariFctController.php`

#### `Intranet\Http\Controllers\CalendariFctController`
Class CalendariFctController

- Metodes:
  - **`search`**(): \Illuminate\Support\Collection

    Recupera els alumnes del professor autenticat.
  - **`iniBotones`**()
  - **`days`**($id): \Illuminate\Contracts\View\View

    Mostra el calendari FCT d'un alumne.


### `app/Http/Controllers/CentroController.php`

#### `Intranet\Http\Controllers\CentroController`
Class CentroController

- Metodes:
  - **`update`**(CentroRequest $request, $id): \Illuminate\Http\RedirectResponse

    /
  - **`showEmpresa`**($id)
  - **`store`**(CentroRequest $request): \Illuminate\Http\RedirectResponse

    /
  - **`destroy`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`empresaCreateCentro`**(EmpresaCentroRequest $request, $id)


### `app/Http/Controllers/CicloController.php`

#### `Intranet\Http\Controllers\CicloController`
Class CicloController

- Metodes:
  - **`iniBotones`**()
  - **`store`**(CicloRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(CicloRequest $request, $id)
  - **`destroy`**($id)

    Elimina un cicle amb autorització explícita.


### `app/Http/Controllers/CicloDualController.php`

#### `Intranet\Http\Controllers\CicloDualController`
Class CicloController

- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`edit`**()
  - **`update`**(CicloDualRequest $request)


### `app/Http/Controllers/ColaboracionAlumnoController.php`

#### `Intranet\Http\Controllers\ColaboracionAlumnoController`
Class PanelColaboracionController

- Metodes:
  - **`index`**(): mixed

    /
  - **`search`**(): mixed

    /


### `app/Http/Controllers/ColaboracionController.php`

#### `Intranet\Http\Controllers\ColaboracionController`
Class ColaboracionController

- Metodes:
  - **`iniBotones`**()

    /
  - **`search`**(): mixed

    /
  - **`update`**(ColaboracionRequest $request, $id): \Illuminate\Http\RedirectResponse

    Actualitza una col·laboració des del formulari específic de panell.
  - **`show`**($id): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /
  - **`printAnexeIV`**($colaboracion)

#### `Intranet\Http\Controllers\fillAndSave`
- Metodes:
  - **`makeArrayPdfAnexoIV`**($colaboracion)
  - **`makeArrayPdfConveni`**($colaboracion)
  - **`deleteDir`**($folder)


### `app/Http/Controllers/ComisionController.php`

#### `Intranet\Http\Controllers\ComisionController`
Class ComisionController

- Metodes:
  - **`__construct`**(?ComisionService $comisionService = null)
  - **`comisionService`**(): ComisionService

#### `Intranet\Http\Controllers\comisionService`
- Metodes:
  - **`store`**(ComisionRequest $request)

#### `Intranet\Http\Controllers\merge`
- Metodes:
  - **`update`**(ComisionRequest $request, $id)
  - **`confirm`**($id)
  - **`iniBotones`**()

    /
  - **`createWithDefaultValues`**($default=[])
  - **`enviarCorreos`**($comision)
  - **`sendEmail`**($elemento, $fecha)
  - **`init`**($id)
  - **`payment`**(): \Illuminate\Http\RedirectResponse

    /
  - **`printAutoritzats`**()
  - **`paid`**($id)

    /
  - **`unpaid`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`autorizar`**(): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\StateService`
- Metodes:
  - **`detalle`**($id)
  - **`createFct`**(Request $request, $comisionId)
  - **`deleteFct`**($comisionId, $fctId)
  - **`setEstado`**($id, int $estado): void
  - **`buildFctOptions`**(): array

    Retorna opcions de FCT per al selector de detall:


### `app/Http/Controllers/Controller.php`

#### `Intranet\Http\Controllers\Controller`
- Metodes:
  - **`__construct`**()


### `app/Http/Controllers/Core/BaseController.php`

#### `Intranet\Http\Controllers\Core\BaseController`
- Metodes:
  - **`__construct`**()
  - **`chooseView`**($tipo): string

    Resol la vista a utilitzar per a una acció.
  - **`grid`**($todos, $modal=false): \Illuminate\Contracts\View\View

    Renderitza el grid amb o sense formulari modal.
  - **`parametres`**(): array

    Extensió per a paràmetres addicionals en classes filles.
  - **`index`**(): \Illuminate\Contracts\View\View

    Acció index estàndard.
  - **`confirm`**($id): mixed

    Mostra modal de confirmació per a un registre.
  - **`indice`**($search): \Illuminate\Contracts\View\View

    Variante d'index amb filtre extern.
  - **`search`**(): \Illuminate\Support\Collection

    Cerca per defecte del controlador base.
  - **`llist`**($todos, $panel): \Illuminate\Contracts\View\View

    Renderitza una vista de llistat sobre un panell concret.
  - **`iniBotones`**(): void

    Punt d'extensió per inicialitzar botons en classes filles.
  - **`iniPestanas`**($parametres = null): void

    Inicialitza pestanyes per defecte.
  - **`resolveModelClass`**(): string

    Resol la classe de model actual del controlador.

#### `Intranet\Http\Controllers\Core\ltrim`
- Metodes:
  - **`hasModelColumn`**(string $class, string $column): bool

    Comprova si un model té una columna, amb cache en memòria.


### `app/Http/Controllers/Core/IntranetController.php`

#### `Intranet\Http\Controllers\Core\IntranetController`
- Metodes:
  - **`redirect`**(): \Illuminate\Http\RedirectResponse

    Calcula la redirecció de retorn després de store/update/destroy.
  - **`destroy`**($id): \Illuminate\Http\RedirectResponse

    Elimina un registre i fitxers associats si escau.
  - **`borrarFichero`**($fichero): void

    Esborra un fitxer del `public/` o `storage/app/` si la ruta és segura.
  - **`store`**(Request $request): \Illuminate\Http\RedirectResponse

    Guarda un nou registre.
  - **`realStore`**(Request $request, $id = null): mixed

    Crea o actualitza un registre i retorna la clau primària resultant.
  - **`persist`**(Request $request, $id = null): mixed

    Alias semàntic de persistència per compatibilitat amb flux modal.
  - **`update`**(Request $request, $id): \Illuminate\Http\RedirectResponse

    Actualitza un registre existent.
  - **`active`**($id): \Illuminate\Http\RedirectResponse

    Alterna l'estat `activo` d'un registre.
  - **`document`**($id): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse

    Retorna el document físic associat al registre.
  - **`gestor`**($id): \Illuminate\Http\RedirectResponse

    Redirigeix al gestor documental del registre si està enllaçat.
  - **`validateAll`**($request, $elemento): array

    Valida el request segons les regles del model.
  - **`manageCheckBox`**($request, $elemento): Request

    Normalitza camps checkbox en el request abans de validar/guardar.


### `app/Http/Controllers/Core/ModalController.php`

#### `Intranet\Http\Controllers\Core\ModalController`
- Metodes:
  - **`__construct`**()
  - **`index`**()
  - **`grid`**(): \Illuminate\Contracts\View\View

    Renderitza la vista modal amb grid i formulari d'alta.
  - **`resolveIndexView`**(): string

    Resol la vista d'index del modal.
  - **`search`**()

    Cerca per defecte del modal:
  - **`hasModelColumn`**(string $modelClass, string $column): bool

    Comprova si la taula del model té una columna.
  - **`resolveModelClass`**(): string

    Resol la classe de model del controlador modal.
  - **`create`**()

    Per a recursos amb vista modal, la ruta create redirigeix a l'índex
  - **`edit`**($id = null)
  - **`persist`**(Request $request, $id = null): mixed

    Persistix un model del controlador modal.
  - **`createWithDefaultValues`**($default = []): mixed

    Crea una instància buida del model per al formulari modal.
  - **`destroy`**($id)
  - **`document`**($id): \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse

    Retorna el document físic associat al registre.
  - **`confirm`**($id): mixed

    Retorna la vista de confirmació per al model.
  - **`iniBotones`**()
  - **`iniPestanas`**(): void

    Inicialitza pestanyes per defecte.
  - **`redirect`**(): \Illuminate\Http\RedirectResponse

    Resol redirecció de retorn en fluxos modal.


### `app/Http/Controllers/CotxeController.php`

#### `Intranet\Http\Controllers\CotxeController`
- Metodes:
  - **`store`**(CotxeRequest $request)

#### `Intranet\Http\Controllers\merge`
- Metodes:
  - **`update`**(CotxeRequest $request, $id)
  - **`destroy`**($id)

    Elimina un cotxe amb autorització explícita.
  - **`iniBotones`**()


### `app/Http/Controllers/CursoController.php`

#### `Intranet\Http\Controllers\CursoController`
Class CursoController

- Metodes:
  - **`store`**(CursoRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(CursoRequest $request, $id)
  - **`detalle`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`indexAlumno`**(): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /
  - **`iniAluBotones`**()

    /
  - **`iniBotones`**()

    /
  - **`saveFile`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`makeReport`**($id): mixed

    /
  - **`document`**($id)
  - **`pdf`**($id): mixed

    /
  - **`email`**($id)
  - **`active`**($id)
  - **`destroy`**($id)

    Elimina un curs amb autorització explícita.


### `app/Http/Controllers/DepartamentoController.php`

#### `Intranet\Http\Controllers\DepartamentoController`
Class CicloController

- Metodes:
  - **`iniBotones`**()
  - **`store`**(DepartamentoRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(DepartamentoRequest $request, $id)
  - **`destroy`**($id)

    Elimina un departament amb autorització explícita.
  - **`search`**()


### `app/Http/Controllers/Deprecated/DualController.php`

#### `Intranet\Http\Controllers\Deprecated\DualController`
Class DualAlumnoController

- Metodes:
  - **`search`**(): mixed

    /
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\Deprecated\grupoService`
- Metodes:
  - **`iniBotones`**()

    /
  - **`show`**($id): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View

    /
  - **`update`**(DualRequest $request, $id): \Illuminate\Http\RedirectResponse

    /
  - **`create`**(): \Illuminate\Http\RedirectResponse

    /
  - **`store`**(DualRequest $request): \Illuminate\Http\RedirectResponse

    /
  - **`destroy`**($id)
  - **`informe`**($fct, $informe='anexe_vii', $stream=true, $data=null): mixed

    /
  - **`getGestor`**($doc, $ciclo)
  - **`chooseAction`**($fct, $document, &$zip, $data)
  - **`certificado`**($fct, $date)
  - **`getInforme`**($id)
  - **`deleteDir`**($folder)
  - **`putInforme`**($id, Request $request)
  - **`printAnexeXII`**($fct, $data)

#### `Intranet\Http\Controllers\Deprecated\fillAndSave`
- Metodes:
  - **`makeArrayPdfAnexoVII`**($fct, $data)
  - **`printAnexeVI`**()

#### `Intranet\Http\Controllers\Deprecated\semanalByGrupo`
- Metodes:
  - **`makeArrayPdfAnexoXII`**($fct, $data): mixed

    /
  - **`printAnexeXIII`**($fct, $data)

#### `Intranet\Http\Controllers\Deprecated\fillAndSend`
- Metodes:
  - **`makeArrayPdfAnexoXIV`**(): mixed

    /


### `app/Http/Controllers/Deprecated/ImportEmailController.php`

#### `Intranet\Http\Controllers\Deprecated\ImportEmailController`
/

- Metodes:
  - **`create`**(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`hazDNI`**($dni, $nia)
  - **`store`**(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /
  - **`modifica`**($key, $email)

#### `Intranet\Http\Controllers\Deprecated\find`
- Metodes: cap


### `app/Http/Controllers/Docs/DocblockDocsController.php`

#### `Intranet\Http\Controllers\Docs\DocblockDocsController`
Renderitza la documentacio de doc-blocks de l'aplicacio.

- Metodes:
  - **`index`**(): \Illuminate\Http\Response

    Mostra la pagina de documentacio amb index lateral de seccions.


### `app/Http/Controllers/DocumentoController.php`

#### `Intranet\Http\Controllers\DocumentoController`
Controlador de gestió de documents i fluxos associats de FCT/qualitat.

- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null, ?AlumnoFctService $alumnoFctService = null, ?DocumentoLifecycleService $documentoLifecycleService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`alumnoFcts`**(): AlumnoFctService

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - **`documentos`**(): DocumentoLifecycleService

#### `Intranet\Http\Controllers\documentoLifecycleService`
- Metodes:
  - **`forms`**(): DocumentoFormService

#### `Intranet\Http\Controllers\documentoFormService`
- Metodes:
  - **`redirect`**()
  - **`store`**(Request $request, $fct = null)

#### `Intranet\Http\Controllers\validate`
- Metodes:
  - **`createWithDefaultValues`**($default=[])
  - **`project`**($idFct)
  - **`qualitatUpload`**($id)

#### `Intranet\Http\Controllers\app`
- Metodes: cap

#### `Intranet\Http\Controllers\findOrFail`
- Metodes:
  - **`qualitat`**()

#### `Intranet\Http\Controllers\grupos`
- Metodes:
  - **`edit`**($id = null)
  - **`show`**($id)
  - **`destroy`**($id)
  - **`readFile`**($name)


### `app/Http/Controllers/EmpresaController.php`

#### `Intranet\Http\Controllers\EmpresaController`
- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null, ?EmpresaService $empresaService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`empreses`**(): EmpresaService

#### `Intranet\Http\Controllers\empresaService`
- Metodes:
  - **`search`**()
  - **`create`**($default=[])
  - **`show`**($id)
  - **`iniBotones`**()
  - **`store`**(Request $request)

#### `Intranet\Http\Controllers\empreses`
- Metodes:
  - **`update`**(Request $request, $id)
  - **`document`**($id)
  - **`A1`**($id)


### `app/Http/Controllers/EspacioController.php`

#### `Intranet\Http\Controllers\EspacioController`
Class EspacioController

- Metodes:
  - **`search`**()
  - **`store`**(EspacioRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(EspacioRequest $request, $id)
  - **`destroy`**($id)

    Elimina un espai amb autorització explícita.
  - **`detalle`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`iniBotones`**()

    /
  - **`barcode`**($id, $posicion=1)


### `app/Http/Controllers/ExpedienteController.php`

#### `Intranet\Http\Controllers\ExpedienteController`
Class ExpedienteController

- Metodes:
  - **`__construct`**(?ExpedienteService $expedienteService = null)
  - **`expedients`**(): ExpedienteService

#### `Intranet\Http\Controllers\expedienteService`
- Metodes:
  - **`store`**(ExpedienteRequest $request)

#### `Intranet\Http\Controllers\expedients`
- Metodes:
  - **`update`**(ExpedienteRequest $request, $id)
  - **`iniBotones`**()

    /
  - **`autorizar`**(): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\authorizePending`
- Metodes:
  - **`init`**($id): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\init`
- Metodes:
  - **`createWithDefaultValues`**($default = [])
  - **`pasaOrientacion`**($id): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\passToOrientation`
- Metodes:
  - **`assigna`**($id, Request $request)

#### `Intranet\Http\Controllers\assignCompanion`
- Metodes:
  - **`pdf`**($id): mixed

    /
  - **`imprimir`**(): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse

    /
  - **`show`**($id)
  - **`destroy`**($id)

    Elimina un expedient amb autorització explícita.


### `app/Http/Controllers/FaltaController.php`

#### `Intranet\Http\Controllers\FaltaController`
Class FaltaController

- Metodes:
  - **`__construct`**(?FaltaService $faltaService = null)
  - **`faltas`**(): FaltaService

#### `Intranet\Http\Controllers\faltaService`
- Metodes:
  - **`iniBotones`**()

    /
  - **`store`**(Request $request): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\validate`
- Metodes:
  - **`update`**(Request $request, $id): \Illuminate\Http\RedirectResponse

    /
  - **`createWithDefaultValues`**($default = [])
  - **`init`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`alta`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`findFaltaOrFail`**(int $id): Falta

    Recupera la falta per aplicar autorització explícita.
  - **`show`**($id): \Illuminate\Contracts\View\View

    Mostra el detall d'una falta.


### `app/Http/Controllers/FaltaItacaController.php`

#### `Intranet\Http\Controllers\FaltaItacaController`
- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - **`faltes`**(): FaltaItacaWorkflowService

#### `Intranet\Http\Controllers\faltaItacaWorkflowService`
- Metodes:
  - **`index`**()
  - **`printReport`**($request)

#### `Intranet\Http\Controllers\findElements`
- Metodes:
  - **`resolve`**($id)
  - **`refuse`**($id, Request $request)


### `app/Http/Controllers/FctAlumnoController.php`

#### `Intranet\Http\Controllers\FctAlumnoController`
- Metodes:
  - **`__construct`**(?AlumnoFctService $alumnoFctService = null)
  - **`alumnoFcts`**(): AlumnoFctService

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - **`search`**()
  - **`iniBotones`**()
  - **`setQualityB`**(): void

    /
  - **`days`**($id)
  - **`nuevaConvalidacion`**()
  - **`unlink`**($id)
  - **`storeConvalidacion`**(FctConvalidacionStoreRequest $request)
  - **`update`**(AlumnoFctUpdateRequest $request, $id)
  - **`show`**($id)
  - **`pdf`**($id)
  - **`Signatura`**($id, $num)
  - **`Valoratiu`**($id)
  - **`AVI`**($id)
  - **`AEng`**($id)
  - **`auth`**($id)
  - **`AutDual`**($id)
  - **`preparePdf`**($id)

#### `Intranet\Http\Controllers\findOrFail`
- Metodes: cap

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - **`pg0301`**($id)

    public function email($id)
  - **`email`**($id)
  - **`importa`**($id)


### `app/Http/Controllers/FctController.php`

#### `Intranet\Http\Controllers\FctController`
Class FctController

- Metodes:
  - **`__construct`**(?FctService $fctService = null)
  - **`fcts`**(): FctService

#### `Intranet\Http\Controllers\fctService`
- Metodes:
  - **`certificates`**(): FctCertificateService

#### `Intranet\Http\Controllers\fctCertificateService`
- Metodes:
  - **`edit`**($id=null)
  - **`update`**(Request $request, $id): \Illuminate\Http\RedirectResponse

    /
  - **`certificat`**($id)
  - **`certificatColaboradores`**($id)

#### `Intranet\Http\Controllers\findOrFail`
- Metodes: cap

#### `Intranet\Http\Controllers\streamColaboradorCertificate`
- Metodes:
  - **`store`**(Request $request): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\validate`
- Metodes:
  - **`show`**($id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`destroy`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`nouAlumno`**($idFct, Request $request): \Illuminate\Http\RedirectResponse

    /
  - **`nouFctAlumno`**(Request $request): \Illuminate\Http\RedirectResponse

    /
  - **`nouInstructor`**($idFct, ColaboradorRequest $request): \Illuminate\Http\RedirectResponse

    /
  - **`deleteInstructor`**($idFct, $idInstructor): \Illuminate\Http\RedirectResponse

    /
  - **`alumnoDelete`**($idFct, $idAlumno): \Illuminate\Http\RedirectResponse

    /
  - **`modificaHoras`**($idFct, Request $request): \Illuminate\Http\RedirectResponse

    /
  - **`cotutor`**(Request $request, $idFct)


### `app/Http/Controllers/FctMailController.php`

#### `Intranet\Http\Controllers\FctMailController`
- Metodes:
  - **`__construct`**(FctMailService $fctMailService)
  - **`showMailById`**($id, $documento)
  - **`showMailByRequest`**(Request $request, $documento)


### `app/Http/Controllers/FicharController.php`

#### `Intranet\Http\Controllers\FicharController`
- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\profesorService`
- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - **`ficha`**(FitxatgeService $fitxatgeService)
  - **`search`**()
  - **`store`**(Request $request)

#### `Intranet\Http\Controllers\profesores`
- Metodes:
  - **`control`**()
  - **`controlDia`**()
  - **`loadHoraries`**($profesores)
  - **`loadHorary`**($profesor)
  - **`resumenRango`**()


### `app/Http/Controllers/GrupoController.php`

#### `Intranet\Http\Controllers\GrupoController`
Class GrupoController

- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`workflows`**(): GrupoWorkflowService

#### `Intranet\Http\Controllers\grupoWorkflowService`
- Metodes:
  - **`search`**(): \Illuminate\Database\Eloquent\Collection|Grupo[]|mixed

    /
  - **`detalle`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`iniBotones`**()

    /
  - **`horario`**($id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`asigna`**(): \Illuminate\Http\RedirectResponse

    /
  - **`pdf`**($grupo): mixed

    /
  - **`carnet`**($grupo): mixed

    /
  - **`certificados`**($grupo): mixed

    /
  - **`certificado`**($alumno): mixed

    /
  - **`checkFol`**($id)


### `app/Http/Controllers/GrupoTrabajoController.php`

#### `Intranet\Http\Controllers\GrupoTrabajoController`
Class GrupoTrabajoController

- Metodes:
  - **`store`**(GrupoTrabajoRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(GrupoTrabajoRequest $request, $id)
  - **`seach`**(): mixed

    /
  - **`detalle`**($id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /

#### `Intranet\Http\Controllers\allOrderedBySurname`
- Metodes:
  - **`altaProfesor`**(GTProfesorRequest $request, $gtId): \Illuminate\Http\RedirectResponse

    /
  - **`borrarProfesor`**($gtId, $profesorId): \Illuminate\Http\RedirectResponse

    /
  - **`coordinador`**($grupoId, $profesorId): \Illuminate\Http\RedirectResponse

    /
  - **`removeCoord`**($grupoId): bool

    /
  - **`addCoord`**($grupoId, $profesorId)

    /
  - **`iniBotones`**()

    /
  - **`destroy`**($id)

    Elimina un grup de treball amb autorització explícita.


### `app/Http/Controllers/GuardiaController.php`

#### `Intranet\Http\Controllers\GuardiaController`
Class GuardiaController

- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /


### `app/Http/Controllers/HorarioController.php`

#### `Intranet\Http\Controllers\HorarioController`
Gestiona el canvi temporal d'horaris i la revisió de propostes.

- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\profesorService`
- Metodes:
  - **`getJsonFromFile`**($dni)
  - **`changeHorary`**(string $dni, $cambios): void

    Aplica els canvis d'horari utilitzant la posició original com a referència.
  - **`saveCopy`**($dni, $data)
  - **`changeTable`**($dni, $redirect=true)
  - **`changeTableAll`**(): \Illuminate\Http\RedirectResponse

    /
  - **`changeIndex`**(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`propuestas`**(Request $request)
  - **`aceptarPropuesta`**($dni, $id)

#### `Intranet\Http\Controllers\send`
- Metodes:
  - **`esborrarProposta`**($dni, $id)
  - **`sendAcceptationEmail`**(string $dni, array $data, string $id): void
  - **`horarioCambiar`**($id = null): \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

    /
  - **`iniBotones`**()

    /
  - **`index`**(): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /
  - **`update`**(HorarioUpdateRequest $request, $id)
  - **`modificarHorario`**($idProfesor): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /


### `app/Http/Controllers/ImportController.php`

#### `Intranet\Http\Controllers\ImportController`
- Metodes:
  - **`create`**()
  - **`store`**(Request $request)
  - **`storeAsync`**(Request $request, mixed $validatedFile = null)
  - **`history`**()
  - **`status`**(int $importRunId)
  - **`asignarTutores`**()
  - **`run`**($fxml, Request $request)
  - **`sacaCampos`**($atrxml, $llave, $func = 1)
  - **`filtro`**($filtro, $campos)
  - **`required`**($required, $campos)
  - **`imports`**(): ImportService

#### `Intranet\Http\Controllers\importService`
- Metodes:
  - **`workflows`**(): ImportWorkflowService

#### `Intranet\Http\Controllers\importWorkflowService`
- Metodes:
  - **`schemas`**(): ImportSchemaProvider

#### `Intranet\Http\Controllers\importSchemaProvider`
- Metodes:
  - **`xmlHelper`**(): ImportXmlHelperService

#### `Intranet\Http\Controllers\importXmlHelperService`
- Metodes:
  - **`executions`**(): GeneralImportExecutionService

#### `Intranet\Http\Controllers\generalImportExecutionService`
- Metodes:
  - **`camposBdXml`**(): array

    /
  - **`executeSyncImport`**(mixed $file, Request $request)
  - **`resolveImportMode`**(Request $request): string
  - **`authorizeImportManagement`**(bool $allowConsole = false): void


### `app/Http/Controllers/IncidenciaController.php`

#### `Intranet\Http\Controllers\IncidenciaController`
Class IncidenciaController

- Metodes:
  - **`search`**()
  - **`generarOrden`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`generateOrder`**(Incidencia $incidencia): OrdenTrabajo

    /
  - **`removeOrden`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`edit`**($id=null): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`store`**(IncidenciaRequest $request)

#### `Intranet\Http\Controllers\merge`
- Metodes:
  - **`update`**(IncidenciaRequest $request, $id)
  - **`storeImagen`**(Incidencia $incidencia, IncidenciaRequest $request): void
  - **`createWithDefaultValues`**($default = [])
  - **`show`**($id)
  - **`notify`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`iniBotones`**()

    /
  - **`destroy`**($id)
  - **`currentProfesorDni`**(): string


### `app/Http/Controllers/InstructorController.php`

#### `Intranet\Http\Controllers\InstructorController`
Class InstructorController

- Metodes:
  - **`instructors`**(): InstructorWorkflowService

#### `Intranet\Http\Controllers\instructorWorkflowService`
- Metodes:
  - **`iniBotones`**()

    /
  - **`search`**(): mixed

    /
  - **`show`**($id): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View

    /
  - **`crea`**($centro): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`edita`**($id, $empresa): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`guarda`**(Request $request, $id, $centro): \Illuminate\Http\RedirectResponse

    /
  - **`showEmpresa`**($id)
  - **`almacena`**(Request $request, $centro): \Illuminate\Http\RedirectResponse

    /
  - **`delete`**($id, $centro): \Illuminate\Http\RedirectResponse

    /
  - **`copy`**($id, $idCentro): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`toCopy`**(Request $request, $id, $idCentro): \Illuminate\Http\RedirectResponse

    /
  - **`pdf`**($id): \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

    /


### `app/Http/Controllers/InventarioController.php`

#### `Intranet\Http\Controllers\InventarioController`
Class MaterialController

- Metodes:
  - **`barcode`**(Request $request)
  - **`edit`**($id = null)
  - **`espacio`**($espacio): mixed

    /


### `app/Http/Controllers/IpGuardiaController.php`

#### `Intranet\Http\Controllers\IpGuardiaController`
Class LoteController

- Metodes:
  - **`search`**()
  - **`iniBotones`**()
  - **`store`**(IpGuardiaRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(IpGuardiaRequest $request, $id)
  - **`destroy`**($id)

    Elimina una IP de guàrdia amb autorització explícita.


### `app/Http/Controllers/ItacaController.php`

#### `Intranet\Http\Controllers\ItacaController`
- Metodes:
  - **`extraescolars`**(Request $request)
  - **`birret`**(Request $request)
  - **`faltes`**(PasswordRequest $request)
  - **`tryOne`**(ItacaService $itacaService, mixed $falta, $fecha): int


### `app/Http/Controllers/LoteController.php`

#### `Intranet\Http\Controllers\LoteController`
Class LoteController

- Metodes:
  - **`search`**()
  - **`store`**(LoteRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(LoteRequest $request, $id)
  - **`destroy`**($id)

    Elimina un lot amb autorització explícita.
  - **`iniBotones`**()
  - **`capture`**($lote)
  - **`postCapture`**($lote, Request $request)


### `app/Http/Controllers/MaterialBajaController.php`

#### `Intranet\Http\Controllers\MaterialBajaController`
Class MaterialController

- Metodes:
  - **`search`**()

    /
  - **`iniBotones`**()
  - **`delete`**($id)
  - **`active`**($id)
  - **`recover`**($id)


### `app/Http/Controllers/MaterialController.php`

#### `Intranet\Http\Controllers\MaterialController`
Class MaterialController

- Metodes:
  - **`__construct`**()

    MaterialController constructor.
  - **`iniBotones`**()

    /
  - **`copy`**($id): \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

    /
  - **`incidencia`**($id): \Illuminate\Http\RedirectResponse

    /


### `app/Http/Controllers/MaterialModController.php`

#### `Intranet\Http\Controllers\MaterialModController`
Class MaterialController

- Metodes:
  - **`search`**()

    /
  - **`iniBotones`**()
  - **`refuse`**($id)
  - **`resolve`**($id)


### `app/Http/Controllers/MensualController.php`

#### `Intranet\Http\Controllers\MensualController`
- Metodes:
  - **`vistaImpresion`**()
  - **`imprimir`**(DesdeHastaRequest $request, FaltaReportService $faltaReportService)
  - **`printFaltaReport`**(DesdeHastaRequest $request, FaltaReportService $faltaReportService)


### `app/Http/Controllers/MenuController.php`

#### `Intranet\Http\Controllers\MenuController`
Class MenuController

- Metodes:
  - **`search`**(): \Illuminate\Database\Eloquent\Collection|Menu[]|mixed

    /
  - **`realStore`**(Request $request, $id = null)
  - **`copy`**($id): \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

    /
  - **`up`**($id): \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

    /
  - **`down`**($id): \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector

    /
  - **`store`**(Request $request)

    Guarda un nou menú amb autorització explícita.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`update`**(Request $request, $id)

    Actualitza un menú amb autorització explícita.
  - **`active`**($id)
  - **`destroy`**($id)
  - **`menus`**(): MenuService

#### `Intranet\Http\Controllers\menuService`
- Metodes:
  - **`iniBotones`**()

    /


### `app/Http/Controllers/ModuloController.php`

#### `Intranet\Http\Controllers\ModuloController`
Class ModuloController

- Metodes:
  - **`iniBotones`**()

    /
  - **`update`**(ModuloRequest $request, $id)


### `app/Http/Controllers/ModuloGrupoController.php`

#### `Intranet\Http\Controllers\ModuloGrupoController`
Class Modulo_cicloController

- Metodes:
  - **`iniBotones`**()

    /
  - **`search`**()

#### `Intranet\Http\Controllers\misModulos`
- Metodes:
  - **`link`**($id)


### `app/Http/Controllers/Modulo_cicloController.php`

#### `Intranet\Http\Controllers\Modulo_cicloController`
Class Modulo_cicloController

- Metodes:
  - **`iniBotones`**()

    /
  - **`store`**(ModuloCicloRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(ModuloCicloRequest $request, $id)
  - **`destroy`**($id)

    Elimina un enllaç mòdul-cicle amb autorització explícita.


### `app/Http/Controllers/MyMailController.php`

#### `Intranet\Http\Controllers\MyMailController`
Class AdministracionController

- Metodes:
  - **`send`**(Request $request)
  - **`store`**(MyMailStoreRequest $request)
  - **`create`**()


### `app/Http/Controllers/NotificationController.php`

#### `Intranet\Http\Controllers\NotificationController`
Class NotificationController

- Metodes:
  - **`inbox`**(): NotificationInboxService

#### `Intranet\Http\Controllers\notificationInboxService`
- Metodes:
  - **`search`**(): mixed

    /
  - **`read`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`readAll`**(): \Illuminate\Http\RedirectResponse

    /
  - **`deleteAll`**(): \Illuminate\Http\RedirectResponse

    /
  - **`destroy`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`iniBotones`**()

    /
  - **`show`**($id)

    /


### `app/Http/Controllers/OcrController.php`

#### `Intranet\Http\Controllers\OcrController`
- Metodes:
  - **`index`**()


### `app/Http/Controllers/OptionController.php`

#### `Intranet\Http\Controllers\OptionController`
Class OptionController

- Metodes:
  - **`store`**(OptionStoreRequest $request): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`destroy`**($id): \Illuminate\Http\RedirectResponse

    /


### `app/Http/Controllers/OrdenTrabajoController.php`

#### `Intranet\Http\Controllers\OrdenTrabajoController`
Class OrdenTrabajoController

- Metodes:
  - **`iniBotones`**()

    /
  - **`destroy`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`store`**(OrdenTrabajoRequest $request)
  - **`update`**(OrdenTrabajoRequest $request, $id)
  - **`imprime`**($id, $orientacion = 'portrait'): mixed

    /
  - **`resolve`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`open`**($id): \Illuminate\Http\RedirectResponse

    /


### `app/Http/Controllers/PPollController.php`

#### `Intranet\Http\Controllers\PPollController`
- Metodes:
  - **`iniBotones`**()
  - **`show`**($id)
  - **`store`**(PPollRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(PPollRequest $request, $id)
  - **`destroy`**($id)

    Elimina una plantilla de poll amb autorització explícita.


### `app/Http/Controllers/PanelActaController.php`

#### `Intranet\Http\Controllers\PanelActaController`
Class PanelActaController

- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\profesorService`
- Metodes:
  - **`index`**($grupo=null): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /

#### `Intranet\Http\Controllers\Session`
- Metodes:
  - **`search`**($grupo = null): mixed

    /

#### `Intranet\Http\Controllers\RolesUser`
- Metodes:
  - **`createGrupsPestana`**($grupos)

    /
  - **`iniPestanas`**($grupo = null): bool|void

    /


### `app/Http/Controllers/PanelActasController.php`

#### `Intranet\Http\Controllers\PanelActasController`
Class PanelActasController

- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`indice`**($search): \Illuminate\Contracts\View\View

    Mostra l'acta pendent del grup indicat amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`iniBotones`**()

    /

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap

#### `Intranet\Http\Controllers\send`
- Metodes: cap


### `app/Http/Controllers/PanelActividadController.php`

#### `Intranet\Http\Controllers\PanelActividadController`
Class PanelActividadController

- Metodes:
  - **`iniBotones`**()

    /


### `app/Http/Controllers/PanelActividadOrientacionController.php`

#### `Intranet\Http\Controllers\PanelActividadOrientacionController`
Class PanelActividadOrientacionController

- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\View

    Mostra el panell d'activitats d'orientació amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`iniBotones`**()

    /

#### `Intranet\Http\Controllers\panel`
- Metodes:
  - **`search`**($grupo = null): mixed

    /

#### `Intranet\Http\Controllers\Actividad`
- Metodes:
  - **`createWithDefaultValues`**($default=[]): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /


### `app/Http/Controllers/PanelAlumnoCursoController.php`

#### `Intranet\Http\Controllers\PanelAlumnoCursoController`
Class PanelAlumnoCursoController

- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\View

    Mostra el llistat de cursos d'alumne amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`search`**(): mixed

    /

#### `Intranet\Http\Controllers\Curso`
- Metodes:
  - **`iniBotones`**()

    /

#### `Intranet\Http\Controllers\panel`
- Metodes: cap


### `app/Http/Controllers/PanelColaboracionController.php`

#### `Intranet\Http\Controllers\PanelColaboracionController`
Class PanelColaboracionController

- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null, ?ColaboracionService $colaboracionService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`colaboraciones`**(): ColaboracionService

#### `Intranet\Http\Controllers\colaboracionService`
- Metodes:
  - **`index`**(): mixed

    /
  - **`iniBotones`**()

    /
  - **`search`**(): \Illuminate\Support\Collection<int,

    Carrega les col·laboracions del tutor i les relacionades per centre/departament.
  - **`update`**(Request $request, $id): \Illuminate\Http\RedirectResponse

    /
  - **`store`**(Request $request): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\validate`
- Metodes:
  - **`showEmpresa`**($id)
  - **`copy`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`destroy`**($id): \Illuminate\Http\RedirectResponse

    /
  - **`live`**()


### `app/Http/Controllers/PanelComisionController.php`

#### `Intranet\Http\Controllers\PanelComisionController`
Class PanelComisionController

- Metodes:
  - **`iniBotones`**()

    /


### `app/Http/Controllers/PanelControlProgramacionController.php`

#### `Intranet\Http\Controllers\PanelControlProgramacionController`
Class PanelControlProgramacionController

- Metodes:
  - **`search`**(): \Illuminate\Database\Eloquent\Collection|Modulo_ciclo[]|mixed

    /
  - **`iniBotones`**()


### `app/Http/Controllers/PanelCursoController.php`

#### `Intranet\Http\Controllers\PanelCursoController`
Class PanelCursoController

- Metodes:
  - **`search`**(): mixed

    /
  - **`iniBotones`**()

    /


### `app/Http/Controllers/PanelDocAgrupadosController.php`

#### `Intranet\Http\Controllers\PanelDocAgrupadosController`
Class PanelDocAgrupadosController

- Metodes:
  - **`index`**($grupo=null): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /

#### `Intranet\Http\Controllers\iniPestanas`
- Metodes:
  - **`search`**(): mixed

    /

#### `Intranet\Http\Controllers\Documento`
- Metodes:
  - **`iniPestanas`**($grupo= null)

    /


### `app/Http/Controllers/PanelDocumentoController.php`

#### `Intranet\Http\Controllers\PanelDocumentoController`
Class PanelDocumentoController

- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /

#### `Intranet\Http\Controllers\Session`
- Metodes:
  - **`iniBotones`**()

    /
  - **`search`**(): mixed

    /

#### `Intranet\Http\Controllers\Documento`
- Metodes: cap


### `app/Http/Controllers/PanelDualController.php`

#### `Intranet\Http\Controllers\PanelDualController`
- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\View

    Mostra el panell de control dual amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`iniBotones`**()

#### `Intranet\Http\Controllers\panel`
- Metodes:
  - **`search`**()

#### `Intranet\Http\Controllers\Fct`
- Metodes:
  - **`show`**($id)

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap


### `app/Http/Controllers/PanelEmpresaSCController.php`

#### `Intranet\Http\Controllers\PanelEmpresaSCController`
Class PanelEmpresaSCController

- Metodes:
  - **`__construct`**(?EmpresaService $empresaService = null)
  - **`empreses`**(): EmpresaService

#### `Intranet\Http\Controllers\empresaService`
- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\View

    Mostra el panell d'empreses amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`search`**(): mixed

    /

#### `Intranet\Http\Controllers\empreses`
- Metodes:
  - **`iniBotones`**()

    /


### `app/Http/Controllers/PanelErasmusController.php`

#### `Intranet\Http\Controllers\PanelErasmusController`
Class PanelEmpresaSCController

- Metodes:
  - **`search`**(): mixed

    /

#### `Intranet\Http\Controllers\empreses`
- Metodes:
  - **`iniBotones`**()

    /

#### `Intranet\Http\Controllers\panel`
- Metodes: cap


### `app/Http/Controllers/PanelExpedienteController.php`

#### `Intranet\Http\Controllers\PanelExpedienteController`
Class PanelExpedienteController

- Metodes:
  - **`iniBotones`**()

    /
  - **`search`**(): mixed

    /


### `app/Http/Controllers/PanelFaltaController.php`

#### `Intranet\Http\Controllers\PanelFaltaController`
Class PanelFaltaController

- Metodes:
  - **`search`**()
  - **`iniBotones`**()

    /


### `app/Http/Controllers/PanelFaltaItacaController.php`

#### `Intranet\Http\Controllers\PanelFaltaItacaController`
Class PanelFaltaItacaController

- Metodes:
  - **`iniBotones`**()

    /


### `app/Http/Controllers/PanelFctAvalController.php`

#### `Intranet\Http\Controllers\PanelFctAvalController`
Class PanelFctAvalController

- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null, ?AlumnoFctService $alumnoFctService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`alumnoFcts`**(): AlumnoFctService

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - **`avals`**(): AlumnoFctAvalService

#### `Intranet\Http\Controllers\alumnoFctAvalService`
- Metodes:
  - **`search`**(): \Illuminate\Support\Collection|mixed

    /
  - **`iniBotones`**()

    /
  - **`apte`**($id): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\avals`
- Metodes:
  - **`demanarActa`**(): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap

#### `Intranet\Http\Controllers\findOrFail`
- Metodes:
  - **`send`**($id)
  - **`estadistiques`**()


### `app/Http/Controllers/PanelFctController.php`

#### `Intranet\Http\Controllers\PanelFctController`
Class FctController

- Metodes:
  - **`__construct`**(?FctService $fctService = null)
  - **`fcts`**(): FctService

#### `Intranet\Http\Controllers\fctService`
- Metodes:
  - **`index`**(): mixed

    /

#### `Intranet\Http\Controllers\search`
- Metodes:
  - **`iniBotones`**()

    /
  - **`search`**(): mixed

    /

#### `Intranet\Http\Controllers\fcts`
- Metodes: cap


### `app/Http/Controllers/PanelFinCursoController.php`

#### `Intranet\Http\Controllers\PanelFinCursoController`
Class PanelActaController

- Metodes:
  - **`index`**($profesor=null): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /

#### `Intranet\Http\Controllers\find`
- Metodes:
  - **`profesor`**()
  - **`mantenimiento`**()
  - **`direccion`**()
  - **`jefe_dpto`**()
  - **`tutor`**()
  - **`lookForCheckFol`**(&$avisos)

#### `Intranet\Http\Controllers\misGrupos`
- Metodes:
  - **`lookForIssues`**(&$avisos)
  - **`lookForActesPendents`**(&$avisos)

#### `Intranet\Http\Controllers\withActaPendiente`
- Metodes:
  - **`lookforInformsDepartment`**(&$avisos)
  - **`lookAtQualitatUpload`**(&$avisos)
  - **`lookAtActasUpload`**(&$avisos)
  - **`lookAtPollsTutor`**(&$avisos)
  - **`lookAtFctsProjects`**(&$avisos)

#### `Intranet\Http\Controllers\firstByTutor`
- Metodes:
  - **`loadPreviousVotes`**($poll)
  - **`lookForMyResults`**(&$avisos)

#### `Intranet\Http\Controllers\misModulos`
- Metodes:
  - **`lookforMyPrograms`**(&$avisos)
  - **`lookUnPaidBills`**(&$avisos)

#### `Intranet\Http\Controllers\hasPendingUnpaidByProfesor`
- Metodes: cap


### `app/Http/Controllers/PanelGuardiaController.php`

#### `Intranet\Http\Controllers\PanelGuardiaController`
- Metodes:
  - **`comisions`**(): ComisionService

#### `Intranet\Http\Controllers\profesores`
- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\horarios`
- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Http\Controllers\fitxatge`
- Metodes:
  - **`fitxatge`**(): FitxatgeService

#### `Intranet\Http\Controllers\index`
- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\View

    Mostra el panell de guàrdies amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`iniBotones`**()

#### `Intranet\Http\Controllers\panel`
- Metodes:
  - **`search`**()

#### `Intranet\Http\Controllers\sesion`
- Metodes:
  - **`coincideHorario`**($elemento, $sesion): bool
  - **`getHorasAfectas`**($elemento): array


### `app/Http/Controllers/PanelIncidenciaController.php`

#### `Intranet\Http\Controllers\PanelIncidenciaController`
- Metodes:
  - **`index`**()

#### `Intranet\Http\Controllers\panel`
- Metodes: cap


### `app/Http/Controllers/PanelInfDptoController.php`

#### `Intranet\Http\Controllers\PanelInfDptoController`
- Metodes:
  - **`search`**()
  - **`iniPestanas`**($parametres = null)


### `app/Http/Controllers/PanelListadoEntregasController.php`

#### `Intranet\Http\Controllers\PanelListadoEntregasController`
- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\View

    Mostra el llistat d'entregues amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`search`**()

#### `Intranet\Http\Controllers\hazArray`
- Metodes: cap

#### `Intranet\Http\Controllers\distinctModulos`
- Metodes:
  - **`iniBotones`**()
  - **`hazInformeTrimestral`**(Request $request)

#### `Intranet\Http\Controllers\hazPdfInforme`
- Metodes:
  - **`avisaTodos`**()
  - **`avisaFaltaEntrega`**($id)

#### `Intranet\Http\Controllers\Modulo_grupo`
- Metodes: cap

#### `Intranet\Http\Controllers\profesorIds`
- Metodes:
  - **`pdf`**($id)

#### `Intranet\Http\Controllers\response`
- Metodes:
  - **`existeInforme`**()
  - **`hazPdfInforme`**($observaciones, $trimestre, $proyectos = null)

#### `Intranet\Http\Controllers\byDepartamento`
- Metodes:
  - **`faltan`**()


### `app/Http/Controllers/PanelLoteController.php`

#### `Intranet\Http\Controllers\PanelLoteController`
Class LoteController

- Metodes:
  - **`search`**()
  - **`iniBotones`**()
  - **`barcode`**($id, $posicion=1)


### `app/Http/Controllers/PanelModuloGrupoController.php`

#### `Intranet\Http\Controllers\PanelModuloGrupoController`
- Metodes:
  - **`search`**()
  - **`iniBotones`**()
  - **`pdf`**($id)


### `app/Http/Controllers/PanelOrdenTrabajoController.php`

#### `Intranet\Http\Controllers\PanelOrdenTrabajoController`
- Metodes:
  - **`indice`**($search): \Illuminate\Contracts\View\View

    Mostra el detall d'orde de treball amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`search`**()

#### `Intranet\Http\Controllers\Incidencia`
- Metodes:
  - **`iniBotones`**()

#### `Intranet\Http\Controllers\panel`
- Metodes:
  - **`iniPestanas`**($parametres = null)


### `app/Http/Controllers/PanelPG0301Controller.php`

#### `Intranet\Http\Controllers\PanelPG0301Controller`
- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`indice`**($search): \Illuminate\Contracts\View\View

    Mostra el llistat per grup amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`iniBotones`**()

#### `Intranet\Http\Controllers\Session`
- Metodes:
  - **`search`**()

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap


### `app/Http/Controllers/PanelPGDualController.php`

#### `Intranet\Http\Controllers\PanelPGDualController`
- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`indice`**($search): \Illuminate\Contracts\View\View

    Mostra el llistat dual per grup amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`iniBotones`**()

#### `Intranet\Http\Controllers\Session`
- Metodes:
  - **`search`**()

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap


### `app/Http/Controllers/PanelPollResponseController.php`

#### `Intranet\Http\Controllers\PanelPollResponseController`
- Metodes:
  - **`iniBotones`**()
  - **`search`**()


### `app/Http/Controllers/PanelPollResultController.php`

#### `Intranet\Http\Controllers\PanelPollResultController`
- Metodes:
  - **`iniBotones`**()
  - **`search`**()


### `app/Http/Controllers/PanelPracticasController.php`

#### `Intranet\Http\Controllers\PanelPracticasController`
- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\View

    Mostra el panell de control FCT amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`iniBotones`**()

#### `Intranet\Http\Controllers\panel`
- Metodes:
  - **`search`**()

#### `Intranet\Http\Controllers\grupos`
- Metodes: cap


### `app/Http/Controllers/PanelPresenciaController.php`

#### `Intranet\Http\Controllers\PanelPresenciaController`
- Metodes:
  - **`comisions`**(): ComisionService

#### `Intranet\Http\Controllers\profesores`
- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\horarios`
- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Http\Controllers\indice`
- Metodes:
  - **`indice`**($dia = null)

#### `Intranet\Http\Controllers\Session`
- Metodes:
  - **`email`**($usuario, $dia)

#### `Intranet\Http\Controllers\self`
- Metodes: cap

#### `Intranet\Http\Controllers\fitxaDiaManual`
- Metodes:
  - **`noHanFichado`**($dia)


### `app/Http/Controllers/PanelProcedimientoAcompanyantController.php`

#### `Intranet\Http\Controllers\PanelProcedimientoAcompanyantController`
Class PanelExpedienteOrientacionController

- Metodes:
  - **`iniPestanas`**($parametres = null)
  - **`iniBotones`**()

    /
  - **`search`**(): mixed

    /


### `app/Http/Controllers/PanelProcedimientoController.php`

#### `Intranet\Http\Controllers\PanelProcedimientoController`
Class PanelExpedienteOrientacionController

- Metodes:
  - **`index`**()

#### `Intranet\Http\Controllers\search`
- Metodes:
  - **`iniBotones`**()

    /
  - **`search`**(): mixed

    /

#### `Intranet\Http\Controllers\Expediente`
- Metodes: cap


### `app/Http/Controllers/PanelProjecteController.php`

#### `Intranet\Http\Controllers\PanelProjecteController`
Class PanelProjecteController

- Metodes:
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\profesorService`
- Metodes:
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`myTutorGroup`**()
  - **`search`**()

    Recupera els projectes del grup del tutor autenticat.
  - **`store`**(ProyectoRequest $request)

#### `Intranet\Http\Controllers\myTutorGroup`
- Metodes:
  - **`pdf`**($id)

#### `Intranet\Http\Controllers\hazZip`
- Metodes:
  - **`acta`**()

    Genera l'acta de valoració de propostes del grup.

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - **`iniBotones`**()


### `app/Http/Controllers/PanelProyectoController.php`

#### `Intranet\Http\Controllers\PanelProyectoController`
- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\View

    Mostra el panell de projectes documentals amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`iniPestanas`**($parametres = null)
  - **`search`**()
  - **`iniBotones`**()


### `app/Http/Controllers/PanelSeguimientoAlumnosController.php`

#### `Intranet\Http\Controllers\PanelSeguimientoAlumnosController`
- Metodes:
  - **`indice`**($search)
  - **`store`**(Request $request)
  - **`destroy`**($id)


### `app/Http/Controllers/PanelSignaturaController.php`

#### `Intranet\Http\Controllers\PanelSignaturaController`
Class PanelExpedienteController

- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\View

    Mostra el panell de signatures de direcció amb autorització prèvia.

#### `Intranet\Http\Controllers\parent`
- Metodes:
  - **`iniBotones`**()

    /
  - **`search`**(): mixed

    /

#### `Intranet\Http\Controllers\Signatura`
- Metodes:
  - **`sign`**(Request $request)

#### `Intranet\Http\Controllers\array_keys`
- Metodes: cap

#### `Intranet\Http\Controllers\send`
- Metodes: cap


### `app/Http/Controllers/PanelSolicitudOrientacionController.php`

#### `Intranet\Http\Controllers\PanelSolicitudOrientacionController`
Class PanelSolicitudOrientacionController

- Metodes:
  - **`iniBotones`**()

    /
  - **`active`**($id)

    Activa una sol·licitud d'orientació pendent.
  - **`resolve`**(Request $request, $id)

    Resol una sol·licitud d'orientació activa.
  - **`search`**()

    Recupera les sol·licituds visibles per a l'orientador autenticat.


### `app/Http/Controllers/PdfController.php`

#### `Intranet\Http\Controllers\PdfController`
- Metodes:
  - **`index`**()


### `app/Http/Controllers/PollController.php`

#### `Intranet\Http\Controllers\PollController`
- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`polls`**(): PollWorkflowService

#### `Intranet\Http\Controllers\pollWorkflowService`
- Metodes:
  - **`iniBotones`**()
  - **`preparaEnquesta`**($id)
  - **`guardaEnquesta`**(Request $request, $id)
  - **`lookAtMyVotes`**($id)
  - **`lookAtAllVotes`**($id)


### `app/Http/Controllers/ProfesorController.php`

#### `Intranet\Http\Controllers\ProfesorController`
- Metodes:
  - **`__construct`**(?ProfesorService $profesorService = null, ?HorarioService $horarioService = null, ?GrupoService $grupoService = null)
  - **`profesores`**(): ProfesorService

#### `Intranet\Http\Controllers\profesorService`
- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`index`**()
  - **`departamento`**()
  - **`fse`**()
  - **`equipoDirectivo`**()
  - **`comissio`**()
  - **`rol`**($rol)
  - **`equipo`**($grupo)
  - **`update`**(Request $request, $id)
  - **`miApiToken`**()
  - **`avisaColectivo`**(Request $request)
  - **`alerta`**(Request $request, $id)
  - **`carnet`**($profesor)
  - **`tarjeta`**($profesor)
  - **`iniBotones`**()
  - **`iniProfileBotones`**()
  - **`horario`**($id)
  - **`imprimirHorarios`**()
  - **`change`**($idProfesor)
  - **`backChange`**()


### `app/Http/Controllers/ProgramacionController.php`

#### `Intranet\Http\Controllers\ProgramacionController`
- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Http\Controllers\horarioService`
- Metodes:
  - **`search`**()
  - **`init`**($id)
  - **`seguimiento`**($id)
  - **`avisaFaltaEntrega`**($id)

#### `Intranet\Http\Controllers\profesorIds`
- Metodes:
  - **`advise`**($id)
  - **`updateSeguimiento`**(Request $request, $id)
  - **`link`**($id)
  - **`iniBotones`**()


### `app/Http/Controllers/ProjecteController.php`

#### `Intranet\Http\Controllers\ProjecteController`
Class EspacioController

- Metodes:
  - **`search`**()
  - **`store`**(ProyectoRequest $request)
  - **`update`**(ProyectoRequest $request, $id)
  - **`email`**($id)

#### `Intranet\Http\Controllers\send`
- Metodes:
  - **`pdf`**($id)

#### `Intranet\Http\Controllers\hazPdf`
- Metodes:
  - **`iniBotones`**()


### `app/Http/Controllers/QualitatDocumentoController.php`

#### `Intranet\Http\Controllers\QualitatDocumentoController`
Class PanelDocumentoController

- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /
  - **`iniBotones`**()

    /
  - **`search`**(): mixed

    /


### `app/Http/Controllers/RedirectAfterAuthenticationController.php`

#### `Intranet\Http\Controllers\RedirectAfterAuthenticationController`
Orquestra l'execució d'accions SAO després de validar la contrasenya.

- Metodes:
  - **`__construct`**(?SaoRunner $saoRunner = null)

#### `Intranet\Http\Controllers\__invoke`
- Metodes:
  - **`__invoke`**(PasswordRequest $request)

#### `Intranet\Http\Controllers\method_exists`
- Metodes: cap


### `app/Http/Controllers/ReservaController.php`

#### `Intranet\Http\Controllers\ReservaController`
Class ReservaController

- Metodes:
  - **`index`**(): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /


### `app/Http/Controllers/ResultadoController.php`

#### `Intranet\Http\Controllers\ResultadoController`
Class ResultadoController

- Metodes:
  - **`iniBotones`**()

    /
  - **`__construct`**(?GrupoService $grupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`rellenaPropuestasMejora`**($idModulo)
  - **`store`**(ResultadoStoreRequest $request)
  - **`update`**(ResultadoUpdateRequest $request, $id)
  - **`destroy`**($id)

    Elimina un resultat amb autorització explícita.
  - **`search`**(): mixed

    /

#### `Intranet\Http\Controllers\misModulos`
- Metodes:
  - **`listado`**(): \Illuminate\Http\RedirectResponse

    /
  - **`createWithDefaultValues`**($default=[])


### `app/Http/Controllers/ReunionController.php`

#### `Intranet\Http\Controllers\ReunionController`
Controlador de gestió de reunions i assistències.

- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null, ?ReunionService $reunionService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`reunionService`**(): ReunionService

#### `Intranet\Http\Controllers\reunionService`
- Metodes:
  - **`search`**()
  - **`createWithDefaultValues`**($default=[])
  - **`store`**(ReunionStoreRequest $request)

#### `Intranet\Http\Controllers\DB`
- Metodes:
  - **`edit`**($id = null)

#### `Intranet\Http\Controllers\activosOrdered`
- Metodes:
  - **`update`**(ReunionUpdateRequest $request, $id)
  - **`tAlumnos`**($reunion, $sAlumnos)
  - **`altaProfesor`**(Request $request, $reunion_id)
  - **`borrarProfesor`**($reunion_id, $profesor_id)
  - **`borrarAlumno`**($reunion_id, $alumno_id)
  - **`altaAlumno`**(Request $request, $reunion_id)
  - **`altaOrden`**(OrdenReunionStoreRequest $request, $reunion_id)
  - **`borrarOrden`**($reunion_id, $orden_id)
  - **`notify`**($id)
  - **`email`**($id)
  - **`iniBotones`**()
  - **`pdf`**($id)
  - **`actaCompleta`**(Reunion $reunion)
  - **`saveFile`**($id)
  - **`deleteFile`**(Request $request, $id)
  - **`listado`**($dia = null)
  - **`avisaFaltaActa`**(Request $request)
  - **`construye_pdf`**($id)
  - **`informe`**($elemento)
  - **`preparePdf`**($informe, $aR)


### `app/Http/Controllers/SendAvaluacioEmailController.php`

#### `Intranet\Http\Controllers\SendAvaluacioEmailController`
Class ImportController

- Metodes:
  - **`create`**(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View

    /
  - **`generaToken`**()
  - **`store`**(SendAvaluacioEmailStoreRequest $request): \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View

    /
  - **`getToken`**(Request $request)
  - **`obtenToken`**($aR)
  - **`sendMatricula`**($aR)


### `app/Http/Controllers/SettingController.php`

#### `Intranet\Http\Controllers\SettingController`
Controlador de manteniment de settings de sistema.

- Metodes:
  - **`search`**()
  - **`iniBotones`**()
  - **`store`**(SettingRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(SettingRequest $request, $id)
  - **`destroy`**($id)

    Elimina un setting existent.


### `app/Http/Controllers/SignaturaAlumneController.php`

#### `Intranet\Http\Controllers\SignaturaAlumneController`
Class PanelExpedienteController

- Metodes:
  - **`__construct`**(?AlumnoFctService $alumnoFctService = null)
  - **`alumnoFcts`**(): AlumnoFctService

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - **`index`**()
  - **`grid`**()
  - **`pdf`**($id): mixed

    /
  - **`iniBotones`**()


### `app/Http/Controllers/SignaturaController.php`

#### `Intranet\Http\Controllers\SignaturaController`
Class PanelExpedienteController

- Metodes:
  - **`__construct`**(?AlumnoFctService $alumnoFctService = null)
  - **`alumnoFcts`**(): AlumnoFctService

#### `Intranet\Http\Controllers\alumnoFctService`
- Metodes:
  - **`store`**(SignaturaStoreRequest $request)

#### `Intranet\Http\Controllers\file`
- Metodes:
  - **`iniBotones`**()

    /
  - **`deleteAll`**()

#### `Intranet\Http\Controllers\search`
- Metodes:
  - **`pdf`**($id)
  - **`destroy`**($id)
  - **`sendUnique`**($id)
  - **`sendMultiple`**(Request $request, $tipus)
  - **`upload`**(Request $request, $id)
  - **`a5`**()

#### `Intranet\Http\Controllers\Signatura`
- Metodes: cap


### `app/Http/Controllers/SolicitudController.php`

#### `Intranet\Http\Controllers\SolicitudController`
Class ExpedienteController

- Metodes:
  - **`store`**(SolicitudRequest $request)

#### `Intranet\Http\Controllers\merge`
- Metodes:
  - **`update`**(SolicitudRequest $request, $id)
  - **`confirm`**($id)
  - **`iniBotones`**()

    /
  - **`init`**($id): \Illuminate\Http\RedirectResponse

    /

#### `Intranet\Http\Controllers\send`
- Metodes:
  - **`createWithDefaultValues`**($default = [])
  - **`show`**($id)
  - **`destroy`**($id)

    Elimina una sol·licitud amb autorització explícita.


### `app/Http/Controllers/TaskController.php`

#### `Intranet\Http\Controllers\TaskController`
Controlador de manteniment i validació de tasques.

- Metodes:
  - **`__construct`**(?TaskValidationService $taskValidationService = null)
  - **`validationService`**(): TaskValidationService

#### `Intranet\Http\Controllers\taskValidationService`
- Metodes:
  - **`iniBotones`**()
  - **`store`**(TaskRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(TaskRequest $request, $id)
  - **`check`**($id)


### `app/Http/Controllers/TeacherImportController.php`

#### `Intranet\Http\Controllers\TeacherImportController`
- Metodes:
  - **`create`**()
  - **`store`**(Request $request)
  - **`storeAsync`**(Request $request, mixed $validatedFile = null)
  - **`run`**($fxml, Request $request)
  - **`sacaCampos`**($atrxml, $llave, $func = 1)
  - **`filtro`**($filtro, $campos)
  - **`required`**($required, $campos)
  - **`imports`**(): ImportService

#### `Intranet\Http\Controllers\importService`
- Metodes:
  - **`workflows`**(): ImportWorkflowService

#### `Intranet\Http\Controllers\importWorkflowService`
- Metodes:
  - **`schemas`**(): ImportSchemaProvider

#### `Intranet\Http\Controllers\importSchemaProvider`
- Metodes:
  - **`xmlHelper`**(): ImportXmlHelperService

#### `Intranet\Http\Controllers\importXmlHelperService`
- Metodes:
  - **`executions`**(): TeacherImportExecutionService

#### `Intranet\Http\Controllers\teacherImportExecutionService`
- Metodes:
  - **`camposBdXml`**(): array

    /
  - **`executeSyncImport`**(mixed $file, Request $request)
  - **`resolveImportMode`**(Request $request): string
  - **`authorizeImportManagement`**(bool $allowConsole = false): void


### `app/Http/Controllers/TipoActividadController.php`

#### `Intranet\Http\Controllers\TipoActividadController`
Class LoteController

- Metodes:
  - **`store`**(TipoActividadRequest $request)
  - **`update`**(TipoActividadUpdateRequest $request, $id)
  - **`destroy`**($id)

    Elimina un tipus d'activitat amb autorització explícita.
  - **`search`**()
  - **`iniBotones`**()


### `app/Http/Controllers/TipoIncidenciaController.php`

#### `Intranet\Http\Controllers\TipoIncidenciaController`
Class ComisionController

- Metodes:
  - **`iniBotones`**()

    /
  - **`search`**()
  - **`store`**(TipoIncidenciaRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(TipoIncidenciaRequest $request, $id)
  - **`destroy`**($id)

    Elimina un tipus d'incidència amb autorització explícita.


### `app/Http/Controllers/TutoriaController.php`

#### `Intranet\Http\Controllers\TutoriaController`
- Metodes:
  - **`__construct`**(?GrupoService $grupoService = null)
  - **`grupos`**(): GrupoService

#### `Intranet\Http\Controllers\grupoService`
- Metodes:
  - **`index`**()
  - **`search`**()
  - **`detalle`**($id)
  - **`indexTutoria`**()
  - **`anexo`**($id)
  - **`iniTutBotones`**()
  - **`iniBotones`**()


### `app/Http/Controllers/TutoriaGrupoController.php`

#### `Intranet\Http\Controllers\TutoriaGrupoController`
- Metodes:
  - **`createfrom`**($tutoria, $grupo)
  - **`create`**($default = [])

#### `Intranet\Http\Controllers\FormBuilder`
- Metodes:
  - **`edit`**($id = null)
  - **`store`**(TutoriaGrupoStoreRequest $request)

#### `Intranet\Http\Controllers\persist`
- Metodes:
  - **`update`**(TutoriaGrupoUpdateRequest $request, $id)
  - **`destroy`**($id)

    Elimina una relació tutoria-grup amb autorització explícita.
  - **`search`**()
  - **`iniBotones`**()


### `app/Http/Controllers/VotesController.php`

#### `Intranet\Http\Controllers\VotesController`
- Metodes:
  - **`showColaboracion`**($colaboracion)



## Models

### `app/Entities/Actividad.php`

#### `Intranet\Entities\Actividad`
Model d'activitats extraescolars/complementàries.

- Metodes: cap

#### `Intranet\Entities\grupos`
- Metodes:
  - **`grupos`**()

#### `Intranet\Entities\profesores`
- Metodes:
  - **`profesores`**()

#### `Intranet\Entities\withPivot`
- Metodes:
  - **`tipoActividad`**()

#### `Intranet\Entities\Creador`
- Metodes:
  - **`Creador`**(): string

    Devueld el id del Coordinador
  - **`scopeProfesor`**($query, $dni)
  - **`getDesdeAttribute`**($entrada): string|null

    Accessor de `desde` en format de visualització.
  - **`getHastaAttribute`**($salida): string|null

    Accessor de `hasta` en format de visualització.
  - **`scopeNext`**($query): \Illuminate\Database\Eloquent\Builder

    Filtra activitats futures.
  - **`scopeAuth`**($query): \Illuminate\Database\Eloquent\Builder

    Filtra activitats autoritzades o no extraescolars.
  - **`scopeDia`**($query, $dia): \Illuminate\Database\Eloquent\Builder

    Filtra activitats que cauen en un dia concret.
  - **`scopeDepartamento`**($query, $dep): \Illuminate\Database\Eloquent\Builder

    Filtra per departament a través dels grups de l'activitat.
  - **`Tutor`**(): \Illuminate\Database\Eloquent\Relations\BelongsToMany

    Relació de professor coordinador de l'activitat.

#### `Intranet\Entities\wherePivot`
- Metodes:
  - **`getcoordAttribute`**(): int

    Accessor booleà per saber si l'usuari autenticat és coordinador.
  - **`getsituacionAttribute`**(): string

    Accessor de text de situació segons estat.
  - **`loadPoll`**(): \Illuminate\Support\Collection

    Carrega activitats de poll dels grups de l'usuari.
  - **`getRecomendadaAttribute`**(): string

    Accessor de "recomanada" en format Sí/No.
  - **`getTipoActividadIdOptions`**(): array

    Opcions de tipus d'activitat segons departament de l'usuari.


### `app/Entities/ActividadGrupo.php`

#### `Intranet\Entities\ActividadGrupo`
- Metodes:
  - **`scopeDepartamento`**($query, $dep)

#### `Intranet\Entities\byDepartamento`
- Metodes: cap


### `app/Entities/ActividadProfesor.php`

#### `Intranet\Entities\ActividadProfesor`
- Metodes:
  - **`scopeTutor`**($query)


### `app/Entities/Activity.php`

#### `Intranet\Entities\Activity`
- Metodes:
  - **`record`**(string $action, ?Model $model = null, ?string $comentari = null, ?string $fecha = null, ?string $document = null): self

    Manté API estàtica legacy delegant en el servei d'aplicació.

#### `Intranet\Entities\record`
- Metodes:
  - **`scopeProfesor`**($query, $profesor)
  - **`scopeModelo`**($query, $modelo)

    Filtra per classe de model emmagatzemada en format FQCN.
  - **`scopeNotUpdate`**($query)
  - **`scopeMail`**($query)
  - **`scopeId`**($query, $id)
  - **`scopeIds`**($query, $ids)
  - **`scopeRelationId`**($query, $id)
  - **`propietario`**()

#### `Intranet\Entities\getUpdatedAtAttribute`
- Metodes:
  - **`getUpdatedAtAttribute`**($value)


### `app/Entities/Adjunto.php`

#### `Intranet\Entities\Adjunto`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\scopeFindByName`
- Metodes:
  - **`scopeFindByName`**($query, $path, $name)
  - **`scopeGetByPath`**($query, $path)
  - **`getPathAttribute`**()
  - **`getFileAttribute`**()
  - **`getDirectoryAttribute`**()
  - **`getModeloAttribute`**()
  - **`getModeloIdAttribute`**()


### `app/Entities/Alumno.php`

#### `Intranet\Entities\Alumno`
- Metodes:
  - **`Curso`**(): BelongsToMany

#### `Intranet\Entities\withPivot`
- Metodes:
  - **`AlumnoFct`**(): HasMany

#### `Intranet\Entities\Grupo`
- Metodes:
  - **`Grupo`**(): BelongsToMany

#### `Intranet\Entities\Fcts`
- Metodes:
  - **`Fcts`**(): BelongsToMany

#### `Intranet\Entities\FctsColaboracion`
- Metodes:
  - **`FctsColaboracion`**(int $colaboracion): BelongsToMany
  - **`AlumnoResultado`**(): HasMany

#### `Intranet\Entities\Provincia`
- Metodes:
  - **`Provincia`**(): BelongsTo

#### `Intranet\Entities\Municipio`
- Metodes:
  - **`Municipio`**(): BelongsTo

#### `Intranet\Entities\where`
- Metodes:
  - **`Projecte`**()

#### `Intranet\Entities\scopeQGrupo`
- Metodes:
  - **`scopeQGrupo`**(Builder $query, string|array $grupo): Builder
  - **`scopeMenor`**(Builder $query, ?string $fecha = null): Builder
  - **`scopeMisAlumnos`**(Builder $query, ?string $profesor = null, bool $dual = false): Builder

#### `Intranet\Entities\qTutor`
- Metodes: cap


### `app/Entities/AlumnoCurso.php`

#### `Intranet\Entities\AlumnoCurso`
- Metodes:
  - **`scopeCurso`**($query, $curso)
  - **`scopeFinalizado`**($query)
  - **`Alumno`**()

#### `Intranet\Entities\Curso`
- Metodes:
  - **`Curso`**()

#### `Intranet\Entities\getNombreAttribute`
- Metodes:
  - **`getNombreAttribute`**()
  - **`getSexoAttribute`**()
  - **`getFullNameAttribute`**()
  - **`getDniAttribute`**()


### `app/Entities/AlumnoFct.php`

#### `Intranet\Entities\AlumnoFct`
- Metodes: cap

#### `Intranet\Entities\null`
- Metodes:
  - **`Alumno`**()

#### `Intranet\Entities\Fct`
- Metodes:
  - **`Fct`**()

#### `Intranet\Entities\Dual`
- Metodes:
  - **`Dual`**()

#### `Intranet\Entities\Signatures`
- Metodes:
  - **`Signatures`**()

#### `Intranet\Entities\Tutor`
- Metodes:
  - **`Tutor`**()

#### `Intranet\Entities\Contactos`
- Metodes:
  - **`Contactos`**()

#### `Intranet\Entities\mail`
- Metodes: cap

#### `Intranet\Entities\scopeMisFcts`
- Metodes:
  - **`scopeMisFcts`**($query, $profesor = null)
  - **`scopeTotesFcts`**($query, $profesor = null)
  - **`scopeMisProyectos`**($query, $profesor = null)
  - **`scopeEsFct`**($query)
  - **`scopeEsAval`**($query)
  - **`scopeEsDual`**($query)
  - **`scopeMisDual`**($query, $profesor=null)
  - **`scopeMisConvalidados`**($query, $profesor=null)
  - **`scopeNoAval`**($query)
  - **`scopePendiente`**($query)
  - **`scopeAval`**($query)
  - **`scopePendienteNotificar`**($query)
  - **`scopeCalificados`**($query)
  - **`scopeAprobados`**($query)
  - **`scopeTitulan`**($query)
  - **`scopeRealFcts`**($query, $profesor = null)
  - **`scopeAvaluables`**($query, $profesor = null)
  - **`scopeMisErasmus`**($query, $profesor = null)
  - **`scopeEsErasmus`**($query)
  - **`scopeEsExempt`**($query)
  - **`scopeEstaSao`**($query)
  - **`scopeActiva`**($query)
  - **`scopeHaEmpezado`**($query)
  - **`scopeNoHaAcabado`**($query)
  - **`getEmailAttribute`**()
  - **`getCentroAttribute`**()
  - **`getNombreAttribute`**()
  - **`getNomEdatAttribute`**()
  - **`getQualificacioAttribute`**()
  - **`getDesdeAttribute`**($entrada)
  - **`getHastaAttribute`**($entrada)
  - **`getFinPracticasAttribute`**()
  - **`getClassAttribute`**()
  - **`presenter`**(): AlumnoFctPresenter
  - **`getAdjuntosAttribute`**()
  - **`routeFile`**($anexe)
  - **`getSignAttribute`**()
  - **`getContactoAttribute`**()
  - **`getFullNameAttribute`**()
  - **`getHorasRealizadasAttribute`**()
  - **`getHorasTotalAttribute`**()
  - **`getPeriodeAttribute`**()
  - **`getProjecteAttribute`**()
  - **`getAsociacionAttribute`**()
  - **`getMiniCentroAttribute`**()
  - **`getInstructorAttribute`**()
  - **`getGrupAttribute`**()
  - **`scopeGrupo`**($query, $grupo)
  - **`getQuienAttribute`**()
  - **`getSaoAnnexesAttribute`**()
  - **`getA2Attribute`**()
  - **`getA1Attribute`**()
  - **`getA3Attribute`**()
  - **`getIdPrintAttribute`**()
  - **`getAnnexesCollection`**(): \Illuminate\Support\Collection
  - **`signatureService`**(): AlumnoFctSignatureService


### `app/Entities/AlumnoGrupo.php`

#### `Intranet\Entities\AlumnoGrupo`
- Metodes:
  - **`find`**($params, $columns = ['*']): static|\Illuminate\Database\Eloquent\Collection<static>|null

    Troba un registre d'alumne-grup.
  - **`Alumno`**()

#### `Intranet\Entities\Grupo`
- Metodes:
  - **`Grupo`**()

#### `Intranet\Entities\getNombreAttribute`
- Metodes:
  - **`getNombreAttribute`**()
  - **`getPoblacionAttribute`**()
  - **`getEmailAttribute`**()
  - **`getTelef2Attribute`**()
  - **`getTelef1Attribute`**()
  - **`getFolAttribute`**()
  - **`getFotoAttribute`**()
  - **`getDretsAttribute`**()
  - **`getExtraescolarsAttribute`**()
  - **`getDAAttribute`**()


### `app/Entities/AlumnoResultado.php`

#### `Intranet\Entities\AlumnoResultado`
- Metodes:
  - **`Alumno`**()

#### `Intranet\Entities\ModuloGrupo`
- Metodes:
  - **`ModuloGrupo`**()

#### `Intranet\Entities\getNombreAttribute`
- Metodes:
  - **`getNombreAttribute`**()
  - **`getidAlumnoOptions`**()
  - **`getValoracionAttribute`**()
  - **`getModuloAttribute`**()


### `app/Entities/AlumnoReunion.php`

#### `Intranet\Entities\AlumnoReunion`
- Metodes:
  - **`Alumno`**()

#### `Intranet\Entities\Reunion`
- Metodes:
  - **`Reunion`**()


### `app/Entities/Articulo.php`

#### `Intranet\Entities\Articulo`
- Metodes:
  - **`Lote`**()

    Lots on apareix l'article a través de la taula pivot `articulos_lote`.

#### `Intranet\Entities\getMiniaturaAttribute`
- Metodes:
  - **`getMiniaturaAttribute`**()
  - **`fillFile`**($file): ?string

    Guarda la imatge de l'article en `public/Articulos` i retorna la ruta relativa.
  - **`setDescripcionAttribute`**($value)


### `app/Entities/ArticuloLote.php`

#### `Intranet\Entities\ArticuloLote`
- Metodes:
  - **`Articulo`**()

#### `Intranet\Entities\Lote`
- Metodes:
  - **`Lote`**()

#### `Intranet\Entities\Materiales`
- Metodes:
  - **`Materiales`**()

#### `Intranet\Entities\getDescripcionAttribute`
- Metodes:
  - **`getDescripcionAttribute`**()


### `app/Entities/Asistencia.php`

#### `Intranet\Entities\Asistencia`
- Metodes:
  - **`Profesor`**()


### `app/Entities/BustiaVioleta.php`

#### `Intranet\Entities\BustiaVioleta`
- Metodes:
  - **`getAutorDisplayNameAttribute`**()
  - **`scopePendents`**($q)
  - **`scopeAmbCategoria`**($q, $c)
  - **`scopeDeTipus`**($q, string $tipus)


### `app/Entities/CalendariEscolar.php`

#### `Intranet\Entities\CalendariEscolar`
- Metodes:
  - **`esNoLectiu`**($date)
  - **`esFestiu`**($date)


### `app/Entities/Centro.php`

#### `Intranet\Entities\Centro`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Empresa`
- Metodes:
  - **`Empresa`**()

#### `Intranet\Entities\scopeEmpresa`
- Metodes:
  - **`scopeEmpresa`**($query, $empresa)
  - **`colaboraciones`**()

#### `Intranet\Entities\instructores`
- Metodes:
  - **`instructores`**()

#### `Intranet\Entities\getIdiomaOptions`
- Metodes:
  - **`getIdiomaOptions`**()


### `app/Entities/Ciclo.php`

#### `Intranet\Entities\Ciclo`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Grupos`
- Metodes:
  - **`Grupos`**()

#### `Intranet\Entities\Departament`
- Metodes:
  - **`Departament`**()

#### `Intranet\Entities\TutoresFct`
- Metodes:
  - **`TutoresFct`**()

#### `Intranet\Entities\Grupo`
- Metodes: cap

#### `Intranet\Entities\where`
- Metodes:
  - **`colaboraciones`**()

#### `Intranet\Entities\fcts`
- Metodes:
  - **`fcts`**()

#### `Intranet\Entities\Colaboracion`
- Metodes: cap

#### `Intranet\Entities\getTipoOptions`
- Metodes:
  - **`getTipoOptions`**()
  - **`getDepartamentoOptions`**()
  - **`getXtipoAttribute`**()
  - **`getCtipoAttribute`**()
  - **`getXdepartamentoAttribute`**()
  - **`getLiteralAttribute`**()
  - **`getCompleteDualAttribute`**()


### `app/Entities/Colaboracion.php`

#### `Intranet\Entities\Colaboracion`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Centro`
- Metodes:
  - **`Centro`**()

#### `Intranet\Entities\Ciclo`
- Metodes:
  - **`Ciclo`**()

#### `Intranet\Entities\fcts`
- Metodes:
  - **`fcts`**()

#### `Intranet\Entities\incidencias`
- Metodes:
  - **`incidencias`**()

#### `Intranet\Entities\Propietario`
- Metodes:
  - **`Propietario`**()

#### `Intranet\Entities\votes`
- Metodes:
  - **`votes`**()

#### `Intranet\Entities\scopeCiclo`
- Metodes:
  - **`scopeCiclo`**($query, $ciclo)
  - **`scopeEmpresa`**($query, $empresa)
  - **`scopeMiColaboracion`**($query, $empresa=null, $dni=null)

#### `Intranet\Entities\qTutor`
- Metodes:
  - **`getEmpresaAttribute`**()
  - **`getShortAttribute`**()
  - **`getXCicloAttribute`**()
  - **`getXEstadoAttribute`**()
  - **`getLocalidadAttribute`**()
  - **`getHorariAttribute`**()
  - **`getEstadoOptions`**()
  - **`getAnotacioAttribute`**()
  - **`getProfesorAttribute`**()
  - **`getUltimoAttribute`**()
  - **`getSituationAttribute`**()


### `app/Entities/Colaborador.php`

#### `Intranet\Entities\Colaborador`
- Metodes:
  - **`Fct`**()


### `app/Entities/Comision.php`

#### `Intranet\Entities\Comision`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Creador`
- Metodes:
  - **`Creador`**()
  - **`scopeActual`**($query)
  - **`scopeNext`**($query)
  - **`getDesdeAttribute`**($entrada)
  - **`getHastaAttribute`**($salida)
  - **`Profesor`**()

#### `Intranet\Entities\Fcts`
- Metodes:
  - **`Fcts`**()

#### `Intranet\Entities\withPivot`
- Metodes:
  - **`getMedioOptions`**()
  - **`getEstadoOptions`**()
  - **`getIdProfesorOptions`**()
  - **`scopeDia`**($query, $dia)
  - **`getnombreAttribute`**()
  - **`getsituacionAttribute`**()
  - **`getTotalAttribute`**()
  - **`getDescripcionAttribute`**()
  - **`getTipoVehiculoAttribute`**()
  - **`showConfirm`**()


### `app/Entities/Concerns/BatoiModels.php`

#### `Intranet\Entities\Concerns\BatoiModels`
Utilitats comunes de model per a formularis, validació i càrrega de fitxers.

- Metodes:
  - **`getDateFormat`**(): string

    /
  - **`getRules`**(): mixed

    /
  - **`isRequired`**($campo): bool

    /
  - **`setInputType`**($id, array $tipo)

    /
  - **`deleteInputType`**($id)

    /
  - **`addFillable`**($field, $first=false)

    /
  - **`setRule`**($id, $rule)

    /
  - **`getRule`**($id): mixed

    /
  - **`getInputType`**($campo): array

    /
  - **`getInputTypes`**(): array

    Retorna la definició completa de tipus d'input del model.
  - **`existsDatepicker`**(): bool

    /
  - **`isTypeDate`**($type): bool

    /
  - **`fillAll`**(Request $request): mixed

    Emplena i persisteix els camps `fillable` des d'un request.
  - **`fillField`**($key, $value): mixed

    Normalitza i transforma el valor d'un camp segons el seu tipus d'input.
  - **`fillFile`**($file): string|null

    Valida i guarda un fitxer annex retornant la ruta final.
  - **`getDirectory`**($clase): string

    Construeix el directori de destí del fitxer segons curs i classe.
  - **`getFileName`**($extension, $clase): string

    Construeix el nom final del fitxer pujat.
  - **`has`**($field): bool

    /
  - **`getLinkAttribute`**(): bool

    /
  - **`showConfirm`**(): array

    Retorna el model serialitzat per a pantalles de confirmació.


### `app/Entities/Cotxe.php`

#### `Intranet\Entities\Cotxe`
- Metodes:
  - **`professor`**(): BelongsTo

#### `Intranet\Entities\scopePlateHamming1`
- Metodes:
  - **`scopePlateHamming1`**($query, string $matricula)


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
  - **`Alumnos`**()

#### `Intranet\Entities\withPivot`
- Metodes:
  - **`Registrado`**()
  - **`getFechaInicioAttribute`**($entrada)
  - **`getFechaFinAttribute`**($salida)
  - **`getHorainiAttribute`**($salida)
  - **`getHorafinAttribute`**($salida)
  - **`getNAlumnosAttribute`**()
  - **`getEstadoAttribute`**()
  - **`scopeActivo`**($query)


### `app/Entities/Departamento.php`

#### `Intranet\Entities\Departamento`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\Modulo`
- Metodes:
  - **`Modulo`**()

#### `Intranet\Entities\Jefe`
- Metodes:
  - **`Jefe`**()

#### `Intranet\Entities\getLiteralAttribute`
- Metodes:
  - **`getLiteralAttribute`**()
  - **`getidProfesorOptions`**()


### `app/Entities/Documento.php`

#### `Intranet\Entities\Documento`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\getCreatedAtAttribute`
- Metodes:
  - **`getCreatedAtAttribute`**($entrada)
  - **`getGrupoOptions`**()
  - **`getTipoDocumentoOptions`**()
  - **`getExistAttribute`**()
  - **`getSituacionAttribute`**()
  - **`getLinkAttribute`**()
  - **`deleteDoc`**()

#### `Intranet\Entities\delete`
- Metodes: cap


### `app/Entities/Dual.php`

#### `Intranet\Entities\Dual`
Mantingut temporalment per compatibilitat amb fluxos antics.

- Metodes:
  - **`getIdAlumnoOptions`**()
  - **`getIdColaboracionOptions`**()

#### `Intranet\Entities\firstByTutor`
- Metodes: cap


### `app/Entities/Empresa.php`

#### `Intranet\Entities\Empresa`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\null`
- Metodes:
  - **`centros`**()

#### `Intranet\Entities\colaboraciones`
- Metodes:
  - **`colaboraciones`**()

#### `Intranet\Entities\Centro`
- Metodes: cap

#### `Intranet\Entities\scopeCiclo`
- Metodes:
  - **`scopeCiclo`**($query, $tutor)

#### `Intranet\Entities\firstByTutor`
- Metodes:
  - **`scopeMenor`**($query, $fecha = null)
  - **`getConveniNouAttribute`**()
  - **`getConveniRenovatAttribute`**()
  - **`getRenovatConveniAttribute`**()
  - **`getConveniCaducatAttribute`**()
  - **`getDataSignaturaAttribute`**($entrada)
  - **`getCiclesAttribute`**()
  - **`convenioFileMtime`**(): ?int


### `app/Entities/Espacio.php`

#### `Intranet\Entities\Espacio`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Departamento`
- Metodes:
  - **`Departamento`**()

#### `Intranet\Entities\GruposMati`
- Metodes:
  - **`GruposMati`**()

#### `Intranet\Entities\GruposVesprada`
- Metodes:
  - **`GruposVesprada`**()

#### `Intranet\Entities\getIdDepartamentoOptions`
- Metodes:
  - **`getIdDepartamentoOptions`**()
  - **`getGMatiOptions`**()

#### `Intranet\Entities\all`
- Metodes:
  - **`getGVespradaOptions`**()
  - **`getXDepartamentoAttribute`**()
  - **`Materiales`**()

#### `Intranet\Entities\where`
- Metodes: cap


### `app/Entities/Expediente.php`

#### `Intranet\Entities\Expediente`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\tipoExpediente`
- Metodes:
  - **`tipoExpediente`**()

#### `Intranet\Entities\getfechaAttribute`
- Metodes:
  - **`getfechaAttribute`**($entrada)
  - **`getfechasolucionAttribute`**($salida)
  - **`getfechatramiteAttribute`**($entrada)
  - **`getTipoOptions`**()
  - **`getIdModuloOptions`**()
  - **`getIdAlumnoOptions`**()

#### `Intranet\Entities\misGrupos`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\Acompanyant`
- Metodes:
  - **`Acompanyant`**()

#### `Intranet\Entities\Alumno`
- Metodes:
  - **`Alumno`**()

#### `Intranet\Entities\Modulo`
- Metodes:
  - **`Modulo`**()

#### `Intranet\Entities\getNomAlumAttribute`
- Metodes:
  - **`getNomAlumAttribute`**()
  - **`getNomProfeAttribute`**()
  - **`getSituacionAttribute`**()
  - **`getXtipoAttribute`**()
  - **`getXmoduloAttribute`**()
  - **`getShortAttribute`**()
  - **`getEsInformeAttribute`**()
  - **`getQuienAttribute`**()
  - **`scopeListos`**($query)
  - **`getAnnexoAttribute`**()


### `app/Entities/Falta.php`

#### `Intranet\Entities\Falta`
Model de faltes.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\profesor`
- Metodes:
  - **`profesor`**()

#### `Intranet\Entities\getDesdeAttribute`
- Metodes:
  - **`getDesdeAttribute`**($entrada)
  - **`getHastaAttribute`**($entrada)
  - **`getHorainiAttribute`**($salida)
  - **`getHorafinAttribute`**($salida)
  - **`getDesdeHoraAttribute`**()
  - **`getMotivosOptions`**()
  - **`getIdProfesorOptions`**()
  - **`scopeDia`**($query, $dia)
  - **`getNombreAttribute`**()
  - **`getSituacionAttribute`**()
  - **`getMotivoAttribute`**()
  - **`showConfirm`**()
  - **`getHoraIniOptions`**()
  - **`getHoraFinOptions`**()


### `app/Entities/Falta_itaca.php`

#### `Intranet\Entities\Falta_itaca`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\Hora`
- Metodes:
  - **`Hora`**()

#### `Intranet\Entities\Grupo`
- Metodes:
  - **`Grupo`**()

#### `Intranet\Entities\getNombreAttribute`
- Metodes:
  - **`getNombreAttribute`**()
  - **`getHorasAttribute`**()
  - **`getXGrupoAttribute`**()
  - **`getFichajeAttribute`**()
  - **`getXestadoAttribute`**()
  - **`getDiaAttribute`**($entrada)
  - **`putEstado`**($id, $estado, $mensaje = null)


### `app/Entities/Falta_profesor.php`

#### `Intranet\Entities\Falta_profesor`
Model de fitxatges de professorat.

- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\scopeHoy`
- Metodes:
  - **`scopeHoy`**($query, $profesor)
  - **`scopehaFichado`**($query, $dia, $profesor)
  - **`fichar`**($profesor = null)

    /

#### `Intranet\Entities\fitxar`
- Metodes:
  - **`fichaDia`**($profesor, $dia)

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
  - **`Comision`**()

#### `Intranet\Entities\withPivot`
- Metodes:
  - **`AlFct`**()

#### `Intranet\Entities\Instructor`
- Metodes:
  - **`Instructor`**()

#### `Intranet\Entities\Contactos`
- Metodes:
  - **`Contactos`**()

#### `Intranet\Entities\mail`
- Metodes:
  - **`Colaboradores`**()

#### `Intranet\Entities\Alumnos`
- Metodes:
  - **`Alumnos`**()

#### `Intranet\Entities\votes`
- Metodes:
  - **`votes`**()

#### `Intranet\Entities\cotutor`
- Metodes:
  - **`cotutor`**()

#### `Intranet\Entities\hasSignatures`
- Metodes:
  - **`hasSignatures`**()
  - **`tutor`**()

#### `Intranet\Entities\Colaboracion`
- Metodes: cap

#### `Intranet\Entities\scopeCentro`
- Metodes:
  - **`scopeCentro`**($query, $centro)
  - **`scopeEmpresa`**($query, $empresa)
  - **`scopeMisFcts`**($query, $profesor=null)
  - **`scopeWithCotutor`**($query, $cotutor=null)
  - **`getEncarregatAttribute`**()
  - **`scopeMisFctsColaboracion`**($query, $profesor = null, $cotutor = null)
  - **`scopeEsExempt`**($query)
  - **`scopeEsErasmus`**($query)
  - **`scopeEsFct`**($query)
  - **`scopeEsAval`**($query)
  - **`scopeEsDual`**($query)
  - **`scopeNoAval`**($query)
  - **`getIdColaboracionOptions`**()

#### `Intranet\Entities\firstByTutor`
- Metodes:
  - **`getIdAlumnoOptions`**()
  - **`getIdInstructorOptions`**()
  - **`getTipusAttribute`**()
  - **`getDesdeAttribute`**($entrada)
  - **`getDualAttribute`**()
  - **`getExentoAttribute`**()
  - **`getCentroAttribute`**()
  - **`getCicloAttribute`**()
  - **`getQuantsAttribute`**()
  - **`getNalumnesAttribute`**()
  - **`getLalumnesAttribute`**()
  - **`getEmailAttribute`**()
  - **`getContactoAttribute`**()
  - **`getXinstructorAttribute`**()
  - **`getSendCorreoAttribute`**()


### `app/Entities/FctColaborador.php`

#### `Intranet\Entities\FctColaborador`
- Metodes: cap


### `app/Entities/FctConvalidacion.php`

#### `Intranet\Entities\FctConvalidacion`
- Metodes: cap


### `app/Entities/FctDay.php`

#### `Intranet\Entities\FctDay`
- Metodes:
  - **`Colaboracion`**()

#### `Intranet\Entities\getHorariAttribute`
- Metodes:
  - **`getHorariAttribute`**()
  - **`setColaboracionIdAttribute`**($value): void

    Normalitza valors buits perquè la BBDD no reba '' en una FK integer nullable.


### `app/Entities/Grupo.php`

#### `Intranet\Entities\Grupo`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Alumnos`
- Metodes:
  - **`Alumnos`**()

#### `Intranet\Entities\Actividades`
- Metodes:
  - **`Actividades`**()

#### `Intranet\Entities\Tutor`
- Metodes:
  - **`Tutor`**()

#### `Intranet\Entities\Ciclo`
- Metodes:
  - **`Ciclo`**()

#### `Intranet\Entities\Horario`
- Metodes:
  - **`Horario`**()

#### `Intranet\Entities\Modulos`
- Metodes:
  - **`Modulos`**()

#### `Intranet\Entities\getTodosOptions`
- Metodes:
  - **`getTodosOptions`**()

#### `Intranet\Entities\all`
- Metodes:
  - **`getIdCicloOptions`**()
  - **`getTutorOptions`**()
  - **`scopeQTutor`**($query, $profesor = null)
  - **`scopeLargestByAlumnes`**($query)
  - **`scopeMisGrupos`**($query, $profesor = null)
  - **`scopeMiGrupoModulo`**($query, $dni, $modulo)
  - **`scopeMatriculado`**($query, $alumno)
  - **`scopeDepartamento`**($query, $dep)
  - **`scopeCurso`**($query, $curso)
  - **`getProyectoAttribute`**()
  - **`getXcicloAttribute`**()
  - **`getXtutorAttribute`**()
  - **`getActaAttribute`**()
  - **`getCalidadAttribute`**()
  - **`getMatriculadosAttribute`**()
  - **`getAvalFctAttribute`**()
  - **`getEnDualAttribute`**()
  - **`getAprobFctAttribute`**()
  - **`getAvalProAttribute`**()
  - **`getAprobProAttribute`**()
  - **`getColocadosAttribute`**()
  - **`getExentosAttribute`**()
  - **`getResfctAttribute`**()
  - **`getResempresaAttribute`**()
  - **`getResproAttribute`**()
  - **`getIsSemiAttribute`**()
  - **`getTornAttribute`**()


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
  - **`profesores`**()

#### `Intranet\Entities\Miembro`
- Metodes: cap

#### `Intranet\Entities\Creador`
- Metodes:
  - **`Creador`**()
  - **`scopeMisGruposTrabajo`**($query)


### `app/Entities/Guardia.php`

#### `Intranet\Entities\Guardia`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Profesor`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\scopeProfesor`
- Metodes:
  - **`scopeProfesor`**($query, $idProfesor)
  - **`scopeDiaHora`**($query, $dia, $hora)


### `app/Entities/Hora.php`

#### `Intranet\Entities\Hora`
- Metodes:
  - **`Horario`**()

#### `Intranet\Entities\horasAfectadas`
- Metodes:
  - **`horasAfectadas`**(string $horaIni, string $horaFin): Collection


### `app/Entities/Horario.php`

#### `Intranet\Entities\Horario`
- Metodes:
  - **`Modulo`**()

#### `Intranet\Entities\Ocupacion`
- Metodes:
  - **`Ocupacion`**()

#### `Intranet\Entities\Grupo`
- Metodes:
  - **`Grupo`**()

#### `Intranet\Entities\Hora`
- Metodes:
  - **`Hora`**()

#### `Intranet\Entities\Mestre`
- Metodes:
  - **`Mestre`**()

#### `Intranet\Entities\scopeProfesor`
- Metodes:
  - **`scopeProfesor`**($query, $profesor)
  - **`scopeGrup`**($query, $grupo)
  - **`scopeDia`**($query, $dia)
  - **`scopeOrden`**($query, $sesion)
  - **`scopeGuardia`**($query)
  - **`scopeGuardiaBiblio`**($query)
  - **`scopeGuardiaAll`**($query)
  - **`scopeLectivos`**($query)
  - **`scopePrimera`**($query, $profesor, $date = null)
  - **`HorarioSemanal`**($profesor)
  - **`HorarioGrupo`**($grupo)
  - **`getProfesorAttribute`**()
  - **`getXGrupoAttribute`**()
  - **`getXModuloAttribute`**()
  - **`getXOcupacionAttribute`**()
  - **`getDesdeAttribute`**()
  - **`getHastaAttribute`**()
  - **`getModuloOptions`**()
  - **`getIdGrupoOptions`**()

#### `Intranet\Entities\all`
- Metodes:
  - **`getOcupacionOptions`**()


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
  - **`Creador`**()

#### `Intranet\Entities\Responsables`
- Metodes:
  - **`Responsables`**()

#### `Intranet\Entities\Tipos`
- Metodes:
  - **`Tipos`**()

#### `Intranet\Entities\Materiales`
- Metodes:
  - **`Materiales`**()

#### `Intranet\Entities\Espacios`
- Metodes:
  - **`Espacios`**()

#### `Intranet\Entities\getEspacioOptions`
- Metodes:
  - **`getEspacioOptions`**()
  - **`getTipoOptions`**()
  - **`getEstadoOptions`**()
  - **`getPrioridadOptions`**()
  - **`getFechasolucionAttribute`**($salida)
  - **`getXestadoAttribute`**()
  - **`getXcreadorAttribute`**()
  - **`getXespacioAttribute`**()
  - **`getXresponsableAttribute`**()
  - **`getXtipoAttribute`**()
  - **`getDesCurtaAttribute`**()
  - **`putEstado`**($id, $estado)
  - **`getSubTipoAttribute`**()


### `app/Entities/Instructor.php`

#### `Intranet\Entities\Instructor`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Fcts`
- Metodes:
  - **`Fcts`**(): HasMany

#### `Intranet\Entities\Centros`
- Metodes:
  - **`Centros`**(): BelongsToMany

#### `Intranet\Entities\getXcentrosAttribute`
- Metodes:
  - **`getXcentrosAttribute`**()
  - **`getXNcentrosAttribute`**()
  - **`getNfctsAttribute`**()
  - **`getNombreAttribute`**()
  - **`getContactoAttribute`**()
  - **`getIdAttribute`**()


### `app/Entities/Inventario.php`

#### `Intranet\Entities\Inventario`
- Metodes:
  - **`getEspaiAttribute`**()
  - **`getDescripcioAttribute`**()
  - **`getEstatAttribute`**()
  - **`getOrigeAttribute`**()


### `app/Entities/IpGuardia.php`

#### `Intranet\Entities\IpGuardia`
- Metodes: cap


### `app/Entities/Lote.php`

#### `Intranet\Entities\Lote`
- Metodes:
  - **`ArticuloLote`**()

#### `Intranet\Entities\Departamento`
- Metodes:
  - **`Departamento`**()

#### `Intranet\Entities\getProcedenciaOptions`
- Metodes:
  - **`getProcedenciaOptions`**()
  - **`getDepartamentoIdOptions`**()
  - **`Materiales`**()

#### `Intranet\Entities\ArticuloLote`
- Metodes: cap

#### `Intranet\Entities\getOrigenAttribute`
- Metodes:
  - **`getOrigenAttribute`**()
  - **`getEstadoAttribute`**()
  - **`resolveArticuloLoteCount`**(): int
  - **`resolveMaterialesStats`**(): array

    /
  - **`getEstatAttribute`**()
  - **`getDepartamentAttribute`**()


### `app/Entities/Material.php`

#### `Intranet\Entities\Material`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Espacios`
- Metodes:
  - **`Espacios`**()

#### `Intranet\Entities\LoteArticulo`
- Metodes:
  - **`LoteArticulo`**()

#### `Intranet\Entities\getEstadoOptions`
- Metodes:
  - **`getEstadoOptions`**()
  - **`getStateAttribute`**()
  - **`getEspacioOptions`**()
  - **`getEspaiAttribute`**()
  - **`getProcedenciaOptions`**()


### `app/Entities/MaterialBaja.php`

#### `Intranet\Entities\MaterialBaja`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\Material`
- Metodes:
  - **`Material`**()

#### `Intranet\Entities\getDescripcionAttribute`
- Metodes:
  - **`getDescripcionAttribute`**()
  - **`getSolicitanteAttribute`**()
  - **`getEspacioAttribute`**()
  - **`getFechaBajaAttribute`**()
  - **`getStateAttribute`**()
  - **`getTipusAttribute`**()
  - **`getNuevoAttribute`**()


### `app/Entities/Menu.php`

#### `Intranet\Entities\Menu`
- Metodes:
  - **`getXrolAttribute`**()
  - **`getXactivoAttribute`**()
  - **`getCategoriaAttribute`**()
  - **`getDescripcionAttribute`**()
  - **`getXajudaAttribute`**()

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
  - **`Horario`**()

#### `Intranet\Entities\Grupos`
- Metodes:
  - **`Grupos`**()

#### `Intranet\Entities\scopeMisModulos`
- Metodes:
  - **`scopeMisModulos`**($query, $profesor = null)
  - **`scopeModulosGrupo`**($query, $grupo)
  - **`scopeLectivos`**($query)
  - **`getliteralAttribute`**()


### `app/Entities/Modulo_ciclo.php`

#### `Intranet\Entities\Modulo_ciclo`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Ciclo`
- Metodes:
  - **`Ciclo`**()

#### `Intranet\Entities\Modulo`
- Metodes:
  - **`Modulo`**()

#### `Intranet\Entities\Departamento`
- Metodes:
  - **`Departamento`**()

#### `Intranet\Entities\Programacion`
- Metodes: cap

#### `Intranet\Entities\Profesor`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\getXmoduloAttribute`
- Metodes:
  - **`getXmoduloAttribute`**()
  - **`getXdepartamentoAttribute`**()
  - **`getXcicloAttribute`**()
  - **`getAcicloAttribute`**()
  - **`getNombreAttribute`**()
  - **`getIdCicloOptions`**()
  - **`getIdModuloOptions`**()
  - **`getIdDepartamentoOptions`**()
  - **`getEstadoAttribute`**()
  - **`getSituacionAttribute`**()


### `app/Entities/Modulo_grupo.php`

#### `Intranet\Entities\Modulo_grupo`
- Metodes:
  - **`Grupo`**()

#### `Intranet\Entities\ModuloCiclo`
- Metodes:
  - **`ModuloCiclo`**()

#### `Intranet\Entities\resultados`
- Metodes:
  - **`resultados`**()

#### `Intranet\Entities\scopeCurso`
- Metodes:
  - **`scopeCurso`**($query, $curso)

#### `Intranet\Entities\byCurso`
- Metodes:
  - **`getXGrupoAttribute`**()
  - **`getXModuloAttribute`**()
  - **`getXcicloAttribute`**()
  - **`getXdepartamentoAttribute`**()
  - **`getXtornAttribute`**()
  - **`getliteralAttribute`**()
  - **`getseguimientoAttribute`**()

#### `Intranet\Entities\hasSeguimiento`
- Metodes:
  - **`getprofesorAttribute`**()

#### `Intranet\Entities\profesorNombres`
- Metodes:
  - **`getProgramacioLinkAttribute`**()

#### `Intranet\Entities\programacioLink`
- Metodes: cap


### `app/Entities/Municipio.php`

#### `Intranet\Entities\Municipio`
- Metodes:
  - **`Provincia`**()


### `app/Entities/Notification.php`

#### `Intranet\Entities\Notification`
- Metodes:
  - **`getMotivoAttribute`**()
  - **`getEmisorAttribute`**()
  - **`getFechaAttribute`**()
  - **`getLeidoAttribute`**()
  - **`decodedData`**(): array


### `app/Entities/Ocupacion.php`

#### `Intranet\Entities\Ocupacion`
- Metodes:
  - **`Ocupacion`**()
  - **`getliteralAttribute`**()


### `app/Entities/OrdenReunion.php`

#### `Intranet\Entities\OrdenReunion`
Model d'ordres de reunió.

- Metodes:
  - **`Reunion`**(): \Illuminate\Database\Eloquent\Relations\BelongsTo

    /

#### `Intranet\Entities\scopeForReunion`
- Metodes:
  - **`scopeForReunion`**($query, $idReunion): \Illuminate\Database\Eloquent\Builder

    /
  - **`scopeOrderNumber`**($query, int $orden): \Illuminate\Database\Eloquent\Builder

    /
  - **`firstByReunionAndOrder`**($idReunion, int $orden): ?self

    /
  - **`resumenByReunionAndOrder`**($idReunion, int $orden): string

    /


### `app/Entities/OrdenTrabajo.php`

#### `Intranet\Entities\OrdenTrabajo`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\Tipos`
- Metodes:
  - **`Tipos`**()

#### `Intranet\Entities\getTipoOptions`
- Metodes:
  - **`getTipoOptions`**()
  - **`getEstadoOptions`**()
  - **`getCreatedAtAttribute`**($entrada)
  - **`getXestadoAttribute`**()
  - **`getXtipoAttribute`**()


### `app/Entities/Poll/Actividad.php`

#### `Intranet\Entities\Poll\Actividad`
- Metodes:
  - **`loadPoll`**($allVotes)


### `app/Entities/Poll/AlumnoFct.php`

#### `Intranet\Entities\Poll\AlumnoFct`
- Metodes:
  - **`loadPoll`**($votes)
  - **`loadVotes`**($id)
  - **`aggregate`**(&$votes, $option1, $option2)
  - **`loadGroupVotes`**($id)
  - **`vista`**()
  - **`has`**()


### `app/Entities/Poll/Fct.php`

#### `Intranet\Entities\Poll\Fct`
- Metodes:
  - **`loadPoll`**($votes)
  - **`interviewed`**()
  - **`keyInterviewed`**()
  - **`loadVotes`**($id)
  - **`aggregate`**(&$votes, $option1, $option2)
  - **`loadGroupVotes`**($id)
  - **`has`**()


### `app/Entities/Poll/ModelPoll.php`

#### `Intranet\Entities\Poll\ModelPoll`
- Metodes:
  - **`loadPoll`**($votes)
  - **`loadVotes`**($id)
  - **`loadGroupVotes`**($id)
  - **`interviewed`**()
  - **`keyInterviewed`**()
  - **`vista`**()

#### `Intranet\Entities\Poll\aggregate`
- Metodes:
  - **`aggregate`**(&$votes, $option1, $option2)
  - **`has`**()


### `app/Entities/Poll/Option.php`

#### `Intranet\Entities\Poll\Option`
- Metodes:
  - **`poll`**(): \Illuminate\Database\Eloquent\Relations\BelongsTo

    An option belongs to one poll

#### `Intranet\Entities\Poll\isPollClosed`
- Metodes:
  - **`isPollClosed`**(): bool

    Check if the option is Closed


### `app/Entities/Poll/PPoll.php`

#### `Intranet\Entities\Poll\PPoll`
- Metodes:
  - **`polls`**()

#### `Intranet\Entities\Poll\options`
- Metodes:
  - **`options`**(): \Illuminate\Database\Eloquent\Relations\HasMany

    A poll has many options related to

#### `Intranet\Entities\Poll\getWhatOptions`
- Metodes:
  - **`getWhatOptions`**()


### `app/Entities/Poll/Poll.php`

#### `Intranet\Entities\Poll\Poll`
- Metodes:
  - **`Plantilla`**(): \Illuminate\Database\Eloquent\Relations\HasMany

    A poll has many options related to

#### `Intranet\Entities\Poll\getStateAttribute`
- Metodes:
  - **`getStateAttribute`**()
  - **`getKeyUserAttribute`**()
  - **`getAnonymousAttribute`**()
  - **`getQueAttribute`**()
  - **`getRemainsAttribute`**()
  - **`getModeloAttribute`**()
  - **`getVistaAttribute`**()
  - **`getIdPPollOptions`**()
  - **`getDesdeAttribute`**($entrada)
  - **`getHastaAttribute`**($entrada)


### `app/Entities/Poll/Profesor.php`

#### `Intranet\Entities\Poll\Profesor`
- Metodes:
  - **`loadPoll`**($votes)
  - **`loadVotes`**($id)

#### `Intranet\Entities\Poll\misModulos`
- Metodes:
  - **`loadGroupVotes`**($id)

#### `Intranet\Entities\Poll\misGrupos`
- Metodes:
  - **`aggregate`**(&$votes, $option1, $option2)
  - **`has`**()
  - **`aggregateGrupo`**($option1, &$votes): void

    /

#### `Intranet\Entities\Poll\all`
- Metodes:
  - **`aggregateDepartamento`**($option2, &$votes): void

    /


### `app/Entities/Poll/Vote.php`

#### `Intranet\Entities\Poll\Vote`
- Metodes:
  - **`Option`**()

#### `Intranet\Entities\Poll\ModuloGrupo`
- Metodes:
  - **`ModuloGrupo`**()

#### `Intranet\Entities\Poll\Actividad`
- Metodes:
  - **`Actividad`**()

#### `Intranet\Entities\Poll\Fct`
- Metodes:
  - **`Fct`**()

#### `Intranet\Entities\Poll\Profesor`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\Poll\Poll`
- Metodes:
  - **`Poll`**()

#### `Intranet\Entities\Poll\getIsValueAttribute`
- Metodes:
  - **`getIsValueAttribute`**()
  - **`optionsPoll`**($id)
  - **`optionsNumericPoll`**($id)
  - **`scopeMyVotes`**($query, $id, $modulo)
  - **`scopeGetVotes`**($query, $poll, $option1, $option2=null)
  - **`scopeMyGroupVotes`**($query, $id, $modulos)
  - **`scopeAllNumericVotes`**($query, $id)
  - **`getGrupoAttribute`**()
  - **`getDepartmentoAttribute`**()
  - **`getCicloAttribute`**()
  - **`getQuestionAttribute`**()
  - **`getAnswerAttribute`**()
  - **`getYearAttribute`**()
  - **`getInstructorAttribute`**()
  - **`scopeTipusEnquesta`**($query, $tipusEnquesta)


### `app/Entities/Poll/VoteAnt.php`

#### `Intranet\Entities\Poll\VoteAnt`
- Metodes:
  - **`Option`**()

#### `Intranet\Entities\Poll\Colaboracion`
- Metodes:
  - **`Colaboracion`**()

#### `Intranet\Entities\Poll\getIsValueAttribute`
- Metodes:
  - **`getIsValueAttribute`**()
  - **`getQuestionAttribute`**()
  - **`getAnswerAttribute`**()


### `app/Entities/Profesor.php`

#### `Intranet\Entities\Profesor`
Model de professor.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Notifiable`
- Metodes:
  - **`Comision`**()

#### `Intranet\Entities\Faltas`
- Metodes:
  - **`Faltas`**()

#### `Intranet\Entities\Actividad`
- Metodes:
  - **`Actividad`**()

#### `Intranet\Entities\Departamento`
- Metodes:
  - **`Departamento`**()

#### `Intranet\Entities\Sustituye`
- Metodes:
  - **`Sustituye`**()

#### `Intranet\Entities\Reserva`
- Metodes:
  - **`Reserva`**()
  - **`Horari`**()

#### `Intranet\Entities\Cotxes`
- Metodes:
  - **`Cotxes`**(): HasMany

#### `Intranet\Entities\grupos`
- Metodes:
  - **`grupos`**()
  - **`Activity`**()

#### `Intranet\Entities\scopeActivo`
- Metodes:
  - **`scopeActivo`**($query)
  - **`getRol`**($rol)
  - **`scopePlantilla`**($query)
  - **`scopeTutoresFCT`**($query)

#### `Intranet\Entities\byCurso`
- Metodes:
  - **`scopeGrupo`**($query, $grupo)
  - **`scopeGrupoT`**($query, $grupoT)
  - **`scopeApiToken`**($query, $api)
  - **`getfechaIngresoAttribute`**($fecha)
  - **`getFechaNacAttribute`**($fecha)
  - **`getFechaBajaAttribute`**($fecha)
  - **`getIdiomaOptions`**()
  - **`getIdAttribute`**()
  - **`getDepartamentoOptions`**()
  - **`sendPasswordResetNotification`**($token)
  - **`getXrolAttribute`**()
  - **`getXdepartamentoAttribute`**()
  - **`getLdepartamentoAttribute`**()
  - **`getEntradaAttribute`**()
  - **`getSalidaAttribute`**()
  - **`getHorarioAttribute`**()
  - **`getFullNameAttribute`**()
  - **`getNameFullAttribute`**()
  - **`getSurNamesAttribute`**()
  - **`getShortNameAttribute`**()
  - **`getAhoraAttribute`**()
  - **`getMiJefeAttribute`**()
  - **`getQualitatFile`**()
  - **`getGrupoTutoriaAttribute`**()

#### `Intranet\Entities\byTutorOrSubstitute`
- Metodes: cap

#### `Intranet\Entities\firstByTutorDual`
- Metodes:
  - **`getFileNameAttribute`**()
  - **`getSubstitutAttribute`**()
  - **`getSustituidosAttribute`**()
  - **`getSubstituts`**($dni)
  - **`getHasCertificateAttribute`**()
  - **`getPathCertificateAttribute`**()


### `app/Entities/Programacion.php`

#### `Intranet\Entities\Programacion`
Model de programacio.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\ModuloCiclo`
- Metodes:
  - **`ModuloCiclo`**()

#### `Intranet\Entities\Departament`
- Metodes:
  - **`Departament`**()

#### `Intranet\Entities\Modulo_ciclo`
- Metodes: cap

#### `Intranet\Entities\Ciclo`
- Metodes:
  - **`Ciclo`**()

#### `Intranet\Entities\Modulo`
- Metodes:
  - **`Modulo`**()

#### `Intranet\Entities\Profesor`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\Grupo`
- Metodes:
  - **`Grupo`**()

#### `Intranet\Entities\Modulo_grupo`
- Metodes: cap

#### `Intranet\Entities\getidModuloCicloOptions`
- Metodes:
  - **`getidModuloCicloOptions`**()
  - **`scopeMisProgramaciones`**($query, $dni = null)
  - **`scopeDepartamento`**($query, $departamento = null)
  - **`nomFichero`**()
  - **`getXdepartamentoAttribute`**()
  - **`getXModuloAttribute`**()
  - **`getXCicloAttribute`**()
  - **`getDescripcionAttribute`**()
  - **`getXnombreAttribute`**()
  - **`getSituacionAttribute`**()
  - **`resolve`**($id, $mensaje = null)


### `app/Entities/Projecte.php`

#### `Intranet\Entities\Projecte`
- Metodes:
  - **`Alumno`**()

#### `Intranet\Entities\Grupo`
- Metodes:
  - **`Grupo`**()

#### `Intranet\Entities\getStatusAttribute`
- Metodes:
  - **`getStatusAttribute`**()
  - **`getAlumneAttribute`**()
  - **`getGrupOptions`**()
  - **`getIdAlumneOptions`**()

#### `Intranet\Entities\byTutorOrSubstitute`
- Metodes:
  - **`getDefensaAttribute`**($entrada)


### `app/Entities/Provincia.php`

#### `Intranet\Entities\Provincia`
- Metodes:
  - **`Municipio`**()


### `app/Entities/Recurso.php`

#### `Intranet\Entities\Recurso`
- Metodes:
  - **`Reserva`**()


### `app/Entities/Reserva.php`

#### `Intranet\Entities\Reserva`
- Metodes:
  - **`Profesor`**()


### `app/Entities/Resultado.php`

#### `Intranet\Entities\Resultado`
Model de resultats.

- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\getEvaluacionOptions`
- Metodes:
  - **`getEvaluacionOptions`**()
  - **`getIdModuloGrupoOptions`**()

#### `Intranet\Entities\misModulos`
- Metodes:
  - **`scopeQGrupo`**($query, $grupo)
  - **`scopeDepartamento`**($query, $dep)
  - **`scopeTrimestreCurso`**($query, $trimestre, $ciclo, $curso)
  - **`Grupo`**()

#### `Intranet\Entities\Profesor`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\ModuloGrupo`
- Metodes:
  - **`ModuloGrupo`**()

#### `Intranet\Entities\getModuloAttribute`
- Metodes:
  - **`getModuloAttribute`**()
  - **`getXEvaluacionAttribute`**()
  - **`getXProfesorAttribute`**()


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
  - **`Creador`**()

#### `Intranet\Entities\profesores`
- Metodes:
  - **`profesores`**()

#### `Intranet\Entities\withPivot`
- Metodes:
  - **`Departament`**()

#### `Intranet\Entities\alumnos`
- Metodes:
  - **`alumnos`**()

#### `Intranet\Entities\Profesor`
- Metodes: cap

#### `Intranet\Entities\scopeMisReuniones`
- Metodes:
  - **`scopeMisReuniones`**($query)
  - **`scopeConvocante`**($query, $dni=null)
  - **`scopeTipo`**($query, $tipo)
  - **`scopeNumero`**($query, $numero)
  - **`scopeArchivada`**($query)
  - **`scopeActaFinal`**($query, $tutor)
  - **`getTipoOptions`**()
  - **`getIdEspacioOptions`**()
  - **`getNumeroOptions`**()
  - **`getGrupoOptions`**()
  - **`getDepartamentoAttribute`**()
  - **`getAvaluacioAttribute`**()
  - **`getModificableAttribute`**()
  - **`getFechaAttribute`**($entrada)
  - **`getUpdatedAtAttribute`**($entrada)
  - **`Tipos`**()
  - **`Grupos`**()

#### `Intranet\Entities\Espacio`
- Metodes:
  - **`Espacio`**()

#### `Intranet\Entities\Responsable`
- Metodes:
  - **`Responsable`**()

#### `Intranet\Entities\getXgrupoAttribute`
- Metodes:
  - **`getXgrupoAttribute`**()

#### `Intranet\Entities\largestByTutor`
- Metodes:
  - **`getInformeAttribute`**()
  - **`getIsSemiAttribute`**()
  - **`scopeNext`**($query)

#### `Intranet\Entities\firstByTutor`
- Metodes:
  - **`getXtipoAttribute`**()
  - **`getXnumeroAttribute`**()
  - **`getAvaluacioFinalAttribute`**()
  - **`getExtraOrdinariaAttribute`**()
  - **`getGrupoClaseAttribute`**()


### `app/Entities/Setting.php`

#### `Intranet\Entities\Setting`
- Metodes: cap


### `app/Entities/Signatura.php`

#### `Intranet\Entities\Signatura`
- Metodes:
  - **`Fct`**()

#### `Intranet\Entities\Teacher`
- Metodes:
  - **`Teacher`**()

#### `Intranet\Entities\Alumno`
- Metodes:
  - **`Alumno`**()

#### `Intranet\Entities\AlumnoFct`
- Metodes: cap

#### `Intranet\Entities\deleteFile`
- Metodes:
  - **`deleteFile`**()
  - **`saveIfNotExists`**($anexe, $idSao, $signat = 0)
  - **`getProfesorAttribute`**()
  - **`getAlumneAttribute`**()
  - **`getCentreAttribute`**()
  - **`getPathAttribute`**()
  - **`getFileNameAttribute`**()
  - **`getRouteFileAttribute`**()
  - **`getSimpleRouteFileAttribute`**()
  - **`getEmailAttribute`**()
  - **`getContactoAttribute`**()
  - **`getSignAttribute`**()
  - **`getSendAttribute`**()
  - **`getEstatAttribute`**()
  - **`getClassAttribute`**()
  - **`getFctOptions`**()
  - **`getTipusOptions`**()
  - **`statusService`**(): SignaturaStatusService


### `app/Entities/Solicitud.php`

#### `Intranet\Entities\Solicitud`
- Metodes:
  - **`getfechaAttribute`**($entrada)
  - **`getfechasolucionAttribute`**($salida)
  - **`getIdOrientadorOptions`**()
  - **`getIdAlumnoOptions`**()

#### `Intranet\Entities\misGrupos`
- Metodes:
  - **`Profesor`**()

#### `Intranet\Entities\Orientador`
- Metodes:
  - **`Orientador`**()

#### `Intranet\Entities\Alumno`
- Metodes:
  - **`Alumno`**()

#### `Intranet\Entities\getNomAlumAttribute`
- Metodes:
  - **`getNomAlumAttribute`**()
  - **`getSituacionAttribute`**()
  - **`getMotiuAttribute`**()
  - **`getQuienAttribute`**()
  - **`scopeListos`**($query)


### `app/Entities/Task.php`

#### `Intranet\Entities\Task`
- Metodes:
  - **`Profesores`**()

#### `Intranet\Entities\withPivot`
- Metodes:
  - **`scopeMisTareas`**($query, $profesor=null)
  - **`getmyDetailsAttribute`**()
  - **`getValidAttribute`**()
  - **`getLinkAttribute`**()
  - **`getVencimientoAttribute`**($entrada)
  - **`getImageAttribute`**()
  - **`getDestinoAttribute`**()
  - **`getDestinatarioOptions`**()
  - **`getActionOptions`**()
  - **`getAccioAttribute`**()
  - **`fillFile`**($file)

#### `Intranet\Entities\store`
- Metodes: cap


### `app/Entities/TipoActividad.php`

#### `Intranet\Entities\TipoActividad`
- Metodes:
  - **`actividades`**()

#### `Intranet\Entities\departament`
- Metodes:
  - **`departament`**()

#### `Intranet\Entities\getDepartamentoAttribute`
- Metodes:
  - **`getDepartamentoAttribute`**()


### `app/Entities/TipoExpediente.php`

#### `Intranet\Entities\TipoExpediente`
- Metodes:
  - **`expedientes`**()


### `app/Entities/TipoIncidencia.php`

#### `Intranet\Entities\TipoIncidencia`
- Metodes:
  - **`getLiteralAttribute`**()
  - **`getTipoAttribute`**()
  - **`Responsable`**()

#### `Intranet\Entities\getIdProfesorOptions`
- Metodes:
  - **`getIdProfesorOptions`**()
  - **`getTipusOptions`**()
  - **`Rol`**($rol)
  - **`getProfesorAttribute`**()


### `app/Entities/Tutoria.php`

#### `Intranet\Entities\Tutoria`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\Grupos`
- Metodes:
  - **`Grupos`**()

#### `Intranet\Entities\TutoriaGrupo`
- Metodes: cap

#### `Intranet\Entities\getDesdeAttribute`
- Metodes:
  - **`getDesdeAttribute`**($entrada)
  - **`getHastaAttribute`**($entrada)
  - **`getGruposOptions`**()
  - **`getTipoOptions`**()
  - **`getXobligatoriaAttribute`**()
  - **`getGrupoAttribute`**()
  - **`getTiposAttribute`**()
  - **`getEstatAttribute`**()
  - **`getFeedBackAttribute`**()


### `app/Entities/TutoriaGrupo.php`

#### `Intranet\Entities\TutoriaGrupo`
- Metodes: cap

#### `Intranet\Entities\ActivityReport`
- Metodes: cap

#### `Intranet\Entities\getFechaAttribute`
- Metodes:
  - **`getFechaAttribute`**($entrada)
  - **`getNombreAttribute`**()
  - **`Grupo`**()



## Serveis

### `app/Application/Activity/ActivityService.php`

#### `Intranet\Application\Activity\ActivityService`
Servei d'aplicació per al registre d'activitat d'usuari.

- Metodes:
  - **`record`**(string $action, ?Model $model = null, ?string $comentari = null, ?string $fecha = null, ?string $document = null): Activity

    Crea un registre d'activitat i, si hi ha usuari autenticat, el persistix associat a l'autor.
  - **`notifyUser`**(Activity $activity): void

    Mostra una alerta de confirmació quan el registre està vinculat a un model.


### `app/Application/AlumnoFct/AlumnoFctAvalService.php`

#### `Intranet\Application\AlumnoFct\AlumnoFctAvalService`
- Metodes:
  - **`__construct`**(private readonly AlumnoFctService $alumnoFctService)
  - **`latestByProfesor`**(string $dni): Collection
  - **`apte`**(int|string $id): void
  - **`noApte`**(int|string $id, bool $projectRequired): void
  - **`noAval`**(int|string $id): void
  - **`noProyecto`**(int|string $id): void
  - **`nullProyecto`**(int|string $id): void
  - **`nuevoProyecto`**(int|string $id): void
  - **`toggleInsercion`**(int|string $id): void
  - **`requestActaForTutor`**(string $dni, Collection $grupos): array
  - **`estadistiques`**(Collection $grupos): array
  - **`markStudentsAsActaPending`**(string $dni, bool $projectNeeded, mixed $grupo = null): bool


### `app/Application/AlumnoFct/AlumnoFctService.php`

#### `Intranet\Application\AlumnoFct\AlumnoFctService`
Servei d'aplicació per a casos d'ús d'AlumnoFct.

- Metodes:
  - **`__construct`**(private readonly AlumnoFctRepositoryInterface $alumnoFctRepository)
  - **`all`**(): EloquentCollection

    Recupera tots els registres d'alumnat en FCT.
  - **`totesFcts`**(?string $profesor = null): EloquentCollection

    Recupera les FCT visibles per al tutor indicat.
  - **`find`**(int|string $id): ?AlumnoFct

    Cerca un registre per identificador.
  - **`findOrFail`**(int|string $id): AlumnoFct

    Cerca un registre per identificador o llança excepció.
  - **`firstByIdSao`**(int|string $idSao): ?AlumnoFct

    Recupera el primer registre associat a un id SAO.
  - **`byAlumno`**(string $nia): EloquentCollection

    Llista tots els registres d'un alumne.
  - **`byAlumnoWithA56`**(string $nia): EloquentCollection

    Llista registres d'un alumne amb annex A56 en curs.
  - **`byGrupoEsFct`**(string $grupo): EloquentCollection

    Llista registres d'un grup que són FCT.
  - **`byGrupoEsDual`**(string $grupo): EloquentCollection

    Llista registres d'un grup que són dual.
  - **`reassignProfesor`**(string $fromDni, string $toDni): int

    Reassigna en bloc el tutor responsable.
  - **`avalDistinctAlumnoIdsByProfesor`**(?string $profesor = null): array

    Recupera identificadors d'alumnes amb FCT avaluable del tutor.
  - **`latestAvalByAlumnoAndProfesor`**(string $idAlumno, ?string $profesor = null): ?AlumnoFct

    Recupera l'últim registre avaluable d'un alumne per tutor.
  - **`avaluablesNoAval`**(?string $profesor = null, mixed $grupo = null): EloquentCollection

    Recupera registres avaluables no tancats en acta.


### `app/Application/AlumnoFct/AlumnoFctSignatureService.php`

#### `Intranet\Application\AlumnoFct\AlumnoFctSignatureService`
Casos d'ús de signatures vinculades a AlumnoFct.

- Metodes:
  - **`hasAnySignature`**(AlumnoFct $alumnoFct): bool

    Determina si el registre té alguna signatura associada.
  - **`findByType`**(AlumnoFct $alumnoFct, string $tipus, ?bool $signed = null): ?Signatura

    Cerca la signatura per tipus i estat de signatura.
  - **`routeFile`**(AlumnoFct $alumnoFct, string $annexCode): string

    Construeix la ruta física de l'annex per al registre.


### `app/Application/Colaboracion/ColaboracionQueryService.php`

#### `Intranet\Application\Colaboracion\ColaboracionQueryService`
Consultes de lectura per al domini de col·laboracions.

- Metodes:
  - **`myColaboraciones`**(string $dni): Collection

    /
  - **`relatedByCenterDepartment`**(Collection $meves): Collection

    /
  - **`groupedActivitiesByColaboracion`**(Collection $colaboraciones): Collection

    /
  - **`attachRelatedAndContacts`**(Collection $meves, Collection $relacionades, Collection $activitiesByColab): Collection

    /


### `app/Application/Colaboracion/ColaboracionService.php`

#### `Intranet\Application\Colaboracion\ColaboracionService`
Casos d'ús d'aplicació per al panell de col·laboracions.

- Metodes:
  - **`__construct`**(private readonly ColaboracionQueryService $queryService)
  - **`panelListingByTutor`**(string $dni): Collection

    /
  - **`resolvePanelTitle`**(Collection $colaboraciones): ?string

    /


### `app/Application/Comision/ComisionService.php`

#### `Intranet\Application\Comision\ComisionService`
Casos d'ús d'aplicació per al domini de comissions.

- Metodes:
  - **`__construct`**(private readonly ComisionRepositoryInterface $comisionRepository)
  - **`pendingAuthorization`**(): EloquentCollection

    /
  - **`find`**(int $id): ?Comision
  - **`findOrFail`**(int $id): Comision
  - **`byDay`**(string $dia): EloquentCollection

    /
  - **`withProfesorByDay`**(string $dia): EloquentCollection

    /
  - **`authorizationApiList`**(): EloquentCollection

    /
  - **`prePayByProfesor`**(string $dni): EloquentCollection

    /
  - **`hasPendingUnpaidByProfesor`**(string $dni): bool
  - **`setEstado`**(int $id, int $estado): Comision
  - **`attachFct`**(int $comisionId, int $fctId, string $horaIni, bool $aviso): void
  - **`detachFct`**(int $comisionId, int $fctId): void


### `app/Application/Documento/DocumentoFormService.php`

#### `Intranet\Application\Documento\DocumentoFormService`
- Metodes:
  - **`updateNota`**(AlumnoFctService $alumnoFctService, int|string $fctId, mixed $nota): void
  - **`projectDefaults`**(AlumnoFct $fct, string $ciclo, string $supervisor): array
  - **`qualitatDefaults`**(mixed $grupo, string $fullName): array


### `app/Application/Documento/DocumentoLifecycleService.php`

#### `Intranet\Application\Documento\DocumentoLifecycleService`
Servei de cicle de vida per a Documento.

- Metodes:
  - **`delete`**(Documento $documento): bool

    Esborra un document i, si aplica, també el fitxer físic associat.
  - **`mustDeleteFile`**(Documento $documento): bool


### `app/Application/Empresa/EmpresaService.php`

#### `Intranet\Application\Empresa\EmpresaService`
Casos d'ús d'aplicació per al domini d'empreses.

- Metodes:
  - **`__construct`**(private readonly EmpresaRepositoryInterface $empresaRepository)
  - **`listForGrid`**(): EloquentCollection

    /
  - **`findForShow`**(int $empresaId): Empresa
  - **`colaboracionIdsForTutorCycle`**(?int $tutorCycleId, Empresa $empresa): Collection

    /
  - **`departmentCycles`**(?string $department): EloquentCollection

    /
  - **`convenioList`**(): EloquentCollection

    /
  - **`socialConcertList`**(): EloquentCollection

    /
  - **`erasmusList`**(): EloquentCollection

    /
  - **`saveFromRequest`**(Request $request, $id = null): mixed

    Persisteix una empresa (alta o edició) amb validació i normalització.
  - **`createCenter`**(int|string $empresaId, Request $request): int

    Crea el centre inicial d'una empresa.
  - **`createColaboration`**(int|string $centroId, Request $request, int|string $cicloId, string $tutorName): int

    Crea col·laboració inicial associada al cicle del tutor.
  - **`fillMissingCenterData`**(Empresa $empresa): void

    Propaga camps bàsics d'empresa a centres incomplets.
  - **`normalizeRequest`**(Request $request): Request

    Normalitza checkbox i CIF en l'entrada del formulari d'empresa.


### `app/Application/Expediente/ExpedienteService.php`

#### `Intranet\Application\Expediente\ExpedienteService`
Casos d'ús d'aplicació per al domini d'expedients.

- Metodes:
  - **`__construct`**(private readonly ExpedienteRepositoryInterface $expedienteRepository)
  - **`find`**(int|string $id): ?Expediente
  - **`findOrFail`**(int|string $id): Expediente
  - **`createFromRequest`**(Request $request): Expediente
  - **`updateFromRequest`**(int|string $id, Request $request): Expediente
  - **`pendingAuthorization`**(): EloquentCollection

    /
  - **`readyToPrint`**(): EloquentCollection

    /
  - **`allTypes`**(): EloquentCollection

    /


### `app/Application/Falta/FaltaService.php`

#### `Intranet\Application\Falta\FaltaService`
Casos d'ús d'aplicació per al domini de faltes de professorat.

- Metodes:
  - **`create`**(Request $request): int

#### `Intranet\Application\Falta\markLeave`
- Metodes:
  - **`update`**(int|string $id, Request $request): Falta
  - **`init`**(int|string $id): Falta

#### `Intranet\Application\Falta\sendTutorEmail`
- Metodes:
  - **`alta`**(int|string $id): Falta

#### `Intranet\Application\Falta\reactivate`
- Metodes: cap


### `app/Application/FaltaItaca/FaltaItacaWorkflowService.php`

#### `Intranet\Application\FaltaItaca\FaltaItacaWorkflowService`
- Metodes:
  - **`findElements`**(string $desde, string $hasta)
  - **`monthlyReportFileName`**(string $desde): string
  - **`deletePreviousMonthlyReport`**(string $path): void
  - **`resolveByAbsenceId`**(int|string $id): bool
  - **`refuseByAbsenceId`**(int|string $id, ?string $explicacion = null): bool


### `app/Application/Fct/FctCertificateService.php`

#### `Intranet\Application\Fct\FctCertificateService`
- Metodes:
  - **`colaboradorCertificateData`**(): array
  - **`streamColaboradorCertificate`**(mixed $fct)

#### `Intranet\Application\Fct\hazPdf`
- Metodes: cap


### `app/Application/Fct/FctService.php`

#### `Intranet\Application\Fct\FctService`
Casos d'ús d'aplicació per al domini FCT.

- Metodes:
  - **`__construct`**(private readonly FctRepositoryInterface $fctRepository)
  - **`find`**(int|string $id): ?Fct
  - **`findOrFail`**(int|string $id): Fct
  - **`panelListingByProfesor`**(string $dni): EloquentCollection

    /
  - **`setInstructor`**(int|string $idFct, string $idInstructor): Fct
  - **`findBySignature`**(int|string $idColaboracion, int|string $asociacion, int|string $idInstructor): ?Fct
  - **`createFromRequest`**(Request $request): Fct
  - **`attachAlumnoFromStoreRequest`**(Fct $fct, Request $request): void
  - **`attachAlumnoSimple`**(int|string $idFct, Request $request): void
  - **`detachAlumno`**(int|string $idFct, string $idAlumno): void
  - **`addColaborador`**(int|string $idFct, Colaborador $colaborador): void
  - **`deleteColaborador`**(int|string $idFct, string $idInstructor): int
  - **`updateColaboradorHoras`**(int|string $idFct, array $horasByInstructor): void
  - **`setCotutor`**(int|string $idFct, ?string $cotutor): void
  - **`empresaIdByFct`**(int|string $idFct): ?int
  - **`deleteFct`**(int|string $idFct): void


### `app/Application/Grupo/GrupoService.php`

#### `Intranet\Application\Grupo\GrupoService`
Servei d'aplicació per a casos d'ús relacionats amb grups.

- Metodes:
  - **`__construct`**(private readonly GrupoRepositoryInterface $grupoRepository)
  - **`create`**(array $attributes): Grupo

    /
  - **`find`**(string $codigo): ?Grupo
  - **`all`**(): EloquentCollection

    /
  - **`qTutor`**(string $dni): EloquentCollection

    /
  - **`firstByTutor`**(string $dni): ?Grupo
  - **`largestByTutor`**(string $dni): ?Grupo
  - **`byCurso`**(int $curso): EloquentCollection

    /
  - **`byDepartamento`**(int $departamento): EloquentCollection

    /
  - **`tutoresDniList`**(): array

    /
  - **`reassignTutor`**(string $fromDni, string $toDni): int
  - **`misGrupos`**(): EloquentCollection

    /
  - **`misGruposByProfesor`**(string $dni): EloquentCollection

    /
  - **`withActaPendiente`**(): EloquentCollection

    /
  - **`byTutorOrSubstitute`**(string $dni, ?string $sustituyeA): ?Grupo

    Retorna el primer grup on el professor és tutor o substitueix al tutor.
  - **`withStudents`**(): EloquentCollection

    /
  - **`firstByTutorDual`**(string $dni): ?Grupo
  - **`byCodes`**(array $codigos): EloquentCollection

    /
  - **`allWithTutorAndCiclo`**(): EloquentCollection

    /
  - **`misGruposWithCiclo`**(): EloquentCollection

    Retorna els grups del professor amb la relació de cicle carregada.


### `app/Application/Grupo/GrupoWorkflowService.php`

#### `Intranet\Application\Grupo\GrupoWorkflowService`
- Metodes:
  - **`assignMissingCiclo`**(): int
  - **`selectedStudentsPlainText`**(array $payload): string
  - **`sendFolCertificates`**(Grupo $grupo, callable $pdfSaver): array

    /


### `app/Application/Horario/HorarioService.php`

#### `Intranet\Application\Horario\HorarioService`
Casos d'ús d'aplicació per al domini d'horaris.

- Metodes:
  - **`__construct`**(private readonly HorarioRepositoryInterface $horarioRepository)
  - **`semanalByProfesor`**(string $dni): array

    /
  - **`semanalByGrupo`**(string $grupo): array

    /
  - **`lectivosByDayAndSesion`**(string $dia, int $sesion): EloquentCollection

    /
  - **`countByProfesorAndDay`**(string $dni, string $dia): int
  - **`guardiaAllByDia`**(string $dia): EloquentCollection

    /
  - **`guardiaAllByProfesorAndDiaAndSesiones`**(string $dni, string $dia, array $sesiones): EloquentCollection

    /
  - **`guardiaAllByProfesorAndDia`**(string $dni, string $dia): EloquentCollection

    /
  - **`guardiaAllByProfesor`**(string $dni): EloquentCollection

    /
  - **`firstByProfesorDiaSesion`**(string $dni, string $dia, int|string $sesion): ?Horario
  - **`byProfesor`**(string $dni): EloquentCollection

    /
  - **`byProfesorWithRelations`**(string $dni, array $relations): EloquentCollection

    /
  - **`lectivasByProfesorAndDayOrdered`**(string $dni, string $dia): EloquentCollection

    /
  - **`reassignProfesor`**(string $fromDni, string $toDni): int
  - **`deleteByProfesor`**(string $dni): int
  - **`gruposByProfesor`**(string $dni): Collection

    /
  - **`gruposByProfesorDiaAndSesiones`**(string $dni, string $dia, array $sesiones): Collection

    /
  - **`profesoresByGruposExcept`**(array $grupos, string $emisorDni): Collection

    /
  - **`primeraByProfesorAndDateOrdered`**(string $dni, string $date): EloquentCollection

    /
  - **`firstByModulo`**(string $modulo): ?Horario
  - **`byProfesorDiaOrdered`**(string $dni, string $dia): EloquentCollection

    /
  - **`distinctModulos`**(): Collection

    /
  - **`create`**(array $data): Horario

    /
  - **`forProgramacionImport`**(): EloquentCollection

    /
  - **`firstForDepartamentoAsignacion`**(string $dni): ?Horario
  - **`situacionAhora`**(string $dni): ?array

    Retorna la situació actual del professor segons el seu horari.


### `app/Application/Import/Concerns/SharedImportFieldTransformers.php`

#### `Intranet\Application\Import\Concerns\SharedImportFieldTransformers`
- Metodes:
  - **`emailConselleriaImport`**($nombre, $apellido1, $apellido2)
  - **`emailProfesorImport`**($nombre, $apellido)
  - **`aleatorio`**($length = 60): string
  - **`hazDNI`**(string $dni, int $nia)
  - **`getFechaFormatoIngles`**($fecha): string|null

    /
  - **`cifrar`**($cadena)
  - **`digitos`**($telefono)
  - **`hazDomicilio`**($tipo_via, $domicilio, $numero, $puerta, $escalera, $letra, $piso)
  - **`creaCodigoProfesor`**($unused = null): int

#### `Intranet\Application\Import\Concerns\usedCodigosBetween`
- Metodes:
  - **`crea_codigo_profesor`**($unused = null): int


### `app/Application/Import/GeneralImportExecutionService.php`

#### `Intranet\Application\Import\GeneralImportExecutionService`
- Metodes:
  - **`__construct`**(private readonly HorarioService $horarioService, private readonly ProfesorService $profesorService, private readonly GrupoService $grupoService, )
  - **`handlePreImport`**(string $className, string $xmlName): void
  - **`handlePostImport`**(string $className, string $xmlName, mixed $firstImport): void
  - **`importTable`**(mixed $xmltable, array $tabla, callable $extractField, callable $passesFilter, callable $requiredCheck, string $mode = 'full'): void

    /
  - **`createRecordByClass`**(string $className, array $arrayDatos): mixed

    /
  - **`createHorario`**(array $arrayDatos): void

    /
  - **`createModuloCicloAndGrupoFromHorarios`**(): void
  - **`createModuloCiclo`**(mixed $horario, mixed $departamentoProfesor = null): Modulo_ciclo
  - **`markAllAlumnosAsBaja`**(): void
  - **`cleanupSustituciones`**(): void
  - **`assignDepartamentoByHorario`**(): void
  - **`disableProfesores`**(): void
  - **`removeBajaAlumnosFromGroups`**(): void
  - **`markAllGruposWithoutTutor`**(): void
  - **`deleteBajaGrupos`**(): void
  - **`normalizeEmptyTutor`**(): void
  - **`truncateTables`**(array|string $tables): void

    /
  - **`cloneTable`**(string $table): void
  - **`deleteBlankRecords`**(string $table, string $column): void
  - **`restoreAlumnosGrupoCopy`**(): void
  - **`keepLatestHorarioPlantilla`**(): void
  - **`setForeignKeys`**(bool $enabled): void
  - **`preloadExistingRecords`**(mixed $xmltable, array $tabla, callable $extractField, string $class): array

    /
  - **`normalizeCacheKey`**(mixed $key): ?string
  - **`loadEstadoFromHorarioJson`**(): void


### `app/Application/Import/ImportSchemaProvider.php`

#### `Intranet\Application\Import\ImportSchemaProvider`
Proveeix l'esquema de mapatge XML -> camps de BD per als imports.

- Metodes:
  - **`forGeneralImport`**(): array

    /
  - **`forTeacherImport`**(): array

    /


### `app/Application/Import/ImportService.php`

#### `Intranet\Application\Import\ImportService`
Servei d'aplicació per a operacions comunes d'importació.

- Metodes:
  - **`resolveXmlFile`**(Request $request, string $field = 'fichero'): ?UploadedFile

    Valida i retorna el fitxer XML d'importació.
  - **`runWithExtendedTimeout`**(callable $runner, UploadedFile $file, Request $request): void

    Executa una importació amb timeout ampliat.
  - **`isFirstImport`**(Request $request): bool


### `app/Application/Import/ImportWorkflowService.php`

#### `Intranet\Application\Import\ImportWorkflowService`
Servei d'orquestració del flux d'importació.

- Metodes:
  - **`__construct`**(private readonly ProfesorService $profesorService, private readonly GrupoService $grupoService)
  - **`executeXmlImport`**(mixed $fxml, array $camposBdXml, mixed $firstImport, callable $tableHandler): void

    Executa el recorregut de taules d'un XML d'importació.
  - **`resolveXmlPath`**(mixed $fxml): string
  - **`executeXmlImportWithHooks`**(mixed $fxml, array $camposBdXml, mixed $context, callable $preHandler, callable $inHandler, callable $postHandler): void

    Executa el recorregut amb pipeline pre/in/post.
  - **`executeXmlImportSimple`**(mixed $fxml, array $camposBdXml, mixed $context, callable $inHandler): void

    Executa el recorregut amb pipeline simple.
  - **`assignTutores`**(): void
  - **`applyTutorRoleRules`**(bool $isTutor, mixed $role): mixed


### `app/Application/Import/ImportXmlHelperService.php`

#### `Intranet\Application\Import\ImportXmlHelperService`
Utilitats compartides per a parseig i validació de camps XML.

- Metodes:
  - **`extractField`**(mixed $attributes, mixed $key, int $func, object $context): mixed
  - **`passesFilter`**(array $filter, mixed $fields): bool

    /
  - **`findMissingRequired`**(array $required, mixed $fields, bool $strictSpaceCheck = false): ?string

    /
  - **`invokeContextMethod`**(object $context, string $method, array $params): mixed

    /


### `app/Application/Import/TeacherImportExecutionService.php`

#### `Intranet\Application\Import\TeacherImportExecutionService`
- Metodes:
  - **`__construct`**(private readonly HorarioService $horarioService, private readonly ProfesorService $profesorService, )
  - **`clearTeacherHorarios`**(string $idProfesor, bool $lost = false): void
  - **`importTable`**(mixed $xmltable, array $tabla, string $idProfesor, callable $extractField, callable $passesFilter, callable $requiredCheck, string $mode = 'full'): void

    /
  - **`createRecordByClass`**(string $className, array $arrayDatos, string $idProfesor): mixed

    /
  - **`preloadExistingRecords`**(mixed $xmltable, array $tabla, callable $extractField, string $class): array

    /
  - **`normalizeCacheKey`**(mixed $key): ?string


### `app/Application/Instructor/InstructorWorkflowService.php`

#### `Intranet\Application\Instructor\InstructorWorkflowService`
- Metodes:
  - **`searchForTutorFcts`**(): Collection
  - **`empresaIdFromInstructor`**(int|string $id): ?int
  - **`upsertAndAttachToCentro`**(object $request, int|string $centro, callable $createInstructor): int
  - **`detachFromCentroAndDeleteIfOrphan`**(int|string $id, int|string $centro, callable $deleteInstructor): int
  - **`copyInstructorToCentro`**(int|string $id, int|string $sourceCentro, int|string $targetCentro, string $action): int
  - **`ultimaFecha`**(Collection|array|null $fcts): ?Carbon


### `app/Application/Menu/MenuService.php`

#### `Intranet\Application\Menu\MenuService`
Servei d'aplicació per construir i cachejar menús de navegació.

- Metodes:
  - **`make`**(string $nom, bool $array = false, $user = null): mixed

    Construeix el menú per nom i usuari.
  - **`clearCache`**(?string $nom = null, ?string $dni = null): void

    Neteja el cache de menú (global o filtrat per nom/dni).
  - **`listForGrid`**()

    Retorna menús ordenats per al grid, normalitzant ordres abans.
  - **`saveFromRequest`**(Request $request, $id = null): Menu

    Persisteix un menú des del request.
  - **`copy`**(int|string $id): Menu

    Duplica un menú dins del mateix grup/submenú.
  - **`moveUp`**(int|string $id): void

    Mou un menú cap amunt dins del bloc actual.
  - **`moveDown`**(int|string $id): void

    Mou un menú cap avall dins del bloc actual.
  - **`build`**(string $nom, object $user): array

    Construeix l'estructura de menú a partir dels registres actius.
  - **`tipoUrl`**($url): string

    Determina si una URL és externa o interna per a StydeMenu.
  - **`cacheKey`**(string $nom, string $dni): string

    Compon la clau de cache per menú i usuari.
  - **`registerCacheKey`**(string $key): void

    Registra la clau en l'índex global per permetre invalidació selectiva.
  - **`isAdminUser`**(object $user): bool

    Comprovació local de rol admin sobre l'usuari rebut.
  - **`renderMenu`**(array $menu): HtmlString

    Renderitza l'arbre de menú amb el markup legacy del tema bootstrap.
  - **`translateMenuTitle`**(string $key): string

    Resol la traducció d'un ítem de menú amb fallback compatible amb legacy.
  - **`sortForGrid`**(): void

    Reordena pares i fills per mantindre seqüència contínua.


### `app/Application/Notification/NotificationInboxService.php`

#### `Intranet\Application\Notification\NotificationInboxService`
Casos d'ús per a la safata de notificacions d'usuari.

- Metodes:
  - **`__construct`**(?ProfesorService $profesorService = null)
  - **`listForUser`**(object $user): EloquentCollection

    /
  - **`markAsRead`**(int|string $id): bool
  - **`markAllAsRead`**(object $user): bool
  - **`deleteAll`**(object $user): bool
  - **`deleteById`**(int|string $id): void
  - **`findForShow`**(int|string $id): ?Notification
  - **`profesores`**(): ProfesorService

#### `Intranet\Application\Notification\profesorService`
- Metodes:
  - **`resolveNotifiable`**(object $user): mixed
  - **`hydratePayload`**(Notification $notification): Notification


### `app/Application/Poll/PollWorkflowService.php`

#### `Intranet\Application\Poll\PollWorkflowService`
- Metodes:
  - **`prepareSurvey`**(int|string $id, object $user): ?array
  - **`saveSurvey`**(Request $request, int|string $id, object $user): bool
  - **`myVotes`**(int|string $id): ?array
  - **`allVotes`**(int|string $id, GrupoService $grupoService): ?array
  - **`userKey`**(Poll $poll, object $user): string
  - **`loadPreviousVotes`**(Poll $poll, object $user): array
  - **`saveVote`**(Poll $poll, mixed $option, mixed $option1, mixed $option2, mixed $value, object $user): void
  - **`initValues`**(array &$votes, mixed $options, GrupoService $grupoService): void


### `app/Application/Profesor/ProfesorService.php`

#### `Intranet\Application\Profesor\ProfesorService`
Casos d'ús d'aplicació per al domini de professorat.

- Metodes:
  - **`__construct`**(private readonly ProfesorRepositoryInterface $profesorRepository)
  - **`plantillaOrderedWithDepartamento`**(): EloquentCollection

    /
  - **`activosByDepartamentosWithHorario`**(array $departamentosIds, string $dia, int $sesion): EloquentCollection

    /
  - **`activosOrdered`**(): EloquentCollection

    /
  - **`all`**(): EloquentCollection

    /
  - **`plantilla`**(): EloquentCollection

    /
  - **`plantillaByDepartamento`**(int|string $departamento): EloquentCollection

    /
  - **`activos`**(): EloquentCollection

    /
  - **`byDepartamento`**(int|string $departamento): EloquentCollection

    /
  - **`byGrupo`**(string $grupo): EloquentCollection

    /
  - **`byGrupoTrabajo`**(string $grupoTrabajo): EloquentCollection

    /
  - **`byDnis`**(array $dnis): EloquentCollection

    /
  - **`find`**(string $dni): ?Profesor
  - **`findOrFail`**(string $dni): Profesor
  - **`findBySustituyeA`**(string $dni): ?Profesor
  - **`findByCodigo`**(string $codigo): ?Profesor
  - **`findByApiToken`**(string $apiToken): ?Profesor
  - **`findByEmail`**(string $email): ?Profesor
  - **`plantillaOrderedByDepartamento`**(): EloquentCollection

    /
  - **`plantillaForResumen`**(): EloquentCollection

    /
  - **`allOrderedBySurname`**(): EloquentCollection

    /
  - **`clearFechaBaja`**(): int
  - **`countByCodigo`**(int|string $codigo): int
  - **`usedCodigosBetween`**(int $min, int $max): array

    /
  - **`create`**(array $data): Profesor

    /
  - **`withSustituyeAssigned`**(): EloquentCollection

    /


### `app/Domain/AlumnoFct/AlumnoFctRepositoryInterface.php`

#### `Intranet\Domain\AlumnoFct\AlumnoFctRepositoryInterface`
Contracte d'accés a dades d'AlumnoFct.

- Metodes:
  - **`all`**(): EloquentCollection

    Recupera tots els registres d'alumnat en FCT.
  - **`totesFcts`**(?string $profesor = null): EloquentCollection

    Recupera les FCT visibles per a un tutor (incloent substitucions).
  - **`find`**(int|string $id): ?AlumnoFct

    Cerca un registre per identificador.
  - **`findOrFail`**(int|string $id): AlumnoFct

    Cerca un registre per identificador o llança excepció.
  - **`firstByIdSao`**(int|string $idSao): ?AlumnoFct

    Recupera el primer registre associat a un id SAO.
  - **`byAlumno`**(string $nia): EloquentCollection

    Llista tots els registres d'un alumne.
  - **`byAlumnoWithA56`**(string $nia): EloquentCollection

    Llista registres d'un alumne amb annex A56 en curs.
  - **`byGrupoEsFct`**(string $grupo): EloquentCollection

    Llista registres d'un grup que són FCT (no dual).
  - **`byGrupoEsDual`**(string $grupo): EloquentCollection

    Llista registres d'un grup que són dual.
  - **`reassignProfesor`**(string $fromDni, string $toDni): int

    Reassigna tutor responsable en bloc.
  - **`avalDistinctAlumnoIdsByProfesor`**(?string $profesor = null): array

    Recupera els identificadors d'alumnat amb FCT avaluable del tutor.
  - **`latestAvalByAlumnoAndProfesor`**(string $idAlumno, ?string $profesor = null): ?AlumnoFct

    Recupera l'últim registre avaluable d'un alumne per tutor.
  - **`avaluablesNoAval`**(?string $profesor = null, mixed $grupo = null): EloquentCollection

    Recupera registres avaluables que encara no estan tancats en acta.


### `app/Domain/Comision/ComisionRepositoryInterface.php`

#### `Intranet\Domain\Comision\ComisionRepositoryInterface`
Contracte de persistència per al domini de Comissió.

- Metodes:
  - **`find`**(int $id): ?Comision
  - **`findOrFail`**(int $id): Comision
  - **`byDay`**(string $dia): EloquentCollection

    /
  - **`withProfesorByDay`**(string $dia): EloquentCollection

    /
  - **`pendingAuthorization`**(): EloquentCollection

    /
  - **`authorizationApiList`**(): EloquentCollection

    Llistat per a l'API d'autorització (inclou nom concatenat de professor/a).
  - **`authorizeAllPending`**(): int
  - **`prePayByProfesor`**(string $dni): EloquentCollection

    /
  - **`setEstado`**(int $id, int $estado): Comision
  - **`hasPendingUnpaidByProfesor`**(string $dni): bool
  - **`attachFct`**(int $comisionId, int $fctId, string $horaIni, bool $aviso): void
  - **`detachFct`**(int $comisionId, int $fctId): void


### `app/Domain/Empresa/EmpresaRepositoryInterface.php`

#### `Intranet\Domain\Empresa\EmpresaRepositoryInterface`
Contracte de persistència per al domini d'empreses.

- Metodes:
  - **`listForGrid`**(): EloquentCollection

    /
  - **`findForShow`**(int $id): Empresa
  - **`colaboracionIdsByCycleAndCenters`**(int $cycleId, array $centerIds): Collection

    /
  - **`cyclesByDepartment`**(string $department): EloquentCollection

    /
  - **`convenioList`**(): EloquentCollection

    /
  - **`socialConcertList`**(): EloquentCollection

    /
  - **`erasmusList`**(): EloquentCollection

    /


### `app/Domain/Expediente/ExpedienteRepositoryInterface.php`

#### `Intranet\Domain\Expediente\ExpedienteRepositoryInterface`
Contracte de persistència per al domini d'expedients.

- Metodes:
  - **`find`**(int|string $id): ?Expediente
  - **`findOrFail`**(int|string $id): Expediente
  - **`createFromRequest`**(Request $request): Expediente
  - **`updateFromRequest`**(int|string $id, Request $request): Expediente
  - **`pendingAuthorization`**(): EloquentCollection

    /
  - **`readyToPrint`**(): EloquentCollection

    /
  - **`allTypes`**(): EloquentCollection

    /


### `app/Domain/FaltaProfesor/FaltaProfesorRepositoryInterface.php`

#### `Intranet\Domain\FaltaProfesor\FaltaProfesorRepositoryInterface`
Contracte de persistència per al domini de fitxatges de professorat.

- Metodes:
  - **`lastTodayByProfesor`**(string $dni): ?Falta_profesor
  - **`hasFichadoOnDay`**(string $dia, string $dni): bool
  - **`createEntry`**(string $dni, string $dia, string $hora): Falta_profesor
  - **`closeExit`**(Falta_profesor $fichaje, string $hora): Falta_profesor
  - **`byDayAndProfesor`**(string $dia, string $dni): EloquentCollection

    /
  - **`rangeByProfesor`**(string $dni, string $desde, string $hasta): EloquentCollection

    /


### `app/Domain/Fct/FctRepositoryInterface.php`

#### `Intranet\Domain\Fct\FctRepositoryInterface`
Contracte de persistència per al domini FCT.

- Metodes:
  - **`find`**(int|string $id): ?Fct
  - **`findOrFail`**(int|string $id): Fct
  - **`firstByColaboracionAsociacionInstructor`**(int|string $idColaboracion, int|string $asociacion, int|string $idInstructor): ?Fct
  - **`panelListingByProfesor`**(string $dni): EloquentCollection

    /
  - **`save`**(Fct $fct): Fct
  - **`create`**(array $attributes): Fct
  - **`attachAlumno`**(int|string $idFct, string $idAlumno, array $pivotAttributes): void
  - **`detachAlumno`**(int|string $idFct, string $idAlumno): void
  - **`saveColaborador`**(int|string $idFct, Colaborador $colaborador): void
  - **`deleteColaborador`**(int|string $idFct, string $idInstructor): int
  - **`updateColaboradorHoras`**(int|string $idFct, string $idInstructor, int|string $horas): int
  - **`setCotutor`**(int|string $idFct, ?string $cotutor): void
  - **`empresaIdByFct`**(int|string $idFct): ?int


### `app/Domain/Grupo/GrupoRepositoryInterface.php`

#### `Intranet\Domain\Grupo\GrupoRepositoryInterface`
Contracte d'accés a dades de l'agregat Grupo.

- Metodes:
  - **`create`**(array $attributes): Grupo

    /
  - **`find`**(string $codigo): ?Grupo
  - **`all`**(): EloquentCollection

    /
  - **`qTutor`**(string $dni): EloquentCollection

    /
  - **`firstByTutor`**(string $dni): ?Grupo
  - **`largestByTutor`**(string $dni): ?Grupo
  - **`byCurso`**(int $curso): EloquentCollection

    /
  - **`byDepartamento`**(int $departamento): EloquentCollection

    /
  - **`tutoresDniList`**(): array

    /
  - **`reassignTutor`**(string $fromDni, string $toDni): int
  - **`misGrupos`**(): EloquentCollection

    /
  - **`misGruposByProfesor`**(string $dni): EloquentCollection

    /
  - **`withActaPendiente`**(): EloquentCollection

    /
  - **`byTutorOrSubstitute`**(string $dni, ?string $sustituyeA): ?Grupo

    Retorna el primer grup on el professor és tutor o tutor substituït.
  - **`withStudents`**(): EloquentCollection

    /
  - **`firstByTutorDual`**(string $dni): ?Grupo
  - **`byCodes`**(array $codigos): EloquentCollection

    /
  - **`allWithTutorAndCiclo`**(): EloquentCollection

    /
  - **`misGruposWithCiclo`**(): EloquentCollection

    /


### `app/Domain/Horario/HorarioRepositoryInterface.php`

#### `Intranet\Domain\Horario\HorarioRepositoryInterface`
Contracte de persistència per a l'agregat Horario.

- Metodes:
  - **`semanalByProfesor`**(string $dni): array

    /
  - **`semanalByGrupo`**(string $grupo): array

    /
  - **`lectivosByDayAndSesion`**(string $dia, int $sesion): EloquentCollection

    /
  - **`countByProfesorAndDay`**(string $dni, string $dia): int
  - **`guardiaAllByDia`**(string $dia): EloquentCollection

    /
  - **`guardiaAllByProfesorAndDiaAndSesiones`**(string $dni, string $dia, array $sesiones): EloquentCollection

    /
  - **`guardiaAllByProfesorAndDia`**(string $dni, string $dia): EloquentCollection

    /
  - **`guardiaAllByProfesor`**(string $dni): EloquentCollection

    /
  - **`firstByProfesorDiaSesion`**(string $dni, string $dia, int|string $sesion): ?Horario
  - **`byProfesor`**(string $dni): EloquentCollection

    /
  - **`byProfesorWithRelations`**(string $dni, array $relations): EloquentCollection

    /
  - **`lectivasByProfesorAndDayOrdered`**(string $dni, string $dia): EloquentCollection

    /
  - **`reassignProfesor`**(string $fromDni, string $toDni): int
  - **`deleteByProfesor`**(string $dni): int
  - **`gruposByProfesor`**(string $dni): Collection

    /
  - **`gruposByProfesorDiaAndSesiones`**(string $dni, string $dia, array $sesiones): Collection

    /
  - **`profesoresByGruposExcept`**(array $grupos, string $emisorDni): Collection

    /
  - **`primeraByProfesorAndDateOrdered`**(string $dni, string $date): EloquentCollection

    /
  - **`firstByModulo`**(string $modulo): ?Horario
  - **`byProfesorDiaOrdered`**(string $dni, string $dia): EloquentCollection

    /
  - **`distinctModulos`**(): Collection

    /
  - **`create`**(array $data): Horario

    /
  - **`forProgramacionImport`**(): EloquentCollection

    /
  - **`firstForDepartamentoAsignacion`**(string $dni): ?Horario


### `app/Domain/Profesor/ProfesorRepositoryInterface.php`

#### `Intranet\Domain\Profesor\ProfesorRepositoryInterface`
Contracte de persistència per a l'agregat Profesor.

- Metodes:
  - **`plantillaOrderedWithDepartamento`**(): EloquentCollection

    /
  - **`activosByDepartamentosWithHorario`**(array $departamentosIds, string $dia, int $sesion): EloquentCollection

    /
  - **`activosOrdered`**(): EloquentCollection

    /
  - **`all`**(): EloquentCollection

    /
  - **`plantilla`**(): EloquentCollection

    /
  - **`plantillaByDepartamento`**(int|string $departamento): EloquentCollection

    /
  - **`activos`**(): EloquentCollection

    /
  - **`byDepartamento`**(int|string $departamento): EloquentCollection

    /
  - **`byGrupo`**(string $grupo): EloquentCollection

    /
  - **`byGrupoTrabajo`**(string $grupoTrabajo): EloquentCollection

    /
  - **`byDnis`**(array $dnis): EloquentCollection

    /
  - **`find`**(string $dni): ?Profesor
  - **`findOrFail`**(string $dni): Profesor
  - **`findBySustituyeA`**(string $dni): ?Profesor
  - **`findByCodigo`**(string $codigo): ?Profesor
  - **`findByApiToken`**(string $apiToken): ?Profesor
  - **`findByEmail`**(string $email): ?Profesor
  - **`plantillaOrderedByDepartamento`**(): EloquentCollection

    /
  - **`plantillaForResumen`**(): EloquentCollection

    /
  - **`allOrderedBySurname`**(): EloquentCollection

    /
  - **`clearFechaBaja`**(): int
  - **`countByCodigo`**(int|string $codigo): int
  - **`usedCodigosBetween`**(int $min, int $max): array

    /
  - **`create`**(array $data): Profesor

    /
  - **`withSustituyeAssigned`**(): EloquentCollection

    /


### `app/Services/Auth/ApiSessionTokenService.php`

#### `Intranet\Services\Auth\ApiSessionTokenService`
Gestiona el token Sanctum de sessió web per al professorat.

- Metodes:
  - **`issueForProfesor`**(Profesor $profesor, string $deviceName = 'web-session'): string

    Emet un token Sanctum i el guarda en sessió per a ús del client web.
  - **`revokeCurrentFromSession`**(): void

    Revoca el token actual emmagatzemat en sessió i neteja claus de sessió.
  - **`currentToken`**(): ?string

    Retorna el token de sessió actual, si existeix.


### `app/Services/Auth/JWTTokenService.php`

#### `Intranet\Services\Auth\JWTTokenService`
- Metodes:
  - **`__construct`**(?ProfesorService $profesorService = null)

#### `Intranet\Services\Auth\createTokenProgramacio`
- Metodes:
  - **`createTokenProgramacio`**($idModuleGrupo, $dni=null)
  - **`getTokenLink`**($id, $dni = null)
  - **`role`**($role): array
  - **`turno`**($turno)


### `app/Services/Auth/PerfilService.php`

#### `Intranet\Services\Auth\PerfilService`
- Metodes:
  - **`__construct`**(ComisionService $comisionService, HorarioService $horarioService)
  - **`carregarDadesProfessor`**(string $dni): array
  - **`carregarDadesAlumne`**(string $nia): array


### `app/Services/Auth/RemoteLoginService.php`

#### `Intranet\Services\Auth\RemoteLoginService`
- Metodes:
  - **`login`**($link, $user, $pass)


### `app/Services/Auth/VioletHasher.php`

#### `Intranet\Services\Auth\VioletHasher`
- Metodes:
  - **`dniHash`**(string $dni, ?string $pepper = null): string


### `app/Services/Automation/SeleniumService.php`

#### `Intranet\Services\Automation\SeleniumService`
- Metodes:
  - **`__construct`**($dni, $password)
  - **`getDriverSelenium`**(mixed $desiredCapabilities=null): RemoteWebDriver

    /
  - **`getDriver`**(): ?RemoteWebDriver

    /
  - **`quit`**()
  - **`loginSAO`**($dni, $password, $desiredCapabilities=null): RemoteWebDriver

    /
  - **`loginItaca`**($dni, $password): RemoteWebDriver

    /
  - **`restartSelenium`**()
  - **`fill`**($selector, $keys, $driver = null)
  - **`waitAndClick`**($xpath, $driver = null)
  - **`gTPersonalLlist`**()
  - **`closeNoticias`**()


### `app/Services/Calendar/CalendarService.php`

#### `Intranet\Services\Calendar\CalendarService`
- Metodes:
  - **`build`**($elemento, $descripcion='descripcion', $objetivos='objetivos'): Calendar

    /
  - **`render`**($ini, $fin, $descripcion, $objetivos, $location)


### `app/Services/Calendar/GoogleCalendarService.php`

#### `Intranet\Services\Calendar\GoogleCalendarService`
- Metodes:
  - **`__construct`**()
  - **`getClient`**()
  - **`getCalendar`**()
  - **`addEvent`**($title, $description, $start, $end, $attendees = [])
  - **`dateToGoogle`**($dataHoraOriginal)
  - **`saveEvents`**()


### `app/Services/Calendar/MeetingOrderGenerateService.php`

#### `Intranet\Services\Calendar\MeetingOrderGenerateService`
- Metodes:
  - **`__construct`**($reunion)
  - **`exec`**()
  - **`isOrderAdvanced`**($texto)
  - **`storeAdvancedItems`**($query, $resumen, &$contador)
  - **`getResumenAdvanced`**($query, $asArray = false)
  - **`storeItem`**(&$contador, $text, $resumen)


### `app/Services/Document/AttachedFileService.php`

#### `Intranet\Services\Document\AttachedFileService`
- Metodes:
  - **`safeFile`**($file, string $route, ?string $dni, ?string $title): int
  - **`saveLink`**(string $nameFile, string $referencesTo, string $title, string $extension, string $route, ?string $dni = null): int
  - **`save`**($files, string $route, ?string $dni = null, ?string $title = null): array
  - **`delete`**(Adjunto $attached): int
  - **`saveExistingFile`**(string $filePath, string $route, string $dni, ?string $title = null): int
  - **`moveAndPreserveDualFiles`**()
  - **`deleteNonDualFiles`**($dualRoutes)


### `app/Services/Document/CreateOrUpdateDocumentAction.php`

#### `Intranet\Services\Document\CreateOrUpdateDocumentAction`
- Metodes:
  - **`fromRequest`**(Request $request, array $overrides = [], ?Documento $document = null, $elemento = null): Documento
  - **`fromArray`**(array $data, ?Documento $document = null, $elemento = null): Documento
  - **`build`**(array $data, ?Documento $document = null, $elemento = null): Documento
  - **`applyDefaults`**(array $data, ?Documento $document, $elemento = null): array
  - **`firstAvailable`**(array $data, ?Documento $document, string $property)
  - **`resolveElementoId`**($elemento, array $payload, ?Documento $document): ?string


### `app/Services/Document/DocumentAccessChecker.php`

#### `Intranet\Services\Document\DocumentAccessChecker`
- Metodes:
  - **`isAllowed`**(DocumentContext $context): bool


### `app/Services/Document/DocumentContext.php`

#### `Intranet\Services\Document\DocumentContext`
- Metodes:
  - **`__construct`**(?Documento $document, ?string $link, bool $isFile)
  - **`document`**(): ?Documento
  - **`link`**(): ?string
  - **`isFile`**(): bool


### `app/Services/Document/DocumentPathService.php`

#### `Intranet\Services\Document\DocumentPathService`
- Metodes:
  - **`resolvePath`**(DocumentContext $context): ?string
  - **`exists`**(DocumentContext $context): bool
  - **`mimeType`**(DocumentContext $context): ?string
  - **`responseFile`**(DocumentContext $context)
  - **`existsPath`**(string $path): bool
  - **`responseFromPath`**(string $path)


### `app/Services/Document/DocumentResolver.php`

#### `Intranet\Services\Document\DocumentResolver`
- Metodes:
  - **`resolve`**($elemento = null, $documento = null): DocumentContext
  - **`findDocument`**($elemento): ?Documento
  - **`getFileIfExistFromModel`**($elemento): array


### `app/Services/Document/DocumentResponder.php`

#### `Intranet\Services\Document\DocumentResponder`
- Metodes:
  - **`__construct`**(?DocumentAccessChecker $accessChecker = null, ?DocumentPathService $pathService = null)
  - **`respond`**(DocumentContext $context)


### `app/Services/Document/DocumentService.php`

#### `Intranet\Services\Document\DocumentService`
Servei per generar documents (PDF, ZIP o correus) a partir de la configuració

- Metodes:
  - **`__construct`**(Finder $finder)

    DocumentService constructor.
  - **`__get`**($key)
  - **`load`**(): \Illuminate\Support\Collection

    Retorna els elements carregats pel Finder.
  - **`render`**(): \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse

    Renderitza el document segons la configuració (email o impressió).
  - **`mail`**(): \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse

    Envia el document per correu utilitzant la configuració del Finder.
  - **`generatePdfFromView`**(): \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse

    Genera un PDF a partir d'una vista Blade.

#### `Intranet\Services\Document\hazZip`
- Metodes: cap

#### `Intranet\Services\Document\hazPdf`
- Metodes:
  - **`generatePdfFromTemplate`**(): \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse

    Genera un PDF a partir d'una plantilla (`printResource`).
  - **`generateSignedPdf`**(): \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse

    Genera un PDF signat si està activada la signatura digital.
  - **`generateMultiplePdfs`**(): \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse

    Genera diversos PDFs i els empaqueta si cal.

#### `Intranet\Services\Document\merge`
- Metodes:
  - **`generateZip`**($pdfs, $filename): \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\JsonResponse

    Genera un ZIP amb els PDFs indicats i retorna una resposta de fitxer.
  - **`normalizePdfPaths`**($elements): array

    Normalitza un conjunt d'entrades a rutes de fitxers PDF existents.


### `app/Services/Document/ExcelService.php`

#### `Intranet\Services\Document\ExcelService`
- Metodes:
  - **`__construct`**($inputFileName)

    /
  - **`render`**(...$colums)


### `app/Services/Document/FDFPrepareService.php`

#### `Intranet\Services\Document\FDFPrepareService`
Servei per preparar PDFs de plantilles FDF i concatenar fitxers resultants.

- Metodes:
  - **`exec`**(PrintResource $resource, $id=null): string|null

    Genera un PDF a partir d'un recurs imprimible i retorna la ruta absoluta.

#### `Intranet\Services\Document\fillForResource`
- Metodes:
  - **`joinPDFs`**($pdfs, $nameFile): string

    Concatena diversos PDFs i retorna la ruta relativa del resultat.

#### `Intranet\Services\Document\merge`
- Metodes: cap


### `app/Services/Document/PdfFormService.php`

#### `Intranet\Services\Document\PdfFormService`
Encapsula les operacions de formularis PDF basades en pdftk via CLI.

- Metodes:
  - **`fillAndSave`**(string $templatePath, array $fields, string $outputPath, bool $flatten = false): void

    Emplena una plantilla PDF i desa el resultat en un fitxer.
  - **`fillAndSend`**(string $templatePath, array $fields, string $downloadName, bool $flatten = false): void

    Emplena una plantilla PDF i l'envia al navegador.
  - **`fillForResource`**(string $templatePath, array $fields, string $outputPath, bool $flatten = false, ?string $stampPath = null): void

    Emplena una plantilla i aplica el flux de preparació utilitzat pels recursos FDF.
  - **`runCommand`**(array $args): void

    Executa un comandament de procés i valida l'eixida.
  - **`createTempFdf`**(array $fields): string

    Crea un fitxer temporal FDF amb les dades del formulari.
  - **`escapeFdfString`**(string $value): string

    Escapa valors per a cadenes literals en FDF.
  - **`resolveTemplatePath`**(string $templatePath): string

    Resol una ruta de plantilla relativa o absoluta.
  - **`binary`**(): string

    Retorna el binari pdftk configurat.
  - **`ensureOutputDirectory`**(string $outputPath): void

    Crea el directori de destí si no existix.


### `app/Services/Document/PdfMergeService.php`

#### `Intranet\Services\Document\PdfMergeService`
Servei per concatenar múltiples PDFs en un únic document amb FPDI.

- Metodes:
  - **`merge`**(array $pdfs, string $outputPath): void

    Concatena els fitxers PDF indicats i guarda el resultat en la ruta de destí.


### `app/Services/Document/PdfService.php`

#### `Intranet\Services\Document\PdfService`
Servei de generació de PDFs i ZIPs.

- Metodes:
  - **`footerText`**($informe): string

    Calcula el text del peu segons el document.
  - **`hazPdf`**($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4', $marginTop = 15, $driver = null): mixed

    Genera un PDF amb el driver indicat.
  - **`hazZip`**($informe, $all, $datosInforme = null, $orientacion = 'portrait', $field = 'id'): string|null

    Genera un ZIP amb PDFs per a cada element.
  - **`hazSnappyPdf`**($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4', $marginTop = 15): mixed

    Genera un PDF amb Snappy.
  - **`hazDomPdf`**($informe, $todos, $datosInforme, $orientacion, $dimensiones): mixed

    Genera un PDF amb DomPDF.


### `app/Services/Document/TipoDocumentoService.php`

#### `Intranet\Services\Document\TipoDocumentoService`
- Metodes:
  - **`allPestana`**()
  - **`allDocuments`**()
  - **`allRol`**($grupo)
  - **`rol`**($index)
  - **`all`**($grupo)
  - **`get`**($index)


### `app/Services/Document/TipoReunionService.php`

#### `Intranet\Services\Document\TipoReunionService`
- Metodes:
  - **`__construct`**($id)
  - **`__get`**($key)
  - **`__isset`**($key)
  - **`allSelect`**($colectivo = null, $superUsuari=false)
  - **`find`**($id)
  - **`all`**()
  - **`literal`**($a)
  - **`get`**()


### `app/Services/Document/ZipService.php`

#### `Intranet\Services\Document\ZipService`
- Metodes:
  - **`exec`**(iterable $files, string $nameFile): string

    Crea un fitxer ZIP amb els paths indicats i retorna el path relatiu dins de storage/tmp.


### `app/Services/General/AutorizacionPrintService.php`

#### `Intranet\Services\General\AutorizacionPrintService`
Servei d'impressió en lot per a fluxos d'autorització.

- Metodes:
  - **`__construct`**(PdfService $pdfService)
  - **`imprimir`**(string $class, string $model, ?string $modelo = null, ?int $inicial = null, int|string|null $final = null, string $orientacion = 'portrait', bool $link = true): mixed

    Executa la generació de document i canvi d'estat en lot.


### `app/Services/General/AutorizacionStateService.php`

#### `Intranet\Services\General\AutorizacionStateService`
Servei d'aplicació per a transicions d'estat en fluxos d'autorització.

- Metodes:
  - **`__construct`**(string $class)
  - **`cancel`**(int|string $id): bool

    Mou l'element a estat de cancel·lació.
  - **`init`**(int|string $id, int $initState = 1): bool

    Inicialitza l'element a l'estat configurat pel caller.
  - **`resolve`**(int|string $id, ?string $explicacion = null): array|false

    /
  - **`accept`**(int|string $id): array|false

    /
  - **`resign`**(int|string $id): array|false

    /
  - **`refuse`**(int|string $id, ?string $explicacion = null): array|false

    /
  - **`setState`**(int|string $id, int $state): bool

    Assigna un estat concret i retorna si l'operació és correcta.
  - **`transitionWithResult`**(int|string $id, callable $resolver): array|false

    Executa una transició i retorna els estats per a la capa de presentació.


### `app/Services/General/GestorService.php`

#### `Intranet\Services\General\GestorService`
- Metodes:
  - **`__construct`**($elemento = null, $documento = null, ?DocumentResolver $resolver = null, ?DocumentResponder $responder = null)
  - **`save`**($parametres = null)
  - **`render`**()
  - **`saveDocument`**($filePath, $tags, $descripcion = null, $supervisor = null)


### `app/Services/General/StateService.php`

#### `Intranet\Services\General\StateService`
Servei per gestionar canvis d'estat d'un model i accions associades.

- Metodes:
  - **`__construct`**($class, $id = null)

    Crea el servei amb un model o una classe.
  - **`putEstado`**($estado, $mensaje = null, $fecha = null): int|false

    Canvia l'estat i executa accions associades.
  - **`makeDocument`**()

    Guarda el document associat si hi ha fitxer.
  - **`dateResolve`**($fecha, $mensaje)

    Assigna la data de resolucio i el missatge al camp configurat.
  - **`resolve`**($mensaje = null): int|false

    Resol l'element segons la configuracio del model.
  - **`refuse`**($mensaje = null): int|false

    Rebutja l'element segons la configuracio del model.
  - **`_print`**(): int|false

    Marca l'element com a imprimit segons la configuracio del model.
  - **`getEstado`**(): int|null

    Retorna l'estat actual de l'element.
  - **`normalizeStatesElement`**(): void

    Normalitza la configuracio del model.
  - **`getConfiguredState`**(string $key): ?int

    Retorna un estat configurat o null si falta.
  - **`makeAll`**($todos, $accio)

    Modifica l'estat d'un conjunt d'elements
  - **`makeLink`**($todos, $doc)

    Enllaça múltiples elements a un document.


### `app/Services/HR/FitxatgeService.php`

#### `Intranet\Services\HR\FitxatgeService`
Servei de gestio de fitxatges.

- Metodes:
  - **`__construct`**(private readonly FaltaProfesorRepositoryInterface $faltaProfesorRepository)
  - **`fitxar`**(?string $dni = null): Falta_profesor|bool|null
  - **`fitxaDiaManual`**(string $dni, string $dia, string $hora = '12:00:00'): Falta_profesor
  - **`hasFichado`**(string $dia, string $dni): bool
  - **`isInside`**(?string $dni = null, bool $storeInSession = true): bool
  - **`sessionEntry`**(): ?string
  - **`sessionExit`**(): ?string
  - **`wasInsideAt`**(string $dni, string $dia, string $hora): bool
  - **`registrosEntreFechas`**(string $dni, string $desde, string $hasta): EloquentCollection

    /


### `app/Services/HR/PresenciaResumenService.php`

#### `Intranet\Services\HR\PresenciaResumenService`
- Metodes:
  - **`__construct`**(private int $GRACE_MINUTES = 10, // tolerància general private bool $FLEX_NO_DOCENCIA = true, // trams no docents més flexibles private bool $DOCENCIA_RIGIDA = true, // trams docents estrictes private int $MIN_CHUNK_MINUTES = 3, // fusionar intervals molt propers private int $NO_SALIDA_AFTER_MIN = 30 // després de quant temps marquem NO_SALIDA)
  - **`resumenDia`**(\DateTimeInterface|string $dia, ?Collection $profes = null): array

    Resum d'un dia per a un conjunt de professors.
  - **`firstEntry`**(?Collection $fichajesRows): ?string
  - **`weekdayLetter`**(Carbon $date): string
  - **`buildPlannedSlotsFromDbRows`**(Collection $rows, Carbon $day): array
  - **`sanitizeFichajes`**(Collection $rows, array $plan, Carbon $day): Collection

    El professor a vegades marca una "entrada" quan en realitat està eixint.
  - **`lastPlannedEnd`**(array $plan): ?Carbon
  - **`hasOpenStay`**(Collection $fichajesRows): bool
  - **`buildStayIntervals`**(Collection $fichajes, Carbon $day): array
  - **`buildExceptionIntervals`**(Collection $acts, Collection $coms, Collection $faltas, Carbon $day): array
  - **`computeCoverage`**(array $plan, array $stays, array $exc): array
  - **`decideStatus`**(array $c, bool $hasOpenStay, array $plan, Carbon $date): string
  - **`clampToDay`**(Carbon $t, Carbon $start, Carbon $end): Carbon
  - **`overlapMinutes`**(array $a, array $b): int
  - **`overlapWithGrace`**(array $slot, array $stay, int $graceStart, int $graceEnd): int
  - **`mergeOverlappingOrClose`**(array $intervals, int $gapMinutes): array
  - **`mergeTouchingByType`**(array $slots): array
  - **`mergeByType`**(array $exc): array


### `app/Services/Mail/EmailPostSendService.php`

#### `Intranet\Services\Mail\EmailPostSendService`
Accions post-enviament per a correus.

- Metodes:
  - **`handleAnnexeIndividual`**($signatura): void

    Actualitza l'estat d'enviament d'annexos individuals.
  - **`markFctEmailSent`**($elemento, string $correo): void

    Marca el correu enviat per a FCT.
  - **`updateAlumnoFct`**(AlumnoFct $alumnoFct): void

    /
  - **`updateSignatura`**(Signatura $signatura): void

    /


### `app/Services/Mail/FctMailService.php`

#### `Intranet\Services\Mail\FctMailService`
- Metodes:
  - **`getMailById`**(int $id, string $documento): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse

    Obté un correu per ID.
  - **`getMailByRequest`**($request, string $documento): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse

    Obté un correu a partir d'una petició.
  - **`generateMail`**($finder): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse

    Genera el correu a partir d'un Finder.


### `app/Services/Mail/MailSender.php`

#### `Intranet\Services\Mail\MailSender`
Envia correus a partir d'un MyMail.

- Metodes:
  - **`send`**(MyMail $mail, $fecha = null): void

    Envia el correu a tots els receptors.
  - **`sendMail`**(MyMail $mail, $element, $fecha): void

    Envia un correu a un receptor.
  - **`handlePostSend`**(MyMail $mail): void

    Lança l'esdeveniment definit en sessió, si existeix.

#### `Intranet\Services\Mail\handleAnnexeIndividual`
- Metodes: cap


### `app/Services/Mail/MyMail.php`

#### `Intranet\Services\Mail\MyMail`
Correu compost a partir de dades de configuració i receptors.

- Metodes:
  - **`__get`**($key): mixed

    Retorna propietats internes o del mapa de característiques.
  - **`__set`**($key, $value): void

    Assigna propietats internes o del mapa de característiques.
  - **`__construct`**($elements = null, $view = null, $features = [], $attach = null, $editable = null, RecipientResolver $resolver = null, MailSender $sender = null)

    /
  - **`render`**($route): \Illuminate\Contracts\View\View

    Renderitza la vista d'edició del correu.
  - **`send`**($fecha = null): void

    Envia el correu a tots els receptors.
  - **`resolveViewForSend`**(): string

    Resol la vista a enviar (carrega el fitxer si cal).
  - **`getTo`**(): mixed

    Retorna la col·lecció o element(s) a qui s'enviarà el correu.


### `app/Services/Mail/RecipientResolver.php`

#### `Intranet\Services\Mail\RecipientResolver`
Resol i formata receptors per a MyMail.

- Metodes:
  - **`resolveElements`**($elements, $class = null): \Illuminate\Support\Collection

    Converteix una llista d'elements en col·lecció d'objectes.
  - **`resolveElement`**($element, $class = null): mixed|null

    Resol un element a objecte, si cal.
  - **`formatReceivers`**($elements): string

    Dona format a la llista de receptors per a la vista.
  - **`formatReceiver`**($element): string

    Dona format a un receptor: id(mail;contacte).


### `app/Services/Media/ImageService.php`

#### `Intranet\Services\Media\ImageService`
- Metodes:
  - **`openGdImage`**($source)

    Obri una imatge GD des d'un UploadedFile o path, detectant el tipus real.
  - **`imagetypeFromMime`**(?string $mime): ?int
  - **`convertHeicToPng`**(string $inputPath): string
  - **`transform`**($fitxerOriginal)

    Redimensiona a 68x90 mantenint proporció i farcint amb transparent (PNG).
  - **`updatePhotoCarnet`**($fitxerOriginal, $fitxerDesti)
  - **`newPhotoCarnet`**($fitxerOriginal, $directoriDesti): string
  - **`toPng`**($fitxerOriginal, $fitxerDesti)


### `app/Services/Notifications/ActividadNotificationService.php`

#### `Intranet\Services\Notifications\ActividadNotificationService`
Servei d'enviament de notificacions relacionades amb activitats.

- Metodes:
  - **`__construct`**(private ?NotificationService $notificationService = null, ?callable $groupTeachersResolver = null, ?callable $adviseTeacherExecutor = null)

#### `Intranet\Services\Notifications\app`
- Metodes: cap

#### `Intranet\Services\Notifications\groupTeachersResolver`
- Metodes: cap

#### `Intranet\Services\Notifications\advise`
- Metodes:
  - **`notifyActivity`**(Actividad $actividad, Profesor $coordinador): void

    Envia notificacions a professorat de grups i participants.
  - **`notifyGroups`**(Actividad $actividad, Profesor $coordinador): void

    Envia missatge als professors dels grups inclosos en l'activitat.
  - **`notifyParticipants`**(Actividad $actividad): void

    Envia avís als professors participants de la pròpia activitat.


### `app/Services/Notifications/AdviseService.php`

#### `Intranet\Services\Notifications\AdviseService`
- Metodes:
  - **`exec`**(object $element, ?string $message = null): void
  - **`__construct`**(object $element, ?string $message = null)
  - **`file`**(): string
  - **`getAdvises`**(): array
  - **`addDescriptionToMessage`**(): string
  - **`advise`**($dnis): void

#### `Intranet\Services\Notifications\send`
- Metodes:
  - **`setExplanation`**(?string $message): void
  - **`setLink`**(): void
  - **`resolveRecipients`**(): array
  - **`buildMessage`**(): array
  - **`send`**(): void


### `app/Services/Notifications/AdviseTeacher.php`

#### `Intranet\Services\Notifications\AdviseTeacher`
- Metodes:
  - **`__construct`**(private ?NotificationService $notificationService = null, private ?ProfesorService $profesorService = null, private ?HorarioService $horarioService = null, private ?GrupoService $grupoService = null)

#### `Intranet\Services\Notifications\profesorService`
- Metodes: cap

#### `Intranet\Services\Notifications\horarioService`
- Metodes: cap

#### `Intranet\Services\Notifications\grupoService`
- Metodes: cap

#### `Intranet\Services\Notifications\advise`
- Metodes:
  - **`advise`**(object $elemento, ?string $mensaje = null, ?string $idEmisor = null, mixed $emisor = null): void

    API nova injectable.
  - **`affectedGroups`**(object $elemento, string $idProfesor): Collection
  - **`sendTutorEmail`**(object $elemento): void
  - **`horarioAltreGrup`**(object $elemento, string $professorId): Collection
  - **`teachersAffected`**(Collection $grupos, string $emisor): Collection
  - **`hoursAffected`**(object $elemento): Collection


### `app/Services/Notifications/ConfirmAndSend.php`

#### `Intranet\Services\Notifications\ConfirmAndSend`
- Metodes:
  - **`render`**($model, $id, $message=null, $route=null, $back=null)


### `app/Services/Notifications/NotificationService.php`

#### `Intranet\Services\Notifications\NotificationService`
- Metodes:
  - **`__construct`**(?callable $findAlumno = null, ?callable $findProfesor = null, ?callable $hasTable = null, ?callable $fechaProvider = null)

#### `Intranet\Services\Notifications\findAlumno`
- Metodes:
  - **`receptor`**($id)
  - **`emisor`**($emisor)
  - **`send`**($id, $mensaje, $enlace = '#', $emisor = null)


### `app/Services/School/ActividadParticipantsService.php`

#### `Intranet\Services\School\ActividadParticipantsService`
Gestiona participants i coordinació d'activitats.

- Metodes:
  - **`assignInitialParticipants`**(Actividad $actividad, ?string $dni = null): void

    Assigna coordinador i grup per defecte en crear l'activitat.

#### `Intranet\Services\School\largestByTutor`
- Metodes:
  - **`addGroup`**(int|string $actividadId, string $groupId): void

    Afig un grup sense desassignar els existents.
  - **`removeGroup`**(int|string $actividadId, string $groupId): void

    Esborra un grup del pivot.
  - **`addProfesor`**(int|string $actividadId, string $profesorId): void

    Afig un professor sense duplicar pivots.
  - **`removeProfesor`**(int|string $actividadId, string $profesorId): bool

    Esborra un professor i, si era coordinador, en reassigna un de nou.
  - **`assignCoordinator`**(int|string $actividadId, string $profesorId): bool

    Marca un únic coordinador per a l'activitat.


### `app/Services/School/CotxeAccessService.php`

#### `Intranet\Services\School\CotxeAccessService`
- Metodes:
  - **`recentAccessWithin`**(string $matricula, int $seconds): bool

    Comprova si hi ha hagut un accés recent d'una matrícula.
  - **`registrarAcces`**(string $matricula, bool $autoritzat, bool $porta_oberta, string $device = null, string $tipus = null): void

    Registra un nou accés al pàrquing.
  - **`obrirIPorta`**(): bool

    Envia les ordres d'obrir i tancar la porta al dispositiu IoT.


### `app/Services/School/ExpedienteWorkflowService.php`

#### `Intranet\Services\School\ExpedienteWorkflowService`
Fluxos de negoci d'estat per a expedients.

- Metodes:
  - **`__construct`**(?ExpedienteService $expedienteService = null)
  - **`expedients`**(): ExpedienteService

#### `Intranet\Services\School\expedienteService`
- Metodes:
  - **`authorizePending`**(): void

    Autoritza en lot tots els expedients pendents (estat 1 -> 2).
  - **`init`**(int|string $id): bool

    Inicialitza un expedient.
  - **`passToOrientation`**(int|string $id): bool

    Passa l'expedient a orientació tancada (estat 5) i fixa data de solució.
  - **`assignCompanion`**(int|string $id, ?string $idAcompanyant): bool

    Assigna professor acompanyant i passa l'expedient a estat 5.


### `app/Services/School/FaltaReportService.php`

#### `Intranet\Services\School\FaltaReportService`
- Metodes:
  - **`getComunicacioElements`**(Carbon $desde, Carbon $hasta)
  - **`getMensualElements`**(Carbon $desde, Carbon $hasta)
  - **`markPrinted`**(Carbon $hasta): void
  - **`nameFile`**(): string
  - **`buildQuery`**(Carbon $desde, Carbon $hasta, string $estadoUpper)


### `app/Services/School/ItacaService.php`

#### `Intranet\Services\School\ItacaService`
- Metodes:
  - **`__construct`**(string $dni, string $password, $selenium = null, bool $validateDriver = true)
  - **`close`**()
  - **`goToLlist`**()
  - **`processActivitat`**(Actividad $activitat): bool
  - **`processFalta`**(Falta_itaca $falta): bool
  - **`closeNoticias`**()


### `app/Services/School/ModuloGrupoService.php`

#### `Intranet\Services\School\ModuloGrupoService`
- Metodes:
  - **`hasSeguimiento`**(Modulo_grupo $moduloGrupo): bool
  - **`profesorNombres`**(Modulo_grupo $moduloGrupo): string
  - **`programacioLink`**(Modulo_grupo $moduloGrupo): string
  - **`profesorIds`**(Modulo_grupo $moduloGrupo): Collection
  - **`misModulos`**(string $dni, ?string $modulo = null): array
  - **`buildProgramacioUrl`**(string $centerId, string $cycleId, string $moduleCode, string $turn): string


### `app/Services/School/ReunionService.php`

#### `Intranet\Services\School\ReunionService`
- Metodes:
  - **`makeMessage`**(Reunion $reunion): string
  - **`addProfesor`**(Reunion $reunion, string $idProfesor): void
  - **`removeProfesor`**(Reunion $reunion, string $idProfesor): void
  - **`addAlumno`**(Reunion $reunion, string $idAlumno, int $capacitats): void
  - **`removeAlumno`**(Reunion $reunion, string $idAlumno): void
  - **`notify`**(Reunion $reunion): void


### `app/Services/School/SecretariaService.php`

#### `Intranet\Services\School\SecretariaService`
- Metodes:
  - **`__construct`**()
  - **`uploadFile`**($document)
  - **`error`**($response)


### `app/Services/School/SignaturaStatusService.php`

#### `Intranet\Services\School\SignaturaStatusService`
- Metodes:
  - **`estat`**(Signatura $sig): string
  - **`cssClass`**(Signatura $sig): string
  - **`yesNo`**(bool|int $value): string
  - **`estatA1`**(Signatura $sig): string
  - **`estatA2`**(Signatura $sig): string
  - **`estatA3`**(Signatura $sig): string


### `app/Services/School/TaskFileService.php`

#### `Intranet\Services\School\TaskFileService`
- Metodes:
  - **`store`**(UploadedFile $file, Task $task): ?string


### `app/Services/School/TaskValidationService.php`

#### `Intranet\Services\School\TaskValidationService`
- Metodes:
  - **`resolve`**(?string $action, ?string $dni = null): int
  - **`avalPrg`**(): int
  - **`entrPrg`**(): int
  - **`segAval`**(string $dni): int

#### `Intranet\Services\School\misModulos`
- Metodes:
  - **`actAval`**(string $dni): int
  - **`actaDel`**(string $dni): int
  - **`actaFse`**(string $dni): int
  - **`infDept`**(string $dni): int


### `app/Services/School/TeacherSubstitutionService.php`

#### `Intranet\Services\School\TeacherSubstitutionService`
Gestiona lògica d'alta/baixa de professorat amb cadena de substitucions.

- Metodes:
  - **`__construct`**(?ProfesorService $profesorService = null, ?HorarioService $horarioService = null, ?GrupoService $grupoService = null, ?AlumnoFctService $alumnoFctService = null)

#### `Intranet\Services\School\horarioService`
- Metodes: cap

#### `Intranet\Services\School\grupoService`
- Metodes: cap

#### `Intranet\Services\School\alumnoFctService`
- Metodes: cap

#### `Intranet\Services\School\markLeave`
- Metodes:
  - **`markLeave`**(string $idProfesor, string $fecha): void

    Marca un professor com de baixa en una data.
  - **`reactivate`**(string $idProfesor): void

    Reactiva un professor i reverteix canvis dels substituts en cadena.
  - **`changeWithSubstitute`**(Profesor $profesorAlta, Profesor $sustituto): void

    Mou càrrega docent/administrativa del substitut al professor original.
  - **`markAssistenceMeetings`**(string $dniProfesor, Asistencia $meeting): void

    Marca assistència pendent del professor reactiu a una reunió.


### `app/Services/Signature/DigitalSignatureService.php`

#### `Intranet\Services\Signature\DigitalSignatureService`
- Metodes:
  - **`readCertificat`**($certificat, $password): array

    Llig i valida un certificat PKCS#12 amb OpenSSL.
  - **`readCertificate`**($certificat, $password): array

    Llig i valida un certificat PKCS#12 amb OpenSSL.
  - **`cryptCertificate`**($certificat, $fileName, $password): void
  - **`encryptCertificate`**($certificat, $fileName, $password): void
  - **`decryptCertificate`**($fileName, $password): string
  - **`decryptUserCertificate`**($fileName, $password): string
  - **`decryptCertificateUser`**($decrypt, $user): ?string
  - **`decryptUserCertificateInstance`**($decrypt, $user): ?string
  - **`deleteCertificate`**($user): void
  - **`removeCertificate`**($user): void
  - **`validateUserSign`**($file)
  - **`validateUserSignature`**($file, $dni = null): bool
  - **`sign`**($file, $newFile, $coordx, $coordy, $certPath, $certPassword): void
  - **`signDocument`**($file, $newFile, $coordx, $coordy, $certPath, $certPassword): void
  - **`signWithJSignPdf`**($file, $newFile, $coordx, $coordy, $certPath, $certPassword): void
  - **`buildJSignPdfCommand`**(string $java, string $jar, string $inputFile, string $outputDir, float $coordx, float $coordy, float $width, float $height, int $page, string $certPath, string $certPassword, bool $append, string $visibleText, ?string $bgPath, ?string $bgScale, ?string $imgPath): array
  - **`resolveJSignPdfOutputFile`**(string $outputDir, string $inputFile): ?string
  - **`stringifyCommand`**(array $command): string
  - **`prepareBackgroundImage`**(string $sourcePath, int $threshold): ?string
  - **`composeLogoBackground`**(string $sourcePath, int $boxWidth, int $boxHeight, float $scale, int $topPadding, float $maxHeightRatio): ?string
  - **`getLastPageNumber`**(string $inputFile, int $fallback): int
  - **`buildVisibleSignatureText`**(string $certPath, string $certPassword): string
  - **`normalizePdf`**(string $inputFile): string
  - **`getEncrypter`**($password): Encrypter
  - **`fileNameCrypt`**($fileName): string
  - **`fileNameDeCrypt`**($fileName): string


### `app/Services/Signature/SignaturaService.php`

#### `Intranet\Services\Signature\SignaturaService`
- Metodes:
  - **`exec`**($dni, $style='', $ratio=1, $notFound=null)

#### `Intranet\Services\Signature\find`
- Metodes:
  - **`getFile`**($dni)


### `app/Services/UI/AlertLogger.php`

#### `Intranet\Services\UI\AlertLogger`
- Metodes:
  - **`info`**($message, $channel='sao')
  - **`warning`**($message, $channel='sao')
  - **`error`**($message, $channel='sao')
  - **`log`**($message, $level, $channel)


### `app/Services/UI/AppAlert.php`

#### `Intranet\Services\UI\AppAlert`
Façana pròpia d'alertes de la intranet.

- Metodes:
  - **`info`**(string $message): void

    Mostra un missatge informatiu.
  - **`warning`**(string $message): void

    Mostra un missatge d'avís.
  - **`danger`**(string $message): void

    Mostra un missatge d'error.
  - **`success`**(string $message): void

    Mostra un missatge d'èxit.
  - **`error`**(string $message): void

    Mostra un missatge d'error (alias de danger per compatibilitat).
  - **`message`**(string $message, string $level = 'info'): void

    Mostra un missatge amb nivell explícit.
  - **`render`**(): HtmlString

    Renderitza i buida les alertes pendents de la sessió.
  - **`send`**(string $level, string $message): void

    Encapsula l'enviament real de l'alerta.


### `app/Services/UI/FieldBuilder.php`

#### `Intranet\Services\UI\FieldBuilder`
Builder compatible amb `Field::*` per desacoblar Styde Html.

- Metodes:
  - **`__construct`**(CollectiveFormBuilder $form, Translator $lang, ViewFactory $view)

    /
  - **`setAbbreviations`**(array $abbreviations): void

    /
  - **`setCssClasses`**(array $cssClasses): void

    /
  - **`setTemplates`**(array $templates): void

    /
  - **`__call`**(string $method, array $parameters): string

    Redirigix qualsevol mètode desconegut a un build dinàmic per tipus.
  - **`text`**(string $name, mixed $value = null, array $attributes = [], array $extra = []): string

    /
  - **`textarea`**(string $name, mixed $value = null, array $attributes = [], array $extra = []): string

    /
  - **`hidden`**(string $name, mixed $value = null, array $attributes = []): string

    /
  - **`file`**(string $name, array $attributes = [], array $extra = []): string

    /
  - **`select`**(string $name, ?array $options = [], mixed $selected = null, array $attributes = [], array $extra = []): string

    /
  - **`checkbox`**(string $name, mixed $value = 1, mixed $selected = null, array $attributes = [], array $extra = []): string

    /
  - **`checkboxes`**(string $name, array $options = [], mixed $selected = null, array $attributes = [], array $extra = []): string

    /
  - **`radios`**(string $name, array $options = [], mixed $selected = null, array $attributes = [], array $extra = []): string

    /
  - **`build`**(string $type, string $name, mixed $value = null, array $attributes = [], array $extra = [], mixed $options = null): string

    /
  - **`doBuild`**(string $type, string $name, mixed $value = null, array $attributes = [], array $extra = [], mixed $options = null): string

    /
  - **`resolveFieldTemplate`**(?string $customTemplate, string $defaultTemplate): string

    /
  - **`getDefaultTemplate`**(string $type): string

    /
  - **`getCustomTemplate`**(array $attributes): ?string

    /
  - **`getHtmlName`**(string $name): string

    /
  - **`getHtmlId`**(string $name, array $attributes): string

    /
  - **`getRequired`**(array $attributes): bool

    /
  - **`getLabel`**(string $name, array $attributes = []): string

    /
  - **`getDefaultClasses`**(string $type): string

    /
  - **`getClasses`**(string $type, array $attributes = [], array $errors = []): string

    /
  - **`getControlErrors`**(string $name): array

    /
  - **`getHtmlAttributes`**(string $type, array $attributes, array $errors, string $htmlId): array

    /
  - **`replaceAttributes`**(array $attributes): array

    /
  - **`checkAccess`**(array $attributes): bool

    /
  - **`buildControl`**(string $type, string $name, mixed $value, array $attributes, mixed $options, string $htmlName, bool $hasErrors = false): string

    /
  - **`normalizeOptionsArray`**(mixed $options): array

    /
  - **`getOptionsList`**(string $name, array $options): array

    /
  - **`getOptionsFromModel`**(string $name): array

    /
  - **`addEmptyOption`**(string $name, array $options, array &$attributes): array

    /
  - **`getEmptyOption`**(string $name): string|false

    /
  - **`renderRadioCollection`**(string $name, array $options, mixed $selected, array $attributes): string

    /
  - **`renderCheckboxCollection`**(string $name, array $options, mixed $selected, array $attributes, bool $hasErrors = false): string

    /


### `app/Services/UI/FormBuilder.php`

#### `Intranet\Services\UI\FormBuilder`
- Metodes:
  - **`__construct`**($elemento, $formFields = null)
  - **`getElemento`**(): mixed

    /
  - **`getDefault`**(): array

    /
  - **`render`**(string $method = 'POST', ? string $afterView = null): View
  - **`modal`**()
  - **`fillDefaultOptionsToForm`**($formFields)
  - **`translate`**($key)
  - **`aspect`**(&$parametres, $originalType)
  - **`fillDefaultOptionsFromModel`**(): array

    /


### `app/Services/UI/NavigationService.php`

#### `Intranet\Services\UI\NavigationService`
- Metodes:
  - **`customBack`**($default = '/home')
  - **`dropFromHistory`**()
  - **`addToHistory`**()
  - **`getPreviousUrl`**($default = '/')



## Requests

### `app/Http/Requests/ActividadRequest.php`

#### `Intranet\Http\Requests\ActividadRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/AlumnoFctUpdateRequest.php`

#### `Intranet\Http\Requests\AlumnoFctUpdateRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/AlumnoGrupoUpdateRequest.php`

#### `Intranet\Http\Requests\AlumnoGrupoUpdateRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/AlumnoPerfilUpdateRequest.php`

#### `Intranet\Http\Requests\AlumnoPerfilUpdateRequest`
- Metodes:
  - **`rules`**()


### `app/Http/Requests/AlumnoResultadoStoreRequest.php`

#### `Intranet\Http\Requests\AlumnoResultadoStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/AlumnoUpdateRequest.php`

#### `Intranet\Http\Requests\AlumnoUpdateRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/ArticuloLoteRequest.php`

#### `Intranet\Http\Requests\ArticuloLoteRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/ArticuloRequest.php`

#### `Intranet\Http\Requests\ArticuloRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/AuthPerfilUpdateRequest.php`

#### `Intranet\Http\Requests\AuthPerfilUpdateRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/CentroRequest.php`

#### `Intranet\Http\Requests\CentroRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/CicloDualRequest.php`

#### `Intranet\Http\Requests\CicloDualRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/CicloRequest.php`

#### `Intranet\Http\Requests\CicloRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/ColaboracionRequest.php`

#### `Intranet\Http\Requests\ColaboracionRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.
  - **`messages`**(): array<string,

    Missatges curts per al formulari d'edició de col·laboració.


### `app/Http/Requests/ColaboradorRequest.php`

#### `Intranet\Http\Requests\ColaboradorRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/ComisionRequest.php`

#### `Intranet\Http\Requests\ComisionRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/CotxeRequest.php`

#### `Intranet\Http\Requests\CotxeRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/CursoRequest.php`

#### `Intranet\Http\Requests\CursoRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/DepartamentoRequest.php`

#### `Intranet\Http\Requests\DepartamentoRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/DesdeHastaRequest.php`

#### `Intranet\Http\Requests\DesdeHastaRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/DocumentoStoreRequest.php`

#### `Intranet\Http\Requests\DocumentoStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/DualRequest.php`

#### `Intranet\Http\Requests\DualRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/EmpresaCentroRequest.php`

#### `Intranet\Http\Requests\EmpresaCentroRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/EmpresaRequest.php`

#### `Intranet\Http\Requests\EmpresaRequest`
- Metodes:
  - **`authorize`**(): bool

    Determina si l'usuari està autoritzat a fer la petició.
  - **`rules`**(): array

    Regles de validació del formulari d'empresa.
  - **`prepareForValidation`**(): void

    Normalitza dades abans de validar/guardar.


### `app/Http/Requests/EspacioRequest.php`

#### `Intranet\Http\Requests\EspacioRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/ExpedienteRequest.php`

#### `Intranet\Http\Requests\ExpedienteRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/FaltaRequest.php`

#### `Intranet\Http\Requests\FaltaRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/FctConvalidacionStoreRequest.php`

#### `Intranet\Http\Requests\FctConvalidacionStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/FctStoreRequest.php`

#### `Intranet\Http\Requests\FctStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/FctUpdateRequest.php`

#### `Intranet\Http\Requests\FctUpdateRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/FicharStoreRequest.php`

#### `Intranet\Http\Requests\FicharStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/GTProfesorRequest.php`

#### `Intranet\Http\Requests\GTProfesorRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.
  - **`messages`**(): array


### `app/Http/Requests/GrupoTrabajoRequest.php`

#### `Intranet\Http\Requests\GrupoTrabajoRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/HorarioUpdateRequest.php`

#### `Intranet\Http\Requests\HorarioUpdateRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/ImportStoreRequest.php`

#### `Intranet\Http\Requests\ImportStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/IncidenciaRequest.php`

#### `Intranet\Http\Requests\IncidenciaRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/IpGuardiaRequest.php`

#### `Intranet\Http\Requests\IpGuardiaRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/LoteRequest.php`

#### `Intranet\Http\Requests\LoteRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/ModuloCicloRequest.php`

#### `Intranet\Http\Requests\ModuloCicloRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/ModuloRequest.php`

#### `Intranet\Http\Requests\ModuloRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/MyMailStoreRequest.php`

#### `Intranet\Http\Requests\MyMailStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/OptionStoreRequest.php`

#### `Intranet\Http\Requests\OptionStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/OrdenReunionStoreRequest.php`

#### `Intranet\Http\Requests\OrdenReunionStoreRequest`
Validació per a l'alta d'ordres de reunió.

- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/OrdenTrabajoRequest.php`

#### `Intranet\Http\Requests\OrdenTrabajoRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/PPollRequest.php`

#### `Intranet\Http\Requests\PPollRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/PasswordRequest.php`

#### `Intranet\Http\Requests\PasswordRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/PerfilFilesRequest.php`

#### `Intranet\Http\Requests\PerfilFilesRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.
  - **`messages`**()


### `app/Http/Requests/ProfesorPerfilUpdateRequest.php`

#### `Intranet\Http\Requests\ProfesorPerfilUpdateRequest`
- Metodes:
  - **`rules`**()


### `app/Http/Requests/ProfesorUpdateRequest.php`

#### `Intranet\Http\Requests\ProfesorUpdateRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/ProyectoRequest.php`

#### `Intranet\Http\Requests\ProyectoRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/ResultadoStoreRequest.php`

#### `Intranet\Http\Requests\ResultadoStoreRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/ResultadoUpdateRequest.php`

#### `Intranet\Http\Requests\ResultadoUpdateRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/ReunionStoreRequest.php`

#### `Intranet\Http\Requests\ReunionStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/ReunionUpdateRequest.php`

#### `Intranet\Http\Requests\ReunionUpdateRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/SendAvaluacioEmailStoreRequest.php`

#### `Intranet\Http\Requests\SendAvaluacioEmailStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/SettingRequest.php`

#### `Intranet\Http\Requests\SettingRequest`
- Metodes:
  - **`authorize`**()

    Determina si l'usuari autenticat pot modificar settings.
  - **`rules`**(): array<string,

    Retorna les regles de validació del formulari de settings.


### `app/Http/Requests/SignaturaStoreRequest.php`

#### `Intranet\Http\Requests\SignaturaStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/SolicitudRequest.php`

#### `Intranet\Http\Requests\SolicitudRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/StoreBustiaRequest.php`

#### `Intranet\Http\Requests\StoreBustiaRequest`
- Metodes:
  - **`authorize`**(): bool
  - **`rules`**(): array
  - **`messages`**(): array


### `app/Http/Requests/TaskRequest.php`

#### `Intranet\Http\Requests\TaskRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/TeacherImportStoreRequest.php`

#### `Intranet\Http\Requests\TeacherImportStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/TipoActividadRequest.php`

#### `Intranet\Http\Requests\TipoActividadRequest`
- Metodes:
  - **`authorize`**(): bool
  - **`rules`**(): array


### `app/Http/Requests/TipoActividadUpdateRequest.php`

#### `Intranet\Http\Requests\TipoActividadUpdateRequest`
- Metodes:
  - **`authorize`**(): bool
  - **`rules`**(): array


### `app/Http/Requests/TipoIncidenciaRequest.php`

#### `Intranet\Http\Requests\TipoIncidenciaRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.


### `app/Http/Requests/TutoriaGrupoStoreRequest.php`

#### `Intranet\Http\Requests\TutoriaGrupoStoreRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/TutoriaGrupoUpdateRequest.php`

#### `Intranet\Http\Requests\TutoriaGrupoUpdateRequest`
- Metodes:
  - **`authorize`**()
  - **`rules`**()


### `app/Http/Requests/ValoracionRequest.php`

#### `Intranet\Http\Requests\ValoracionRequest`
- Metodes:
  - **`authorize`**(): bool

    Determine if the user is authorized to make this request.
  - **`rules`**(): array

    Get the validation rules that apply to the request.



## Policies

### `app/Policies/ActividadPolicy.php`

#### `Intranet\Policies\ActividadPolicy`
Policy d'autorització per al flux d'activitats.

- Metodes:
  - **`viewAny`**($user): bool

    /
  - **`create`**($user): bool

    /
  - **`view`**($user, Actividad $actividad): bool

    /
  - **`update`**($user, Actividad $actividad): bool

    /
  - **`manageParticipants`**($user, Actividad $actividad): bool

    /
  - **`notify`**($user, Actividad $actividad): bool

    /
  - **`canManage`**($user, Actividad $actividad): bool

    Regla de gestió: coordinador de l'activitat o rol elevat.


### `app/Policies/ArticuloPolicy.php`

#### `Intranet\Policies\ArticuloPolicy`
Policy d'autorització per a catàleg d'articles.

- Metodes:
  - **`view`**($user, Articulo $articulo): bool

    Determina si l'usuari pot veure articles.
  - **`create`**($user): bool

    Determina si l'usuari pot crear articles.
  - **`update`**($user, Articulo $articulo): bool

    Determina si l'usuari pot actualitzar articles.
  - **`delete`**($user, Articulo $articulo): bool

    Determina si l'usuari pot eliminar articles.


### `app/Policies/CicloPolicy.php`

#### `Intranet\Policies\CicloPolicy`
Policy d'autorització per a cicles.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear cicles.
  - **`update`**($user, Ciclo $ciclo): bool

    Determina si l'usuari pot actualitzar cicles.
  - **`delete`**($user, Ciclo $ciclo): bool

    Determina si l'usuari pot eliminar cicles.


### `app/Policies/ColaboracionPolicy.php`

#### `Intranet\Policies\ColaboracionPolicy`
- Metodes:
  - **`create`**($user): bool
  - **`update`**($user, Colaboracion $colaboracion): bool
  - **`isTutor`**($user): bool


### `app/Policies/ComisionPolicy.php`

#### `Intranet\Policies\ComisionPolicy`
Policy d'autorització per a comissions de servei.

- Metodes:
  - **`create`**($user): bool

    /
  - **`update`**($user, Comision $comision): bool

    /
  - **`view`**($user, Comision $comision): bool

    /
  - **`manageFct`**($user, Comision $comision): bool

    /
  - **`isOwner`**($user, Comision $comision): bool

    Regla de propietat de la comissió.


### `app/Policies/Concerns/InteractsWithProfesorOwnership.php`

#### `Intranet\Policies\Concerns\InteractsWithProfesorOwnership`
Utilitats de policy per a regles basades en propietari professor i rols elevats.

- Metodes:
  - **`hasProfesorIdentity`**($user): bool

    Comprova que l'usuari tinga identitat de professor.
  - **`hasRole`**($user, string $roleConfigKey): bool

    Comprova si l'usuari té un rol concret (bitmask de rols).
  - **`isDirectionOrAdmin`**($user): bool

    Comprova si l'usuari és direcció o administració.
  - **`ownsOrIsDirectionOrAdmin`**($user, string $ownerDni): bool

    Regla genèrica: propietari professor o rols elevats.


### `app/Policies/CotxePolicy.php`

#### `Intranet\Policies\CotxePolicy`
Policy d'autorització per a vehicles de professorat.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear vehicles.
  - **`view`**($user, Cotxe $cotxe): bool

    Determina si l'usuari pot veure vehicles.
  - **`update`**($user, Cotxe $cotxe): bool

    Determina si l'usuari pot actualitzar vehicles.
  - **`delete`**($user, Cotxe $cotxe): bool

    Determina si l'usuari pot eliminar vehicles.


### `app/Policies/CursoPolicy.php`

#### `Intranet\Policies\CursoPolicy`
Policy d'autorització per a cursos.

- Metodes:
  - **`viewAny`**($user): bool

    Determina si l'usuari pot accedir als llistats de cursos.
  - **`create`**($user): bool

    Determina si l'usuari pot crear cursos.
  - **`update`**($user, Curso $curso): bool

    Determina si l'usuari pot actualitzar cursos.
  - **`delete`**($user, Curso $curso): bool

    Determina si l'usuari pot eliminar cursos.


### `app/Policies/DepartamentoPolicy.php`

#### `Intranet\Policies\DepartamentoPolicy`
Policy d'autorització per a departaments.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear departaments.
  - **`update`**($user, Departamento $departamento): bool

    Determina si l'usuari pot actualitzar departaments.
  - **`delete`**($user, Departamento $departamento): bool

    Determina si l'usuari pot eliminar departaments.


### `app/Policies/DocumentoPolicy.php`

#### `Intranet\Policies\DocumentoPolicy`
Policy d'autorització per a la gestió de documents.

- Metodes:
  - **`viewAny`**($user): bool

    Determina si l'usuari pot accedir als panells de llistat documental.
  - **`create`**($user): bool

    Determina si l'usuari pot crear documents.
  - **`view`**($user, Documento $documento): bool

    Determina si l'usuari pot veure documents.
  - **`update`**($user, Documento $documento): bool

    Determina si l'usuari pot actualitzar documents.
  - **`delete`**($user, Documento $documento): bool

    Determina si l'usuari pot eliminar documents.
  - **`hasIdentity`**($user): bool

    /


### `app/Policies/EmpresaPolicy.php`

#### `Intranet\Policies\EmpresaPolicy`
- Metodes:
  - **`viewAny`**($user): bool

    Determina si l'usuari pot accedir als llistats d'empreses.
  - **`create`**($user): bool
  - **`update`**($user, Empresa $empresa): bool
  - **`canMutate`**($user): bool


### `app/Policies/EspacioPolicy.php`

#### `Intranet\Policies\EspacioPolicy`
Policy d'autorització per a espais.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear espais.
  - **`update`**($user, Espacio $espacio): bool

    Determina si l'usuari pot actualitzar espais.
  - **`delete`**($user, Espacio $espacio): bool

    Determina si l'usuari pot eliminar espais.
  - **`printBarcode`**($user, Espacio $espacio): bool

    Determina si l'usuari pot imprimir codis de barres de l'espai.


### `app/Policies/ExpedientePolicy.php`

#### `Intranet\Policies\ExpedientePolicy`
Policy d'autorització per a expedients.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear expedients.
  - **`view`**($user, Expediente $expediente): bool

    Determina si l'usuari pot veure expedients.
  - **`update`**($user, Expediente $expediente): bool

    Determina si l'usuari pot actualitzar expedients.
  - **`delete`**($user, Expediente $expediente): bool

    Determina si l'usuari pot eliminar expedients.


### `app/Policies/FaltaPolicy.php`

#### `Intranet\Policies\FaltaPolicy`
Policy d'autorització per a la gestió de faltes.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear una falta.
  - **`view`**($user, Falta $falta): bool

    Determina si l'usuari pot veure una falta.
  - **`update`**($user, Falta $falta): bool

    Determina si l'usuari pot actualitzar una falta.
  - **`delete`**($user, Falta $falta): bool

    Determina si l'usuari pot eliminar una falta.


### `app/Policies/FctPolicy.php`

#### `Intranet\Policies\FctPolicy`
Policy d'autorització per a les operacions de FCT.

- Metodes:
  - **`viewAny`**($user): bool

    Determina si l'usuari pot accedir al panell general de FCT.
  - **`create`**($user): bool

    Determina si l'usuari pot crear una FCT.
  - **`update`**($user, Fct $fct): bool

    Determina si l'usuari pot actualitzar una FCT.
  - **`delete`**($user, Fct $fct): bool

    Determina si l'usuari pot eliminar una FCT.
  - **`manageAval`**($user): bool

    Determina si l'usuari pot gestionar avaluacions FCT (apte/no apte/projecte/inserció).
  - **`requestActa`**($user): bool

    Determina si l'usuari pot demanar actes d'avaluació.
  - **`sendA56`**($user): bool

    Determina si l'usuari pot enviar annexos A56 a secretaria.
  - **`viewStats`**($user): bool

    Determina si l'usuari pot consultar estadístiques d'avaluació FCT.
  - **`managePendingActa`**($user): bool

    Determina si l'usuari pot validar/rebutjar actes pendents de FCT.
  - **`manageFctControl`**($user): bool

    Determina si l'usuari pot gestionar el panell de control de dual.
  - **`canMutate`**($user): bool

    Regla comuna de permisos per a mutacions de FCT.


### `app/Policies/GrupoTrabajoPolicy.php`

#### `Intranet\Policies\GrupoTrabajoPolicy`
Policy d'autorització per a grups de treball.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear grups de treball.
  - **`update`**($user, GrupoTrabajo $grupoTrabajo): bool

    Determina si l'usuari pot actualitzar un grup de treball.
  - **`delete`**($user, GrupoTrabajo $grupoTrabajo): bool

    Determina si l'usuari pot eliminar un grup de treball.
  - **`manageMembers`**($user, GrupoTrabajo $grupoTrabajo): bool

    Determina si l'usuari pot gestionar membres/coordinador del grup.
  - **`viewMembers`**($user, GrupoTrabajo $grupoTrabajo): bool

    Determina si l'usuari pot veure els membres del grup.
  - **`isOwner`**($user, GrupoTrabajo $grupoTrabajo): bool

    /
  - **`isMember`**($user, GrupoTrabajo $grupoTrabajo): bool

    /


### `app/Policies/ImportRunPolicy.php`

#### `Intranet\Policies\ImportRunPolicy`
- Metodes:
  - **`manage`**($user): bool
  - **`viewAny`**($user): bool
  - **`view`**($user, ImportRun $importRun): bool


### `app/Policies/IncidenciaPolicy.php`

#### `Intranet\Policies\IncidenciaPolicy`
Policy d'autorització per a incidències.

- Metodes:
  - **`viewAny`**($user): bool

    Determina si l'usuari pot accedir als llistats d'incidències.
  - **`create`**($user): bool

    Determina si l'usuari pot crear incidències.
  - **`view`**($user, Incidencia $incidencia): bool

    Determina si l'usuari pot veure una incidència.
  - **`update`**($user, Incidencia $incidencia): bool

    Determina si l'usuari pot actualitzar una incidència.
  - **`delete`**($user, Incidencia $incidencia): bool

    Determina si l'usuari pot eliminar una incidència.
  - **`ownsOrIsResponsible`**($user, Incidencia $incidencia): bool

    Regla: creador o responsable.


### `app/Policies/IpGuardiaPolicy.php`

#### `Intranet\Policies\IpGuardiaPolicy`
Policy d'autorització per a IPs de guàrdia.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear IPs.
  - **`update`**($user, IpGuardia $ipGuardia): bool

    Determina si l'usuari pot actualitzar IPs.
  - **`delete`**($user, IpGuardia $ipGuardia): bool

    Determina si l'usuari pot eliminar IPs.


### `app/Policies/LotePolicy.php`

#### `Intranet\Policies\LotePolicy`
Policy d'autorització per a lots d'inventari.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear lots.
  - **`update`**($user, Lote $lote): bool

    Determina si l'usuari pot actualitzar lots.
  - **`delete`**($user, Lote $lote): bool

    Determina si l'usuari pot eliminar lots.


### `app/Policies/MaterialBajaPolicy.php`

#### `Intranet\Policies\MaterialBajaPolicy`
Policy d'autorització per a gestió de baixes de material.

- Metodes:
  - **`update`**($user, MaterialBaja $materialBaja): bool

    Determina si l'usuari pot actualitzar una baixa de material.
  - **`delete`**($user, MaterialBaja $materialBaja): bool

    Determina si l'usuari pot eliminar una baixa de material.
  - **`recover`**($user, MaterialBaja $materialBaja): bool

    Determina si l'usuari pot recuperar material des de baixa.


### `app/Policies/MenuPolicy.php`

#### `Intranet\Policies\MenuPolicy`
Policy d'autorització per a opcions de menú.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear menús.
  - **`update`**($user, Menu $menu): bool

    Determina si l'usuari pot actualitzar menús.
  - **`delete`**($user, Menu $menu): bool

    Determina si l'usuari pot eliminar menús.


### `app/Policies/ModuloCicloPolicy.php`

#### `Intranet\Policies\ModuloCicloPolicy`
Policy d'autorització per a l'enllaç mòdul-cicle.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear enllaços mòdul-cicle.
  - **`update`**($user, Modulo_ciclo $moduloCiclo): bool

    Determina si l'usuari pot actualitzar enllaços mòdul-cicle.
  - **`delete`**($user, Modulo_ciclo $moduloCiclo): bool

    Determina si l'usuari pot eliminar enllaços mòdul-cicle.


### `app/Policies/OptionPolicy.php`

#### `Intranet\Policies\OptionPolicy`
Policy d'autorització per a opcions de polls.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear opcions.
  - **`delete`**($user, Option $option): bool

    Determina si l'usuari pot eliminar opcions.


### `app/Policies/PPollPolicy.php`

#### `Intranet\Policies\PPollPolicy`
Policy d'autorització per a plantilles de polls.

- Metodes:
  - **`view`**($user, PPoll $ppoll): bool

    Determina si l'usuari pot veure la plantilla.
  - **`create`**($user): bool

    Determina si l'usuari pot crear plantilles.
  - **`update`**($user, PPoll $ppoll): bool

    Determina si l'usuari pot actualitzar plantilles.
  - **`delete`**($user, PPoll $ppoll): bool

    Determina si l'usuari pot eliminar plantilles.


### `app/Policies/ProfesorPolicy.php`

#### `Intranet\Policies\ProfesorPolicy`
Policy d'autorització per a professorat.

- Metodes:
  - **`update`**($user, Profesor $profesor): bool

    Determina si l'usuari pot actualitzar el perfil d'un professor.
  - **`manageQualityFinal`**($user, Profesor $profesor): bool

    Determina si l'usuari pot gestionar la qualitat final (cap de pràctiques).
  - **`manageAttendance`**($user): bool

    Determina si l'usuari pot gestionar incidències de fitxatge/presència.


### `app/Policies/ProjectePolicy.php`

#### `Intranet\Policies\ProjectePolicy`
Policy d'autorització per al flux de propostes de projecte.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear propostes dins del seu grup de tutoria.
  - **`view`**($user, Projecte $projecte): bool

    Determina si l'usuari pot vore una proposta del seu grup de tutoria.
  - **`update`**($user, Projecte $projecte): bool

    Determina si l'usuari pot actualitzar una proposta del seu grup.
  - **`delete`**($user, Projecte $projecte): bool

    Determina si l'usuari pot eliminar una proposta del seu grup.
  - **`check`**($user, Projecte $projecte): bool

    Determina si l'usuari pot validar una proposta del seu grup.
  - **`send`**($user): bool

    Determina si l'usuari pot enviar projectes del seu grup.
  - **`createActa`**($user): bool

    Determina si l'usuari pot crear l'acta de valoració del seu grup.
  - **`createDefenseActa`**($user): bool

    Determina si l'usuari pot crear l'acta de defenses del seu grup.
  - **`isTutorOfAnyGroup`**($user): bool

    /

#### `Intranet\Policies\byTutorOrSubstitute`
- Metodes: cap


### `app/Policies/ResultadoPolicy.php`

#### `Intranet\Policies\ResultadoPolicy`
Policy d'autorització per a resultats acadèmics.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear resultats.
  - **`view`**($user, Resultado $resultado): bool

    Determina si l'usuari pot veure resultats.
  - **`update`**($user, Resultado $resultado): bool

    Determina si l'usuari pot actualitzar resultats.
  - **`delete`**($user, Resultado $resultado): bool

    Determina si l'usuari pot eliminar resultats.


### `app/Policies/ReunionPolicy.php`

#### `Intranet\Policies\ReunionPolicy`
Policy d'autorització per a la gestió de reunions.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear reunions.
  - **`update`**($user, Reunion $reunion): bool

    Determina si l'usuari pot veure/editar la reunió.
  - **`manageParticipants`**($user, Reunion $reunion): bool

    Determina si l'usuari pot modificar participants de la reunió.
  - **`manageOrder`**($user, Reunion $reunion): bool

    Determina si l'usuari pot gestionar l'orde de reunió.
  - **`notify`**($user, Reunion $reunion): bool

    Determina si l'usuari pot notificar o enviar correu de la reunió.
  - **`manageDepartmentReport`**($user): bool

    Determina si l'usuari pot gestionar l'informe trimestral de departament.
  - **`isOwner`**($user, Reunion $reunion): bool

    /


### `app/Policies/SettingPolicy.php`

#### `Intranet\Policies\SettingPolicy`
Policy d'autorització per a la gestió de settings.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear settings (rol administrador).
  - **`update`**($user, Setting $setting): bool

    Determina si l'usuari pot actualitzar settings (rol administrador).
  - **`delete`**($user, Setting $setting): bool

    Determina si l'usuari pot eliminar settings (rol administrador).
  - **`isAdministrador`**($user): bool

    /


### `app/Policies/SignaturaPolicy.php`

#### `Intranet\Policies\SignaturaPolicy`
Policy d'autorització per a signatures de FCT.

- Metodes:
  - **`manageDirectionPanel`**($user): bool

    Determina si l'usuari pot accedir al panell de signatures de direcció.
  - **`manage`**($user): bool

    Determina si l'usuari pot gestionar fluxos globals de signatures.
  - **`create`**($user): bool

    Determina si l'usuari pot crear signatures.
  - **`view`**($user, Signatura $signatura): bool

    Determina si l'usuari pot veure signatures.
  - **`update`**($user, Signatura $signatura): bool

    Determina si l'usuari pot actualitzar signatures.
  - **`delete`**($user, Signatura $signatura): bool

    Determina si l'usuari pot eliminar signatures.


### `app/Policies/SolicitudPolicy.php`

#### `Intranet\Policies\SolicitudPolicy`
Policy d'autorització per a sol·licituds.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear sol·licituds.
  - **`view`**($user, Solicitud $solicitud): bool

    Determina si l'usuari pot veure sol·licituds.
  - **`update`**($user, Solicitud $solicitud): bool

    Determina si l'usuari pot actualitzar sol·licituds.
  - **`activate`**($user, Solicitud $solicitud): bool

    Determina si l'usuari pot activar una sol·licitud d'orientació.
  - **`resolve`**($user, Solicitud $solicitud): bool

    Determina si l'usuari pot resoldre una sol·licitud d'orientació.
  - **`delete`**($user, Solicitud $solicitud): bool

    Determina si l'usuari pot eliminar sol·licituds.


### `app/Policies/TaskPolicy.php`

#### `Intranet\Policies\TaskPolicy`
Policy d'autorització per a tasques.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear tasques (rol administrador).
  - **`update`**($user, Task $task): bool

    Determina si l'usuari pot actualitzar tasques (rol administrador).
  - **`check`**($user, Task $task): bool

    Determina si l'usuari pot marcar/desmarcar una tasca pròpia.
  - **`isAdministrador`**($user): bool

    /


### `app/Policies/TipoActividadPolicy.php`

#### `Intranet\Policies\TipoActividadPolicy`
Policy d'autorització per a tipus d'activitat.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear tipus d'activitat.
  - **`update`**($user, TipoActividad $tipoActividad): bool

    Determina si l'usuari pot actualitzar un tipus d'activitat.
  - **`delete`**($user, TipoActividad $tipoActividad): bool

    Determina si l'usuari pot eliminar un tipus d'activitat.
  - **`isDirectionOrHeadOfDepartment`**($user): bool

    Comprova si l'usuari té rol de direcció/admin o cap de departament.


### `app/Policies/TipoIncidenciaPolicy.php`

#### `Intranet\Policies\TipoIncidenciaPolicy`
Policy d'autorització per a tipus d'incidència.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear tipus d'incidència.
  - **`update`**($user, TipoIncidencia $tipoIncidencia): bool

    Determina si l'usuari pot actualitzar tipus d'incidència.
  - **`delete`**($user, TipoIncidencia $tipoIncidencia): bool

    Determina si l'usuari pot eliminar tipus d'incidència.


### `app/Policies/TutoriaGrupoPolicy.php`

#### `Intranet\Policies\TutoriaGrupoPolicy`
Policy d'autorització per a tutories de grup.

- Metodes:
  - **`create`**($user): bool

    Determina si l'usuari pot crear registres de tutoria-grup.
  - **`view`**($user, TutoriaGrupo $tutoriaGrupo): bool

    Determina si l'usuari pot veure registres de tutoria-grup.
  - **`update`**($user, TutoriaGrupo $tutoriaGrupo): bool

    Determina si l'usuari pot actualitzar registres de tutoria-grup.
  - **`delete`**($user, TutoriaGrupo $tutoriaGrupo): bool

    Determina si l'usuari pot eliminar registres de tutoria-grup.



## Events

### `app/Events/ActivityReport.php`

#### `Intranet\Events\ActivityReport`
Event de registre d'activitat.

- Metodes:
  - **`__construct`**(Model $model)

    /


### `app/Events/FctAlDeleted.php`

#### `Intranet\Events\FctAlDeleted`
Event de baixa d'alumne en FCT.

- Metodes:
  - **`__construct`**(AlumnoFct $fctAl)

    /


### `app/Events/FctCreated.php`

#### `Intranet\Events\FctCreated`
Event de FCT creada.

- Metodes:
  - **`__construct`**(Fct $fct)

    /


### `app/Events/FichaCreated.php`

#### `Intranet\Events\FichaCreated`
Event de fitxatge creat.

- Metodes:
  - **`__construct`**(Falta_profesor $fichaje)

    /


### `app/Events/GrupoCreated.php`

#### `Intranet\Events\GrupoCreated`
Event de grup creat.

- Metodes:
  - **`__construct`**(GrupoTrabajo $grupo)

    /


### `app/Events/ReunionCreated.php`

#### `Intranet\Events\ReunionCreated`
Event de reunio creada.

- Metodes:
  - **`__construct`**(Reunion $reunion)

    /



## Listeners

### `app/Listeners/AsistentesCreate.php`

#### `Intranet\Listeners\AsistentesCreate`
- Metodes:
  - **`__construct`**(?ProfesorService $profesorService = null): void

    Create the event listener.

#### `Intranet\Listeners\queAlumnes`
- Metodes:
  - **`queAlumnes`**(Reunion $reunion): void

    Handle the event.
  - **`assignaAlumnes`**(Reunion $reunion): void

    /
  - **`handle`**(ReunionCreated $event): void

    Handle the event.
  - **`esJefe`**(): array

    /
  - **`asignaProfeReunion`**($profesores, Reunion $reunion): void

    /


### `app/Listeners/ColaboracionColabora.php`

#### `Intranet\Listeners\ColaboracionColabora`
Marca la col·laboració com a finalitzada en crear una FCT.

- Metodes:
  - **`__construct`**(): void

    Create the event listener.
  - **`handle`**(FctCreated $event): void

    Handle the event.


### `app/Listeners/CoordinadorCreate.php`

#### `Intranet\Listeners\CoordinadorCreate`
- Metodes:
  - **`__construct`**(): void

    Create the event listener.
  - **`handle`**(GrupoCreated $event): void

    Handle the event.


### `app/Listeners/FctDelete.php`

#### `Intranet\Listeners\FctDelete`
Elimina la FCT si es queda sense alumnes.

- Metodes:
  - **`__construct`**(): void

    Create the event listener.
  - **`handle`**(FctAlDeleted $event): void

    Handle the event.


### `app/Listeners/LogLastLogin.php`

#### `Intranet\Listeners\LogLastLogin`
- Metodes:
  - **`__construct`**(): void

    Create the event listener.
  - **`handle`**(Login $event): void

    Handle the event.


### `app/Listeners/RegisterActivity.php`

#### `Intranet\Listeners\RegisterActivity`
- Metodes:
  - **`__construct`**(): void

    Create the event listener.
  - **`handle`**(ActivityReport $event): void

    Handle the event.


### `app/Listeners/UpdateLastLoggedAt.php`

#### `Intranet\Listeners\UpdateLastLoggedAt`
- Metodes:
  - **`__construct`**(): void

    Create the event listener.
  - **`handle`**(Logout $event): void

    Handle the event.



## Jobs

### `app/Jobs/RunImportJob.php`

#### `Intranet\Jobs\RunImportJob`
- Metodes:
  - **`__construct`**(private readonly int $importRunId)
  - **`handle`**(): void

#### `Intranet\Jobs\run`
- Metodes: cap


### `app/Jobs/SendEmail.php`

#### `Intranet\Jobs\SendEmail`
- Metodes:
  - **`__construct`**($correo, $remitente, $vista, $elemento, $attach=null)
  - **`handle`**(Mailer $mailer): void

    Execute the job.

#### `Intranet\Jobs\markFctEmailSent`
- Metodes: cap



## Comandes

### `app/Console/Commands/CreateDailyGuards.php`

#### `Intranet\Console\Commands\CreateDailyGuards`
- Metodes:
  - **`__construct`**()

#### `Intranet\Console\Commands\profesorService`
- Metodes: cap

#### `Intranet\Console\Commands\horarioService`
- Metodes: cap

#### `Intranet\Console\Commands\substitutoActual`
- Metodes:
  - **`substitutoActual`**($dni): mixed

    Execute the console command.
  - **`handle`**()
  - **`creaGuardia`**($elemento, $mensaje, $idProfesor = null)
  - **`saveGuardia`**($dades)
  - **`createGuardias`**(): mixed

    /


### `app/Console/Commands/DeleteOldCotxeAccessos.php`

#### `Intranet\Console\Commands\DeleteOldCotxeAccessos`
- Metodes:
  - **`handle`**()


### `app/Console/Commands/NotifyDailyFaults.php`

#### `Intranet\Console\Commands\NotifyDailyFaults`
- Metodes:
  - **`__construct`**()

#### `Intranet\Console\Commands\profesorService`
- Metodes: cap

#### `Intranet\Console\Commands\horarioService`
- Metodes: cap

#### `Intranet\Console\Commands\handle`
- Metodes:
  - **`handle`**(): mixed

    Execute the console command.
  - **`noHanFichado`**($dia)
  - **`profeSinFichar`**($dia, array &$noHanFichado): void

    /
  - **`profesoresEnActividad`**($dia, array &$noHanFichado): void

    /
  - **`profesoresDeComision`**($dia, array &$noHanFichado): void

    /
  - **`profesoresDeBaja`**($dia, array &$noHanFichado): void

    /


### `app/Console/Commands/SaoAnnexes.php`

#### `Intranet\Console\Commands\SaoAnnexes`
- Metodes:
  - **`handle`**(): mixed

    Execute the console command.


### `app/Console/Commands/SaoConnect.php`

#### `Intranet\Console\Commands\SaoConnect`
- Metodes:
  - **`handle`**()


### `app/Console/Commands/SendAvaluacioEmails.php`

#### `Intranet\Console\Commands\SendAvaluacioEmails`
- Metodes:
  - **`generaToken`**()
  - **`obtenToken`**($aR)
  - **`sendMatricula`**($aR)
  - **`handle`**(): mixed

    Execute the console command.


### `app/Console/Commands/SendDailyEmails.php`

#### `Intranet\Console\Commands\SendDailyEmails`
- Metodes:
  - **`__construct`**(private readonly ProfesorService $profesorService)
  - **`handle`**(): mixed

    Execute the console command.


### `app/Console/Commands/SendFctEmails.php`

#### `Intranet\Console\Commands\SendFctEmails`
Envia correus diaris de FCT a alumnat i instructors.

- Metodes:
  - **`handle`**(): mixed

    Execute the console command.
  - **`correuInstructor`**($fct): int

    /
  - **`normalizeEmail`**(?string $email): ?string

    Normalitza i valida un email.


### `app/Console/Commands/UploadAnexes.php`

#### `Intranet\Console\Commands\UploadAnexes`
- Metodes:
  - **`handle`**(): mixed

    Execute the console command.
  - **`buscaDocuments`**($fct, array &$document): string

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
  - **`schedule`**(Schedule $schedule): void

    Define the application's command schedule.
  - **`commands`**(): void

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
  - **`render`**($request, Throwable $exception)

#### `Intranet\Exceptions\send`
- Metodes: cap


### `app/Exceptions/IntranetException.php`

#### `Intranet\Exceptions\IntranetException`
- Metodes: cap


### `app/Exceptions/SeleniumException.php`

#### `Intranet\Exceptions\SeleniumException`
- Metodes:
  - **`__construct`**($message = null, $code = 0, \Exception $previous = null)
  - **`incrementCounter`**()


### `app/Exports/PollResultsExport.php`

#### `Intranet\Exports\PollResultsExport`
- Metodes:
  - **`__construct`**($poll, $votes, $options_numeric, $hasVotes, $stats)
  - **`sheets`**(): array


### `app/Exports/PollResultsSheet.php`

#### `Intranet\Exports\PollResultsSheet`
- Metodes:
  - **`__construct`**(string $view, string $title, array $data)
  - **`view`**(): View
  - **`title`**(): string


### `app/Finders/A1Finder.php`

#### `Intranet\Finders\A1Finder`
- Metodes:
  - **`exec`**()


### `app/Finders/A2Finder.php`

#### `Intranet\Finders\A2Finder`
- Metodes:
  - **`exec`**()


### `app/Finders/A3Finder.php`

#### `Intranet\Finders\A3Finder`
- Metodes:
  - **`exec`**()


### `app/Finders/AlumnoEnFctFinder.php`

#### `Intranet\Finders\AlumnoEnFctFinder`
- Metodes:
  - **`exec`**()
  - **`filter`**(&$elements)


### `app/Finders/AlumnoFctFinder.php`

#### `Intranet\Finders\AlumnoFctFinder`
- Metodes:
  - **`exec`**()
  - **`filter`**(&$elements)


### `app/Finders/AlumnoFctNoFinder.php`

#### `Intranet\Finders\AlumnoFctNoFinder`
- Metodes:
  - **`exec`**()
  - **`filter`**(&$elements)


### `app/Finders/AlumnoNoFctFinder.php`

#### `Intranet\Finders\AlumnoNoFctFinder`
- Metodes:
  - **`exec`**()
  - **`filter`**(&$elements)


### `app/Finders/ColaboracionFinder.php`

#### `Intranet\Finders\ColaboracionFinder`
- Metodes:
  - **`exec`**()
  - **`filter`**(&$elements)
  - **`checkFcts`**($needsFcts, $existsFcts)


### `app/Finders/FctActivaFinder.php`

#### `Intranet\Finders\FctActivaFinder`
- Metodes:
  - **`exec`**()
  - **`filter`**(&$elements)


### `app/Finders/FctFinder.php`

#### `Intranet\Finders\FctFinder`
- Metodes:
  - **`exec`**()
  - **`filter`**(&$elements)


### `app/Finders/Finder.php`

#### `Intranet\Finders\Finder`
- Metodes:
  - **`__construct`**($document)
  - **`existsActivity`**($id)
  - **`getDocument`**()
  - **`getZip`**()


### `app/Finders/MailFinders/AlumnosAllFinder.php`

#### `Intranet\Finders\MailFinders\AlumnosAllFinder`
- Metodes:
  - **`__construct`**()


### `app/Finders/MailFinders/Finder.php`

#### `Intranet\Finders\MailFinders\Finder`
- Metodes:
  - **`getElements`**()


### `app/Finders/MailFinders/InstructoresAllFinder.php`

#### `Intranet\Finders\MailFinders\InstructoresAllFinder`
- Metodes:
  - **`__construct`**()


### `app/Finders/MailFinders/MyA1Finder.php`

#### `Intranet\Finders\MailFinders\MyA1Finder`
- Metodes:
  - **`__construct`**()


### `app/Finders/MailFinders/MySignaturesFinder.php`

#### `Intranet\Finders\MailFinders\MySignaturesFinder`
- Metodes:
  - **`__construct`**()


### `app/Finders/MailFinders/SignaturesFinder.php`

#### `Intranet\Finders\MailFinders\SignaturesFinder`
- Metodes:
  - **`__construct`**()


### `app/Finders/ModelInStateFinder.php`

#### `Intranet\Finders\ModelInStateFinder`
- Metodes:
  - **`exec`**($estado=null)


### `app/Finders/RequestFinder.php`

#### `Intranet\Finders\RequestFinder`
- Metodes:
  - **`__construct`**($document)

    RequestFinder constructor.
  - **`exec`**()
  - **`getZip`**()
  - **`getRequest`**()


### `app/Finders/SignedFinder.php`

#### `Intranet\Finders\SignedFinder`
- Metodes:
  - **`exec`**()
  - **`filter`**(&$aluFcts)


### `app/Finders/UniqueFinder.php`

#### `Intranet\Finders\UniqueFinder`
- Metodes:
  - **`__construct`**($document)
  - **`exec`**()


### `app/Http/Kernel.php`

#### `Intranet\Http\Kernel`
- Metodes: cap


### `app/Http/Middleware/ApiTokenToBearer.php`

#### `Intranet\Http\Middleware\ApiTokenToBearer`
Compatibilitat temporal:

- Metodes:
  - **`handle`**(Request $request, Closure $next): Response

    /


### `app/Http/Middleware/CustomBackMiddleware.php`

#### `Intranet\Http\Middleware\CustomBackMiddleware`
- Metodes:
  - **`handle`**(Request $request, Closure $next)


### `app/Http/Middleware/EncryptCookies.php`

#### `Intranet\Http\Middleware\EncryptCookies`
- Metodes: cap


### `app/Http/Middleware/LangMiddleware.php`

#### `Intranet\Http\Middleware\LangMiddleware`
- Metodes:
  - **`handle`**($request, Closure $next): mixed

    Handle an incoming request.


### `app/Http/Middleware/LegacyApiTokenDeprecation.php`

#### `Intranet\Http\Middleware\LegacyApiTokenDeprecation`
Marca ús legacy de `api_token` en query/body per facilitar retirada gradual.

- Metodes:
  - **`handle`**(Request $request, Closure $next): Response

    /


### `app/Http/Middleware/OwnerMiddleware.php`

#### `Intranet\Http\Middleware\OwnerMiddleware`
- Metodes:
  - **`handle`**(Request $request, Closure $next, $model): mixed

    Handle an incoming request.
  - **`owner`**($model)


### `app/Http/Middleware/RedirectIfAuthenticated.php`

#### `Intranet\Http\Middleware\RedirectIfAuthenticated`
- Metodes:
  - **`handle`**($request, Closure $next, $guard = null): mixed

    Handle an incoming request.


### `app/Http/Middleware/RoleMiddleware.php`

#### `Intranet\Http\Middleware\RoleMiddleware`
- Metodes:
  - **`handle`**($request, Closure $next, $role)
  - **`normalizeRedirector`**($response, $request)


### `app/Http/Middleware/SessionTimeout.php`

#### `Intranet\Http\Middleware\SessionTimeout`
- Metodes:
  - **`__construct`**(Store $session)
  - **`apiSessionTokens`**(): ApiSessionTokenService

#### `Intranet\Http\Middleware\apiSessionTokenService`
- Metodes:
  - **`handle`**($request, Closure $next): mixed

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
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.


### `app/Http/PrintResources/A1Resource.php`

#### `Intranet\Http\PrintResources\A1Resource`
- Metodes:
  - **`__construct`**($empresa)
  - **`toArray`**(): array

    Transform the resource into an array.
  - **`dataSig`**()


### `app/Http/PrintResources/A2ENResource.php`

#### `Intranet\Http\PrintResources\A2ENResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/A3ENResource.php`

#### `Intranet\Http\PrintResources\A3ENResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/A5Resource.php`

#### `Intranet\Http\PrintResources\A5Resource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\firstByTutor`
- Metodes: cap


### `app/Http/PrintResources/AVIIAResource.php`

#### `Intranet\Http\PrintResources\AVIIAResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.


### `app/Http/PrintResources/AVIIBResource.php`

#### `Intranet\Http\PrintResources\AVIIBResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.


### `app/Http/PrintResources/AVIIIResource.php`

#### `Intranet\Http\PrintResources\AVIIIResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/AVIResource.php`

#### `Intranet\Http\PrintResources\AVIResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/AutorizacionDireccionGrupoResource.php`

#### `Intranet\Http\PrintResources\AutorizacionDireccionGrupoResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/AutorizacionDireccionResource.php`

#### `Intranet\Http\PrintResources\AutorizacionDireccionResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/AutorizacionGrupoResource.php`

#### `Intranet\Http\PrintResources\AutorizacionGrupoResource`
- Metodes:
  - **`__construct`**($elements)
  - **`setFlatten`**($flatten)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/CertificatInstructorResource.php`

#### `Intranet\Http\PrintResources\CertificatInstructorResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.


### `app/Http/PrintResources/ConformidadAlumnadoGrupoResource.php`

#### `Intranet\Http\PrintResources\ConformidadAlumnadoGrupoResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/ConformidadAlumnadoResource.php`

#### `Intranet\Http\PrintResources\ConformidadAlumnadoResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/ConformidadTutoriaResource.php`

#### `Intranet\Http\PrintResources\ConformidadTutoriaResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/ExempcioFCTResource.php`

#### `Intranet\Http\PrintResources\ExempcioFCTResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/ExempcioResource.php`

#### `Intranet\Http\PrintResources\ExempcioResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/NotificacioInspeccioResource.php`

#### `Intranet\Http\PrintResources\NotificacioInspeccioResource`
- Metodes:
  - **`__construct`**($elements)
  - **`toArray`**(): array

    Transform the resource into an array.

#### `Intranet\Http\PrintResources\largestByTutor`
- Metodes: cap


### `app/Http/PrintResources/PrintResource.php`

#### `Intranet\Http\PrintResources\PrintResource`
- Metodes:
  - **`build`**($source, $elements)
  - **`__construct`**($elements, $file=null, $flatten=true, $stamp=null)
  - **`getElements`**(): mixed

    /
  - **`getFlatten`**(): bool|mixed

    /
  - **`getStamp`**(): mixed

    /
  - **`getFile`**(): mixed

    /


### `app/Http/Resources/AlumnoFctControlResource.php`

#### `Intranet\Http\Resources\AlumnoFctControlResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/AlumnoFctResource.php`

#### `Intranet\Http\Resources\AlumnoFctResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/ArticuloLoteResource.php`

#### `Intranet\Http\Resources\ArticuloLoteResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/DualResource.php`

#### `Intranet\Http\Resources\DualResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/EmpresaResource.php`

#### `Intranet\Http\Resources\EmpresaResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/FaltaConfirmResource.php`

#### `Intranet\Http\Resources\FaltaConfirmResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/InventariableResource.php`

#### `Intranet\Http\Resources\InventariableResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/JDepartamentoResource.php`

#### `Intranet\Http\Resources\JDepartamentoResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/LoteResource.php`

#### `Intranet\Http\Resources\LoteResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/MaterialBajaResource.php`

#### `Intranet\Http\Resources\MaterialBajaResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.
  - **`descripcion`**($des, $mod, $mar)


### `app/Http/Resources/MaterialResource.php`

#### `Intranet\Http\Resources\MaterialResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.
  - **`descripcion`**($des, $mod, $mar)


### `app/Http/Resources/SelectAlumnoFctResource.php`

#### `Intranet\Http\Resources\SelectAlumnoFctResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/SelectAlumnoResource.php`

#### `Intranet\Http\Resources\SelectAlumnoResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/SelectColaboracionResource.php`

#### `Intranet\Http\Resources\SelectColaboracionResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/SelectFctResource.php`

#### `Intranet\Http\Resources\SelectFctResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/SelectSignaturaResource.php`

#### `Intranet\Http\Resources\SelectSignaturaResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/SignaturaDireccionResource.php`

#### `Intranet\Http\Resources\SignaturaDireccionResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/SignaturaResource.php`

#### `Intranet\Http\Resources\SignaturaResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Resources/SolicitudResource.php`

#### `Intranet\Http\Resources\SolicitudResource`
- Metodes:
  - **`toArray`**($request): array

    Transform the resource into an array.


### `app/Http/Traits/Autorizacion.php`

#### `Intranet\Http\Traits\Autorizacion`
Trait de suport per a controllers amb fluxos d'autorització per estats.

- Metodes:
  - **`getAutorizacionStateService`**(): AutorizacionStateService

    Resol i memoitza el servei de transicions d'estat per al model actual.

#### `Intranet\Http\Traits\class`
- Metodes:
  - **`getAutorizacionPrintService`**(): AutorizacionPrintService

    Resol i memoitza el servei d'impressió en lot.

#### `Intranet\Http\Traits\autorizacionPrintService`
- Metodes:
  - **`cancel`**($id): \Illuminate\Http\RedirectResponse

    Mou un element a estat de cancel·lació (`-1`).
  - **`init`**($id): \Illuminate\Http\RedirectResponse

    Inicialitza un element a l'estat definit en `$this->init`.
  - **`_print`**($id): \Illuminate\Http\RedirectResponse|null

    Aplica la transició `_print` a un element.
  - **`resolve`**(Request $request, $id, $redirect = true): \Illuminate\Http\RedirectResponse|null

    Resol l'element i opcionalment redirigeix a la pestanya d'estat resultant.
  - **`accept`**($id, $redirect = true): \Illuminate\Http\RedirectResponse|null

    Incrementa en una unitat l'estat actual de l'element.
  - **`resign`**($id, $redirect = true): \Illuminate\Http\RedirectResponse|null

    Decrementa en una unitat l'estat actual de l'element.
  - **`refuse`**(Request $request, $id, $redirect = true): \Illuminate\Http\RedirectResponse|null

    Refusa l'element amb explicació opcional.
  - **`follow`**($inicial, $final): \Illuminate\Http\RedirectResponse

    Tria la pestanya de retorn segons `notFollow`.
  - **`imprimir`**($modelo = '', $inicial = null, $final = null, $orientacion='portrait', $link=true): mixed

    Genera un PDF en lot per als elements en estat inicial i aplica transició.
  - **`guardAutorizacionContract`**(bool $requireModel = false): void

    Valida que el controller definisca el contracte mínim del trait.


### `app/Http/Traits/Core/DropZone.php`

#### `Intranet\Http\Traits\Core\DropZone`
Trait per gestionar la vista DropZone i la neteja d'adjunts associats.

- Metodes:
  - **`deleteAttached`**($id)

    Elimina tots els adjunts vinculats al path `{model}/{id}`.
  - **`link`**($id): \Illuminate\Contracts\View\View

    Mostra la pantalla d'adjunts DropZone per a un registre.


### `app/Http/Traits/Core/Imprimir.php`

#### `Intranet\Http\Traits\Core\Imprimir`
Trait de suport per a funcionalitats d'impressió i calendari en controllers.

- Metodes:
  - **`notify`**($id): \Illuminate\Http\RedirectResponse

    Envia notificació de recordatori al professor responsable del registre.

#### `Intranet\Http\Traits\Core\advise`
- Metodes:
  - **`hazPdf`**($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4', $margin_top= 15): mixed

    Fa de façana del servei de PDF per mantindre compatibilitat als controllers.

#### `Intranet\Http\Traits\Core\hazPdf`
- Metodes:
  - **`ics`**($id, $descripcion='descripcion', $objetivos='objetivos'): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse

    Genera la resposta iCalendar d'un registre.
  - **`gestor`**($id): \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\BinaryFileResponse

    Mostra o descarrega el document vinculat al registre en el gestor documental.
  - **`guardPrintableContract`**(): void

    Valida el contracte mínim requerit pel trait.
  - **`hasField`**(object $elemento, string $field): bool

    Comprova si un camp existeix encara que el valor siga `null`.


### `app/Http/Traits/Core/Panel.php`

#### `Intranet\Http\Traits\Core\Panel`
Trait de suport per a controllers tipus panell.

- Metodes:
  - **`index`**()

    Mostra la llista d'elements del panell.
  - **`search`**()

    Retorna els elements filtrats segons el seu estat i data.
  - **`setAuthBotonera`**(array $default = ['2' => 'pdf', '1' => 'autorizar'], bool $enlace = true)

    Configura la botónera segons els permisos i estats disponibles.
  - **`getActiveTab`**($default = 0)

    Retorna la pestanya activa actual.
  - **`setTabs`**($estados, $vista, $sustituye = null, $field = 'estado')

    Configura les pestanyes del panell.
  - **`guardPanelContract`**(): void

    Valida els atributs mínims que necessita el trait.


### `app/Http/Traits/Core/SCRUD.php`

#### `Intranet\Http\Traits\Core\SCRUD`
Trait de suport per a operacions bàsiques de tipus SCRUD en controllers.

- Metodes:
  - **`modelClass`**(): string

    Resol la FQCN del model i la guarda en `$this->class`.

#### `Intranet\Http\Traits\Core\ltrim`
- Metodes:
  - **`show`**($id): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse

    Mostra el detall d'un registre.
  - **`create`**($default = []): \Illuminate\Contracts\View\View

    Mostra el formulari de creació.
  - **`edit`**($id=null): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse

    Mostra el formulari d'edició.
  - **`createWithDefaultValues`**($default = []): mixed

    Crea una nova instància del model amb valors per defecte.
  - **`chooseView`**($view): string

    Retorna la vista per a una acció CRUD concreta.


### `app/Infrastructure/Persistence/Eloquent/AlumnoFct/EloquentAlumnoFctRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\AlumnoFct\EloquentAlumnoFctRepository`
Implementació Eloquent del repositori d'AlumnoFct.

- Metodes:
  - **`all`**(): EloquentCollection

    {@inheritdoc}
  - **`totesFcts`**(?string $profesor = null): EloquentCollection

    {@inheritdoc}
  - **`find`**(int|string $id): ?AlumnoFct

    {@inheritdoc}
  - **`findOrFail`**(int|string $id): AlumnoFct

    {@inheritdoc}
  - **`firstByIdSao`**(int|string $idSao): ?AlumnoFct

    {@inheritdoc}
  - **`byAlumno`**(string $nia): EloquentCollection

    {@inheritdoc}
  - **`byAlumnoWithA56`**(string $nia): EloquentCollection

    {@inheritdoc}
  - **`byGrupoEsFct`**(string $grupo): EloquentCollection

    {@inheritdoc}
  - **`byGrupoEsDual`**(string $grupo): EloquentCollection

    {@inheritdoc}
  - **`reassignProfesor`**(string $fromDni, string $toDni): int

    {@inheritdoc}
  - **`avalDistinctAlumnoIdsByProfesor`**(?string $profesor = null): array

    {@inheritdoc}
  - **`latestAvalByAlumnoAndProfesor`**(string $idAlumno, ?string $profesor = null): ?AlumnoFct

    {@inheritdoc}
  - **`avaluablesNoAval`**(?string $profesor = null, mixed $grupo = null): EloquentCollection

    {@inheritdoc}


### `app/Infrastructure/Persistence/Eloquent/Comision/EloquentComisionRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Comision\EloquentComisionRepository`
Implementació Eloquent del repositori de comissions.

- Metodes:
  - **`find`**(int $id): ?Comision
  - **`findOrFail`**(int $id): Comision
  - **`byDay`**(string $dia): EloquentCollection
  - **`withProfesorByDay`**(string $dia): EloquentCollection
  - **`pendingAuthorization`**(): EloquentCollection
  - **`authorizationApiList`**(): EloquentCollection
  - **`authorizeAllPending`**(): int
  - **`prePayByProfesor`**(string $dni): EloquentCollection
  - **`setEstado`**(int $id, int $estado): Comision
  - **`hasPendingUnpaidByProfesor`**(string $dni): bool
  - **`attachFct`**(int $comisionId, int $fctId, string $horaIni, bool $aviso): void
  - **`detachFct`**(int $comisionId, int $fctId): void


### `app/Infrastructure/Persistence/Eloquent/Empresa/EloquentEmpresaRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Empresa\EloquentEmpresaRepository`
Implementació Eloquent del repositori d'empreses.

- Metodes:
  - **`listForGrid`**(): EloquentCollection
  - **`findForShow`**(int $id): Empresa
  - **`colaboracionIdsByCycleAndCenters`**(int $cycleId, array $centerIds): Collection
  - **`cyclesByDepartment`**(string $department): EloquentCollection
  - **`convenioList`**(): EloquentCollection
  - **`socialConcertList`**(): EloquentCollection
  - **`erasmusList`**(): EloquentCollection


### `app/Infrastructure/Persistence/Eloquent/Expediente/EloquentExpedienteRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Expediente\EloquentExpedienteRepository`
Implementació Eloquent del repositori d'expedients.

- Metodes:
  - **`find`**(int|string $id): ?Expediente
  - **`findOrFail`**(int|string $id): Expediente
  - **`createFromRequest`**(Request $request): Expediente
  - **`updateFromRequest`**(int|string $id, Request $request): Expediente
  - **`pendingAuthorization`**(): EloquentCollection
  - **`readyToPrint`**(): EloquentCollection
  - **`allTypes`**(): EloquentCollection


### `app/Infrastructure/Persistence/Eloquent/FaltaProfesor/EloquentFaltaProfesorRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\FaltaProfesor\EloquentFaltaProfesorRepository`
Implementació Eloquent del repositori de fitxatges de professorat.

- Metodes:
  - **`lastTodayByProfesor`**(string $dni): ?Falta_profesor
  - **`hasFichadoOnDay`**(string $dia, string $dni): bool
  - **`createEntry`**(string $dni, string $dia, string $hora): Falta_profesor
  - **`closeExit`**(Falta_profesor $fichaje, string $hora): Falta_profesor
  - **`byDayAndProfesor`**(string $dia, string $dni): EloquentCollection
  - **`rangeByProfesor`**(string $dni, string $desde, string $hasta): EloquentCollection


### `app/Infrastructure/Persistence/Eloquent/Fct/EloquentFctRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Fct\EloquentFctRepository`
Implementació Eloquent del repositori FCT.

- Metodes:
  - **`find`**(int|string $id): ?Fct
  - **`findOrFail`**(int|string $id): Fct
  - **`firstByColaboracionAsociacionInstructor`**(int|string $idColaboracion, int|string $asociacion, int|string $idInstructor): ?Fct
  - **`panelListingByProfesor`**(string $dni): EloquentCollection
  - **`save`**(Fct $fct): Fct
  - **`create`**(array $attributes): Fct
  - **`attachAlumno`**(int|string $idFct, string $idAlumno, array $pivotAttributes): void
  - **`detachAlumno`**(int|string $idFct, string $idAlumno): void
  - **`saveColaborador`**(int|string $idFct, Colaborador $colaborador): void
  - **`deleteColaborador`**(int|string $idFct, string $idInstructor): int
  - **`updateColaboradorHoras`**(int|string $idFct, string $idInstructor, int|string $horas): int
  - **`setCotutor`**(int|string $idFct, ?string $cotutor): void
  - **`empresaIdByFct`**(int|string $idFct): ?int


### `app/Infrastructure/Persistence/Eloquent/Grupo/EloquentGrupoRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Grupo\EloquentGrupoRepository`
Implementació Eloquent del repositori de Grupo.

- Metodes:
  - **`create`**(array $attributes): Grupo
  - **`find`**(string $codigo): ?Grupo
  - **`all`**(): EloquentCollection
  - **`qTutor`**(string $dni): EloquentCollection
  - **`firstByTutor`**(string $dni): ?Grupo
  - **`largestByTutor`**(string $dni): ?Grupo
  - **`byCurso`**(int $curso): EloquentCollection
  - **`byDepartamento`**(int $departamento): EloquentCollection
  - **`tutoresDniList`**(): array
  - **`reassignTutor`**(string $fromDni, string $toDni): int
  - **`misGrupos`**(): EloquentCollection
  - **`misGruposByProfesor`**(string $dni): EloquentCollection
  - **`withActaPendiente`**(): EloquentCollection
  - **`byTutorOrSubstitute`**(string $dni, ?string $sustituyeA): ?Grupo

    Cerca el primer grup associat al tutor o al professor substituït.
  - **`withStudents`**(): EloquentCollection
  - **`firstByTutorDual`**(string $dni): ?Grupo
  - **`byCodes`**(array $codigos): EloquentCollection
  - **`allWithTutorAndCiclo`**(): EloquentCollection

    Retorna tots els grups amb relacions bàsiques per a llistats de direcció.
  - **`misGruposWithCiclo`**(): EloquentCollection

    Retorna els grups del professor amb la relació de cicle carregada.


### `app/Infrastructure/Persistence/Eloquent/Horario/EloquentHorarioRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Horario\EloquentHorarioRepository`
Implementació Eloquent del repositori d'horaris.

- Metodes:
  - **`semanalByProfesor`**(string $dni): array
  - **`semanalByGrupo`**(string $grupo): array
  - **`lectivosByDayAndSesion`**(string $dia, int $sesion): EloquentCollection
  - **`countByProfesorAndDay`**(string $dni, string $dia): int
  - **`guardiaAllByDia`**(string $dia): EloquentCollection
  - **`guardiaAllByProfesorAndDiaAndSesiones`**(string $dni, string $dia, array $sesiones): EloquentCollection
  - **`guardiaAllByProfesorAndDia`**(string $dni, string $dia): EloquentCollection
  - **`guardiaAllByProfesor`**(string $dni): EloquentCollection
  - **`firstByProfesorDiaSesion`**(string $dni, string $dia, int|string $sesion): ?Horario
  - **`byProfesor`**(string $dni): EloquentCollection
  - **`byProfesorWithRelations`**(string $dni, array $relations): EloquentCollection
  - **`lectivasByProfesorAndDayOrdered`**(string $dni, string $dia): EloquentCollection
  - **`reassignProfesor`**(string $fromDni, string $toDni): int
  - **`deleteByProfesor`**(string $dni): int
  - **`gruposByProfesor`**(string $dni): Collection
  - **`gruposByProfesorDiaAndSesiones`**(string $dni, string $dia, array $sesiones): Collection
  - **`profesoresByGruposExcept`**(array $grupos, string $emisorDni): Collection
  - **`primeraByProfesorAndDateOrdered`**(string $dni, string $date): EloquentCollection
  - **`firstByModulo`**(string $modulo): ?Horario
  - **`byProfesorDiaOrdered`**(string $dni, string $dia): EloquentCollection
  - **`distinctModulos`**(): Collection
  - **`create`**(array $data): Horario
  - **`forProgramacionImport`**(): EloquentCollection
  - **`firstForDepartamentoAsignacion`**(string $dni): ?Horario


### `app/Infrastructure/Persistence/Eloquent/Profesor/EloquentProfesorRepository.php`

#### `Intranet\Infrastructure\Persistence\Eloquent\Profesor\EloquentProfesorRepository`
Implementació Eloquent del repositori de professorat.

- Metodes:
  - **`plantillaOrderedWithDepartamento`**(): EloquentCollection
  - **`activosByDepartamentosWithHorario`**(array $departamentosIds, string $dia, int $sesion): EloquentCollection
  - **`activosOrdered`**(): EloquentCollection
  - **`all`**(): EloquentCollection
  - **`plantilla`**(): EloquentCollection
  - **`plantillaByDepartamento`**(int|string $departamento): EloquentCollection
  - **`activos`**(): EloquentCollection
  - **`byDepartamento`**(int|string $departamento): EloquentCollection
  - **`byGrupo`**(string $grupo): EloquentCollection
  - **`byGrupoTrabajo`**(string $grupoTrabajo): EloquentCollection
  - **`byDnis`**(array $dnis): EloquentCollection
  - **`find`**(string $dni): ?Profesor
  - **`findOrFail`**(string $dni): Profesor
  - **`findBySustituyeA`**(string $dni): ?Profesor
  - **`findByCodigo`**(string $codigo): ?Profesor
  - **`findByApiToken`**(string $apiToken): ?Profesor
  - **`findByEmail`**(string $email): ?Profesor
  - **`plantillaOrderedByDepartamento`**(): EloquentCollection
  - **`plantillaForResumen`**(): EloquentCollection
  - **`allOrderedBySurname`**(): EloquentCollection
  - **`clearFechaBaja`**(): int
  - **`countByCodigo`**(int|string $codigo): int
  - **`usedCodigosBetween`**(int $min, int $max): array

    /
  - **`create`**(array $data): Profesor
  - **`withSustituyeAssigned`**(): EloquentCollection


### `app/Livewire/BustiaVioleta/AdminList.php`

#### `Intranet\Livewire\BustiaVioleta\AdminList`
- Metodes:
  - **`mount`**()
  - **`updating`**($prop)
  - **`viewContact`**(int $id)

#### `Intranet\Livewire\BustiaVioleta\find`
- Metodes:
  - **`closeContact`**()
  - **`viewMessage`**(int $id)
  - **`closeMessage`**()
  - **`setEstado`**(int $id, string $estado)
  - **`togglePublicable`**(int $id)
  - **`delete`**(int $id)
  - **`render`**()


### `app/Livewire/BustiaVioleta/Form.php`

#### `Intranet\Livewire\BustiaVioleta\Form`
- Metodes:
  - **`confirmAndSubmit`**()
  - **`mount`**()
  - **`updatedTipus`**($value)
  - **`reloadCategories`**(): void
  - **`rules`**()
  - **`updatedFinalitat`**($value)
  - **`submit`**()
  - **`render`**()


### `app/Livewire/CalendariComponent.php`

#### `Intranet\Livewire\CalendariComponent`
- Metodes:
  - **`dataCompletada`**(int $dia): string
  - **`mount`**($any = null, $mes = null)
  - **`updatedMes`**()
  - **`canviarMes`**($increment)
  - **`carregarDies`**()
  - **`seleccionarDia`**($dia)
  - **`guardarCanvis`**()
  - **`resetSeleccionat`**()
  - **`cancelarEdicio`**()
  - **`render`**()


### `app/Livewire/Controlguardia.php`

#### `Intranet\Livewire\Controlguardia`
- Metodes:
  - **`mount`**()
  - **`weekBefore`**()
  - **`weekAfter`**()
  - **`render`**()


### `app/Livewire/DocumentoTable.php`

#### `Intranet\Livewire\DocumentoTable`
- Metodes:
  - **`mount`**(): void
  - **`updating`**($name): void
  - **`render`**()
  - **`searchableFields`**(): array
  - **`sortBy`**(string $field): void
  - **`sanitizeSortField`**(string $field): string
  - **`sanitizeSortDirection`**(string $direction): string
  - **`isDireccion`**(): bool


### `app/Livewire/FctCalendar.php`

#### `Intranet\Livewire\FctCalendar`
- Metodes:
  - **`mount`**($alumno)
  - **`alumno`**(): Alumno
  - **`addTram`**()
  - **`removeTram`**($index)
  - **`createCalendar`**()
  - **`deleteCalendar`**()
  - **`loadCalendar`**()
  - **`updateDay`**($id, $hours)
  - **`normalizeHours`**($hours): ?float
  - **`normalizeNullableInt`**($value): ?int
  - **`exportCalendarPdf`**()
  - **`sendCalendarEmails`**(): void

    Enviar calendaris per correu des de la vista (botó).
  - **`mapDaysToMonthlyCalendar`**($days, array $colorMap = []): array

    Retorna el calendari agrupat per mes amb any inclòs per evitar desquadres.
  - **`renderPdfContent`**(array $monthlyCalendar, float $totalHours, string $titol, ?array $legend = null): string

    Genera el contingut PDF per a un calendari concret.
  - **`buildLegend`**($days, $colaboracions): array
  - **`createZipFromDocuments`**(array $documents): array
  - **`buildDocuments`**($allDays, $colaboracions, $colabLegend, array $colorMap): array

    Genera tots els PDFs (alumne + col·laboracions).
  - **`dispatchCalendarEmails`**(array $documents, $colaboracions): void

    Envia cada PDF de forma separada (alumne i una per empresa) amb còpia a l'usuari actual.
  - **`getTutorContact`**(): array
  - **`render`**()


### `app/Livewire/FicharControlDia.php`

#### `Intranet\Livewire\FicharControlDia`
- Metodes:
  - **`mount`**(): void
  - **`updatedFecha`**(): void
  - **`diaAnterior`**(): void
  - **`diaSeguent`**(): void
  - **`render`**()
  - **`profesores`**(): ProfesorService

#### `Intranet\Livewire\profesorService`
- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Livewire\horarioService`
- Metodes:
  - **`refreshData`**(): void
  - **`loadProfesoresForControlDia`**()


### `app/Livewire/HorariProfessorCanvi.php`

#### `Intranet\Livewire\HorariProfessorCanvi`
Component Livewire per proposar canvis temporals d'horari del professorat.

- Metodes:
  - **`mount`**($dni = null)
  - **`loadPropuestasDisponibles`**(): void
  - **`updatedSelectedPropuestaId`**(): void
  - **`loadHoras`**(): void
  - **`loadHorario`**(): void
  - **`loadCambios`**(): void
  - **`loadPropuestaById`**(string $id): void
  - **`applyPropuestaData`**(array $data): void
  - **`applyCambios`**(array $cambios): void
  - **`forceMove`**(string $from, string $to): void
  - **`cellClicked`**(string $cell): void
  - **`moveFromTo`**(string $from, string $to): void
  - **`moveSelectedTo`**(string $dest): void

    Mou la cel·la seleccionada a la destinació respectant restriccions d'horari.
  - **`resetCanvis`**(): void
  - **`novaProposta`**(): void
  - **`esborrarProposta`**(): void
  - **`guardarProposta`**(): void
  - **`downloadJson`**()
  - **`getCambiosCountProperty`**(): int
  - **`buildCambios`**(): array
  - **`latestPropuestaByEstado`**(string $estado): ?array
  - **`generatePropuestaId`**(): string
  - **`datesOverlapExisting`**(string $inicio, string $fin, ?string $excludeId = null): bool
  - **`cellHasGuardia`**(string $cell): bool
  - **`itemIsGuardia`**(string $itemId): bool
  - **`isDifferentDay`**(string $from, string $to): bool

    /
  - **`isGuardiaOcupacion`**(?string $ocupacion, string $titulo): bool

    Determina si una ocupacio s'ha de tractar com a guardia (no movible).
  - **`isOcupacionGuardiaCode`**(string $ocupacion): bool

    /
  - **`isReunionDepartament`**(string $titulo): bool

    /
  - **`render`**()
  - **`profesores`**(): ProfesorService

#### `Intranet\Livewire\profesorService`
- Metodes:
  - **`horarios`**(): HorarioService

#### `Intranet\Livewire\horarioService`
- Metodes: cap


### `app/Mail/AvalAlumne.php`

#### `Intranet\Mail\AvalAlumne`
- Metodes:
  - **`__construct`**($aR, $informe): void

    Create a new message instance.
  - **`build`**(): $this

    Build the message.


### `app/Mail/AvalFct.php`

#### `Intranet\Mail\AvalFct`
- Metodes:
  - **`__construct`**($fct, $quien): void

    Create a new message instance.
  - **`build`**(): $this

    Build the message.


### `app/Mail/CertificatAlumneFct.php`

#### `Intranet\Mail\CertificatAlumneFct`
- Metodes:
  - **`__construct`**($fct): void

    Create a new message instance.
  - **`build`**(): $this

    Build the message.


### `app/Mail/CertificatInstructorFct.php`

#### `Intranet\Mail\CertificatInstructorFct`
- Metodes:
  - **`__construct`**($fct, $emitent): void

    Create a new message instance.
  - **`build`**(): $this

    Build the message.
  - **`certificatColaboradors`**()

#### `Intranet\Mail\hazPdf`
- Metodes: cap


### `app/Mail/Comunicado.php`

#### `Intranet\Mail\Comunicado`
- Metodes:
  - **`getmodel`**($elemento)
  - **`__construct`**($remitente, $elemento, $vista, $attach=null)
  - **`build`**()


### `app/Mail/DocumentRequest.php`

#### `Intranet\Mail\DocumentRequest`
Class DocumentRequest

- Metodes:
  - **`__construct`**($mail, $view, $elemento, $attach=null): void

    Create a new message instance.
  - **`build`**(): $this

    Build the message.


### `app/Mail/MatriculaAlumne.php`

#### `Intranet\Mail\MatriculaAlumne`
- Metodes:
  - **`__construct`**($aR, $vista, $convocatoria=null): void

    Create a new message instance.
  - **`build`**(): $this

    Build the message.


### `app/Mail/ResumenDiario.php`

#### `Intranet\Mail\ResumenDiario`
- Metodes:
  - **`__construct`**($notificaciones): void

    Create a new message instance.
  - **`build`**(): $this

    Build the message.


### `app/Mail/TitolAlumne.php`

#### `Intranet\Mail\TitolAlumne`
- Metodes:
  - **`__construct`**($fct): void

    Create a new message instance.
  - **`build`**(): $this

    Build the message.


### `app/Notifications/MyResetPassword.php`

#### `Intranet\Notifications\MyResetPassword`
- Metodes:
  - **`toMail`**($notifiable): \Illuminate\Notifications\Messages\MailMessage

    Get the mail representation of the notification.


### `app/Notifications/mensajePanel.php`

#### `Intranet\Notifications\mensajePanel`
- Metodes:
  - **`__construct`**($mensaje)
  - **`via`**($notifiable): array

    Get the notification's delivery channels.
  - **`toMail`**($notifiable): \Illuminate\Notifications\Messages\MailMessage

    Get the mail representation of the notification.
  - **`toArray`**($notifiable): array

    Get the array representation of the notification.


### `app/OpenApi/ApiCustomEndpoints.php`

#### `Intranet\OpenApi\ApiCustomEndpoints`
Documentacio OpenAPI per a endpoints custom (no REST resource) definits en routes/api.php.

- Metodes:
  - **`alumnofct_grupo_grupo_get`**(): void
  - **`convenio_get`**(): void
  - **`miIp_get`**(): void
  - **`actividad_actividad_getFiles_get`**(): void
  - **`server_time_get`**(): void
  - **`porta_obrir_get`**(): void
  - **`porta_obrir_automatica_post`**(): void
  - **`eventPortaSortida_post`**(): void
  - **`eventPorta_post`**(): void
  - **`presencia_resumen_rango_get`**(): void
  - **`grupo_list_id_get`**(): void
  - **`alumnofct_grupo_dual_get`**(): void
  - **`fct_id_alFct_get`**(): void
  - **`fct_id_alFct_post`**(): void
  - **`comision_dni_prePay_put`**(): void
  - **`autorizar_comision_get`**(): void
  - **`notification_id_get`**(): void
  - **`profesor_dni_rol_get`**(): void
  - **`profesor_rol_rol_get`**(): void
  - **`doficha_get`**(): void
  - **`ipGuardias_get`**(): void
  - **`verficha_get`**(): void
  - **`itaca_dia_idProfesor_get`**(): void
  - **`itaca_post`**(): void
  - **`aula_get`**(): void
  - **`faltaProfesor_horas_condicion_get`**(): void
  - **`material_cambiarUbicacion_put`**(): void
  - **`material_cambiarEstado_put`**(): void
  - **`material_cambiarUnidad_put`**(): void
  - **`material_cambiarInventario_put`**(): void
  - **`material_espacio_espacio_get`**(): void
  - **`inventario_get`**(): void
  - **`inventario_espai_get`**(): void
  - **`guardia_range_get`**(): void
  - **`alumnoGrupoModulo_dni_modulo_get`**(): void
  - **`horario_idProfesor_guardia_get`**(): void
  - **`horariosDia_fecha_get`**(): void
  - **`asistencia_cambiar_put`**(): void
  - **`reunion_idReunion_alumno_idAlumno_put`**(): void
  - **`tiporeunion_id_get`**(): void
  - **`modulo_id_get`**(): void
  - **`horarioChange_dni_get`**(): void
  - **`horarioChange_dni_post`**(): void
  - **`centro_fusionar_post`**(): void
  - **`colaboracion_instructores_id_get`**(): void
  - **`colaboracion_colaboracion_resolve_get`**(): void
  - **`colaboracion_colaboracion_refuse_get`**(): void
  - **`colaboracion_colaboracion_unauthorize_get`**(): void
  - **`colaboracion_colaboracion_switch_get`**(): void
  - **`colaboracion_colaboracion_telefonico_post`**(): void
  - **`colaboracion_colaboracion_book_post`**(): void
  - **`documentacionFCT_documento_get`**(): void
  - **`signatura_get`**(): void
  - **`signatura_director_get`**(): void
  - **`signatura_a1_get`**(): void
  - **`matricula_token_get`**(): void
  - **`test_matricula_token_get`**(): void
  - **`alumno_dni_foto_post`**(): void
  - **`alumno_dni_dades_post`**(): void
  - **`matricula_send_post`**(): void
  - **`lote_id_articulos_get`**(): void
  - **`lote_id_articulos_put`**(): void
  - **`articuloLote_id_materiales_get`**(): void
  - **`attachFile_post`**(): void
  - **`getAttached_modelo_id_get`**(): void
  - **`getNameAttached_modelo_id_filename_get`**(): void
  - **`removeAttached_modelo_id_file_get`**(): void
  - **`activity_id_move_fct_get`**(): void
  - **`tutoriagrupo_id_get`**(): void


### `app/OpenApi/ApiDomainSchemas.php`

#### `Intranet\OpenApi\ApiDomainSchemas`
Esquemes de domini reutilitzables per a la documentacio OpenAPI.

- Metodes: cap


### `app/OpenApi/ApiResourceDocumentation.php`

#### `Intranet\OpenApi\ApiResourceDocumentation`
Documentacio OpenAPI de rutes REST definides amb Route::resource.

- Metodes:
  - **`alumnofctResourceEndpoints`**(): void
  - **`projecteResourceEndpoints`**(): void
  - **`actividadResourceEndpoints`**(): void
  - **`programacionResourceEndpoints`**(): void
  - **`reunionResourceEndpoints`**(): void
  - **`faltaResourceEndpoints`**(): void
  - **`documentoResourceEndpoints`**(): void
  - **`modulo_cicloResourceEndpoints`**(): void
  - **`resultadoResourceEndpoints`**(): void
  - **`comisionResourceEndpoints`**(): void
  - **`instructorResourceEndpoints`**(): void
  - **`ipguardiaResourceEndpoints`**(): void
  - **`settingResourceEndpoints`**(): void
  - **`ppollResourceEndpoints`**(): void
  - **`profesorResourceEndpoints`**(): void
  - **`faltaprofesorResourceEndpoints`**(): void
  - **`materialResourceEndpoints`**(): void
  - **`materialbajaResourceEndpoints`**(): void
  - **`espacioResourceEndpoints`**(): void
  - **`guardiaResourceEndpoints`**(): void
  - **`departamentoResourceEndpoints`**(): void
  - **`reservaResourceEndpoints`**(): void
  - **`ordenreunionResourceEndpoints`**(): void
  - **`colaboracionResourceEndpoints`**(): void
  - **`centroResourceEndpoints`**(): void
  - **`grupotrabajoResourceEndpoints`**(): void
  - **`empresaResourceEndpoints`**(): void
  - **`ordentrabajoResourceEndpoints`**(): void
  - **`incidenciaResourceEndpoints`**(): void
  - **`tipoincidenciaResourceEndpoints`**(): void
  - **`expedienteResourceEndpoints`**(): void
  - **`solicitudResourceEndpoints`**(): void
  - **`tipoexpedienteResourceEndpoints`**(): void
  - **`alumnogrupoResourceEndpoints`**(): void
  - **`activityResourceEndpoints`**(): void
  - **`cursoResourceEndpoints`**(): void
  - **`cicloResourceEndpoints`**(): void
  - **`taskResourceEndpoints`**(): void
  - **`horarioResourceEndpoints`**(): void
  - **`horaResourceEndpoints`**(): void
  - **`alumnoresultadoResourceEndpoints`**(): void
  - **`loteResourceEndpoints`**(): void
  - **`articuloloteResourceEndpoints`**(): void
  - **`articuloResourceEndpoints`**(): void
  - **`cotxeResourceEndpoints`**(): void
  - **`tipoactividadResourceEndpoints`**(): void


### `app/OpenApi/OpenApiSpec.php`

#### `Intranet\OpenApi\OpenApiSpec`
Especificacio global OpenAPI per a la API del projecte.

- Metodes: cap


### `app/Presentation/AlumnoFct/AlumnoFctPresenter.php`

#### `Intranet\Presentation\AlumnoFct\AlumnoFctPresenter`
- Metodes:
  - **`__construct`**(private readonly AlumnoFct $alumnoFct)
  - **`cssClass`**(): string

    Retorna la classe CSS de fons segons estat/temporalitat del registre.
  - **`centerName`**(int $length = 30): string
  - **`studentShortName`**(): string
  - **`studentNameWithMinorIcon`**(): string
  - **`remainingPracticeTimeLabel`**(): string
  - **`contactName`**(): string
  - **`fullName`**(): string
  - **`completedHoursLabel`**(): string
  - **`instructorName`**(int $length = 30): string
  - **`printableId`**(): string
  - **`backgroundByDates`**(): string


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
  - **`requestRules`**(bool $isDirector): array

    Regles del formulari principal de comissió.


### `app/Presentation/Crud/CotxeCrudSchema.php`

#### `Intranet\Presentation\Crud\CotxeCrudSchema`
- Metodes:
  - **`requestRules`**(string|int|null $cotxeId, string $dni): array

    Regles de validació del formulari de cotxe.


### `app/Presentation/Crud/CursoCrudSchema.php`

#### `Intranet\Presentation\Crud\CursoCrudSchema`
- Metodes: cap


### `app/Presentation/Crud/DocumentoCrudSchema.php`

#### `Intranet\Presentation\Crud\DocumentoCrudSchema`
- Metodes:
  - **`projectFormFields`**(): array

    Formulari del flux de projecte.
  - **`qualitatFormFields`**(): array

    Formulari del flux de qualitat.
  - **`editFormFields`**(bool $hasLink): array

    Formulari d'edició segons siga enllaç o fitxer.


### `app/Presentation/Crud/EmpresaCrudSchema.php`

#### `Intranet\Presentation\Crud\EmpresaCrudSchema`
- Metodes:
  - **`requestRules`**(string|int|null $empresaId): array

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
  - **`requestRules`**(string $imagenRule): array

    Regles de request afegint validació de fitxer d'imatge.
  - **`editFormFields`**(): array

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
  - **`requestRules`**(int|string|null $currentId = null): array

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
  - **`boot`**(): void

    Bootstrap any application services.
  - **`register`**(): void

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
  - **`boot`**(): void

    Register any authentication / authorization services.


### `app/Providers/BroadcastServiceProvider.php`

#### `Intranet\Providers\BroadcastServiceProvider`
- Metodes:
  - **`boot`**(): void

    Bootstrap any application services.


### `app/Providers/EventServiceProvider.php`

#### `Intranet\Providers\EventServiceProvider`
Registre d'esdeveniments de l'aplicacio.

- Metodes:
  - **`boot`**(): void

    Register any events for your application.


### `app/Providers/HelperServiceProvider.php`

#### `Intranet\Providers\HelperServiceProvider`
- Metodes:
  - **`boot`**(): void

    Bootstrap the application services.
  - **`register`**(): void

    Register the application services.


### `app/Providers/RouteServiceProvider.php`

#### `Intranet\Providers\RouteServiceProvider`
- Metodes:
  - **`boot`**(): void

    Define your route model bindings, pattern filters, etc.
  - **`map`**(): void

    Define the routes for the application.
  - **`mapWebRoutes`**(): void

    Define the "web" routes for the application.
  - **`mapApiRoutes`**(): void

    Define the "api" routes for the application.
  - **`profesorRoutes`**(): void

    Define the "auth" routes for the application.
  - **`adminRoutes`**()
  - **`todosRoutes`**()
  - **`consergeRoutes`**()
  - **`direccionRoutes`**()
  - **`alumnoRoutes`**()
  - **`mantenimientoRoutes`**()
  - **`jefeRoutes`**()


### `app/Providers/SettingsProvider.php`

#### `Intranet\Providers\SettingsProvider`
- Metodes:
  - **`register`**(): void

    Register services.
  - **`boot`**(): void

    Bootstrap services.


### `app/Providers/TelescopeServiceProvider.php`

#### `Intranet\Providers\TelescopeServiceProvider`
- Metodes:
  - **`register`**(): void

    Register any application services.
  - **`hideSensitiveRequestDetails`**(): void

    Prevent sensitive request details from being logged by Telescope.
  - **`gate`**(): void

    Register the Telescope gate.


### `app/Providers/ValidationServiceProvider.php`

#### `Intranet\Providers\ValidationServiceProvider`
- Metodes:
  - **`boot`**(): void

    Bootstrap the application services.
  - **`register`**(): void

    Register the application services.


### `app/Providers/ViewComposerServiceProvider.php`

#### `Intranet\Providers\ViewComposerServiceProvider`
- Metodes:
  - **`boot`**(): void

#### `Intranet\Providers\isInside`
- Metodes: cap


### `app/Sao/Actions/SAOAction.php`

#### `Intranet\Sao\Actions\SAOAction`
Entrypoint unificat per a les operacions SAO.

- Metodes:
  - **`__construct`**(?DigitalSignatureService $digitalSignatureService = null)

#### `Intranet\Sao\Actions\setFireFoxCapabilities`
- Metodes:
  - **`setFireFoxCapabilities`**(): mixed

    Retorna les capacitats Firefox necessàries per a descàrregues SAO.
  - **`index`**(RemoteWebDriver $driver, array $requestData, ?UploadedFile $file = null): mixed

    /
  - **`executeLegacyAction`**(string $action, RemoteWebDriver $driver, array $requestData, ?UploadedFile $file = null): mixed

    Manté compatibilitat amb accions SAO legacy no migrades.


### `app/Sao/Actions/SaoActionInterface.php`

#### `Intranet\Sao\Actions\SaoActionInterface`
Contracte base per a una acció SAO executable amb un driver Selenium.

- Metodes:
  - **`index`**(RemoteWebDriver $driver, array $requestData, ?UploadedFile $file = null): mixed

    Executa l'acció SAO.


### `app/Sao/Documents/A1DocumentService.php`

#### `Intranet\Sao\Documents\A1DocumentService`
Gestiona la descàrrega de l'annex A1/A1DUAL.

- Metodes:
  - **`__construct`**(?SaoNavigator $navigator = null, ?SaoDownloadManager $downloadManager = null)
  - **`download`**(AlumnoFct $fctAl, RemoteWebDriver $driver): bool

    Descarrega l'annex A1/A1DUAL.


### `app/Sao/Documents/A2DocumentService.php`

#### `Intranet\Sao\Documents\A2DocumentService`
Gestiona la descàrrega i signatura dels annexes A2 i A3.

- Metodes:
  - **`__construct`**(DigitalSignatureService $digitalSignatureService, ?SaoNavigator $navigator = null, ?SaoDownloadManager $downloadManager = null)
  - **`download`**(AlumnoFct $fctAl, RemoteWebDriver $driver, ?string $certPath, ?string $certPassword, int $annexeNum): bool

    Descarrega i, si cal, firma digitalment l'annex A2/A3.


### `app/Sao/Documents/A5DocumentService.php`

#### `Intranet\Sao\Documents\A5DocumentService`
Gestiona la descàrrega i processat de l'annex A5.

- Metodes:
  - **`__construct`**(DigitalSignatureService $digitalSignatureService, ?SaoNavigator $navigator = null, ?SaoDownloadManager $downloadManager = null)
  - **`download`**(AlumnoFct $fctAl, RemoteWebDriver $driver, ?string $certPath, ?string $certPassword): bool

    Descarrega, processa i opcionalment firma l'annex A5.


### `app/Sao/SaoAnnexesAction.php`

#### `Intranet\Sao\SaoAnnexesAction`
Acció SAO per descarregar i enllaçar annexos.

- Metodes:
  - **`__construct`**($digitalSignatureService = null)
  - **`execute`**($driver, ?callable $queryCallback = null)
  - **`index`**($driver)
  - **`processFcts`**()
  - **`getValidFcts`**()
  - **`isAnnexDownloaded`**($fct): bool
  - **`downloadAnnex`**($fct)
  - **`saveAnnex`**($name, $downloadLink, $fct)
  - **`deleteSignatures`**($fct)
  - **`closePopup`**()


### `app/Sao/SaoComparaAction.php`

#### `Intranet\Sao\SaoComparaAction`
Acció SAO per comparar dades Intranet vs SAO.

- Metodes:
  - **`compara`**(Request $request)
  - **`igual`**($intranet, $sao)
  - **`index`**($driver)
  - **`descomposaClau`**($clau)


### `app/Sao/SaoDocumentsAction.php`

#### `Intranet\Sao\SaoDocumentsAction`
Gestió de documents SAO (A1, A2 i A5).

- Metodes:
  - **`__construct`**(DigitalSignatureService $digitalSignatureService, ?A1DocumentService $a1DocumentService = null, ?A2DocumentService $a2DocumentService = null, ?A5DocumentService $a5DocumentService = null)
  - **`setFireFoxCapabilities`**()
  - **`index`**($driver, $request, $file = null)

#### `Intranet\Sao\send`
- Metodes: cap


### `app/Sao/SaoImportaAction.php`

#### `Intranet\Sao\SaoImportaAction`
Acció SAO per importar dades de FCT des de la plataforma externa.

- Metodes:
  - **`buscaCentro`**($dada, $empresa)
  - **`extractFromModal`**(&$dades, $index, $tr, $driver)
  - **`extractFromEdit`**($dada, RemoteWebDriver $driver): mixed

    /
  - **`deepMerge`**(array $array1, array $array2): array

    Funció per fusionar profundament dos arrays
  - **`selectDirectorFct`**($driver)
  - **`index`**($driver)

#### `Intranet\Sao\firstByTutor`
- Metodes:
  - **`importa`**(Request $request)
  - **`getCentro`**($dades)
  - **`getColaboracion`**($dada, $idCiclo, $idCentro)
  - **`getAlumno`**(\Facebook\WebDriver\Remote\RemoteWebElement $tr): string

    /
  - **`getEmpresa`**(\Facebook\WebDriver\Remote\RemoteWebElement $tr): array

    /
  - **`getIdSao`**(\Facebook\WebDriver\Remote\RemoteWebElement $tr): mixed|string

    /
  - **`getPeriode`**(\Facebook\WebDriver\Remote\RemoteWebElement $tr): array

    /
  - **`altaInstructor`**(string $instructorDNI, string $instructorName, string $emailCentre, string $telefonoCentre, $ciclo): Instructor

    /
  - **`getDni`**($centro, $dades, $ciclo): mixed

    /
  - **`getFct`**($dni, $idColaboracion, $asociacion, $erasmus): Fct

    /
  - **`saveFctAl`**(Fct $fct, $dades, $flexible=0): AlumnoFct

    /
  - **`extractPage`**(RemoteWebDriver $driver, array &$dades, $page): array

    /


### `app/Sao/SaoSyncAction.php`

#### `Intranet\Sao\SaoSyncAction`
Acció SAO per sincronitzar dades d'alumnat FCT.

- Metodes:
  - **`__construct`**($digitalSignatureService = null)
  - **`execute`**($driver, ?callable $queryCallback = null)
  - **`index`**($driver)
  - **`processFcts`**()
  - **`getValidFcts`**()
  - **`obtenirHoresFct`**($idSao): ?int
  - **`actualitzarFct`**($fct, $novaHora): void
  - **`consultaDiario`**()


### `app/Sao/Support/SaoDownloadManager.php`

#### `Intranet\Sao\Support\SaoDownloadManager`
Operacions comunes de fitxers temporals en processos SAO.

- Metodes:
  - **`tempDirectory`**(): string

    Retorna el directori temporal compartit per SAO.
  - **`waitForFile`**(string $filePath, int $timeoutSeconds): void

    Espera a l'existència d'un fitxer dins del timeout indicat.
  - **`unlinkIfExists`**(string $filePath): void

    Esborra un fitxer si existeix.


### `app/Sao/Support/SaoNavigator.php`

#### `Intranet\Sao\Support\SaoNavigator`
Utilitats bàsiques de navegació per al flux SAO.

- Metodes:
  - **`backToMain`**(RemoteWebDriver $driver, ?int $sleepSeconds = null): void

    Torna a la pantalla principal de SAO i aplica una xicoteta espera.


### `app/Sao/Support/SaoRunner.php`

#### `Intranet\Sao\Support\SaoRunner`
Gestiona el cicle de vida de Selenium per a accions SAO.

- Metodes:
  - **`run`**(string $className, string $dni, string $password, array $requestData, mixed $caps = null, ?UploadedFile $file = null): mixed

    Executa una acció SAO amb login previ i tancament garantit de sessió.
  - **`executeAction`**(string $className, mixed $driver, array $requestData, ?UploadedFile $file = null): mixed

    Resol i executa el mètode `index` de l'acció SAO.


### `app/Support/Concerns/DatesTranslator.php`

#### `Intranet\Support\Concerns\DatesTranslator`
- Metodes:
  - **`getCreatedAttribute`**($date)
  - **`getUpdatedAttribute`**($date)
  - **`getSalidaAttribute`**($date)
  - **`getEntradaAttribute`**($date)


### `app/Support/Facades/Field.php`

#### `Intranet\Support\Facades\Field`
Façana de compatibilitat per a l'API `Field::*`.

- Metodes:
  - **`getFacadeAccessor`**(): string

    /


### `app/Support/Fct/DocumentoFctConfig.php`

#### `Intranet\Support\Fct\DocumentoFctConfig`
- Metodes:
  - **`__construct`**($document)
  - **`__get`**($key)
  - **`__isset`**($key)
  - **`__set`**($key, $value)
  - **`getFinder`**()
  - **`getResource`**()


### `app/Support/Helpers/MyHelpers.php`

#### `asset_nocache`
- Metodes:
  - **`asset_nocache`**(string $path): string

    Genera una URL d'asset amb versió basada en `filemtime` per evitar caché antic.
  - **`profile_photo_url`**(?string $foto): string

    Retorna la URL de la foto de perfil o un placeholder si no existeix.
  - **`emailConselleria`**($nombre, $apellido1, $apellido2): string

    Genera un correu institucional de Conselleria a partir del nom i cognoms.
  - **`eliminarTildes`**($cadena): string

    Elimina espais i accents d'una cadena.
  - **`genre`**($persona, $masculi=''): mixed|string

    /
  - **`voteValue`**($dni, $value): int|float

    Ajusta aleatòriament el valor d'una votació per a un DNI concret.
  - **`evaluacion`**(): int

    Retorna l'avaluació actual segons les dates configurades en `curso.evaluaciones`.
  - **`curso`**(): string

    Retorna el curs acadèmic actual (`YYYY-YYYY+1`).
  - **`cursoAnterior`**(): string

    Retorna el curs acadèmic anterior.
  - **`fullDireccion`**(): string

    Devuelve la direccion completa
  - **`cargo`**($cargo): \Intranet\Entities\Profesor|null

    Retorna el professor associat a un càrrec configurat.
  - **`signatura`**($document): string|null

    Retorna la forma textual de signatura adequada al document i gènere de qui signa.
  - **`imgSig`**($document): string|null

    Retorna el codi d'imatge de signatura per a un document.
  - **`userIsNameAllow`**($role): bool

    Mira si al usuario actual le esta permitido el nombre de rol
  - **`authUser`**(): \Illuminate\Contracts\Auth\Authenticatable|null

    Retorna l'usuari autenticat de `profesor` o, en defecte, d'`alumno`.
  - **`apiAuthUser`**($token=null): \Intranet\Entities\Profesor|null

    Resol l'usuari professor per context API.
  - **`isProfesor`**(): bool

    Comprova si l'usuari autenticat és professor.
  - **`userIsAllow`**($role): bool

    Mira si al usuario actual le esta permitido el  rol
  - **`roleIsInArray`**(array $role, \Illuminate\Contracts\Auth\Authenticatable $usuario): bool

    /
  - **`nameRolesUser`**($rolUsuario): Array

    Devuelve todos los roles de un usuario
  - **`rolesUser`**($rolUsuario): Array

    Devuelve todos los roles de un usuario
  - **`esRol`**($rolUsuario, $rol): bool

    Comprova si un rol concret està inclòs dins del rol compost de l'usuari.
  - **`isAdmin`**(): bool

    Comprova si l'usuari autenticat té rol d'administració (11).
  - **`usersWithRol`**($rol): array

    Retorna els DNI dels professors actius que compleixen un rol determinat.
  - **`rol`**($roles): integer

    Devuelve el rol de un conjunto de roles
  - **`blankTrans`**($mensaje): array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Translation\Translator|string|null

    /
  - **`isblankTrans`**($mensaje): bool

    Indica si una clau de traducció no existeix.
  - **`valorReal`**($elemento, $string): mixed

    Resol una propietat simple o anidada (`foo->bar`) d'un element.
  - **`hazArray`**($elementos, $campo1, $campo2=null, $separador = ' '): array

    Construeix un array associatiu a partir d'una col·lecció/lista d'elements.
  - **`extrauValor`**($campo1, $elemento, $separador): array

    /
  - **`getClase`**($elemento): string

    Retorna el nom curt d'una classe o entitat.
  - **`getClass`**($str): string

    Retorna el nom curt d'una FQCN d'entitat.
  - **`avisa`**($id, $mensaje, $enlace = '#', $emisor = null): void

    Envia notificació interna a alumne/professor.
  - **`primryKey`**($elemento): mixed

    Retorna el valor de la clau primària de l'element.
  - **`subsRequest`**(Illuminate\Http\Request $request, $fields): Illuminate\Http\Request

    Substitueix valors en un Request i retorna una còpia.
  - **`mdFind`**($file, $link): string

    Retalla un fragment de documentació markdown des d'un enllaç concret.
  - **`existsHelp`**($url): mixed

    Retorna si hi ha ajuda associada a una URL de menú.
  - **`inRol`**($roles): array

    Prepara una estructura `['roles' => [...]]` per passar-la a components/polítiques de UI.
  - **`existsTranslate`**($text): string|null

    Retorna la traducció o `null` si no existeix.
  - **`firstWord`**($cadena): string

    Retorna la primera paraula d'una cadena separada per espais.
  - **`cargaDatosCertificado`**($datos, $date=null): mixed

    /
  - **`getClientIpAddress`**(): String

    Obté l'adreça IP client des de capçaleres comunes o `REMOTE_ADDR`.
  - **`isPrivateAddress`**($ip): bool

    Comprova si una IP pertany a rangs privats/predefinits de confiança.
  - **`mbUcfirst`**($string): string

    Capitalitza el primer caràcter d'una cadena multibyte.
  - **`nomAmbTitol`**($sexe, $nom): string

    Afig tractament (`en`, `na`, `n'`) a un nom segons sexe i vocal inicial.
  - **`deleteDir`**($folder): void

    Elimina tots els fitxers d'una carpeta i, després, la carpeta.
  - **`provincia`**($codiPostal): string

    Retorna el nom de província a partir del codi postal espanyol.
  - **`replaceCachitos`**($view): string

    Substitueix tokens `[nom]` per `@include('email.fct.cachitos.nom')` de manera recursiva.
  - **`in_substr`**($item, int $long): string

    Retalla valors llargs de forma segura, normalitzant tipus comuns (array, bool, dates...).
  - **`array_depth`**($array): int

    Calcula la profunditat màxima d'un array multidimensional.
  - **`asociacion_fct`**($tipus): int|string|null

    Retorna la clau associada a un tipus FCT en configuració.


### `app/UI/Botones/Boton.php`

#### `Intranet\UI\Botones\Boton`
- Metodes:
  - **`translateText`**()

    Resol el text del botó amb traduccions i textos per defecte.
  - **`translateExistingText`**()

    Tradueix un text ja proporcionat si hi ha clau existent.
  - **`__construct`**($href, $atributos = [], $relative = false, $postUrl = null)

    /
  - **`__set`**($name, $value)

    Assigna atributs dinàmics.
  - **`__get`**($name)

    Llig atributs dinàmics.
  - **`show`**($elemento = null): string

    Retorna el botó renderitzat perquè el caller el puga imprimir.
  - **`render`**($elemento = null)

    Retorna el botó renderitzat si l'usuari té permís.
  - **`html`**($key = null)
  - **`split`**()

    Separa model/acció a partir del `href`.
  - **`cleanAttr`**(?string $value): string

    Neteja valors per a atributs HTML (classes, id, etc.).
  - **`isDisabled`**(): bool

    Indica si el botó està deshabilitat.
  - **`clase`**(): string

    Retorna la classe CSS final del botó.
  - **`id`**($key = null): string|null

    Retorna l'ID HTML del botó.
  - **`disabledAttr`**(string $type = 'link'): string

    Retorna atributs per a desactivar el botó segons el tipus.
  - **`data`**(): string

    Retorna els atributs `data-*` en format HTML.
  - **`href`**($key = null): string

    Construeix l'URL final del botó.
  - **`getPrefix`**(): string

    Obté el prefix de ruta segons el mode `relative`.
  - **`getPostfix`**(): string

    Obté el sufix de ruta si està definit.
  - **`getAdress`**($key, $prefix, $close): string

    Construeix l'adreça final a partir de prefix, clau i sufix.


### `app/UI/Botones/BotonBasico.php`

#### `Intranet\UI\Botones\BotonBasico`
Botó bàsic amb renderització d'enllaç i icona opcional.

- Metodes:
  - **`html`**($key = null)

    Genera el HTML del botó bàsic.


### `app/UI/Botones/BotonConfirmacion.php`

#### `Intranet\UI\Botones\BotonConfirmacion`
Botó bàsic amb classe de confirmació.

- Metodes: cap


### `app/UI/Botones/BotonElemento.php`

#### `Intranet\UI\Botones\BotonElemento`
- Metodes:
  - **`show`**($elemento = null, $key = null): string

    Mostra el botó si compleix les condicions de visibilitat.
  - **`render`**($elemento = null)

    Retorna el botó renderitzat si compleix les condicions.
  - **`isVisible`**($elemento)

    Avalua si l'element compleix les condicions de visibilitat.
  - **`extractConditions`**($elemento, $condicio)

    Extreu i avalua les condicions configurades.
  - **`avalAndConditions`**($conditions)

    Avalua condicions amb AND.
  - **`avalOrConditions`**($conditions)

    Avalua condicions amb OR.
  - **`avalCondition`**($elemento, $op, $valor)

    Avalua una condició individual.


### `app/UI/Botones/BotonIcon.php`

#### `Intranet\UI\Botones\BotonIcon`
Botó amb icona (font-awesome).

- Metodes:
  - **`html`**($key = null)

    Genera el HTML del botó amb icona.


### `app/UI/Botones/BotonImg.php`

#### `Intranet\UI\Botones\BotonImg`
Botó amb icona en format imatge/font-awesome.

- Metodes:
  - **`__construct`**($href, $atributos = [], $relative = false, $postUrl = null)

    /
  - **`html`**($key = null)

    Genera el HTML del botó amb imatge.


### `app/UI/Botones/BotonPost.php`

#### `Intranet\UI\Botones\BotonPost`
Botó per a enviament de formulari (submit).

- Metodes:
  - **`html`**($key = null)

    Genera el HTML del botó tipus submit.


### `app/UI/Panels/Panel.php`

#### `Intranet\UI\Panels\Panel`
Contenidor de pestanyes, botons i dades de vista per als panells CRUD.

- Metodes:
  - **`__construct`**(string $modelo, ?array $rejilla = null, ?string $vista = null, bool $creaPestana = true, ?array $include = [])

    /
  - **`render`**($todos, $titulo, $vista, $formulario = null): View|RedirectResponse

    Ompli el panell i retorna la vista final.
  - **`setBotonera`**(array $index = [], array $grid = [], array $profile = []): void

    Crea una botonera estàndard a partir de noms d'accions.
  - **`setBoton`**(string $tipo, Boton $boton): void

    Afig un botó al grup indicat.
  - **`setBothBoton`**(string $href, array $atributos = [], bool $relative = false): void

    Afig el mateix botó a `grid` i `profile`.
  - **`setPestana`**(string $nombre, bool $activo = false, ?string $vista = null, ?array $filtro = null, ?array $rejilla = null, ?bool $sustituye = null, array $include = []): void

    Afig una pestanya o substituïx la primera.
  - **`countPestana`**(): int

    Retorna el nombre de pestanyes disponibles.
  - **`setTitulo`**(array $titulo): void

    /
  - **`desactivaAll`**(): void
  - **`getModel`**(): string

    Retorna el nom del model associat al panell.
  - **`getPestanas`**(): array

    Retorna totes les pestanyes del panell.
  - **`getRejilla`**(): ?array
  - **`setRejilla`**(?array $grid): void
  - **`getBotones`**(?string $tipo = null): array

    /
  - **`countBotones`**(string $tipo): int

    Retorna quants botons hi ha en un grup.
  - **`getTitulo`**(string $que = 'index'): string

    Resol el títol traduït segons el model i l'acció.
  - **`setElementos`**($elementos): void

    /
  - **`getElemento`**(): mixed

    /
  - **`getElementos`**(Pestana $pestana)
  - **`getPaginator`**(): ?Paginator

    Retorna el paginador si la cerca original era paginada.
  - **`activaPestana`**(string $nombre): void

    Activa una pestanya pel nom i desactiva la resta.
  - **`getView`**(string $nombre, ?string $vista): string
  - **`__set`**(string $name, $value): void
  - **`__get`**(string $name): mixed

    /
  - **`ensureValidBotonType`**(string $tipo): void

    Valida que el tipus pertany a la botonera coneguda.
  - **`feedPanel`**($todos, array $titulo): Panel

    /
  - **`getLastPestanaWithModals`**(): array


### `app/UI/Panels/Pestana.php`

#### `Intranet\UI\Panels\Pestana`
- Metodes:
  - **`__construct`**(string $nombre, bool $activa = false, ?string $vista = null, ?array $filtro = [], ?array $rejilla = null, array $include = [])

    /
  - **`setVista`**(?string $vista): void

    /
  - **`getVista`**(): ?string

    /
  - **`getNombre`**(): string

    /
  - **`getActiva`**(): string

    /
  - **`getInclude`**(string $index): array

    /
  - **`setInclude`**(array $include): void

    /
  - **`setActiva`**(bool $activa): void

    /
  - **`getFiltro`**(): array

    /
  - **`getRejilla`**(): ?array

    /
  - **`setRejilla`**(?array $grid): void

    /
  - **`getLabel`**(): string

    Retorna l'etiqueta traduïda; si no hi ha traducció, usa el nom original.


### `app/View/Components/Activity.php`

#### `Intranet\View\Components\Activity`
- Metodes:
  - **`__construct`**(ActivityModel $activity)
  - **`render`**()
  - **`getClass`**()
  - **`getAction`**()


### `app/View/Components/Botones.php`

#### `Intranet\View\Components\Botones`
- Metodes:
  - **`__construct`**(public mixed $panel, public string $tipo, public mixed $elemento = null, public bool $centrado = true)
  - **`render`**(): View|Closure|string


### `app/View/Components/Form/DynamicFieldRenderer.php`

#### `Intranet\View\Components\Form\DynamicFieldRenderer`
- Metodes:
  - **`__construct`**(string $name, string $type, ?string $label = null, mixed $value = null, array $params = [], mixed $currentFile = null)
  - **`render`**()


### `app/View/Components/Form/FileInput.php`

#### `Intranet\View\Components\Form\FileInput`
- Metodes:
  - **`__construct`**(string $name, string $label, mixed $currentFile = null, array $params = [])
  - **`render`**()


### `app/View/Components/Form/GenericField.php`

#### `Intranet\View\Components\Form\GenericField`
- Metodes:
  - **`__construct`**(string $name, string $type, mixed $value = null, array $params = [])
  - **`render`**()


### `app/View/Components/Form/TagInput.php`

#### `Intranet\View\Components\Form\TagInput`
- Metodes:
  - **`__construct`**(string $name, ?string $value = null)
  - **`render`**()


### `app/View/Components/Grid/Header.php`

#### `Intranet\View\Components\Grid\Header`
- Metodes:
  - **`__construct`**()

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Grid/Row.php`

#### `Intranet\View\Components\Grid\Row`
- Metodes:
  - **`__construct`**()

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Grid/Table.php`

#### `Intranet\View\Components\Grid\Table`
- Metodes:
  - **`__construct`**()

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Horari.php`

#### `Intranet\View\Components\Horari`
- Metodes:
  - **`__construct`**($horario, $config = [])
  - **`render`**()


### `app/View/Components/Label.php`

#### `Intranet\View\Components\Label`
- Metodes:
  - **`__construct`**($id, $cab1, $cab2, $title, $subtitle=null, $inside=null, $view='date')

    /
  - **`render`**()


### `app/View/Components/Layouts/App.php`

#### `Intranet\View\Components\Layouts\App`
- Metodes:
  - **`__construct`**(public $panel=null, public $title = ' ')

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Footer.php`

#### `Intranet\View\Components\Layouts\Footer`
- Metodes:
  - **`__construct`**()

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Leftside.php`

#### `Intranet\View\Components\Layouts\Leftside`
- Metodes:
  - **`__construct`**()

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Meta.php`

#### `Intranet\View\Components\Layouts\Meta`
- Metodes:
  - **`__construct`**()

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Page.php`

#### `Intranet\View\Components\Layouts\Page`
- Metodes:
  - **`__construct`**(public string $title = '')

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Panel.php`

#### `Intranet\View\Components\Layouts\Panel`
- Metodes:
  - **`__construct`**(public $panel, )

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Pestanas.php`

#### `Intranet\View\Components\Layouts\Pestanas`
- Metodes:
  - **`__construct`**(public $panel, public $elemento)

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Titlebar.php`

#### `Intranet\View\Components\Layouts\Titlebar`
- Metodes:
  - **`__construct`**()

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Topmenu.php`

#### `Intranet\View\Components\Layouts\Topmenu`
- Metodes:
  - **`__construct`**(FitxatgeService $fitxatgeService)

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Layouts/Topnav.php`

#### `Intranet\View\Components\Layouts\Topnav`
- Metodes:
  - **`__construct`**(FitxatgeService $fitxatgeService)

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/Llist.php`

#### `Intranet\View\Components\Llist`
- Metodes:
  - **`__construct`**($image, $date)

    /
  - **`render`**()


### `app/View/Components/Modal.php`

#### `Intranet\View\Components\Modal`
- Metodes:
  - **`__construct`**(String $name, String $title, String $message, String $action ="#", String $clase='', $cancel='Cancelar', $dismiss=true)

    Modal constructor.
  - **`render`**()


### `app/View/Components/Note.php`

#### `Intranet\View\Components\Note`
- Metodes:
  - **`__construct`**($name, $title, $message, $color, $linkEdit='#', $linkShow='#')

    /
  - **`render`**()


### `app/View/Components/ReunionItem.php`

#### `Intranet\View\Components\ReunionItem`
- Metodes:
  - **`__construct`**($reunion)
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/UserProfile.php`

#### `Intranet\View\Components\UserProfile`
- Metodes:
  - **`__construct`**($usuario)
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/UserTabs.php`

#### `Intranet\View\Components\UserTabs`
- Metodes:
  - **`__construct`**($tabs)
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/ui/Errors.php`

#### `Intranet\View\Components\ui\Errors`
- Metodes:
  - **`__construct`**()

    Create a new component instance.
  - **`render`**(): View|Closure|string

    Get the view / contents that represent the component.


### `app/View/Components/ui/Tabs.php`

#### `Intranet\View\Components\ui\Tabs`
- Metodes:
  - **`__construct`**(string $id, $panel)
  - **`render`**()



