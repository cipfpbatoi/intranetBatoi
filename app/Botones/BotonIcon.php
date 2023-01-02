<?php

namespace Intranet\Botones;

class BotonIcon extends BotonElemento
{

    protected $defaultClase = 'btn-primary';
    protected $permanentClase = 'btn btn-xs iconButton';

    protected function html($key = null)
    {
        return view('partials.botonIcon', [
            'href' => $this->href($key),
            'class' => $this->clase(),
            'id' => $this->id($key),
            'title' => $this->title,
            'icon' => $this->icon??config("iconos.$this->accion"),
            'text' => $this->text
        ]);
    }

}
