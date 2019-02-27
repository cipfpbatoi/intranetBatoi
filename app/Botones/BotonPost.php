<?php

namespace Intranet\Botones;

class BotonPost extends BotonElemento
{

    protected $defaultClase = 'btn-success';
    protected $permanentClase = 'btn btn-xs txtButton';

    protected function html($key = null)
    {
        return "<input " . $this->clase() . $this->id() . " type=submit  value='" . $this->text . "' />";
    }

}
