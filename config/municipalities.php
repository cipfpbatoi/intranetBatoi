<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Noms canònics i àlies de municipis
    |--------------------------------------------------------------------------
    |
    | Les localitats dels centres són text lliure. Este mapa permet presentar
    | amb un únic nom valencià les variants que no es poden deduir només
    | ignorant accents, majúscules i puntuació.
    |
    */
    'aliases' => [
        'Alcoi' => ['Alcoi', 'Alcoy'],
        'Muro d’Alcoi' => [
            'Muro',
            'Muro d’Alcoi',
            "Muro d'Alcoi",
            'Muro d’Alcoy',
            "Muro d'Alcoy",
            'Muro de Alcoi',
            'Muro de Alcoy',
        ],
        'València' => ['València', 'Valencia'],
        'Xàtiva' => ['Xàtiva', 'Xativa', 'Játiva', 'Jativa'],
        'Dénia' => ['Dénia', 'Denia'],
        'Ontinyent' => ['Ontinyent', 'Onteniente'],
    ],
];
