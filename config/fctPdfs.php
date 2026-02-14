<?php

return [
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
        'finder' => 'FctActiva',
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
        'modelo' => 'Alumno',
        'route' => 'autTutor',
        'finder' => 'AlumnoFctNo',
        'multiple' => false,
        'sign' => true,
    ],
    'autDireccio' => [
        'printResource' => 'AutorizacionDireccionGrupoResource',
        'modelo' => 'Alumno',
        'route' => 'alumnofct',
        'finder' => 'AlumnoFctNo',
        'multiple' => false,
    ],
    'autAlumnat' => [
        'printResource' => 'ConformidadAlumnadoGrupoResource',
        'modelo' => 'Alumno',
        'route' => 'alumnofct',
        'finder' => 'AlumnoFctNo',
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
