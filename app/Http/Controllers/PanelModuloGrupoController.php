<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Programacion;
use Intranet\Botones\BotonImg;
use Illuminate\Support\Facades\Session;

class PanelModuloGrupoController extends BaseController
{

   
    protected $model = 'Modulo_grupo';
    protected $gridFields = ['Xdepartamento','XCiclo','XModulo','XTorn'];
    protected $redirect = 'PanelModuloGrupoController@index';
    protected $parametresVista = [];
    
    public function search()
    {
        return Modulo_grupo::all();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton(
            'grid',
            new BotonImg('modulogrupo.pdf')
        );
    }

    protected function pdf($id)
    {
        $modulogrupo = Modulo_grupo::findorfail($id);

            // Obtén los datos necesarios para construir la URL
        $centerId = config('contacto.codi');
        $cycleId = $modulogrupo->ModuloCiclo->idCiclo;
        $moduleCode = $modulogrupo->ModuloCiclo->idModulo;
        $turn = $modulogrupo->Xtorn;

            // Construye la URL
        $url = "https://pcompetencies.cipfpbatoi.es/public/syllabus/{$centerId}/{$cycleId}/{$moduleCode}/{$turn}";

            // Redirige a la URL
        return redirect()->away($url);

    }
}
