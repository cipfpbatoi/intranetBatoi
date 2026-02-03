<?php

namespace Intranet\Botones;

/**
 * Botó amb icona (font-awesome).
 */
class BotonIcon extends BotonElemento
{

    protected ?string $defaultClase = 'btn-primary';
    protected ?string $permanentClase = 'btn btn-xs iconButton';

    /**
     * Genera el HTML del botó amb icona.
     */
    protected function html($key = null)
    {
        return view('partials.botonIcon', [
            'href' => $this->href($key),
            'class' => $this->clase(),
            'id' => $this->id($key),
            'disabled' => $this->disabledAttr(),
            'title' => $this->title,
            'icon' => $this->icon??config("iconos.$this->accion"),
            'text' => $this->text
        ]);
    }

}
