<?php

return [
    // Fica una clau pròpia (llarga i secreta) en .env
    'pepper' => env('VIOLETBOX_PEPPER', env('APP_KEY')),
    // Rol o permís que pot veure el llistat d’entrades
    'admin_gate' => env('VIOLETBOX_ADMIN_GATE', 'manage-violetbox'),
];
