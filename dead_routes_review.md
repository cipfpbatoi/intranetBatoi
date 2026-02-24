# Rutes candidates a revisar

A continuacio tens les rutes que l'escaneig static marca com a candidates a estar mortes (metode no trobat en el codi de l'app). Revisa-les una a una.

## Posibles rutes mortes (app)

- routes/profesor.php: actividad/campo/{campo} -> Intranet\Http\Controllers\ActividadController::includegrid
- routes/profesor.php: /reunion (resource) -> Intranet\Http\Controllers\ReunionController::show
- routes/profesor.php: /reunion/{reunion}/coordinador/{profesor} -> Intranet\Http\Controllers\ReunionController::Coordinador
- routes/profesor.php: reunion/campo/{campo} -> Intranet\Http\Controllers\ReunionController::includegrid
- routes/profesor.php: /grupotrabajo (resource) -> Intranet\Http\Controllers\GrupoTrabajoController::show
- routes/profesor.php: /curso (resource) -> Intranet\Http\Controllers\CursoController::show
- routes/profesor.php: /alumno_grupo (resource) -> Intranet\Http\Controllers\AlumnoGrupoController::store
- routes/profesor.php: /colaboracion (resource) -> Intranet\Http\Controllers\ColaboracionController::store
- routes/profesor.php: /alumnofct (resource) -> Intranet\Http\Controllers\FctAlumnoController::store
- routes/profesor.php: /espacio/verMateriales/{espacio} -> Intranet\Http\Controllers\EspacioController::getMateriales
- routes/todos.php: projecte (resource) -> Intranet\Http\Controllers\ProjecteController::show
- routes/administrador.php: /modulo (resource) -> Intranet\Http\Controllers\ModuloController::store
- routes/administrador.php: /departamento (resource) -> Intranet\Http\Controllers\DepartamentoController::show
- routes/administrador.php: /task (resource) -> Intranet\Http\Controllers\TaskController::show
- routes/administrador.php: /horario (resource) -> Intranet\Http\Controllers\HorarioController::show
- routes/administrador.php: /horario (resource) -> Intranet\Http\Controllers\HorarioController::store
- routes/administrador.php: /ipguardia (resource) -> Intranet\Http\Controllers\IpGuardiaController::show
- routes/administrador.php: /setting (resource) -> Intranet\Http\Controllers\SettingController::show
- routes/api.php: misAlumnosFct -> Intranet\Http\Controllers\API\AlumnoFctController::misAlumnos
- routes/api.php: ipguardia (resource) -> Intranet\Http\Controllers\API\IpGuardiaController::create
- routes/api.php: setting (resource) -> Intranet\Http\Controllers\API\SettingController::create
- routes/api.php: ficha -> Intranet\Http\Controllers\API\ProfesorController::ficha
- routes/api.php: guardia (resource) -> Intranet\Http\Controllers\API\GuardiaController::create
- routes/api.php: departamento (resource) -> Intranet\Http\Controllers\API\DepartamentoController::create
- routes/api.php: reserva (resource) -> Intranet\Http\Controllers\API\ReservaController::create
- routes/api.php: alumnoresultado (resource) -> Intranet\Http\Controllers\API\AlumnoResultadoContoller::create
- routes/api.php: articuloLote (resource) -> Intranet\Http\Controllers\API\ArticuloLoteController::create
- routes/api.php: articulo (resource) -> Intranet\Http\Controllers\API\ArticuloController::create
- routes/api.php: cotxe (resource) -> Intranet\Http\Controllers\API\CotxeController::create
- routes/api.php: tipoactividad (resource) -> Intranet\Http\Controllers\API\TipoActividadController::create

## Possibles falsos positius (traits de Laravel)

Estes rutes venen de traits de Laravel (no estan en el codi de l'app), per tant no les considere mortes.

- routes/public.php: password/reset -> Intranet\Http\Controllers\Auth\ForgotPasswordController::showLinkRequestForm
- routes/public.php: password/reset -> Intranet\Http\Controllers\Auth\ResetPasswordController::reset
