<?php

namespace Intranet\Botones;

/**
 * Botó per a enviament de formulari (submit).
 */
class BotonPost extends BotonElemento
{

    protected ?string $defaultClase = 'btn-success';
    protected ?string $permanentClase = 'btn btn-xs txtButton';

    /**
     * Genera el HTML del botó tipus submit.
     */
    protected function html($key = null)
    {
        return view('partials.botonPost', [
            'class' => $this->clase(),
            'id' => $this->id(),
            'data' => $this->data(),
            'disabled' => $this->disabledAttr('button'),
            'text' => $this->text
        ]);

    }

}
