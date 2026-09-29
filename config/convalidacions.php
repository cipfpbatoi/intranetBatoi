<?php

return [
    /* Límit dels documents de convalidació, expressat en KiB. */
    'max_document_kb' => (int) env('CONVALIDACIONS_MAX_DOCUMENT_KB', 5120),

    /* Límit de cada exportació XML d'avaluació, expressat en KiB. */
    'max_xml_kb' => (int) env('CONVALIDACIONS_MAX_XML_KB', 20480),

    /* Nombre màxim d'exportacions XML acceptades en una mateixa pujada. */
    'max_xml_files_per_upload' => (int) env('CONVALIDACIONS_MAX_XML_FILES_PER_UPLOAD', 20),
];
