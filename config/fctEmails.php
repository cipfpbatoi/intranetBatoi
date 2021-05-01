<?php

return [
    'contacto' => [
        'email' => [
            'subject' => "Sol·licitud Pràctiques de FCT",
            'toPeople' => 'A/A de Recursos Humans',
            'editable' => true
        ],
        'view' => 'email.fct.contact',
        'template' => 'email.fct.contact',
        'redirect' => 'misColaboraciones',
        'fcts' => 0,
        'estado' => 1,
        'unique' => 1,
    ],
    'revision' => [
        'email' => [
            'subject' => 'Revisió Documentació Pràctiques',
            'toPeople' => 'A/A de Recursos Humans',
            'editable' => false
        ],
        'view' => 'email.fct.request',
        'template' => 'email.fct.requestU',
        'fcts' => 0,
        'estado' => 2,
        'unique' => 1,

    ],
    'inicioEmpresa' => [
        'email' => [
            'subject' => "Recordatori inici de pràctiques",
            'to' => 'Instructor',
            'editable' => false
        ],
        'view' => 'email.fct.info',
        'template' =>'email.fct.infoU',
        'fcts' => 1,
        'unique' => 1,

    ],
    'inicioAlumno' => [
        'email' => [
            'subject' => "Informació relativa a l'inici de les Pràctiques de FCT",
            'toPeople' => "Alumnat",
            'editable' => false
        ],
        'view' => 'email.fct.ini',
        'template' => 'email.fct.ini',
        'fcts' => 0,
        'unique' => 1,

    ],
    'seguimiento' => [
        'email' => [
            'subject' => "Seguiment Pràctiques de FCT",
            'toPeople' => 'Instructor',
            'editable' => false
        ],
        'view' => 'email.fct.follow',
        'template' =>  'email.fct.followU',
        'fcts' => 1,
        'unique' => 0,
        'default' => 'Les pràctiques es desenvolupen amb normalitat',

    ],
    'visitaEmpresa' => [
        'email' => [
            'subject' => "Concertar visita de FCT",
            'toPeople' => 'Instructor',
            'editable' => true
        ],
        'template' => 'email.fct.visit',
        'redirect' => 'misColaboraciones',
        'fcts' => 1,
        'unique' => 0,
        'default' => 'Les pràctiques es desenvolupen amb normalitat',

    ],
    'citarAlumnos' => [
        'email' => [
            'subject' => "Citar alumnes per seguiment de FCT",
            'toPeople' => 'Alumno',
            'editable' => true
        ],
        'view' => 'email.fct.student',
        'template' => 'email.fct.student',
        'redirect' => 'misColaboraciones',
        'fcts' => 1,
        'unique' => 0,

    ],
];
