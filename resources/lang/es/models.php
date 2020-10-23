<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used
	| by the validator class. Some of the rules contain multiple versions,
	| such as the size (max, min, between) rules. These versions are used
	| for different input types such as strings and files.
	|
	| These language lines may be easily changed to provide custom error
	| messages in your application. Error messages for custom validation
	| rules may also be added to this file.
	|
	*/

	
        'Alumno' => array(
          'show' => 'Datos del Alumno',
          'edit' => 'Modificación Perfil Alumno'  
        ),
	'Comision' => array(
		'create'=>'Solicitud autorización comisión Servicio',
                'index'=>'Gestionar comisiones Servicio',
                'edit'=> 'Modificación comisión de servicio',
                'alta'=> 'Alta Comisión de Servicio',
                'autorizar' => 'Autorizar comisiones pendientes',
                'pdf' => 'Imprimir Comisiones autorizadas',
                'delete' => 'Borrar Comisión',
                'notification' => 'Avisar equipo educativo',
                'email' => 'Enviar para autorizar',
                'show' => 'Muestra Comisión id.',
                '3' => 'Registrada',
                '4' => 'Pendiente Cobro',
                '2' => 'Autorizada',
                '1' => 'Enviada',
                '-1' => 'Anulada',
                '5' => 'Cobrada',
                '0' => 'No autorizada/comunicada',
                'unpaid' => 'Cobrar',
                'paid' => 'Imprimir pagos'
	     ),
        'Falta' => array(
		'create'=>'Comunicación de Ausencia Profesorado',
                'index'=>'Gestionar Bajas',
                'edit'=> 'Modificación Ausencia',
                'alta' => 'Alta Ausencia',
                'resolve'=> 'Autorizar Bajas',
                'pdf' => 'Imprimir Bajas pendientes',
                'notification' => 'Avisat Equipo',
                'email' => 'Comunicar a dirección',
                'show' => 'Ver Ausencia id.',
                'imprime' => 'Imprimir informe mensual',
                '3' => 'Autorizada',
                '4' => 'Resuelta',
                '2' => 'Justificada',
                '1' => 'Sin justificante',
                '5' => 'Larga Duración',
                '0' => 'No enviada/autorizada'
                
             ),
        'Profesor' => array(
                'edit' => 'Modificar Perfil Profesor',
                'show' => 'Datos Profesor',
                'index' => 'Listado Profesores',
                'list' => 'Profesores Ausentes',
                 'horario-cambiar' => 'Cambiar horario Professr',
            
        ),
        'Menu' => array(
                'index' => 'Elementos del menú',
                'create' => 'Crear entrada',
                'edit' => 'Editar elemento del menú'
        ),
        'Alumnogrupo' => array(
            'index' => 'Gestionar Grupo :quien',
            'carnet' => 'Imprime Carnets de Grupo',
            'profile' => 'Plantilla Alumnos',
            'show' => 'Lista Alumnos',
            'edit' => 'Editar Perfil Alumno',
        ),
        'Inventario' => array(
            'espacios' => 'Ver Espacios',
            'materiales' => 'Ver Materiales'
        ),
        'Alumno_curso' => array(
            'index' => 'Alumnos Curso',
        ),
        'Actividad' => array(
            'index' => 'Gestionar Actividades extraescolares',
            'create' => 'Alta actividad',
            'createO' => 'Alta activitat Orientació',
            'autorizacion' => 'Imprimir Autorización menores',
            'edit' => 'Modificar Actividad extraescolar',
            'detalle' => 'Detalle actividad',
            'titulo' => 'Detalle actividad :actividad',
            'profesores' => 'Profesores participantes',
            'grupos' => 'Grupos participantes',
            'email' => 'Enviar para autorizar',
            'pdf' => 'Imprimir Actividades Autorizadas',
            'delete' => 'Borrar Actividad',
            'autorizar' => 'Autorizar todas actividades',
            'notification' => 'Avisar profesorado',
            '2' => 'Autorizada',
            '3' => 'Impresa',
            '1' => 'Pendiente',
            '0' => 'Rechazada/No comunicada'
        ),
        'Incidencia' => array(
            'index' => 'Gestionar Incidencias',
            'create' => 'Crear Incidencia',
            'email' => 'Pasar a en proceso',
            'authorize' => 'Tratar Incidencia',
            'unauthorize' => 'Abandonar Incidencia',
            'resolve' => 'Resolver Incidencia',
            'edit' => 'Modificar Incidencia',
            'notification' => 'Avisa Responsable',
            '3' => 'Resuelta',
            '2' => 'En proceso',
            '1' => 'Asignada',
            '0' => 'No comunicada'
        ),
        'Material' => array(
            'index' => 'Gestionar Materiales',
            'create' => 'Crear Material',
            'delete' => 'Borrar Material',
            'edit' => 'Editar Material',
            'list' => 'Listado Inventario'
        ),
        'Espacio' => array(
            'index' => 'Gestionar Espacios',
            'create' => 'Crear Espacio',
            'delete' => 'Borrar Espacio',
            'edit' => 'Editar Espacio',
            'detalle' => 'Ver Materiales'
        ),
        'Grupo' => array(
            'index' => 'Grupos',
            'detalle' => 'Ver alumnos',
            'edit' => 'Editar Grupo',
            'pdf' => 'Imprimir Hoja de Fotos',
            'asigna' => 'Asigna ciclo Automáticamente'
        ),
        'Curso' => array(
            'create' => 'Nuevo Curso/Ponencia',
            'index' => 'Gestionar Cursos/Ponencias',
            'edit' => 'Modifica Curso/Ponencia',
        ),
        'Notification' => array(
            'index' => 'Notificaciones',
            'read' => 'Marca cómo leída',
            'show' => 'Detalles Notificación',
        ),
        'Programacion' => array(
            'item0' => 'Propuestas de mejora',
            'item1' => 'Secuenciación',
            'item2' => 'Contenidos',
            'item3' => 'Metodología',
            'item4' => 'Avaluación.Criterios de calificación',
            'item5' => 'Programa de recuperación',
            'item6' => 'Recursos',
            'index' => 'Mantenimiento Programaciones',
            'create' => 'Nueva Programación',
            'checklist' => "Confirmar Requisitos",
            'save' => 'Guardar requisitos',
            'edit' => 'Editar programación',
            'email' => 'Enviar para revisar',
            'seguimiento' => 'Llenar el seguimiento de la programación',
            'resolve' => 'Aprobar',
            'show' => 'Mostrar Programación id.',
            '2' => 'Comprobada y cumple los requirimientos.',
            '3' => 'Aprobada por el departamento',
            '1' => 'Pendiente de revisar',
            '0' => 'Rechazada. Le faltan los siguientes requisitos: '
        ),
        'Guardia' =>  array(
            'edit' => 'Guardia a realizar',
            'create' => 'Datos de la guardia',
            'control' => 'Control de las guardias'
        ),
        'Modulo' => array(
            'asigna' => 'Asigna Ciclo Automáticamente',
            'index' => 'Mantenimiento módulos',
            'edit' => 'Modifica módulo',
            'list' => 'Estado Programaciones'
        ),
        'Expediente' => array(
            'show' => 'Muestra expediente id.',
            'index' => 'Gestión de expedientes',
            'create' => 'Nuevo expediente',
            'edit' => 'Editar expediente',
            'pdf' => 'Imprimir Tramitados',
            'authorize' => 'Tramitar',
            'unauthorize' => 'No tramitar',
            'autorizar' => 'Tramitar todos expedientes',
            '2' => 'En tramite',
            '3' => 'Resueltos',
            '1' => 'Peniente de revisar',
            '0' => 'No comunicado/Rechazado',
            '4' => 'Comunicado Orientador',
            '5' => 'Tratado Orientador',
        ),
        'Reunion'=> array(
            'show' => 'Muestra reunion id.',
            'index' => 'Gestión de reuniones',
            'create' => 'Nueva reunión',
            'edit' => 'Editar ',
            'pdf' => 'Imprimir',
            'email' => 'Enviar convocatoria/acta',
            'notification' => 'Avisar participantes',
            'titulo' => 'Participantes reunión',
            'participantes' => 'Asistentes',
            'ordenes' => 'Orden del dia',
            'detalle' => 'Datos reunión',
            'saveFile' => 'Archivar',
            'deleteFile' => 'Borrar Acta Archivada',
            'control' => 'Control Reuniones por grupo',
            'alumnos' => 'Avaluación individual'
        ),
        'Grupotrabajo'=> array(
            'show' => 'Muestra grupo de trabajo id.',
            'index' => 'Gestión de Grupos',
            'create' => 'Nuevo Grupo',
            'edit' => 'Editar Grupo',
            'participantes' => 'Miembros'
        ),
        'Resultado' => array(
            'index' => 'Gestiona tus resultados de evaluación',
            'create' => 'Crea resultados para un grupo',
            'edit' => 'Edita resultados',
            'delete' => 'Borra resultados',
            'informe' => 'Gestionar Informes de Departamento',
            'llenar' => 'Llenar datos complementarios',
            'faltan' => 'Para generar el informe faltan los resultados de los siguientes módulos',
            'avisa' => 'Avisa Profesores',
            'estan' => 'Tienes todos los resultados del departamento disponibles',
            'generado' => 'El informe del departamento está disponible'
        ),
        'Alumnoresultado' => array(
            'index' => 'Insertar calificaciones del modulo/grupo :quien',
            'create' => 'Añadir calificación'
        ),
        'Documento' => array(
            'index' => 'Buscar Documentos',
            'create' => 'Crear Documento',
            'edit' => 'Modifica Documento',
            'default' => 'Modifica Documentación Calidad',
        ),
        'Proyecto' => array(
            'create' => 'Subir proyecto alumno'
        ),
        'Empresa' => array(
            'index' => 'Listado Empresas',
            'create' => 'Crear empresa',
            'edit' => 'Editar empresa',
            'delete' => 'Borrar empresa',
            'detalle' => 'Editar centro de trabajo',
            'anexo' => 'Editar colaboraciones'
        ),
        'Centro' => array(
            'index' => 'Centros de trabajo de :quien',
            'edit' => 'Editar centro',
            'delete' => 'Borrar centro',
            'copy' => 'Crear centro'
        ),
        'Colaboracion' => array(
            'index' => 'Colaboraciones de trabajo de :quien',
            'edit' => 'Editar colaboración',
            'delete' => 'Borrar colaboración',
            'copy' => 'Crear colaboración',
            'inicia' => 'Reinicia ciclo de contactos',
            'resolve' => 'SI',
            'unauthorize' => '??',
            'refuse' => 'No',
            'contacto' => 'Solicitud de prácticas',
            'info' => 'Revisión documentación',
            'documentacion' => "Recordatorio inicio",
            'seguimiento' => "Seguimeinto",
            'student' => 'Citar Alumnos',
            'visita' => "Concertar Visita",
            'switch' => 'Mi',
            'contactos' => "Contactes con la empresa",
            'fctAl' => "Contactos con el alumnando",
            'fct' => 'FCTs',
        ),
        'Fct' => array(
            'index' => 'FCT x Empresa',
            'create' => 'Nueva Fct',
            'edit' => 'Editar Fct',
            'delete' => 'Borrar Fct',
            'fin' => 'Finalizar Fct',
            'pdf' => 'Imprimir certificados',
            'mail' => 'Enviar emails feedback',
            'apte' => 'Calificar Apto',
            'noApte' => 'Calificar No Apto',
            'noAval' => 'No Evaluado',
            'noProyecto' => 'No presenta Proyecto',
            'nuevoProyecto' => 'Accede a Proyecto',
            'nullProyecto' => 'Borra projecte',
            'upload' => 'Subir información Calidad',
            'convalidacion' => 'FCT Convalidada/Exento',
            'pg0301' => 'Imprime Hoja Control Servicio',
            'pr0301' => 'Imprime Hoja para Informar Alumnado)',
            'pr0601' => 'Imprime PR06-01 (Entrega Certificado)',
            'pr0402' => 'Imprime Entrevista Alumnado',
            'pr0401' => 'Imprime Entrevista Final Instructor',
            'list' => 'Acta Grupo :quien',
            'acta' => 'Pedir acta de evaluación',
            'insercio' => "Inserción Laboral",
            'proyecto' => 'Proyecto',
            'send' => 'Enviar Correo Inicial Alumnos',
            'default' => 'x Empresa',
        ),
        'Dual' => array(
            'index' => 'FP Dual',
            'create' => 'Nueva Dual',
            'edit' => 'Editar Dual',
            'delete' => 'Borrar Dual',
            'fin' => 'Finalizar Dual',
            'anexe_vii' => 'Imprimir Anexo VII',
            'anexe_va' => 'Imprimier Anexo V(a)',
            'anexe_vb' => 'Imprimier Anexo V(b)',
            'anexeVI' => 'Imprimir Anexo VI',
            'anexeXIV' => 'Imprimir Anexo XIV',
            'anexeXIII' => 'Imprimir AnexoXIII',
            'firma' => 'Generar zip firma'
        ),
        'Direccion' => array(
           'acta' => 'Acta completada' 
        ),
        'Falta_profesor' => array(
            'index' => 'Panel Fichaje'
        ),
        'Tutoria' => array(
            'index' => 'Índice de Tutorías :que',
            'edit' => 'Editar tutoría',
            'delete' => 'Borrar tutoría',
            'document' => 'Ver fichero',
            'detalle' => 'Ver comentarios',
            'create' => 'Crear tutoría',
            'anexo' => 'Crear comentario',
            
        ),
        'GrupoTrabajo' => array(
            'index' => 'Grupos trabajo',
            'create' => 'Crear grupo trabajo',
        ),
        'Tutoriagrupo' => array(
            'index' => 'Listado feedbacks :que',
            'edit' => 'Editar FeedBack tutoría',
            'create' => 'Crear FeedBack tutoría',
            'list' => 'Listado feedbacks :que'
        ),
        'Ordentrabajo' => array(
            'index' => 'Gestión Órdenes de Trabajo',
            'edit' => 'Editar orden',
            'delete' => 'Borrar orden',
            'anexo' => 'Ver incidencias',
            'pdf' => 'Listar orden',
            'open' => 'Abrir orden a más incidencias',
            'resolve' => 'Finalizar orden'
        ),
        'Ciclo' => array(
            'index' => 'Mantenimento ciclos',
            'create' => 'Nuevo ciclo',
            'edit' => 'Editar ciclo',
            'delete' => 'Suprimir ciclo'
        ),
        'Falta_itaca' => array(
            'index' => 'Sin marcaje Birret',
            'edit' => 'Editar Birret',
            'resolve' => 'Justificar',
            '0' => 'No comunicada',
            '1' => 'Pendiente',
            '2' => 'Justificada',
            '3' => 'Rechazada'
        ),
        'Instructor' => array(
          'index' => 'Consulta de instructores',
           'edit' => 'Editar instructor',
           'create' => 'Crear instructor',
           'copy' => 'Copiar instructor'
        ),
    '   Infdepartamento' => array(
          'index' => 'Consulta informes de departamento',  
          'create' => 'Crear Informe departamento',
          'edit' => 'Modificar informe departamento',
          'avisa' => 'Avisa Profesores falta informe',  
          'pdf' => 'Ver Informe departamento'
        ),
        'Horario' => array(
            'index' => 'Modificar Horario :quien',
            'edit' => 'Edita Horario',
            'cambiar' => "Canviar funciones horario",
        ),
        'AlumnoFct' => array(
          'index' => 'Fct x Alumno',
          'convalidacion' => 'FCT Convalidada/Exento',

            'create' => 'Nueva Exención',
            'edit' => 'Modificar Datos Fct',
            'pdf' => 'Imprimir certificados Alumno',
            'email' => 'Enviar Correos Valoración Alumno',
            'pg0301' => "Entregada documentación",
            'default' => 'x Alumne',
        ),
        'Alumnofctaval' => array(
          'index' => 'Avaluació FCT i projecte',
        ),
        'Fctcap' => array(
            'index' => 'Control Fct :quien',
            'check' => 'Control documentación'

        ),
        'Poll' => array(
            'show' => 'Muestra resultados',
            'index' => 'Mantenimento Encuestas',
            'edit' => 'Editar Encuesta',
            'chart' => 'Ver resultados agregados',
            'do' => 'Contesta',
        ),
        'Ppoll' => array(
            'show' => 'Plantilla Encuesta',
            'index' => 'Mantenimento Plantillas Encuestas',
            'edit' => 'Editar Plantilla',
            'slave' => 'Ver preguntas',
        ),
        'modelos' => array(
            'Comision' => 'Comisión de Servicio',
            'Curso' => 'Curso',
            'Grupo' => 'Grupo',
            'Falta' => 'Falta',
            'Actividad' => 'Actividad Extraescolar',
            'Profesor' => 'Profesor',
            'AlumnoGrupo' => 'Alumnos del grupo',
            'Alumno' => 'Alumno',
            'Menu' => 'Menu general',
            'Programacion' => 'Programación',
            'Expediente' => 'Expediente',
            'Reunion' => 'Reunión',
            'GrupoTrabajo' => 'Grupo de Trabajo',
            'OrdenReunion' => 'Orden',
            'Resultado' => 'Resultado Evaluación',
            'Documento' => 'Gestor Documental',
            'Empresa' => 'Empresa FCT',
            'Centro' => 'Centro de Trabajo',
            'Colaboracion' => 'Colaboración',
            'Fct' => 'FCT',
            'Tutoria' => 'Tutoría',
            'TutoriaGrupo' => 'FeedBack Tutoría',
            'Material' => 'Material',
            'Incidencia' => 'Incidencia',
            'OrdenTrabajo' => 'Orden de Trabajo',
            'Ciclo' => 'Ciclo',
            'Falta_itaca' => 'Justificar Birret',
            'Instructor' => 'Instructor',
            'Proyecto' => 'Proyecto',
            'Evaluacion' => 'Avaluación',
            'Colaborador' => 'Col.laborador',
            'Ppoll' => 'Plantilla encuesta',
            'Poll' => 'Encuesta',
            'Option' => 'Pregunta',
            'AlumnoResultado' => 'Avaluacio Alumne'
        ),
        'resign' => array(
            'Falta' => 'El documento no justifica la baja',
        ),
        'refuse' => array(
            'Programacion' => 'En la programación faltan los siguientes requisitos: ',
            
        ),
        'deliver' => array(
            'Falta' => 'Tienes que justificar la falta en Jefatura',
        ),
        'accept' => array(
            'Expediente' => "Baja por inasistencia del alumno :alumno iniciada automáticamente por el tutor :profesor",
        ),
        
);
