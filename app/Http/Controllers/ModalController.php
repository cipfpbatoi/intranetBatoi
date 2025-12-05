<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Intranet\Botones\Panel;
use Intranet\Http\Traits\Searchable;
use Intranet\Services\ConfirmAndSend;
use Intranet\Services\FormBuilder;



abstract class ModalController extends Controller
{
    use Searchable;
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
        $this->panel = new Panel($this->model, $this->gridFields, 'grid.standard', true, $this->parametresVista);

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

    protected function grid()
    {
        if (is_array($this->vista)) {
            $vista = $this->vista['index'] ??'intranet.indexModal';
        } else {
            $vista = $this->vista ??'intranet.indexModal';
        }
        return $this->panel->render(
            $this->search(),
            $this->titulo,
            $vista ,
            new FormBuilder($this->createWithDefaultValues(), $this->formFields));
    }

    /**
     * Per a recursos amb vista modal, la ruta create redirigeix a l'índex
     * que ja mostra el formulari d'alta dins de la taula.
     */
    public function create()
    {
        return redirect()->action($this->model . 'Controller@index');
    }

    public function edit()
    {
        return redirect()->action($this->model . 'Controller@index');
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
        $elemento = $this->class::find($id);

        if (!$elemento) {
            return redirect()->back()->with('error', 'Element no trobat');
        }

        if ($elemento->fichero && method_exists($this, 'borrarFichero')) {
            $this->borrarFichero($elemento->fichero);
        }

        if (method_exists($this, 'deleteAttached')) {
            $this->deleteAttached($id);
        }

        $elemento->delete();

        return $this->redirect();
    }


    public function confirm($id){
        return ConfirmAndSend::render($this->model,$id);
    }


    /*
     * Inicialitza el botons del grid
     */
    protected function iniBotones()
    {

    }

    protected function iniPestanas()
    {
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
