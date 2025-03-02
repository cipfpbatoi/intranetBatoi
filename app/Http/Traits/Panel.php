<?php


namespace Intranet\Http\Traits;

use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Jenssegers\Date\Date;

/**
 * Trait traitPanel
 * @package Intranet\Http\Controllers
 */
trait Panel
{
    /**
     * Mostra la llista d'elements del panell.
     */
    public function index()
    {
        if (!isset($this->model) || !isset($this->class)) {
            abort(500, "El model i la classe han d'estar definits en el controlador.");
        }

        $todos = $this->search();
        $estados = config("modelos.{$this->model}.estados");

        $this->setTabs($estados, "profile." . strtolower($this->model));
        $this->iniBotones();
        Session::put('redirect', "Panel{$this->model}Controller@index");

        return $this->grid($todos);
    }

    /**
     * Retorna els elements filtrats segons el seu estat i data.
     */
    protected function search()
    {
        if (!isset($this->model) || !isset($this->class)) {
            abort(500, "El model i la classe han d'estar definits en el controlador.");
        }

        $orden = $this->orden ?? 'desde';
        $query = $this->class::where('estado', '>', 0)->orderBy($orden, 'desc');

        if (!Session::get('completa', false)) {
            $fecha = Date::now()->subDays(config('variables.diasNoCompleta'));
            $query->where(function ($q) use ($orden, $fecha) {
                $q->where('estado', '!=', config("modelos.{$this->model}.resolve"))
                    ->where('estado', '!=', config("modelos.{$this->model}.completa"))
                    ->orWhere($orden, '>', $fecha->toDateString());
            });
        }

        return $query->get();
    }

    /**
     * Configura la botónera segons els permisos i estats disponibles.
     */
    protected function setAuthBotonera(array $default = ['2' => 'pdf', '1' => 'autorizar'], bool $enlace = true)
    {
        if (!isset($this->model) || !isset($this->class)) {
            abort(500, "El model i la classe han d'estar definits en el controlador.");
        }

        // Botons col·lectius
        foreach ($default as $item => $valor) {
            if ($this->class::where('estado', '=', $item)->exists()) {
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
        foreach ($estados as $key => $estado) {
            $sustituto = ($key == $sustituye) ? 1 : null;
            $this->panel->setPestana($estado, $key == $activa, $vista, [$field, $key], null, $sustituto, $this->parametresVista);
        }
    }
}
