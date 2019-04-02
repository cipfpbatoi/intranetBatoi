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
    

    public function __construct($nombre, $activa = false, $vista = null, $filtro = [], $rejilla = null ,$include=[])
    {
        $this->nombre = $nombre;
        $this->activa = $activa;
        $this->filtro = $filtro;
        $this->vista = $vista;
        $this->rejilla = $rejilla;
        $this->include = $include;
    }

    function setVista($vista){
        $this->vista = $vista;
    }
    function getVista()
    {
        return $this->vista;
    }

    function getNombre()
    {
        return $this->nombre;
    }

    function getActiva()
    {
        return $this->activa ? 'active' : '';
    }
    
    function getInclude($index)
    {
        return isset($this->include[$index])?$this->include[$index]:[];
    }

    function setInclude($include)
    {
        $this->include = $include;
    }
    function setActiva($activa)
    {
        $this->activa = $activa;
    }

    function getFiltro()
    {
        return $this->filtro == null ? [] : $this->filtro;
    }

    function getRejilla()
    {
        return $this->rejilla;
    }
    function setRejilla($grid)
    {
        $this->rejilla = $grid;
    }

}
