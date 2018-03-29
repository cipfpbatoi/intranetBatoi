<?php

use Faker\Generator as Faker;
use Intranet\Entities\Comision;

$factory->define(Comision::class,function (Faker $faker,$attributes){
    return [
        'servicio' => 'Hola cocodrilo',
        'desde' =>'2017-08-04 08:00:00',
        'hasta' => '2017-08-04 14:00:00',
        'medio' => 'COCHE',
        'marca' => 'SEAT ALTEA XL',
        'matricula' => '1023-HDX',
        'kilometraje' => random_int(0,100),
    ];
});
