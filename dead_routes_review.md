# Rutes candidates a revisar

A continuacio tens les rutes que l'escaneig static marca com a candidates a estar mortes (metode no trobat en el codi de l'app). Revisa-les una a una.

## Posibles rutes mortes (app)

Estat revisat manualment:

- [ELIMINADA] routes/profesor.php: actividad/campo/{campo} -> Intranet\Http\Controllers\ActividadController::includegrid
- [ELIMINADA] routes/profesor.php: /reunion (resource) -> Intranet\Http\Controllers\ReunionController::show (ara exclosa en `except`)
- [ELIMINADA] routes/profesor.php: /reunion/{reunion}/coordinador/{profesor} -> Intranet\Http\Controllers\ReunionController::Coordinador
- [ELIMINADA] routes/profesor.php: reunion/campo/{campo} -> Intranet\Http\Controllers\ReunionController::includegrid
- [ELIMINADA] routes/profesor.php: /grupotrabajo (resource) -> Intranet\Http\Controllers\GrupoTrabajoController::show (ara exclosa en `except`)
- [ELIMINADA] routes/profesor.php: /curso (resource) -> Intranet\Http\Controllers\CursoController::show (ara exclosa en `except`)
- [ELIMINADA] routes/profesor.php: /alumno_grupo (resource) -> Intranet\Http\Controllers\AlumnoGrupoController::store (ara exclosa en `except`)
- [ELIMINADA] routes/profesor.php: /colaboracion (resource) -> Intranet\Http\Controllers\ColaboracionController::store (ara exclosa en `except`)
- [ELIMINADA] routes/profesor.php: /alumnofct (resource) -> Intranet\Http\Controllers\FctAlumnoController::store (ara exclosa en `except`)
- [ELIMINADA] routes/profesor.php: /espacio/verMateriales/{espacio} -> Intranet\Http\Controllers\EspacioController::getMateriales
- [ELIMINADA] routes/todos.php: projecte (resource) -> Intranet\Http\Controllers\ProjecteController::show (ara exclosa en `except`)
- [ELIMINADA] routes/administrador.php: /modulo (resource) -> Intranet\Http\Controllers\ModuloController::store (ara exclosa en `except`)
- [ELIMINADA] routes/administrador.php: /departamento (resource) -> Intranet\Http\Controllers\DepartamentoController::show (ara exclosa en `except`)
- [ELIMINADA] routes/administrador.php: /task (resource) -> Intranet\Http\Controllers\TaskController::show (ara exclosa en `except`)
- [ELIMINADA] routes/administrador.php: /horario (resource) -> Intranet\Http\Controllers\HorarioController::show (ara exclosa en `except`)
- [ELIMINADA] routes/administrador.php: /horario (resource) -> Intranet\Http\Controllers\HorarioController::store (ara exclosa en `except`)
- [ELIMINADA] routes/administrador.php: /ipguardia (resource) -> Intranet\Http\Controllers\IpGuardiaController::show (ara exclosa en `except`)
- [ELIMINADA] routes/administrador.php: /setting (resource) -> Intranet\Http\Controllers\SettingController::show (ara exclosa en `except`)
- [ELIMINADA] routes/api.php: misAlumnosFct -> Intranet\Http\Controllers\API\AlumnoFctController::misAlumnos
- [ELIMINADA] routes/api.php: ipguardia (resource) -> Intranet\Http\Controllers\API\IpGuardiaController::create (ara exclosa en `except`)
- [ELIMINADA] routes/api.php: setting (resource) -> Intranet\Http\Controllers\API\SettingController::create (ara exclosa en `except`)
- [ELIMINADA] routes/api.php: ficha -> Intranet\Http\Controllers\API\ProfesorController::ficha
- [ELIMINADA] routes/api.php: guardia (resource) -> Intranet\Http\Controllers\API\GuardiaController::create (ara exclosa en `except`)
- [ELIMINADA] routes/api.php: departamento (resource) -> Intranet\Http\Controllers\API\DepartamentoController::create (ara exclosa en `except`)
- [ELIMINADA] routes/api.php: reserva (resource) -> Intranet\Http\Controllers\API\ReservaController::create (ara exclosa en `except`)
- [ELIMINADA] routes/api.php: alumnoresultado (resource) -> Intranet\Http\Controllers\API\AlumnoResultadoContoller::create (ara exclosa en `except`)
- [ELIMINADA] routes/api.php: articuloLote (resource) -> Intranet\Http\Controllers\API\ArticuloLoteController::create (ara exclosa en `except`)
- [ELIMINADA] routes/api.php: articulo (resource) -> Intranet\Http\Controllers\API\ArticuloController::create (ara exclosa en `except`)
- [ELIMINADA] routes/api.php: cotxe (resource) -> Intranet\Http\Controllers\API\CotxeController::create (ara exclosa en `except`)
- [ELIMINADA] routes/api.php: tipoactividad (resource) -> Intranet\Http\Controllers\API\TipoActividadController::create (ara exclosa en `except`)

## Possibles falsos positius (traits de Laravel)

Estes rutes venen de traits de Laravel (no estan en el codi de l'app), per tant no les considere mortes.

- routes/public.php: password/reset -> Intranet\Http\Controllers\Auth\ForgotPasswordController::showLinkRequestForm
- routes/public.php: password/reset -> Intranet\Http\Controllers\Auth\ResetPasswordController::reset
