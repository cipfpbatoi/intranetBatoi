<?php

namespace Intranet\Botones;

class BotonBasico extends Boton
{

    protected $defaultClase = 'btn-primary';
    protected $permanentClase = 'btn txtButton';

    //pinta el boto
    protected function html($key = null)
    {
        return "<a " . $this->href() . $this->clase() . $this->id() . ">".$this->text."</a>";
    }

}
