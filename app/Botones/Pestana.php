<?php

namespace Intranet\Botones;

class Pestana
{

    private $vista;
    private $nombre;
    private $activa;
    private $filtro;
    private $rejilla;
    private $include;
    

    public function __construct($nombre, $activa = false, $vista = null, $filtro = [], $rejilla = null, $include=[])
    {
        $this->nombre = $nombre;
        $this->activa = $activa;
        $this->filtro = $filtro;
        $this->vista = $vista;
        $this->rejilla = $rejilla;
        $this->include = $include;
    }

    public function setVista($vista)
    {
        $this->vista = $vista;
    }

    public function getVista()
    {
        return $this->vista;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getActiva()
    {
        return $this->activa ? 'active' : '';
    }
    
    public function getInclude($index)
    {
        return isset($this->include[$index])?$this->include[$index]:[];
    }

    public function setInclude($include)
    {
        $this->include = $include;
    }

    public function setActiva($activa)
    {
        $this->activa = $activa;
    }

    public function getFiltro()
    {
        return $this->filtro == null ? [] : $this->filtro;
    }

    public function getRejilla()
    {
        return $this->rejilla;
    }

    public function setRejilla($grid)
    {
        $this->rejilla = $grid;
    }

}
