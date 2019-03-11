<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Modulo_ciclo;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonPost;


/**
 * Class PanelControlProgramacionController
 * @package Intranet\Http\Controllers
 */
class PanelControlProgramacionController extends BaseController
{


    /**
     * @var string
     */
    protected $model = 'Modulo_ciclo';
    /**
     * @var array
     */
    protected $gridFields = ['id','Xciclo', 'Xmodulo', 'estado','Nombre', 'situacion'];


    /**
     * @return \Illuminate\Database\Eloquent\Collection|Modulo_ciclo[]|mixed
     */
    protected function search()
    {
        if (UserisAllow(config('roles.rol.direccion')))
            return Modulo_ciclo::all();
        else 
            return Modulo_ciclo::where('idDepartamento', AuthUser()->departamento)
                    ->get();
    }

}
