<?php

return [
    'controlDiario' => 1,
    'diasNoCompleta' => 45,
    'precioKilometro' => [0.19,0.078,0,0,0,0,0],
    'reservaAforo' => 1.2,
    'comisionFCTexterna' => 1,
    'httpFCTexterna' => 'http://www.fpxativa.es/admin',
    'enquestaInstructor' => 'https://forms.gle/nRWt7GNtWkn5X9gS6',
    'ipGuardias' => [ [ 'ip' => '172.16.20.238', 'codOcup' => 149034734 ],
        [ 'ip' => '172.16.109.203', 'codOcup' => 3249454 ],
        [ 'ip' => '172.16.109.201', 'codOcup' => 3249454 ],
        [ 'ip' => '172.16.109.200', 'codOcup' => 3249454 ],
        [ 'ip' => '172.16.20.216', 'codOcup' => 3249454 ]],
    'ocupacionesGuardia' => ['normal' => '3249454','biblio' => '149034734'],
    'actividadImg' => 1,
    'altaInstructores' => 1,
    'ipDomotica' => 'http://172.16.10.74/api/devices/{dispositivo}/action',
    'authGoogle' => false,
];
