<?php

namespace Intranet\Botones;

class BotonBasico extends Boton
{

    protected $defaultClase = 'btn-primary';
    protected $permanentClase = 'btn btn-round txtButton';

    //pinta el boto
    protected function html($key = null)
    {
        return view('partials.botonBasico', [
            'href' => $this->href(),
            'class' => $this->clase(),
            'data' => $this->data(),
            'id' => $this->id(),
            'icon' => $this->icon,
            'text' => $this->text
        ]);
    }
}
