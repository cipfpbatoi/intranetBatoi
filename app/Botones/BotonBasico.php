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
            'target' => $this->target,
            'rel' => $this->rel,
            'ariaLabel' => $this->ariaLabel,
            'title' => $this->title,
            'icon' => $this->icon,
            'text' => $this->text,
            'badge' => $this->badge,
            'onclick' => $this->onclick??null,
        ]);
    }
}
