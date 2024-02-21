<?php

return [
    'contacto' => [
        'email' => [
            'subject' => "Sol·licitud Pràctiques de FCT",
            'toPeople' => 'A/A de Recursos Humans',
            'editable' => true,
            'register' => true
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
            'register' => true
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
            'register' => true
        ],
        'modelo' => 'Fct',
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
            'register' => false
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
            'register' => true
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
            'register' => true
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
            'register' => true
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
            'register' => false
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
            'register' => false,
            'editable' => false
        ],
        'modelo' => 'Fct',
        'view' => 'email.fct.confirm',
    ],
    'pg0301' => [
        'pdf' => [
            'descripcion' => 'FULL DE CONTROL DE SERVEI',
            'orientacion' => 'portrait'],
        'modelo' => 'AlumnoFct',
        'finder' => 'AlumnoNoFct',
        'route' => 'alumnofct',
        'view' => 'pdf.fct.controlServei',
    ],
    'pr0401' => [
        'pdf' => [
            'descripcion' => "ENTREVISTA DEL TUTOR AMB L'INSTRUCTOR DE L'EMPRESA",
            'orientacion' => 'landscape'
        ],
        'modelo' => 'Fct',
        'finder' => 'Fct',
        'route' => 'alumnofct',
        'view' => 'pdf.fct.seguimentInstructor',
        'zip' => true
    ],
    'pr0402' => [
        'pdf' => [
            'descripcion' => "ENTREVISTA DEL TUTOR AMB L'ALUMNAT",
            'orientacion' => 'landscape'
        ],
        'modelo' => 'AlumnoFct',
        'finder' => 'AlumnoEnFct',
        'route' => 'alumnofct',
        'view' => 'pdf.fct.seguimentAlumnes',
    ],
    'autTutor' => [
        'printResource' => 'AutorizacionGrupoResource',
        'modelo' => 'AlumnoFct',
        'route' => 'autTutor',
        'finder' => 'AlumnoEnFct',
        'multiple' => false,
        'sign' => true,
    ],
    'autDireccio' => [
        'printResource' => 'AutorizacionDireccionGrupoResource',
        'modelo' => 'AlumnoFct',
        'route' => 'alumnofct',
        'finder' => 'AlumnoEnFct',
        'multiple' => false,
    ],
    'autAlumnat' => [
        'printResource' => 'ConformidadAlumnadoGrupoResource',
        'modelo' => 'AlumnoFct',
        'route' => 'alumnofct',
        'finder' => 'AlumnoEnFct',
        'multiple' => false,
    ],
    'A1' => [
        'modelo' => 'Signatura',
        'route' => 'alumnofct',
        'finder' => 'A1',
        'zip' => true,
    ],
    'A2' => [
        'modelo' => 'Signatura',
        'route' => 'A2',
        'finder' => 'A2',
        'zip' => true,
        'sign' => true,
    ],
    'A3' => [
        'modelo' => 'Signatura',
        'route' => 'A3',
        'finder' => 'A3',
        'editable' => true,
    ],
    'A5' => [
        'modelo' => 'AlumnoFct',
        'route' => 'alumnofct',
        'finder' => 'AlumnoEnFct',
        'printResource' => 'A5Resource',
        'zip' => true
    ],
    'Signed' => [
        'modelo' => 'AlumnoFct',
        'finder' => 'Signed',
    ],
];
