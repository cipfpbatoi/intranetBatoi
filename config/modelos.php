<?php

return [
    'Falta' => [
        'estados' => [  1 => 'nojustify', 
                        2 => 'withjustify', 
                        3 => 'autorizada', 
                        4 => 'resuelta', 
                        5 => 'larga'],
        'avisos' => ['idProfesor' => [ 0, 1 , 2 , 3],'jefeEstudios' => [1,2]],
        'resolve' => '3',
        'refuse' => '0',
        'print' => '4',
        'completa' => '4',
        'documento' => 'llistat listado mensual ausència profesorat ausencia profesorado',
    ],
    'Actividad' => [
        'estados' => [  1 => 'pendiente', 
                        2 => 'autorizada', 
                        3 => 'impresa'],
        'avisos' => ['Creador' => [ 0, 3],'vicedirector'=>[1]],
        'resolve' => '3',
        'refuse' => '0',
        'print' => '3',
        'documento' => 'llistat activitats extraescolars listado autorización actividades extraescolares',
    ],
    'Comision' => [
        'estados' => [  1 => 'pendiente', 
                        2 => 'autorizada', 
                        3 => 'registrada',
                        4 => 'unpaid',
                        5 => 'cobrada'],
        'avisos' => ['idProfesor' =>[ 0, 3],'director'=>[1],'secretario'=>[4]],
        'resolve' => '3',
        'refuse' => '0',
        'print' => '3',
        'completa' => '5',
        'documento' => 'llistat autorització comissions servei listado autorización comisiones servicio',
    ],
    'Expediente' => [
        'estados' => [  1 => 'pendiente', 
                        2 => 'tramitada', 
                        3 => 'resuelta',
                        ],
        'avisos' => [ 'idProfesor' => [0, 3, 5],'jefeEstudios'=>[1],'orientador'=>[4]],
        'resolve' => '3',
        'refuse' => '0',
        'print' => '3'
    ],
    'Programacion' => [
        'estados' => [  1 => 'pendiente', 
                        2 => 'checkeada', 
                        3 => 'aprobada'],
        'avisos' => ['idProfesor'=>[ 0,2,3],'jefeDepartamento' => [1]],
        'resolve' => '3',
        'refuse' => '0',
    ],
    'TipoIncidencia' => [
        'estados' => [  1 => 'pendiente', 
                        2 => 'proceso', 
                        3 => 'resuelta'],
        'avisos' => [ 'idProfesor' => [0, 3], 'responsable' => [1] ],
        'resolve' => '3',
        'refuse' => '0',
    ],
    'OrdenTrabajo' => [
        'estados' => [
            0 => 'abierta',
            1 => 'cerrada',
            2 => 'resuelta'
        ],
        'print' => 1,
        'resolve' => 2,
        'refuse' => 0
    ],
    'Falta_itaca' =>[
        'estados' => [
            1 => 'pendiente',
            2 => 'autorizada',
            3 => 'refused'
        ],
        'avisos' => [ 'idProfesor' => [2,3],'director' =>[1]],
        'resolve' => 2,
        'refuse' => 3
    ],
    'Colaboracion' => [
        'estados' => [
            1 => 'pendiente',
            2 => 'colabora',
            3 => 'descartada',
        ],
        'avisos' => [],
        'resolve' => 2,
        'refuse' => 3
    ]
    
];

