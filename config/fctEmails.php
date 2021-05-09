<?php

return [
    'contacto' => [
        'email' => [
            'subject' => "Sol·licitud Pràctiques de FCT",
            'toPeople' => 'A/A de Recursos Humans',
            'editable' => true,
            'register' => false
        ],
        'modelo' => 'Colaboracion',
        'view' => 'email.fct.contact',
        'template' => 'email.fct.contact',
        'fcts' => 0,
        'estado' => 1,
        'unique' => 1,

    ],
    'revision' => [
        'email' => [
            'subject' => 'Revisió Documentació Pràctiques',
            'toPeople' => 'A/A de Recursos Humans',
            'editable' => false,
            'register' => true
        ],
        'modelo' => 'Colaboracion',
        'view' => 'email.fct.request',
        'template' => 'email.fct.requestU',
        'fcts' => 0,
        'estado' => 2,
        'unique' => 1,

    ],
    'inicioEmpresa' => [
        'email' => [
            'subject' => "Recordatori inici de pràctiques",
            'toPeople' => 'Instructor',
            'editable' => false,
            'register' => true
        ],
        'modelo' => 'Fct',
        'view' => 'email.fct.info',
        'template' =>'email.fct.infoU',
        'fcts' => 1,
        'unique' => 1,


    ],
    'inicioAlumno' => [
        'email' => [
            'subject' => "Informació relativa a l'inici de les Pràctiques de FCT",
            'toPeople' => "Alumnat",
            'editable' => false,
            'register' => true
        ],
        'modelo'=> 'AlumnoFct',
        'view' => 'email.fct.ini',
        'template' => 'email.fct.ini',
        'fcts' => 0,
        'unique' => 1,

    ],
    'seguimiento' => [
        'email' => [
            'subject' => "Seguiment Pràctiques de FCT",
            'toPeople' => 'Instructor',
            'editable' => false,
            'register' => true
        ],
        'modelo' => 'Fct',
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
            'editable' => true,
            'register' => true
        ],
        'modelo' => 'Fct',
        'template' => 'email.fct.visit',
        'fcts' => 1,
        'unique' => 0,
        'default' => 'Les pràctiques es desenvolupen amb normalitat',


    ],
    'citarAlumnos' => [
        'email' => [
            'subject' => "Citar alumnes per seguiment de FCT",
            'toPeople' => 'Alumno',
            'editable' => true,
            'register' => false
        ],
        'modelo' => 'AlumnoFct',
        'view' => 'email.fct.student',
        'template' => 'email.fct.student',
        'fcts' => 1,
        'unique' => 0,
    ],
    'visitaComision' => [
        'email' => [
            'subject' => "Visita Empresa",
            'toPeople' => 'Instructor',
            'register' => false,
            'editable' => false
        ],
        'modelo' => 'Fct',
        'view' => 'email.fct.confirm',

    ],
    'pg0301' => [
        'pdf' => [
            'documento'=> 'PG03-01',
            'nombre' => 'MANUAL DE PROCEDIMIENTOS',
            'descripcion' => 'HOJA DE CONTROL DE SERVICIO',
            'orientacion' => 'portrait'],
        'modelo' => 'AlumnoFct',
        'finder' => 'AlumnoNoFct',
        'view' => 'pdf.fct.pg0301',
    ],
    'pr0401' => [
        'pdf' => ['documento'=> 'PR04-01',
            'nombre' => 'MANUAL DE PROCESOS',
            'descripcion' => 'ENTREVISTA DEL TUTOR CON EL INSTRUCTOR DE LA EMPRESA',
            'orientacion' => 'landscape'
        ],
        'modelo' => 'AlumnoFct',
        'finder' => 'AlumnoEnFct',
        'view' => 'pdf.fct.pr0401',
    ],
    'pr0402' => [
        'pdf' => ['documento'=> 'PR04-02',
            'nombre' => 'MANUAL DE PROCESOS',
            'descripcion' => 'ENTREVISTA DEL TUTOR CON ALUMNO',
            'orientacion' => 'landscape'
        ],
        'modelo' => 'AlumnoFct',
        'finder' => 'AlumnoEnFct',
        'view' => 'pdf.fct.pr0402',
    ],
    'pasqua' => [
        'pdf' => ['documento'=> 'PASQUA',
            'nombre' => 'FCT VACACIONES DE PASQUA',
            'orientacion' => 'portrait'
        ],
        'modelo' => 'AlumnoFct',
        'finder' => 'AlumnoEnFct',
        'view' => 'pdf.fct.pasqua',
    ]
];
