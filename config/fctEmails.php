<?php

return [
    'contact' => [
        'subject' => "Sol·licitud Pràctiques de FCT",
        'receiver' => 'A/A de Recursos Humans',
        'view' => 'email.fct.contact',
        'redirect' => 'misColaboraciones'
    ],
    'request' => [
        'subject' => 'Detalls Documentació Pràctiques a confirmar',
        'view' => 'email.fct.request',
        'receiver' => 'A/A de Recursos Humans',
    ],
    'info' => [
        'subject' => "Recordatori inici de pràctiques",
        'receiver' => 'A/A de Recursos Humans',
        'view' => 'email.fct.info',
    ]
];
