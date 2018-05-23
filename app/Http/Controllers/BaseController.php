<?php
/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */
namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Controller;
use Intranet\Botones\Panel;
use Illuminate\Support\Facades\Session;

abstract class BaseController extends Controller
{ 
    protected $namespace = 'Intranet\Entities\\'; //string on es troben els models de dades
    protected $model;       // model de dades utilitzat  
    protected $class;       // clase del model de dades
    protected $perfil = null; // perfil que pot accedir al controlador
    protected $gridFields;  // campos que ixen en la rejilla
    protected $vista;       // vistes per defecte
    protected $panel;       // panel per a la vista
    protected $titulo = []; // paràmetres per al titol de la vista
    protected $redirect = null;  // pàgina a la que redirigir després de inserció o modificat
    protected $profile = true;
    
    protected $modal = false; //utilitza vista modal o ono per a insercions i modificats
    
    
    /*  
     * Constructor
     *  asigna: perfil ,classe, panel grid per defecte
     */
    public function __construct()
    {
        if (isset($this->perfil)) $this->middleware($this->perfil);  
        $this->class = $this->namespace . $this->model;
        $this->panel = new Panel($this->model, $this->gridFields, isset($this->vista['grid'])?'grid.'.$this->vista['grid']:'grid.standard');
        
    }
    //seleciona vista para metodo, por defecto intranet
    protected function chooseView($tipo)
    {
        if (isset($this->vista[$tipo]))
            if (strpos($this->vista[$tipo], '.'))
                return strtolower($this->vista[$tipo]);
            else
                return strtolower($this->vista[$tipo]) . ".$tipo";
        else
            return "intranet.$tipo";
    }
    /*
     * redirect 
     * redirecciona per ordre a :
     *   variable de sessio(distinguir professor i direccio
     *   a variable redirect del modelo
     *   al index del modelo
     */
    
    protected function redirect()
    {
        if (Session::get('redirect')) $this->redirect = Session::get('redirect'); //variable session
        if ($this->redirect) return redirect()->action($this->redirect); // variable controlador
        else return redirect()->action($this->model . 'Controller@index'); //defecto
    }
    
    protected function grid($todos,$modal=false)
    {
        if ($modal)return $this->panel->view($todos,$this->titulo,$this->chooseView('indexModal'),new $this->class); 
        else  return $this->panel->view($todos,$this->titulo,$this->chooseView('index'));
    }
    /*  
     * index return vista
     *  carrega les dades que faran falta a la vista (per defecte) 
     *  se utilitza quan no cal més filtrats
     */
    
    public function index()
    {
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniBotones();
        $this->iniPestanas();
        return $this->grid($this->search(),$this->modal);
    }
    
    protected function search(){
        $todos =  $this->class::all(); // carrega totes les dades de un model
        if (isset($todos->first()->idProfesor)) // Si existe profesor en el model limite la cerca a les seues
                $todos = $todos->where('idProfesor', '=', AuthUser()->dni);
        return $todos;
    }
    
    /*
     * llist ($todos,$panel)
     * torna la vista list
     */
    protected function llist($todos, $panel)
    {
        return $panel->view($todos,$this->titulo,$this->chooseView('list'));
    }

    /*
     * Inicialitza el botons del grid
     */
    protected function iniBotones(){}
    protected function iniPestanas($parametres = null){
        if (view()->exists('intranet.partials.profile.'.strtolower($this->model))&&$this->profile) 
                    $this->panel->setPestana('profile', false);
        
    }

    
}