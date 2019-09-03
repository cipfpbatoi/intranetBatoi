<?php

return [
    'contact' => [
        'subject' => "Sol·licitud Pràctiques de FCT",
        'receiver' => 'A/A de Recursos Humans',
        'view' => 'email.fct.contact',
        'redirect' => 'misColaboraciones'
    ],
    'request' => [
        'subject' => 'Revisió Documentació Pràctiques',
        'view' => 'email.fct.request',
        'receiver' => 'A/A de Recursos Humans',
    ],
    'requestU' => [
        'subject' => 'Revisió Documentació Pràctiques',
        'view' => 'email.fct.requestU',
        'receiver' => 'A/A de Recursos Humans',
        'redirect' => 'misColaboraciones'
    ],
    'info' => [
        'subject' => "Recordatori inici de pràctiques",
        'receiver' => 'A/A de Recursos Humans',
        'view' => 'email.fct.info',
    ],
    'infoU' => [
        'subject' => "Recordatori inici de pràctiques",
        'receiver' => 'A/A de Recursos Humans',
        'view' => 'email.fct.infoU',
        'redirect' => 'misColaboraciones',
    ],
    'follow' => [
        'subject' => "Email per seguiment Pràctiques de FCT",
        'receiver' => 'Instructor',
        'view' => 'email.fct.follow',
    ],
    'followU' => [
        'subject' => "Email seguiment ",
        'receiver' => 'Instructor',
        'view' => 'email.fct.followU',
        'redirect' => 'misColaboraciones',
    ],
    'visit' => [
        'subject' => "Concertar visita de FCT",
        'receiver' => 'Instructor',
        'view' => 'email.fct.visit',
        'redirect' => 'misColaboraciones'
    ],
    'student' => [
        'subject' => "Citar alumnes per seguiment de FCT",
        'receiver' => 'Alumno',
        'view' => 'email.fct.student',
        'redirect' => 'misColaboraciones'
    ],
];
