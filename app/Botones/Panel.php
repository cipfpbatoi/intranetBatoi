<?php

namespace Intranet\Botones;

use Illuminate\Database\Eloquent\Model;
use Intranet\Botones\Boton;
use HTML;

class Panel
{

    private $botones;   // botons del panel
    private $model;     // model de dades
    private $pestanas;  // pestanyes
    private $titulo;    // titol
    private $elementos; // elements
    private $data = [];      // array de més dades 
    public $items = [];
    
    public function __construct($modelo, $rejilla = null, $vista = null,$crea=true,$include=null)
    {
        $this->model = $modelo;
        $this->botones['index'] = [];
        $this->botones['grid'] = [];
        $this->botones['profile'] = [];
        if ($crea) $this->setPestana('grid', true, $vista, null, $rejilla,null,$include);
        
    }

    public function view($todos,$titulo,$vista,$elemento =null){
        if (!$this->countPestana()) return redirect()->route('home');
        $this->setElementos($todos);
        $this->setTitulo($titulo);
        $panel = $this;
        if (!$elemento) return view($vista,compact('panel'));
        $default = $elemento->fillDefautOptions();
        return view($vista,compact('panel','elemento','default'));
    }
    // afegix botonera ( tres tipus de botons : 
    //          index -> comuns a tots
    //          grid --> per a cada element
    //          profile --> per a vistes de tipus profile
    
    public function setBotonera($index = [], $grid = [], $profile = [])
    {
        if ($index != []) {
            foreach ($index as $btn) {
                $this->botones['index'][] = new BotonBasico("$this->model.$btn");
            }
        }
        if ($grid != []) {
            foreach ($grid as $btn) {
                $this->botones['grid'][] = new BotonImg($this->model . "." . $btn);
            }
        }
        if ($profile != []) {
            foreach ($profile as $btn) {
                $this->botones['profile'][] = new BotonIcon("$this->model.$btn");
            }
        }
    }

    // afegix boto
    public function setBoton($tipo, Boton $boton)
    {
        $this->botones[$tipo][] = $boton;
    }

    // ageix boto comú a grid i profile    
    public function setBothBoton($href, $atributos = [], $relative = false)
    {
        $this->botones['grid'][] = new BotonImg($href, $atributos, $relative);
        $this->botones['profile'][] = new BotonIcon($href, $atributos, $relative);
    }

    // afeguix pestana
    // sustituye canvia la primera pestana per l'actual
    public function setPestana($nombre, $activo = false, $vista = null, $filtro = null, $rejilla = null, $sustituye = null,$include=null)
    {
        if ($activo)
            $this->desactiva();
        if ($sustituye) $this->pestanas[0] = new Pestana($nombre, $activo, $this->queVista($nombre, $vista), $filtro, $rejilla,$include);
        else $this->pestanas[] = new Pestana($nombre, $activo, $this->queVista($nombre, $vista), $filtro, $rejilla,$include);
    }
    // conta el nombre de pestanes
    public function countPestana(){
        return count($this->pestanas);
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    //Para que solo haya una pestaña activa, desactiva las demas
    private function desactiva()
    {
        if ($this->pestanas) {
            foreach ($this->pestanas as $pestana) {
                $pestana->setActiva(false);
            }
        }
    }
    

    public function getModel()
    {
        return $this->model;
    }

    public function getPestanas()
    {
        return $this->pestanas;
    }

    public function getRejilla()
    {
        return $this->pestanas[0]->getRejilla();
    }
    public function setRejilla($grid)
    {
        $this->pestanas[0]->setRejilla($grid);
    }

    public function getBotones($tipo = null)
    {
        return $tipo == null ? $this->botones : $this->botones[$tipo];
    }
    public function countBotones($tipo)
    {
        return count($this->botones[$tipo]);
    }

    public function getTitulo($que = 'index')
    {
        return trans("models." . ucwords(strtolower($this->getModel())) . ".$que", $this->titulo);
    }

    function setElementos($elementos)
    {
        $this->elementos = $elementos;
    }

   
    // filtra els elements d'una pestana amb condicions
    public function getElementos($pestana)
    {
        $elementos = $this->elementos;
        if ($filtro = $pestana->getFiltro()){
            for ($i = 0; $i < count($filtro); $i = $i + 2) {
                $elementos = $elementos->where($filtro[$i], '=', $filtro[$i+1]);
            }
            return $elementos;
            return $this->elementos->where($filtro[0], '=', $filtro[1]);
        }
        else
            return $this->elementos;
    }

    // activa pestana per nom
    public function activaPestana($nombre)
    {
        foreach ($this->pestanas as $pestana) {
            if ($pestana->getNombre() == $nombre) {
                $pestana->setActiva(true);
            } else
                $pestana->setActiva(false);
        }
    }

    // torna la vista
    private function queVista($nombre, $vista)
    {
        if ($vista == null)
            return 'intranet.partials.' . $nombre . "." . strtolower($this->model);
        else
            if (substr($vista,0,1)=='.')
                return substr($vista,1);
            else
                return 'intranet.partials.' . $vista;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
    }
    public function addGridField($field){
        foreach ($this->pestanas as $pestana)
            $pestana->addField($field);
    }

}
