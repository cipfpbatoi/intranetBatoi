<?php

namespace Intranet\Botones;

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
    
    public function __construct($modelo, $rejilla = null, $vista = null,$creaPestana=true,$include=[])
    {
        $this->model = $modelo;
        $this->botones['index'] = [];
        $this->botones['grid'] = [];
        $this->botones['profile'] = [];
        $this->botones['infile'] = [];
        if ($creaPestana) $this->setPestana('grid', true, $vista, null, $rejilla,null,$include);
        
    }


    public function render($todos,$titulo,$vista){
        if (!$this->countPestana()) return redirect()->route('home');

        $panel = $this->feedPanel($todos, $titulo);

        return view($vista,compact('panel'));

    }

    public function renderModal($todos,$titulo,$vista,$elemento){
        if (!$this->countPestana()) return redirect()->route('home');

        $panel = $this->feedPanel($todos, $titulo);
        $default = $elemento->fillDefautOptions();

        return view($vista,compact('panel','elemento','default'));
    }


    
    public function setBotonera($index = [], $grid = [], $profile = [])
    {
        if ($index != []) {
            foreach ($index as $btn)
                $this->botones['index'][] = new BotonBasico("$this->model.$btn");
        }
        if ($grid != []) {
            foreach ($grid as $btn)
                $this->botones['grid'][] = new BotonImg($this->model . "." . $btn);
        }
        if ($profile != []) {
            foreach ($profile as $btn)
                $this->botones['profile'][] = new BotonIcon("$this->model.$btn");
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
    public function setPestana($nombre, $activo = false, $vista = null, $filtro = null, $rejilla = null, $sustituye = null,$include=[])
    {
        if ($activo) $this->desactivaAll();
        if ($sustituye) $this->pestanas[0] = new Pestana($nombre, $activo, $this->getView($nombre, $vista), $filtro, $rejilla,$include);
        else $this->pestanas[] = new Pestana($nombre, $activo, $this->getView($nombre, $vista), $filtro, $rejilla,$include);
    }

    public function countPestana(){
        return count($this->pestanas);
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    //Para que solo haya una pestaña activa, desactiva las demas
    private function desactivaAll()
    {
        if ($this->pestanas) {
            foreach ($this->pestanas as $pestana)
                $pestana->setActiva(false);
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
        return $tipo == null ? $this->botones : isset($this->botones[$tipo])?$this->botones[$tipo]:[];
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
    function getElemento()
    {
        return $this->elementos;
    }

   
    // filtra els elements d'una pestana amb condicions
    public function getElementos($pestana)
    {
        $elementos = $this->elementos;
        if ($filtro = $pestana->getFiltro()){
            for ($i = 0; $i < count($filtro); $i = $i + 2) {
                $elementos = $elementos->where($filtro[$i], '=', $filtro[$i+1]);
            }
        }
        return $elementos;
    }


    public function activaPestana($nombre)
    {
        foreach ($this->pestanas as $pestana) {
            if ($pestana->getNombre() == $nombre) $pestana->setActiva(true);
            else $pestana->setActiva(false);
        }
    }

    private function getView($nombre, $vista)
    {
        if ($vista == null) return 'intranet.partials.' . $nombre . "." . strtolower($this->model);

        if (substr($vista,0,1)=='.') return substr($vista,1);
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


    /**
     * @param $todos
     * @param $titulo
     * @return Panel
     */
    private function feedPanel($todos, $titulo): Panel
    {
        $this->setElementos($todos);
        $this->setTitulo($titulo);
        return $this;
    }

}
