<?php

return [
    'controlDiario' => 1,
    'diasNoCompleta' => 45,
    'reservaAforo' => 1.2,
    'comisionFCTexterna' => 1,
    'httpFCTexterna' => 'http://www.fpxativa.es/admin',
    'enquestaInstructor' => 'https://forms.office.com/r/rMqmGzMbTn',
    'actividadImg' => 0,
    'altaInstructores' => 0,
    'ipDomotica' => env('DOMOTICA_DEVICE_ENDPOINT', 'http://172.16.10.74/api/devices/{dispositivo}/action'),
    'domotica' => [
        'host' => env('DOMOTICA_HOST', 'http://172.16.10.74'),
        'user' => env('USER_DOMOTICA', 'api'),
        'pass' => env('PASS_DOMOTICA', ''),
        'openSceneId' => (int) env('DOMOTICA_OPEN_SCENE_ID', 111),
    ],
    'authGoogle' => false,
];
