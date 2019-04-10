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
    protected $gridFields = null;  // campos que ixen en la rejilla
    protected $vista;       // vistes per defecte
    protected $panel;       // panel per a la vista
    protected $titulo = []; // paràmetres per al titol de la vista
    protected $parametresVista = null;
    
    protected $profile = true;
    protected $modal = false; //utilitza vista modal o ono per a insercions i modificats
    protected $search = null; //es gasta quan cal filtrar la cerca
    
    /*  
     * Constructor
     *  asigna: perfil ,classe, panel grid per defecte
     */
    public function __construct()
    {
        if (isset($this->perfil)) $this->middleware($this->perfil);  
        $this->class = $this->namespace . $this->model;
        $this->panel = new Panel($this->model, $this->gridFields,
                isset($this->vista['grid'])?'grid.'.$this->vista['grid']:'grid.standard',true,$this->parametresVista);
        
    }
    //seleciona vista para metodo, por defecto intranet
    protected function chooseView($tipo)
    {
        if (!isset($this->vista[$tipo])) return "intranet.$tipo";
        
        if (strpos($this->vista[$tipo], '.')) return strtolower($this->vista[$tipo]);
        
        return strtolower($this->vista[$tipo]) . ".$tipo";
        
        
    }
    
    
    protected function grid($todos,$modal=false)
    {

        if ($modal) return $this->panel->renderModal($todos,$this->titulo,$this->chooseView('indexModal'),new $this->class);
        
        return $this->panel->render($todos,$this->titulo,$this->chooseView('index'));
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
    
    public function indice($search)
    {
        $this->search = $search;
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
        return $panel->render($todos,$this->titulo,$this->chooseView('list'));
    }

    /*
     * Inicialitza el botons del grid
     */
    protected function iniBotones(){}
    protected function iniPestanas($parametres = null){
        if (view()->exists('intranet.partials.profile.'.strtolower($this->model))&&$this->profile) 
                    $this->panel->setPestana('profile', false,null,null,null,null,$this->parametresVista);
    }
    
    protected function crea_pestanas($estados,$vista,$activa=null,$sustituye = null){
        if (!$activa){
            $activa = Session::get('pestana')?Session::get('pestana'):0;
        }
        foreach ($estados as $key => $estado) {
            $sustituto = ($key == $sustituye)?1:null;
            $this->panel->setPestana($estado, $key == $activa ? true : false, $vista,
                ['estado',$key],null,$sustituto,$this->parametresVista);
        }
    }
    

    
}