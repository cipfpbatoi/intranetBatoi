<?php

return [
    'contacto' => [
        'email' => [
            'subject' => "Sol·licitud Pràctiques de FCT",
            'toPeople' => 'A/A de Recursos Humans',
            'editable' => true,
            'register' => 'Sol·licitud Pràctiques de FCT'
        ],
        'modelo' => 'Colaboracion',
        'view' => 'email.fct.contact',
        'template' => 'email.fct.contact',
        'route' => 'misColaboraciones',
        'fcts' => 0,
        'estado' => 1,
        'unique' => 1,
    ],
    'revision' => [
        'email' => [
            'subject' => 'Revisió Documentació Pràctiques',
            'toPeople' => 'A/A de Recursos Humans',
            'editable' => false,
            'register' => 'Revisió Documentació Pràctiques'
        ],
        'modelo' => 'Colaboracion',
        'view' => 'email.fct.request',
        'template' => 'email.fct.requestU',
        'route' => 'misColaboraciones',
        'fcts' => 0,
        'estado' => 2,
        'unique' => 1,

    ],
    'inicioEmpresa' => [
        'email' => [
            'subject' => "Recordatori inici de pràctiques",
            'toPeople' => 'Instructor',
            'editable' => true,
            'register' => "Recordatori inici de pràctiques"
        ],
        'modelo' => 'Fct',
        'finder' => 'Fct',
        'template' =>'email.fct.info',
        'route' => 'fct',
        'fcts' => 1,
        'unique' => 1,
    ],
    'finEmpresa' => [
        'email' => [
            'subject' => "Finalització de pràctiques",
            'toPeople' => 'Instructor',
            'editable' => true,
        ],
        'modelo' => 'Fct',
        'template' =>'email.fct.finish',
        'route' => 'fct',
        'fcts' => 1,
        'unique' => 1,
    ],
    'inicioAlumno' => [
        'email' => [
            'subject' => "Informació relativa a l'inici de les Pràctiques de FCT",
            'toPeople' => "Alumnat",
            'editable' => false,
            'register' => "Informació relativa a l'inici de les Pràctiques de FCT",
        ],
        'modelo'=> 'AlumnoFct',
        'finder' => 'AlumnoNoFct',
        'view' => 'email.fct.ini',
        'route' => 'fct',
        'template' => 'email.fct.ini',
        'fcts' => 0,
        'unique' => 1,

    ],
    'seguimiento' => [
        'email' => [
            'subject' => "Seguiment Pràctiques de FCT",
            'toPeople' => 'Instructor',
            'editable' => true,
            'register' => "Seguiment Pràctiques de FCT",
        ],
        'modelo' => 'Fct',
        'template' =>  'email.fct.follow',
        'route' => 'fct',
        'fcts' => 1,
        'unique' => 0,
        'default' => 'Les pràctiques es desenvolupen amb normalitat',

    ],
    'visitaEmpresa' => [
        'email' => [
            'subject' => "Concertar visita de FCT",
            'toPeople' => 'Instructor',
            'editable' => true,
            'register' => "Concertar visita de FCT",
        ],
        'modelo' => 'Fct',
        'template' => 'email.fct.visit',
        'route' => 'fct',
        'fcts' => 1,
        'unique' => 0,
        'default' => 'Les pràctiques es desenvolupen amb normalitat',
    ],
    'citarAlumnos' => [
        'email' => [
            'subject' => "Citar alumnes per seguiment de FCT",
            'toPeople' => 'Alumno',
            'editable' => true,
        ],
        'modelo' => 'AlumnoFct',
        'finder' => 'AlumnoEnFct',
        'route' => 'fct',
        'template' => 'email.fct.student',
        'fcts' => 1,
        'unique' => 0,
    ],
    'visitaComision' => [
        'email' => [
            'subject' => "Visita Empresa",
            'toPeople' => 'Instructor',
            'editable' => false
        ],
        'modelo' => 'Fct',
        'view' => 'email.fct.confirm',
    ],
 ];
