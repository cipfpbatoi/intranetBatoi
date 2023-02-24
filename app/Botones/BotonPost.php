<?php

namespace Intranet\Botones;

class BotonPost extends BotonElemento
{

    protected $defaultClase = 'btn-success';
    protected $permanentClase = 'btn btn-xs txtButton';

    protected function html($key = null)
    {
        return view('partials.botonPost', [
            'class' => $this->clase(),
            'id' => $this->id(),
            'data' => $this->data(),
            'text' => $this->text
        ]);

    }

}
