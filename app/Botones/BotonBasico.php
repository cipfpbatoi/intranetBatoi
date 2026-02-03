<?php

namespace Intranet\Botones;

/**
 * Botó bàsic amb renderització d'enllaç i icona opcional.
 */
class BotonBasico extends Boton
{

    protected ?string $defaultClase = 'btn-primary';
    protected ?string $permanentClase = 'btn btn-round txtButton';

    /**
     * Genera el HTML del botó bàsic.
     */
    protected function html($key = null)
    {
        return view('partials.botonBasico', [
            'href' => $this->href(),
            'class' => $this->clase(),
            'data' => $this->data(),
            'id' => $this->id(),
            'disabled' => $this->disabledAttr(),
            'icon' => $this->icon,
            'text' => $this->text,
            'onclick' => $this->onclick??null,
        ]);
    }
}
