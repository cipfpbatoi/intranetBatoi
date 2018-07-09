<?php

namespace Intranet\Botones;

class BotonImg extends BotonElemento
{

    protected $permanentClase = 'imgButton';

    public function __construct($href, $atributos = [], $relative = false, $postUrl = null)
    {
        parent::__construct($href,$atributos,$relative,$postUrl);
        $this->id = $this->accion;
    }

    protected function html($key = null)
    {
        $a = "<a " . $this->href($key) . $this->clase() . $this->id($key) . ">";
        $a .= "<i class='fa ";
        $a .= $this->img ? $this->img : config("iconos.$this->accion");
        $a .= "' alt='" . $this->text . "' title='" . $this->text . "' />";
        $a .= "</i>";
        return $a;
    }
    

}
