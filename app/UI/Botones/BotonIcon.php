<?php

namespace Intranet\UI\Botones;

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
        return view('components.buttons.icon', [
            'href' => $this->href($key),
            'class' => $this->clase(),
            'id' => $this->id($key),
            'disabled' => $this->disabledAttr(),
            'target' => $this->target,
            'rel' => $this->rel,
            'ariaLabel' => $this->ariaLabel,
            'title' => $this->title,
            'icon' => $this->icon??config("iconos.$this->accion"),
            'text' => $this->text,
            'badge' => $this->badge
        ]);
    }

}
