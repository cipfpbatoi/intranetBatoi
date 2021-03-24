<?php

namespace Intranet\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Intranet\Botones\Panel;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $namespace = 'Intranet\Entities\\'; //string on es troben els models de dades
    protected $model;       // model de dades utilitzat
    protected $class;       // clase del model de dades
    protected $perfil = null; // perfil que pot accedir al controlador


    /*
     * Constructor
     *  asigna: perfil ,classe, panel grid per defecte
     */
    public function __construct()
    {
        if (isset($this->perfil)) {
            $this->middleware($this->perfil);
        }
        $this->class = $this->namespace . $this->model;
    }
}
