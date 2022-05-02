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
        'composite_unique' => 'Ja existeix eixa combinació :attribute',
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
	| with something more reader friendly such as "E-MyMail Address" instead
	| of "email". Your users will thank you.
	|
	| The Validator class will automatically search this array of lines it
	| is attempting to replace the :attribute place-holder in messages.
	| It's pretty slick. We think you'll like it.
	|
	*/

	'attributes' => array(
                'read_at' => 'lleguida a',
		'username' => 'usuari/a',
		'password' => 'contrasenya',
                'name' => 'Nom',
                'nombre' => 'Nom',
                'surnames' => 'Cognoms',
                'fecha' => 'Data',
                'created_at' => "Data d'Alta",
                'hini' => "Hora d'inici",
                'hfin' => 'Hora de fi',
                'comentarios' => 'Comentaris',
                'descripcion' => ' Descripció',
                'objetivos' => 'Objectius',
                'desde' => "Data d'inici",
                'hasta' => 'Data de fin',
                'medio' => 'Mitjà de Locomoció',
                'marca' => 'Marca',
                'numero' => 'Nombre',
                'matricula' => 'Matrícula del Vehicle',
                'alojamiento' => "Despeses d'allotjament",
                'comida' => 'Despeses de menjar',
                'gastos' => 'Altres Despeses',
                'itinerario' => 'itinerari',
                'servicio' => 'Descripció del servei a realitzar',
                'otros' => 'Altres mitjans',
                'Profesor' => 'Professorat',
                'Alumno' => 'Alumnat',
                'Grupo' => 'Grup',
                'Evaluacion' => 'Avaluació',
                'motivo' => 'Motiu',
                'observaciones' => 'Observacions',
                'id' => 'Clau',
                'operaciones' => 'Operacions',
                'apellido1' => '1er Cognom',
                'apellido2' => '2on Cognom',
                'cargo' => 'Càrrec',
                'codigo' => 'Codi',
                'kilometraje' => 'Quilometratge',
                'emailItaca' => 'Email Itaca',
                'email' => 'Email Centre',
                'foto' => 'Enllaç Foto',
                'domicilio' => 'Adreça',
                'movil1' => 'Telèfon',
                'movil2' => 'Mòbil',
                'sexo' => 'Sexe',
                'codigo_postal' => 'Codi Postal',
                'departamento' => 'Departament',
                'fecha_ingreso' => "Data d'ingrés",
                'fecha_nac' => 'Data de Naixement',
                'fecha_baja' => 'Data de Baixa',
                'rol' => "Rol de la persona",
                'dni' => 'DNI',
                'nia' => 'NIA',
                'expediente' => 'Expedient',
                'telef1' => 'Telèfon',
                'telef2' => 'Telèfon',
                'fecha_matricula' => 'Data de matrícula',
                'repite' => 'És repetidor',
                'turno' => 'Torn',
                'trabaja' => 'Trebaja',
                'provincia' => 'Província',
                'municipio' => 'Població',
                'localidad' => 'Població',
                'direccion' => 'Adreça',
                'instructor' => 'Instructora',
                'telefono' => 'Telèfon',
                'años' => 'Anys',
                'activo' => 'Estat',
                'estado' => 'Estat',
                'fct' => 'FCT',
                'fin' => 'Data fi prevista',
                'tabla' => 'Taula',
                'accion' => 'Operació realitzada',
                'data' => 'Data i Hora',
                'Creador' => 'Creador',
                'Responsable' => 'Responsable',
                'Activo' => 'Actiu',
                'Inactivo' => 'Inactiu',
                'idModulo' => 'Codi mòdul',
                'fichero' => 'Nom fitxer',
                'motivos' => 'Motius',
                'literal' => 'Nom',
                'tipo' => 'Tipus',
                'fechasolucion' => 'Data Solució',
                'explicacion' => 'Explicació',
                'nomAlum' => "Nom de l'Alumne/a",
                'asiste' => 'Asisteix',
                'orden' => 'Ordre',
                'resumen' => 'Resumen',
                'idDocumento' => 'Doc.',
                'ciclo' => 'Cicle / Nivell',
                'contacto' => 'Persona contacte',
                'tutor' => 'Tutor',
                'puestos' => 'Llocs',
                'dual' => 'F.P Dual',
                'menores' => 'Certificat de Menors',
                'delitos' => 'Certificat de delictes',
                'concierto' => 'Concert',
                'sao' => 'Està registrada al SAO',
                'anexo1' => "Hi ha còpia d'annexe 1",
                'centro' => 'Centre Treball',
                'propietario' => 'Autora',
                'supervisor' => 'Creadora',
                'cliteral' => 'Descripción',
                'vliteral' => 'Descripció',
                'tags' => 'Etiquetes',
                'situacion' => 'Situació',
                'dia' => 'Dia',
                'hora' => 'Hora',
                'comentario' => 'Comentari Personal',
                'guardia' => 'Guàrdia Feta',
                'baja' => 'Baixa llarga durada',
                'mostrar' => 'Mostra telèfon',
                'Modulo' => 'Mòdul',
                'Tipo' => 'Tipus',
                'criterios' => "Valoració del criteris i ferramentes d'avaluació ",
                'metodologia' => "Valoració de la metodologia utilitzada",
                'propuestas' => 'Propostes de millora',
                'proyectos' => 'Projectes del departament',
                'idColaboracion' => 'Col.laboració',
                'asociacion' => 'Associació',
                'horas' => 'Hores',
                'obligatoria' => 'Obligatòria',
                'birret' => 'No he marcat birret',
                'enCentro' => 'Estava al centre',
                'horario' => 'Hores',
                'justificacion' => 'Justificació',
                'centros' => 'Centres de Treball',
                'Ncentros' => 'Nombre',
                'profesor' => 'Professorat',
                'Calidad' => 'Qualitat',
                'Matriculados' => 'Matriculats',
                'Resfct' => 'Fct',
                'Respro' => 'Projecte',
                'Resempresa' => 'Inserció',
                'Nfcts' => 'Fcts',
                'TutoresFct' => 'Altres tutors',
                'Lalumnes' => 'Alumnat',
                'nalumnes' => 'Nombre',
                'Instructor' => 'Instructor',
                'question' => 'Enunciat pregunta',
                'scala' => 'Escala valoració',
                'aviso' => 'Envia correu',
                'Dual' => 'Tutora Dual',
                'adquiridosNO' => "Valoració de la tasca del 3r Trimestre. Continguts que falten per impartir al módul",
                'nota' => 'Nota',
                'valoraciones' => "Valoració de competències, capacitats i nivell d'aprenentatge",
                'espacio' => 'Espai',
                'DesCurta' => 'Descripció',
                'punto' => 'Punt a tractar',
                'vencimiento' => 'Data venciment',
                'enlace' => 'Enllaç',
                'informativa' => 'És informativa?',
                'activa' => 'Activa',
                'destinatario' => 'Destinataries',
                'desenvolupament' => 'Desenvolupament',
                'valoracio' => 'Valoració Pedagògica',
                'aspectes' => 'Aspectes Transversals',
                'dades' =>  'Altres dades',
                'recomanada' => 'Es recomanable per a el curs següent',
                'dia_completo' => 'Tot el dia',
                'visita' => 'Visita',
                'alumno' => 'Alumnat',
                'periode' => 'Periode'
             ),
        'empty_option' => array(
            'default' => '-Selecciona-',
            'otros'   => '-Selecciona vehicle-',
            'motivos' => "-Selecciona Motiu de l'absència-"
        ),
        
);
