<?php

namespace Intranet\UI\Panels;

use Illuminate\Support\Facades\Lang;

class Pestana
{

    /** @var string|null */
    private $vista;
    /** @var string */
    private $nombre;
    /** @var bool */
    private $activa;
    /** @var array|null */
    private $filtro;
    /** @var array|null */
    private $rejilla;
    /** @var array */
    private $include;
    

    /**
     * @param string $nombre  Clau/nom de la pestanya.
     * @param bool $activa    Si la pestanya està activa.
     * @param string|null $vista  Vista associada (Blade).
     * @param array|null $filtro  Filtre a aplicar als elements.
     * @param array|null $rejilla Columns per a la vista de rejilla.
     * @param array $include  Elements extra a incloure (p. ex. modals).
     */
    public function __construct(
        string $nombre,
        bool $activa = false,
        ?string $vista = null,
        ?array $filtro = [],
        ?array $rejilla = null,
        array $include = []
    )
    {
        $this->nombre = $nombre;
        $this->activa = $activa;
        $this->filtro = $filtro;
        $this->vista = $vista;
        $this->rejilla = $rejilla;
        $this->include = $include;
    }

    /**
     * @param string|null $vista
     * @return void
     */
    public function setVista(?string $vista): void
    {
        $this->vista = $vista;
    }

    /**
     * @return string|null
     */
    public function getVista(): ?string
    {
        return $this->vista;
    }

    /**
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * @return string 'active' si està activa, buit altrament
     */
    public function getActiva(): string
    {
        return $this->activa ? 'active' : '';
    }
    
    /**
     * @param string $index
     * @return array
     */
    public function getInclude(string $index): array
    {
        return isset($this->include[$index])?$this->include[$index]:[];
    }

    /**
     * @param array $include
     * @return void
     */
    public function setInclude(array $include): void
    {
        $this->include = $include;
    }

    /**
     * @param bool $activa
     * @return void
     */
    public function setActiva(bool $activa): void
    {
        $this->activa = $activa;
    }

    /**
     * @return array
     */
    public function getFiltro(): array
    {
        return $this->filtro == null ? [] : $this->filtro;
    }

    /**
     * @return array|null
     */
    public function getRejilla(): ?array
    {
        return $this->rejilla;
    }

    /**
     * @param array|null $grid
     * @return void
     */
    public function setRejilla(?array $grid): void
    {
        $this->rejilla = $grid;
    }

    /**
     * Retorna l'etiqueta traduïda; si no hi ha traducció, usa el nom original.
     */
    public function getLabel(): string
    {
        $key = 'messages.buttons.' . $this->getNombre();

        return Lang::has($key)
            ? trans($key)
            : $this->getNombre();
    }

}
