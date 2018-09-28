<?php

namespace Intranet\Entities;

use Intranet\Entities\Fct;


class Dual extends Fct
{
    
    public function __construct()
    {
        $this->asociacion = 2;
        $this->horas_semanales = 20;
        $this->horas = 600;
    }
        
}
