<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Modulo_ciclo;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonPost;


class PanelControlProgramacionController extends BaseController
{

    
    protected $model = 'Modulo_ciclo';
    protected $gridFields = ['id','Xciclo', 'Xmodulo', 'estado','Nombre', 'situacion'];
    
   
    protected function search()
    {
        if (UserisAllow(config('roles.rol.direccion')))
            return Modulo_ciclo::all();
        else 
            return Modulo_ciclo::where('idDepartamento', AuthUser()->departamento)
                    ->get();
    }

}
