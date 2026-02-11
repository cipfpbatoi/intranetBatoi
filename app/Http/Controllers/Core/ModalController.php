<?php

namespace Intranet\Http\Controllers\Core;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\Notifications\ConfirmAndSend;
use Intranet\Services\UI\FormBuilder;
use Intranet\UI\Panels\Panel;

/**
 * Controlador base per a recursos amb UX modal.
 *
 * Responsabilitats:
 * - renderitzar el grid + formulari modal en `index`,
 * - cercar registres del model amb filtre opcional per `idProfesor`,
 * - redirigir `create/edit` cap a `index`,
 * - oferir utilitats comunes de destrucció i confirmació.
 */
abstract class ModalController extends Controller
{
    /**
     * Cache local per evitar consultar schema repetidament.
     *
     * @var array<string, bool>
     */
    private static array $searchColumnCache = [];

    /**
     * Camps visibles en grid.
     *
     * @var array|null
     */
    protected $gridFields = null;
    /**
     * Instància de panell de UI.
     *
     * @var mixed
     */
    protected $panel;
    /**
     * Paràmetres extra de vista.
     *
     * @var array
     */
    protected $parametresVista = [];
    /**
     * Vista configurada (string o array per acció).
     *
     * @var mixed
     */
    protected $vista = null;
    /**
     * Paràmetres de títol.
     *
     * @var array
     */
    protected $titulo = [];
    /**
     * Indica si es mostra pestanya `profile`.
     *
     * @var bool
     */
    protected $profile = true;
    /**
     * Acció preferida de redirecció post-operació.
     *
     * @var string|null
     */
    protected $redirect = null;
    /**
     * Config de camps de formulari.
     *
     * @var array|null
     */
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

    /**
     * Renderitza la vista modal amb grid i formulari d'alta.
     *
     * @return \Illuminate\Contracts\View\View
     */
    protected function grid()
    {
        return $this->panel->render(
            $this->search(),
            $this->titulo,
            $this->resolveIndexView(),
            new FormBuilder($this->createWithDefaultValues(), $this->formFields));
    }

    /**
     * Resol la vista d'index del modal.
     *
     * @return string
     */
    protected function resolveIndexView(): string
    {
        if (is_array($this->vista)) {
            return $this->vista['index'] ?? 'intranet.indexModal';
        }

        return $this->vista ?? 'intranet.indexModal';
    }

    /**
     * Cerca per defecte del modal:
     * - retorna tots els registres del model
     * - si existeix `idProfesor`, filtra pel professor autenticat.
     */
    protected function search()
    {
        $modelClass = $this->resolveModelClass();

        $query = $modelClass::query();

        if ($this->hasModelColumn($modelClass, 'idProfesor')) {
            $query->where('idProfesor', '=', AuthUser()->dni);
        }

        return $query->get();
    }

    /**
     * Comprova si la taula del model té una columna.
     *
     * @param string $modelClass
     * @param string $column
     * @return bool
     */
    private function hasModelColumn(string $modelClass, string $column): bool
    {
        $cacheKey = $modelClass . ':' . $column;

        if (!array_key_exists($cacheKey, self::$searchColumnCache)) {
            $table = (new $modelClass)->getTable();
            self::$searchColumnCache[$cacheKey] = Schema::hasColumn($table, $column);
        }

        return self::$searchColumnCache[$cacheKey];
    }

    /**
     * Resol la classe de model del controlador modal.
     *
     * @return string
     */
    private function resolveModelClass(): string
    {
        if (empty($this->class) || !class_exists($this->class)) {
            abort(500, "L'atribut 'class' no està definit o no és vàlid en la classe modal.");
        }

        return $this->class;
    }

    /**
     * Per a recursos amb vista modal, la ruta create redirigeix a l'índex
     * que ja mostra el formulari d'alta dins de la taula.
     */
    public function create()
    {
        return redirect()->action($this->model . 'Controller@index');
    }

    public function edit($id = null)
    {
        return redirect()->action($this->model . 'Controller@index');
    }



    /**
     * Crea una instància buida del model per al formulari modal.
     *
     * @param array $default
     * @return mixed
     */
    protected function createWithDefaultValues($default = [])
    {
        $modelClass = $this->resolveModelClass();
        return new $modelClass($default);
    }

    /*
   * destroy($id) return redirect
   * busca i esborra en un model
   * si hi ha fitxer associat l'esborra
   */
    public function destroy($id)
    {
        $modelClass = $this->resolveModelClass();
        $elemento = $modelClass::find($id);

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


    /**
     * Retorna la vista de confirmació per al model.
     *
     * @param int|string $id
     * @return mixed
     */
    public function confirm($id){
        return ConfirmAndSend::render($this->model,$id);
    }


    /*
     * Inicialitza el botons del grid
     */
    protected function iniBotones()
    {

    }

    /**
     * Inicialitza pestanyes per defecte.
     *
     * @return void
     */
    protected function iniPestanas()
    {
        // Pestana per al profile
        if ($this->profile && view()->exists('intranet.partials.profile.'.strtolower($this->model))) {
            $this->panel->setPestana('profile', false, null, null, null, null, $this->parametresVista);
        }
    }

    /**
     * Resol redirecció de retorn en fluxos modal.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirect()
    {
        if (Session::get('redirect')) {
            return redirect()->action(Session::get('redirect'));
        } //variable session

        return redirect()->action($this->model . 'Controller@index'); //defecto
    }

}
