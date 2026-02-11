<?php


namespace Intranet\Http\Traits;

use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonIcon;

/**
 * Trait de suport per a controllers tipus panell.
 *
 * Contracte esperat del controller que usa el trait:
 * - `protected string $model`
 * - `protected string $class`
 * - `protected mixed $panel`
 * - `protected array $parametresVista`
 * - mètodes `iniBotones()` i `grid($todos)`
 */
trait Panel
{
    /**
     * Mostra la llista d'elements del panell.
     */
    public function index()
    {
        $this->guardPanelContract();

        $todos = $this->search();
        $estados = config("modelos.{$this->model}.estados", []);

        if (is_iterable($estados)) {
            $this->setTabs($estados, "profile." . strtolower($this->model));
        }
        $this->iniBotones();
        Session::put('redirect', "Panel{$this->model}Controller@index");

        return $this->grid($todos);
    }

    /**
     * Retorna els elements filtrats segons el seu estat i data.
     */
    protected function search()
    {
        $this->guardPanelContract();

        $orden = $this->orden ?? 'desde';
        $query = $this->class::where('estado', '>', 0)->orderBy($orden, 'desc');

        return $query->get();
    }

    /**
     * Configura la botónera segons els permisos i estats disponibles.
     */
    protected function setAuthBotonera(array $default = ['2' => 'pdf', '1' => 'autorizar'], bool $enlace = true)
    {
        $this->guardPanelContract();

        $targetStates = array_keys($default);
        $availableStates = $this->class::query()
            ->whereIn('estado', $targetStates)
            ->distinct()
            ->pluck('estado')
            ->map(static fn ($state) => (string) $state)
            ->all();

        // Botons col·lectius
        foreach ($default as $item => $valor) {
            if (in_array((string) $item, $availableStates, true)) {
                $this->panel->setBoton('index', new BotonBasico("{$this->model}.$valor", ['id' => $valor], true));
            }
        }

        // Botons individuals
        $botons = [
            ['authorize', 'btn-success authorize', 'estado', '==', '1'],
            ['unauthorize', 'btn-danger unauthorize', 'estado', '==', '2'],
            ['refuse', 'btn-danger refuse', 'estado', '==', '1'],
        ];

        foreach ($botons as [$action, $class, $field, $operator, $value]) {
            $where = [$field, $operator, $value];
            $this->panel->setBoton('profile', new BotonIcon("{$this->model}.$action", compact('class', 'where'), $enlace));
        }
    }

    /**
     * Retorna la pestanya activa actual.
     */
    protected function getActiveTab($default = 0)
    {
        return Session::get('pestana', $default);
    }

    /**
     * Configura les pestanyes del panell.
     */
    protected function setTabs($estados, $vista, $sustituye = null, $field = 'estado')
    {
        $activa = $this->getActiveTab();
        foreach ($estados ?? [] as $key => $estado) {
            $sustituto = ($key == $sustituye) ? 1 : null;
            $this->panel->setPestana($estado, $key == $activa, $vista, [$field, $key], null, $sustituto, $this->parametresVista);
        }

    }

    /**
     * Valida els atributs mínims que necessita el trait.
     */
    private function guardPanelContract(): void
    {
        if (!isset($this->model) || !isset($this->class)) {
            abort(500, "El model i la classe han d'estar definits en el controlador.");
        }
    }
}
