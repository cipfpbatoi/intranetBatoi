<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Intranet\Botones\Panel;
use Intranet\Services\ConfirmAndSend;
use Intranet\Services\FormBuilder;
use Response;


abstract class ModalController extends Controller
{
    protected $gridFields = null;  // campos que ixen en la rejilla
    protected $panel;       // panel per a la vista
    protected $parametresVista = []; // paràmetres per a la vista
    protected $vista = null;       // vistes per defecte
    protected $titulo = []; // paràmetres per al titol de la vista
    protected $profile = true; // se mostra profile o no
    protected $redirect = null;  // pàgina a la que redirigir després de inserció o modificat
    protected $formFields = null;
    /*
     * Constructor
     *  asigna: perfil ,classe, panel grid per defecte
     */
    public function __construct()
    {
        parent::__construct();
        $this->panel = new Panel($this->model, $this->gridFields,'grid.standard',true,$this->parametresVista);

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
        return $this->grid();

    }

    protected function grid(){
        return $this->panel->render(
            $this->search(),
            $this->titulo,
            $this->vista ?? 'intranet.indexModal',
            new FormBuilder($this->createWithDefaultValues(),$this->formFields));
    }



    protected function createWithDefaultValues($default = []){
        return new $this->class($default);
    }

    /*
   * destroy($id) return redirect
   * busca i esborra en un model
   * si hi ha fitxer associat l'esborra
   */
    public function destroy($id)
    {
        if ($elemento = $this->class::findOrFail($id)) {
            if ($elemento->fichero && method_exists($this,'borrarFichero')) {
                $this->borrarFichero($elemento->fichero);
            }
            if (method_exists($this,'deleteAttached')){
                $this->deleteAttached($id);
            }
            $elemento->delete();
        }
        return $this->redirect();
    }


    protected function search(){
        $todos =  $this->class::all(); // carrega totes les dades de un model
        if (isset($todos->first()->idProfesor)) // Si existe profesor en el model limite la cerca a les seues
        {
            $todos = $todos->where('idProfesor', '=', AuthUser()->dni);
        }
        return $todos;
    }

    public function confirm($id){
        return ConfirmAndSend::render($this->model,$id);
    }


    /*
     * Inicialitza el botons del grid
     */
    protected function iniBotones(){}

    protected function iniPestanas(){
        // Pestana per al profile
        if ($this->profile && view()->exists('intranet.partials.profile.'.strtolower($this->model))) {
            $this->panel->setPestana('profile', false, null, null, null, null, $this->parametresVista);
        }
    }

    protected function redirect()
    {
        if (Session::get('redirect')) {
            return redirect()->action(Session::get('redirect'));
        } //variable session

        return redirect()->action($this->model . 'Controller@index'); //defecto
    }

}