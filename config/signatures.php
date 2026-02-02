<?php
return [
    'llistats' => [
        'director' => ['','horario','birret','comision','falta','expediente','certificado'],
        'vicedirector' => ['','actividad'],
        'secretario' => ['',],
        'jefeEstudios' => ['',]
    ],
    'genere' => [
        'director' => ['H' =>  'EL DIRECTOR', 'M' =>'LA DIRECTORA'],
        'vicedirector' => ['H' => 'EL SOTA-DIRECTOR','M' =>'LA SOTA-DIRECTORA'],
        'secretario' => ['H' => 'EL SECRETARI' , 'M' =>'LA SECRETÀRIA'],
        'jefeEstudios' => ['H' => "EL CAP D'ESTUDIS",'M' =>"LA CAP D'ESTUDIS"],
    ],
    'files' => [
        'A1' => [
            'director' => [
                'x' => 110 ,
                'y' => 75,
            ]
        ],
        'A1DUAL' => [
            'director' => [
                'x' => 110 ,
                'y' => 75,
            ]
        ],
        'A2' => [
            'owner' => [
                'x' => 72 ,
                'y' => 250,
            ],
            'director' => [
                'x' => 12 ,
                'y' => 250,
            ]
        ],
        'A2DUAL' => [
            'owner' => [
                'x' => 220,
                'y' => 80,
            ],
            'director' => [
                'x' => 50,
                'y' => 80,
            ]
        ],
        'A3' => [
            'owner' => [
                'x' => 400 ,
                'y' => 60,
            ],
        ],
        'A3DUAL' => [
            'owner' => [
                'x' => 400 ,
                'y' => 60,
            ],
        ],
        'A5' => [
            'owner' => [
                'x' => 300 ,
                'y' => 15,
            ],
        ],
        'A5DUAL' => [
            'owner' => [
                'x' => 390 ,
                'y' => 30,
            ],
        ],
        'autTutor' => [
            'owner' => [
                'x' => 100 ,
                'y' => 250,
            ],
        ],
    ],
    'jsignpdf' => [
        'java' => env('JSIGNPDF_JAVA', 'java'),
        'jar' => env('JSIGNPDF_JAR', storage_path('app/jsignpdf/JSignPdf.jar')),
        'append' => (bool) env('JSIGNPDF_APPEND', true),
        'page' => (int) env('JSIGNPDF_PAGE', 1),
        'width' => (int) env('JSIGNPDF_WIDTH', 200),
        'height' => (int) env('JSIGNPDF_HEIGHT', 70),
        'bg_path' => env('JSIGNPDF_BG_PATH'),
        'bg_scale' => env('JSIGNPDF_BG_SCALE'),
        'img_path' => env('JSIGNPDF_IMG_PATH'),
        'bg_transparent' => (bool) env('JSIGNPDF_BG_TRANSPARENT', false),
        'bg_threshold' => (int) env('JSIGNPDF_BG_THRESHOLD', 245),
        'bg_compose' => (bool) env('JSIGNPDF_BG_COMPOSE', false),
        'logo_scale' => (float) env('JSIGNPDF_LOGO_SCALE', 0.6),
        'logo_top' => (int) env('JSIGNPDF_LOGO_TOP', 5),
        'logo_max_height_ratio' => (float) env('JSIGNPDF_LOGO_MAX_HEIGHT_RATIO', 0.45),
        'output_suffix' => env('JSIGNPDF_SUFFIX', '_signed'),
        'timeout' => (int) env('JSIGNPDF_TIMEOUT', 60),
    ],
];
