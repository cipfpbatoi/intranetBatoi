<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'The :attribute must be accepted.',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => 'The :attribute must be a date after :date.',
    'after_or_equal'       => 'The :attribute must be a date after or equal to :date.',
    'alpha'                => 'The :attribute may only contain letters.',
    'alpha_dash'           => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => 'The :attribute may only contain letters and numbers.',
    'array'                => 'The :attribute must be an array.',
    'before'               => 'The :attribute must be a date before :date.',
    'before_or_equal'      => 'The :attribute must be a date before or equal to :date.',
    'between'              => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'The :attribute confirmation does not match.',
    'date'                 => 'The :attribute is not a valid date.',
    'date_format'          => 'The :attribute does not match the format :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => 'The :attribute must be a valid email address.',
    'exists'               => 'The selected :attribute is invalid.',
    'file'                 => 'The :attribute must be a file.',
    'filled'               => 'The :attribute field must have a value.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => 'The selected :attribute is invalid.',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'ipv4'                 => 'The :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'The :attribute must be a valid IPv6 address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'mimetypes'            => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'not_regex'            => 'The :attribute format is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => 'The :attribute has already been taken.',
    'uploaded'             => 'The :attribute failed to upload.',
    'url'                  => 'The :attribute format is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => array(
                'read_at' =>'read in',
		'username' =>'username',
		'password' =>'password'
               ,'name' =>'name'
               ,'nombre' =>'Name'
               ,'surnames' => 'Surnames'
               ,'fecha' =>'Date'
               ,'created_at' =>'Entry Date'
               ,'hini' =>'Starting time'
               ,'hfin' =>'Ending time'
               ,'comentarios' =>'Comments'
               ,'descripcion' =>'Description'
               ,'objetivos' =>'Objectives'
               ,'desde' =>'Starting date'
               ,'hasta' =>'End date'
               ,'medio' =>'Means of transport'
               ,'marca' =>'Make'
               ,'numero' =>'Model'
               ,'matricula' =>'Vehicle registration'
               ,'alojamiento' =>'Accomodation expenses'
               ,'comida' =>'Meals expenses'
               ,'gastos' =>'Other expenses'
               ,'servicio' =>'Service description to carry out'
               ,'otros' =>'Other means'
               ,'Profesor' =>'Teacher'
               ,'Alumno' =>'Student'
               ,'Grupo' =>'Group'
               ,'Evaluacion' =>'Assesment'
               ,'motivo' =>'Reason'
               ,'observaciones' =>'Comments'
               ,'id' =>'Key'
               ,'operaciones' =>'Operations'
               ,'apellido1' =>'First Surname'
               ,'apellido2' =>'Second surname'
               ,'cargo' =>'Position'
               ,'codigo' =>'Code'
               ,'kilometraje' =>'Kilometers'
               ,'emailItaca' =>'Itaca e-mail'
               ,'email' =>'School e-mail'
               ,'foto' =>'Link Photography'
               ,'domicilio' =>'Address'
               ,'movil1' =>'Telephone'
               ,'movil2' =>'Mobile'
               ,'sexo' =>'Sex'
               ,'codigo_postal' =>'Postal code'
               ,'departamento' =>'Department'
               ,'fecha_ingreso' =>'Entry date'
               ,'fecha_nac' =>'Date of birth'
               ,'fecha_baja' =>'Leaving date'
               ,'rol' =>"User's role"
               ,'dni' =>"ID card"
               ,'nia' =>"NIA"
               ,'expediente' =>'File'
               ,'telef1' =>'Telephone'
               ,'telef2' =>'Telephone'
               ,'fecha_matricula' =>'Registration date'
               ,'repite' =>'Repeat year'
               ,'turno' =>'Shift'
               ,'trabaja' =>'Work'
               ,'provincia' =>'Province'
               ,'municipio' =>'Town'
               ,'localidad' =>'Locality'
               ,'direccion' =>'Address'
               ,'instructor' =>'Instructor'
               ,'telefono' =>'Telephone'
               ,'años' =>'Years'
               ,'activo' =>'Status'
               ,'estado' =>'Status'
               ,'fct' =>'FCT'
               ,'fin' =>'Scheduled finish date'
               ,'tabla' =>'Chart'
               ,'accion' =>'Activity performed'
               ,'data' =>'Date and hour'
               ,'Creador' =>'Creator'
               ,'Responsable' =>'Person in charge'
               ,'Activo' =>'Active'
               ,'Inactivo' =>'Inactive'
               ,'idModulo' =>'Module Code'
               ,'fichero' =>'File name'
               ,'motivos' =>'Reasons'
               ,'literal' =>'Name'
               ,'tipo' =>'Types'
               ,'fechasolucion' =>'Solution date'
               ,'explicacion' =>'Comments'
               ,'nomAlum' =>"Student's name"
               ,'asiste' =>'Attend'
               ,'orden' =>'Command'
               ,'resumen' =>'Summary'
               ,'idDocumento' =>'Doc.'
               ,'ciclo' =>'Cycle'
               ,'contacto' =>'Contact person'
               ,'tutor' =>'Tutor'
               ,'puestos' =>'Places'
               ,'dual' =>'F.P. Dual Mode'
               ,'menores' =>'Minors Certificate'
               ,'delitos' =>'Certificate of Previous Penalties of Crimes'
               ,'concierto' =>'Concert'
               ,'sao' =>'It has been registered with SAO'
               ,'anexo1' =>'Threre is a copy of Appendix1'
               ,'centro' =>'Work place'
               ,'propietario' =>'Author'
               ,'supervisor' =>'Creator'
               ,'cliteral' =>'Description'
               ,'vliteral' =>'Description'
               ,'tags' =>'Tags'
               ,'situacion' =>'Situation'
               ,'dia' =>'Day'
               ,'hora' =>'Hour'
               ,'comentario' =>'Personal Coment'
               ,'guardia' =>'On Call Completed'
               ,'baja' =>'Long term sick leave'
               ,'mostrar' =>'Show telephone'
               ,'Modulo' =>'Module'
               ,'Tipo' =>'Type'
               ,'criterios' =>'Assesment of the criteria and assesment tools'
               ,'metodologia' =>'Assesment of the methodology used'
               ,'propuestas' =>'Improvement proposals'
               ,'proyectos' =>'Department projects'
               ,'idColaboracion' =>'Collaboration'
               ,'asociacion' =>'Association'
               ,'horas' =>'Hours'
               ,'obligatoria' =>'Compulsory'
               ,'birret' =>'I have not ticked the cap'
               ,'enCentro' =>'I was at the center'
               ,'horario' =>'Hours'
               ,'justificacion' =>'Justification'
               ,'centros' =>'Work places'
               ,'Ncentros' =>'Name'
               ,'Resempresa' => 'Inserció'
               ,'Nfcts' => 'Fcts',
               'TutoresFct' => 'Others tutors',
               'nalumnes' => 'Collect',
               'Instructor' => 'Instructor'
             ),
       'empty_option' => array(
           'default' =>'Select'
           ,'otros'   =>'Select motor vehicle'
           ,'motivos' =>'Select reason for the absence'
        ),

];
