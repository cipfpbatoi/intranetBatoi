<?php

namespace Intranet\Botones;

class BotonBasico extends Boton
{

    protected $defaultClase = 'btn-primary';
    protected $permanentClase = 'btn txtButton';

    //pinta el boto
    protected function html($key = null)
    {
        return "<a " . $this->href() . $this->clase() . $this->id() .$this->data() .">".$this->hasIcon().' '.$this->text."</a>";
    }

    private function hasIcon(){
        if (isset($this->atributos['icon']))  return "<i class='".$this->atributos['icon']."'></i>";

        return '';
    }

}
