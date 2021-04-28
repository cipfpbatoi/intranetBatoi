<?php

return [
    'actaIni' => [
        'subject' => "Informació relativa a l'inici de les Pràctiques de FCT",
        'receiver' => "Alumnat",
        'view' => 'email.fct.ini',
        'fcts' => 0,
    ],
    'contact' => [
        'subject' => "Sol·licitud Pràctiques de FCT",
        'receiver' => 'A/A de Recursos Humans',
        'view' => 'email.fct.contact',
        'redirect' => 'misColaboraciones',
        'fcts' => 0,
        'modelo' => 'Colaboracion'
     ],
    'request' => [
        'subject' => 'Revisió Documentació Pràctiques',
        'view' => 'email.fct.request',
        'viewContent' => 'email.fct.requestU',
        'receiver' => 'A/A de Recursos Humans',
        'fcts' => 0,
        'modelo' => 'Colaboracion'
    ],
    'info' => [
        'subject' => "Recordatori inici de pràctiques",
        'receiver' => 'Instructor',
        'view' => 'email.fct.info',
        'viewContent' =>'email.fct.infoU',
        'fcts' => 1,
        'finder' => ['service' => 'FctAlumnoFindService'],
    ],
    'follow' => [
        'subject' => "Seguiment Pràctiques de FCT",
        'receiver' => 'Instructor',
        'view' => 'email.fct.follow',
        'viewContent' =>  'email.fct.followU',
        'fcts' => 1,
        'default' => 'Les pràctiques es desenvolupen amb normalitat',
    ],
    'visit' => [
        'subject' => "Concertar visita de FCT",
        'receiver' => 'Instructor',
        'view' => 'email.fct.visit',
        'redirect' => 'misColaboraciones',
        'fcts' => 1,
        'default' => 'Les pràctiques es desenvolupen amb normalitat',
    ],
    'student' => [
        'subject' => "Citar alumnes per seguiment de FCT",
        'receiver' => 'Alumno',
        'view' => 'email.fct.student',
        'redirect' => 'misColaboraciones',
        'fcts' => 1,
    ],

];
