<?php

return [
    'urls' => [
        'base' => env('SAO_BASE_URL', 'https://foremp.edu.gva.es'),
        'main' => env('SAO_MAIN_URL', 'https://foremp.edu.gva.es/index.php?op=2&subop=0'),
        'generate_pdf' => env('SAO_GENERATE_PDF_URL', 'https://foremp.edu.gva.es/inc/ajax/generar_pdf.php'),
        'fct' => env('SAO_FCT_URL', 'https://foremp.edu.gva.es/index.php?accion=7&idFct='),
    ],
    'download' => [
        'directory' => env('SAO_DOWNLOAD_DIR', storage_path('tmp')),
        'wait_seconds' => (int) env('SAO_DOWNLOAD_WAIT_SECONDS', 10),
    ],
    'navigation' => [
        'sleep_seconds' => (int) env('SAO_NAVIGATION_SLEEP_SECONDS', 1),
    ],
];

