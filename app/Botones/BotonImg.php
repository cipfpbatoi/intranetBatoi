<?php

namespace Intranet\Botones;

/**
 * Botó amb icona en format imatge/font-awesome.
 */
class BotonImg extends BotonElemento
{

    protected ?string $permanentClase = 'imgButton';

    /**
     * @param string $href Ruta base del botó.
     * @param array $atributos Atributs del botó.
     * @param bool|string $relative Mode de ruta relativa o prefix.
     * @param string|null $postUrl Sufix opcional de ruta.
     */
    public function __construct($href, $atributos = [], $relative = false, $postUrl = null)
    {
        parent::__construct($href, $atributos, $relative, $postUrl);
        $this->id = $this->accion;
    }

    /**
     * Genera el HTML del botó amb imatge.
     */
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
