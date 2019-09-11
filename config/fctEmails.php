<?php

return [
    'contact' => [
        'subject' => "Sol·licitud Pràctiques de FCT",
        'receiver' => 'A/A de Recursos Humans',
        'view' => 'email.fct.contact',
        'redirect' => 'misColaboraciones',
        'fcts' => 0,
        'default' => '',
    ],
    'request' => [
        'subject' => 'Revisió Documentació Pràctiques',
        'view' => 'email.fct.request',
        'receiver' => 'A/A de Recursos Humans',
        'fcts' => 0,
        'default' => '',
    ],
    'requestU' => [
        'subject' => 'Revisió Documentació Pràctiques',
        'view' => 'email.fct.requestU',
        'receiver' => 'A/A de Recursos Humans',
        'redirect' => 'misColaboraciones',
        'fcts' => 0,
        'default' => '',
    ],
    'info' => [
        'subject' => "Recordatori inici de pràctiques",
        'receiver' => 'Instructor',
        'view' => 'email.fct.info',
        'fcts' => 1,
        'default' => '',
    ],
    'infoU' => [
        'subject' => "Recordatori inici de pràctiques",
        'receiver' => 'Instructor',
        'view' => 'email.fct.infoU',
        'redirect' => 'misColaboraciones',
        'fcts' => 1,
        'default' => '',
    ],
    'follow' => [
        'subject' => "Seguiment Pràctiques de FCT",
        'receiver' => 'Instructor',
        'view' => 'email.fct.follow',
        'fcts' => 1,
        'default' => 'Les pràctiques es desenvolupen amb normalitat',
    ],
    'followU' => [
        'subject' => "Seguiment Pràctiques de FCT",
        'receiver' => 'Instructor',
        'view' => 'email.fct.followU',
        'redirect' => 'misColaboraciones',
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
        'default' => '',
    ],

];
