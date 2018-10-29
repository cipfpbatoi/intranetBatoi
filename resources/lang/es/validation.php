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

	"accepted"       => "El campo :attribute debe ser aceptado.",
	"active_url"     => "El campo :attribute no es una URL válida.",
	"after"          => "El campo :attribute debe ser una fecha después de :date.",
	"alpha"          => "El campo :attribute sólo puede contener letras.",
	"alpha_dash"     => "El campo :attribute sólo puede contener letras, números y guiones.",
	"alpha_num"      => "El campo :attribute sólo puede contener letras y números.",
	"array"          => "El campo :attribute debe ser un arreglo.",
	"before"         => "El campo :attribute debe ser una fecha antes :date.",
	"between"        => array(
			"numeric" => "El campo :attribute debe estar entre :min - :max.",
			"file"    => "El campo :attribute debe estar entre :min - :max kilobytes.",
			"string"  => "El campo :attribute debe estar entre :min - :max caracteres.",
			"array"   => "El campo :attribute debe tener entre :min y :max elementos.",
	),
	"boolean"        => "El campo :attribute debe ser verdadero o falso.",
        'composite_unique' => 'Ya existe esa combinación :attribute',
    	"confirmed"      => "El campo de confirmación de :attribute no coincide.",
	"date"           => "El campo :attribute no es una fecha válida.",
	"date_format" 	 => "El campo :attribute no corresponde con el formato :format.",
	"different"      => "Los campos :attribute y :other deben ser diferentes.",
	"digits"         => "El campo :attribute debe ser de :digits dígitos.",
	"digits_between" => "El campo :attribute debe tener entre :min y :max dígitos.",
	"email"          => "El formato del :attribute es inválido.",
	"exists"         => "El campo :attribute seleccionado es inválido.",
        "filled"         => 'El campo :attribute es requerido.',	
	"image"          => "El campo :attribute debe ser una imagen.",
	"in"             => "El campo :attribute seleccionado es inválido.",
	"integer"        => "El campo :attribute debe ser un entero.",
	"ip"             => "El campo :attribute debe ser una dirección IP válida.",
	"json"           => "El campo :attribute debe ser una cadena JSON válida.",
	"match"          => "El formato :attribute es inválido.",
	"max"            => array(
			"numeric" => "El campo :attribute debe ser menor que :max.",
			"file"    => "El campo :attribute debe ser menor que :max kilobytes.",
			"string"  => "El campo :attribute debe ser menor que :max caracteres.",
			"array"   => "El campo :attribute debe tener al menos :min elementos.",
		),

	"mimes"         => "El campo :attribute debe ser un archivo de tipo :values.",
	"min"           => array(
			"numeric" => "El campo :attribute debe tener al menos :min.",
			"file"    => "El campo :attribute debe tener al menos :min kilobytes.",
			"string"  => "El campo :attribute debe tener al menos :min caracteres.",
	),
	"not_in"                => "El campo :attribute seleccionado es invalido.",
	"numeric"               => "El campo :attribute debe ser un número.",
	"regex"                 => "El formato del campo :attribute es inválido.",
	"required"              => "El campo :attribute es requerido.",
	"required_if"           => "El campo :attribute es requerido cuando el campo :other es :value.",
        "required_unless"       => 'El campo :attribute es requerido a menos que :other esté presente en :values.',	
	"required_with"         => "El campo :attribute es requerido cuando :values está presente.",
	"required_with_all"     => "El campo :attribute es requerido cuando :values está presente.",
	"required_without"      => "El campo :attribute es requerido cuando :values no está presente.",
	"required_without_all"  => "El campo :attribute es requerido cuando ningún :values está presente.",
	"same"                  => "El campo :attribute y :other debe coincidir.",
	"size"                  => array(
				"numeric" => "El campo :attribute debe ser :size.",
				"file"    => "El campo :attribute debe tener :size kilobytes.",
				"string"  => "El campo :attribute debe tener :size caracteres.",
				"array"   => "El campo :attribute debe contener :size elementos.",
	),
	"string"               => "El campo :attribute debe ser una cadena.",
	"unique"         => "El campo :attribute ya ha sido tomado.",
	"url"            => "El formato de :attribute es inválido.",
	"timezone"       => "El campo :attribute debe ser una zona válida.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute_rule" to name the lines. This helps keep your
	| custom validation clean and tidy.
	|
	| So, say you want to use a custom validation message when validating that
	| the "email" attribute is unique. Just add "email_unique" to this array
	| with your custom message. The Validator will handle the rest!
	|
	*/

	'custom' => array(
		'attribute-name' => array(
		    'rule-name'  => 'custom-message',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as "E-Mail Address" instead
	| of "email". Your users will thank you.
	|
	| The Validator class will automatically search this array of lines it
	| is attempting to replace the :attribute place-holder in messages.
	| It's pretty slick. We think you'll like it.
	|
	*/

	'attributes' => array(
                'read_at' => 'leído en',
		'username' => 'usuario',
		'password' => 'contraseña',
                'name' => 'Nombre',
                'nombre' => 'Nombre',
                'surnames' => 'Apellidos',
                'fecha' => 'Fecha',
                'created_at' => 'Fecha de Alta',
                'hini' => 'Hora de inicio',
                'hfin' => 'Hora de fin',
                'comentarios' => 'Comentarios',
                'descripcion' => ' Descripción',
                'objetivos' => 'Objetivos',
                'desde' => 'Fecha de inicio',
                'hasta' => 'Fecha de fin',
                'medio' => 'Medio de Locomoción',
                'marca' => 'Marca',
                'numero' => 'Número',
                'matricula' => 'Matricula del Vehículo',
                'alojamiento' => 'Dieta de alojamiento',
                'comida' => 'Dieta de comida',
                'gastos' => 'Otros Gastos',
                'servicio' => 'Descripción del servicio a realizar',
                'otros' => 'Otros medios',
                'Profesor' => 'Profesor',
                'Alumno' => 'Alumno',
                'Grupo' => 'Grupo',
                'motivo' => 'Motivo',
                'observaciones' => 'Observaciones',
                'id' => 'Clave',
                'operaciones' => 'Operaciones',
                'apellido1' => '1º Apellido',
                'apellido2' => '2º Apellido',
                'cargo' => 'Cargo',
                'codigo' => 'Código',
                'kilometraje' => 'Kilometraje',
                'emailItaca' => 'Email Itaca',
                'email' => 'Email Centro',
                'foto' => 'Enlace Foto',
                'domicilio' => 'Dirección',
                'movil1' => 'Teléfono',
                'movil2' => 'Móvil',
                'sexo' => 'Sexo',
                'codigo_postal' => 'Código Postal',
                'departamento' => 'Departamento',
                'fecha_ingreso' => 'Fecha de Ingreso',
                'fecha_nac' => 'Fecha de Nacimiento',
                'fecha_baja' => 'Fecha de Baja',
                'rol' => 'Rol de Usuario',
                'dni' => 'DNI',
                'nia' => 'NIA',
                'expediente' => 'Expediente',
                'telef1' => 'Teléfono',
                'telef2' => 'Telefono',
                'fecha_matricula' => 'Fecha de matricula',
                'repite' => 'Es repetidor',
                'turno' => 'Turno',
                'trabaja' => 'Trabaja',
                'provincia' => 'Provincia',
                'municipio' => 'Población',
                'localidad' => 'Población',
                'direccion' => 'Dirección',
                'instructor' => 'Instructor',
                'telefono' => 'Teléfono',
                'años' => 'Años',
                'activo' => 'Estado',
                'estado' => 'Estado',
                'fct' => 'FCT',
                'fin' => 'Fecha fin prevista',
                'tabla' => 'Tabla',
                'accion' => 'Operación realizada',
                'data' => 'Fecha y Hora',
                'Creador' => 'Creador',
                'Responsable' => 'Responsable',
                'Activo' => 'Activo',
                'Inactivo' => 'Inactivo',
                'idModulo' => 'Código módulo',
                'fichero' => 'Nombre fichero',
                'evaluacion' => 'Evaluación',
                'motivos' => 'Motivos',
                'literal' => 'Nombre',
                'tipo' => 'Tipo',
                'fechasolucion' => 'Fecha Solución',
                'explicacion' => 'Explicación',
                'nomAlum' => 'Nombre del Alumno',
                'asiste' => 'Asiste',
                'orden' => 'Orden',
                'resumen' => 'Resumen',
                'idDocumento' => 'Doc.',
                'ciclo' => 'Ciclo',
                'contacto' => 'Persona contacto',
                'tutor' => 'Tutor',
                'puestos' => 'Puestos',
                'dual' => 'F.P Dual',
                'menores' => 'Certificado de Menores',
                'delitos' => 'Certificado de delitos',
                'concierto' => 'Concierto',
                'sao' => 'Está registrada en el SAO',
                'anexo1' => 'Hay copia de anexo 1',
                'centro' => 'Centro Trabajo',
                'propietario' => 'Autor',
                'supervisor' => 'Creador',
                'cliteral' => 'Descripción',
                'vliteral' => 'Descripció',
                'tags' => 'Etiquetas',
                'situacion' => 'Situación',
                'dia' => 'Día',
                'hora' => 'Hora',
                'comentario' => 'Comentario Personal',
                'guardia' => 'Guardia Hecha',
                'baja' => 'Baja larga duración',
                'mostrar' => 'Mostrar teléfono',
                'modulo' => 'Módulo',
                'tipo' => 'Tipo',
                'criterios' => "Valoración de los criterios y herramientas de evaluación",
                'metodologia' => "Valoración de la metodología utilizada",
                'propuestas' => 'Propuestas de mejora',
                'proyectos' => 'Proyectos del departamento',
                'idColaboracion' => 'Colaboración',
                'asociacion' => 'Asociación',
                'horas' => 'Horas',
                'obligatoria' => 'Obligatoria',
                'birret' => 'No he marcado birret',
                'enCentro' => 'Estaba en el centro',
                'horario' => 'Horas',
                'justificacion' => 'Justificación',
                'centros' => 'Centros de Trabajo',
                'Ncentros' => 'Número',
                'profesor' => 'Profesor',
                'Resempresa' => 'Inserción',
                'NFcts' => 'Fcts',
                'TutoresFct' => 'Otros tutores',
                'nalumnes' => 'Número',
                'Instructor' => 'Instructor'
             ),
        'empty_option' => array(
            'default' => '-Selecciona-',
            'otros'   => '-Selecciona vehiculo-',
            'motivos' => '-Selecciona Motivo de la ausencia-',
            'departamento' => '-Selecciona departamento-',
            'idioma' => '-Selecciona Idioma-',
        ),
        
);
