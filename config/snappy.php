<?php

return array(
    'pdf' => array(
        'enabled' => true,
        'binary' => env('WKHTMLTOPDF_BINARY', '/usr/local/bin/wkhtmltopdf'),
        'timeout' => false,
        'options' => ['images'=>true,'enable-external-links'=>true],
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary'  => env('WKHTMLTOIMAGE_BINARY', '/usr/local/bin/wkhtmltoimage'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
);
