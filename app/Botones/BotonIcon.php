<?php

namespace Intranet\Botones;

class BotonIcon extends BotonElemento
{

    protected $defaultClase = 'btn-primary';
    protected $permanentClase = 'btn btn-xs iconButton';

    protected function html($key = null)
    {
        $a = "<a " . $this->href($key) . $this->clase() . $this->id() . "><i class='fa ";
        $a .= isset($this->icon) ? $this->icon : config("constants.icon.$this->accion");
        $a .= "'></i> " . $this->text;
        $a .= "</a>";
        return $a;
    }

}
