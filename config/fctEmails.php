<?php

return [
    'request' => [
        'subject' => 'Detalls Documentació Pràctiques a confirmar',
        'view' => 'email.fct.request',
        'receiver' => 'A/A de Recursos Humans',
        'direct' => 1,
        'redirect' => 'misColaboraciones'
    ],
    'contact' => [
        'subject' => "Sol·licitud Pràctiques de FCT",
        'receiver' => 'A/A de Recursos Humans',
        'view' => 'email.fct.contact',
        'direct' => 0
    ]
];
