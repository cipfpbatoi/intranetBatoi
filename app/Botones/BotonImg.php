<?php

namespace Intranet\Botones;

class BotonImg extends BotonElemento
{

    protected $permanentClase = 'imgButton';

    public function __construct($href, $atributos = [], $relative = false, $postUrl = null)
    {
        parent::__construct($href, $atributos, $relative, $postUrl);
        $this->id = $this->accion;
    }

    protected function html($key = null)
    {
        return view('partials.botonImg', [
            'href' => $this->href($key),
            'class' => $this->clase(),
            'id' => $this->id($key),
            'img' => $this->img??config("iconos.$this->accion"),
            'text' => $this->text
        ]);

    }
    

}
