<?php
/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */
namespace Intranet\Http\Controllers;

use Intranet\UI\Panels\Panel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Intranet\Services\Notifications\ConfirmAndSend;
use Intranet\Services\UI\FormBuilder;

/**
 * Controlador base per a pantalles de l'intranet amb grid i pestanyes.
 *
 * Proporciona:
 * - resolució de vistes (`chooseView`),
 * - renderitzat de grid (`grid`),
 * - fluxos base (`index`, `indice`, `confirm`),
 * - cerca per defecte amb filtre per `idProfesor` quan existeix.
 */
abstract class BaseController extends Controller
{ 
    /**
     * Cache de comprovacions de columnes per model.
     *
     * @var array<string, bool>
     */
    private static array $modelColumnCache = [];

    /**
     * Camps visibles en el grid.
     *
     * @var array|null
     */
    protected $gridFields = null;  // campos que ixen en la rejilla

    /**
     * Configuració de vistes (string o array per acció).
     *
     * @var mixed
     */
    protected $vista;       // vistes per defecte

    /**
     * Instància de panell de la UI.
     *
     * @var mixed
     */
    protected $panel;       // panel per a la vista

    /**
     * Paràmetres per al títol de la vista.
     *
     * @var array
     */
    protected $titulo = []; // paràmetres per al titol de la vista

    /**
     * Paràmetres addicionals per a la vista/panel.
     *
     * @var array
     */
    protected $parametresVista = [];

    /**
     * Indica si s'ha de mostrar pestanya de profile.
     *
     * @var bool
     */
    protected $profile = true;

    /**
     * Si és `true`, usa modal en index.
     *
     * @var bool
     */
    protected $modal = false; //utilitza vista modal o ono per a insercions i modificats

    /**
     * Filtre de cerca persistent per a `indice`.
     *
     * @var mixed
     */
    protected $search = null; //es gasta quan cal filtrar la cerca

    /**
     * Definició de camps de formulari.
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
        $this->panel = new Panel($this->model, $this->gridFields,
                isset($this->vista['grid'])?'grid.'.$this->vista['grid']:'grid.standard', true,$this->parametresVista);
        
    }
    /**
     * Resol la vista a utilitzar per a una acció.
     *
     * @param string $tipo
     * @return string
     */
    protected function chooseView($tipo)
    {
        if (!isset($this->vista)) {
            return "intranet.$tipo";
        }

        if (is_array($this->vista)) {
            if (!isset($this->vista[$tipo])) {
                return "intranet.$tipo";
            }

            $configured = strtolower($this->vista[$tipo]);
            if (strpos($configured, '.') !== false) {
                return $configured;
            }

            return $configured . ".$tipo";
        }

        $configured = strtolower((string) $this->vista);
        if (strpos($configured, '.') !== false) {
            return $configured;
        }

        return $configured . ".$tipo";
    }
    /**
     * Renderitza el grid amb o sense formulari modal.
     *
     * @param mixed $todos
     * @param bool $modal
     * @return \Illuminate\Contracts\View\View
     */
    protected function grid($todos, $modal=false)
    {
        if ($modal) {
            return $this->panel->render($todos, $this->titulo, $this->chooseView('indexModal'), new FormBuilder($this->createWithDefaultValues(), $this->formFields));
        }
        return $this->panel->render($todos, $this->titulo, $this->chooseView('index'));
    }

    /**
     * Extensió per a paràmetres addicionals en classes filles.
     *
     * @return array
     */
    protected function parametres()
    {
        return [];
    }
    /**
     * Acció index estàndard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniBotones();
        $this->iniPestanas();


        return $this->grid($this->search(), $this->modal);
    }

    /**
     * Mostra modal de confirmació per a un registre.
     *
     * @param int|string $id
     * @return mixed
     */
    public function confirm($id)
    {
        return ConfirmAndSend::render($this->model, $id);
    }
    /**
     * Variante d'index amb filtre extern.
     *
     * @param mixed $search
     * @return \Illuminate\Contracts\View\View
     */
    public function indice($search)
    {
        $this->search = $search;
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniBotones();
        $this->iniPestanas();
        return $this->grid($this->search(), $this->modal);
    }
    
    /**
     * Cerca per defecte del controlador base.
     *
     * Si existeix la columna `idProfesor`, aplica filtre per usuari autenticat.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function search()
    {
        $class = $this->resolveModelClass();
        $query = $class::query();

        if ($this->hasModelColumn($class, 'idProfesor')) {
            $query->where('idProfesor', '=', AuthUser()->dni);
        }

        return $query->get();
    }
    
    /**
     * Renderitza una vista de llistat sobre un panell concret.
     *
     * @param mixed $todos
     * @param mixed $panel
     * @return \Illuminate\Contracts\View\View
     */
    protected function llist($todos, $panel)
    {
        return $panel->render($todos,$this->titulo,$this->chooseView('list'));
    }

    /**
     * Punt d'extensió per inicialitzar botons en classes filles.
     *
     * @return void
     */
    protected function iniBotones()
    {}

    /**
     * Inicialitza pestanyes per defecte.
     *
     * @param mixed $parametres
     * @return void
     */
    protected function iniPestanas($parametres = null)
    {
        if (view()->exists('intranet.partials.profile.'.strtolower($this->model))&&$this->profile) {
            $this->panel->setPestana('profile', false, null, null, null, null, $this->parametresVista);
        }
    }

    /**
     * Resol la classe de model actual del controlador.
     *
     * @return string
     */
    private function resolveModelClass(): string
    {
        if (!empty($this->class) && class_exists($this->class)) {
            return $this->class;
        }

        if (empty($this->model)) {
            abort(500, 'BaseController misconfigured: $model not set in '.static::class);
        }

        $candidate = ltrim($this->model, '\\');
        if (!class_exists($candidate)) {
            $namespace = property_exists($this, 'namespace') ? $this->namespace : 'Intranet\\Entities\\';
            $candidate = $namespace . $this->model;
        }

        if (!class_exists($candidate)) {
            abort(500, 'Model class not found: '.$this->model);
        }

        $this->class = $candidate;
        return $candidate;
    }

    /**
     * Comprova si un model té una columna, amb cache en memòria.
     *
     * @param string $class
     * @param string $column
     * @return bool
     */
    private function hasModelColumn(string $class, string $column): bool
    {
        $cacheKey = $class . ':' . $column;
        if (!array_key_exists($cacheKey, self::$modelColumnCache)) {
            $table = (new $class)->getTable();
            self::$modelColumnCache[$cacheKey] = Schema::hasColumn($table, $column);
        }

        return self::$modelColumnCache[$cacheKey];
    }
}
