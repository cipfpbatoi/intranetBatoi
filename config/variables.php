<?php

return [
    'controlDiario' => 1,
    'diasNoCompleta' => 45,
    'precioKilometro' => 0.19,
    'reservaAforo' => 1.2,
    'comisionFCTexterna' => 1,
    'httpFCTexterna' => 'http://www.fpxativa.es/admin',
    'enquestaInstructor' => 'https://forms.gle/nRWt7GNtWkn5X9gS6',
    'ipGuardias' => [ [ 'ip' => '172.16.20.238', 'codOcup' => 149034734 ],
        [ 'ip' => '172.16.109.206', 'codOcup' => 3249454 ],
        [ 'ip' => '172.16.109.207', 'codOcup' => 3249454 ],
        [ 'ip' => '172.16.109.208', 'codOcup' => 3249454 ],
        [ 'ip' => '192.168.56.255', 'codOcup' => 3249454 ]],
    'ocupacionesGuardia' => ['normal' => '3249454','biblio' => '149034734'],
    'actividadImg' => 1,
    'altaInstructores' => 1,
    'ipDomotica' => 'http://172.16.10.74/api/devices/{dispositivo}/action',
];