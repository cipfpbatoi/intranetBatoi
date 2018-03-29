<?php

namespace Intranet\Botones;

class BotonBasico extends Boton
{

    protected $defaultClase = 'btn-primary';
    protected $permanentClase = 'btn txtButton';

    //pinta el boto
    protected function html($key = null)
    {
        $a = "<a " . $this->href() . $this->clase() . $this->id() . ">";
        $a .= $this->text;
        $a .= "</a>";
        return $a;
    }

}
