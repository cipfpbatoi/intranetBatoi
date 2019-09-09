<?php

return [
    'contact' => [
        'subject' => "Sol·licitud Pràctiques de FCT",
        'receiver' => 'A/A de Recursos Humans',
        'view' => 'email.fct.contact',
        'redirect' => 'misColaboraciones',
        'fcts' => 0,
    ],
    'request' => [
        'subject' => 'Revisió Documentació Pràctiques',
        'view' => 'email.fct.request',
        'receiver' => 'A/A de Recursos Humans',
        'fcts' => 0,
    ],
    'requestU' => [
        'subject' => 'Revisió Documentació Pràctiques',
        'view' => 'email.fct.requestU',
        'receiver' => 'A/A de Recursos Humans',
        'redirect' => 'misColaboraciones',
        'fcts' => 0,
    ],
    'info' => [
        'subject' => "Recordatori inici de pràctiques",
        'receiver' => 'Instructor',
        'view' => 'email.fct.info',
        'fcts' => 1,
    ],
    'infoU' => [
        'subject' => "Recordatori inici de pràctiques",
        'receiver' => 'Instructor',
        'view' => 'email.fct.infoU',
        'redirect' => 'misColaboraciones',
        'fcts' => 1,
    ],
    'follow' => [
        'subject' => "Seguiment Pràctiques de FCT",
        'receiver' => 'Instructor',
        'view' => 'email.fct.follow',
        'fcts' => 1,
    ],
    'followU' => [
        'subject' => "Seguiment Pràctiques de FCT",
        'receiver' => 'Instructor',
        'view' => 'email.fct.followU',
        'redirect' => 'misColaboraciones',
        'fcts' => 1,
    ],
    'visit' => [
        'subject' => "Concertar visita de FCT",
        'receiver' => 'Instructor',
        'view' => 'email.fct.visit',
        'redirect' => 'misColaboraciones',
        'fcts' => 1,
    ],
    'student' => [
        'subject' => "Citar alumnes per seguiment de FCT",
        'receiver' => 'Alumno',
        'view' => 'email.fct.student',
        'redirect' => 'misColaboraciones',
        'fcts' => 1,
    ],

];
