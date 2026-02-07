<?php

namespace Intranet\Botones;

/**
 * @deprecated Use Intranet\UI\Panels\Panel
 */
class Panel extends \Intranet\UI\Panels\Panel
{
    /**
     * @deprecated Use Intranet\UI\Panels\Panel::setBotonera
     */
    public function setBotonera(array $index = [], array $grid = [], array $profile = []): void
    {
        if ($index != []) {
            foreach ($index as $btn) {
                $this->setBoton(self::BOTON_INDEX, new BotonBasico("$this->model.$btn"));
            }
        }
        if ($grid != []) {
            foreach ($grid as $btn) {
                $this->setBoton(self::BOTON_GRID, new BotonImg($this->model . "." . $btn));
            }
        }
        if ($profile != []) {
            foreach ($profile as $btn) {
                $this->setBoton(self::BOTON_PROFILE, new BotonIcon("$this->model.$btn"));
            }
        }
    }

    /**
     * @deprecated Use Intranet\UI\Panels\Panel::setBothBoton
     */
    public function setBothBoton(string $href, array $atributos = [], bool $relative = false): void
    {
        $this->setBoton(self::BOTON_GRID, new BotonImg($href, $atributos, $relative));
        $this->setBoton(self::BOTON_PROFILE, new BotonIcon($href, $atributos, $relative));
    }
}
