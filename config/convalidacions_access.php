<?php

return [
    /* Clau temporal per a accedir a les convalidacions quan Direcció les bloqueja. */
    'password' => env('CONVALIDACIONS_ACCESS_PASSWORD') ?: '4 8 15 16 23 42',
];
