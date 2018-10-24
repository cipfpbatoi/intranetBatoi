<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFct;
use DB;
use Styde\Html\Facades\Alert;
use Intranet\Botones\Panel;
use Intranet\Entities\Documento;
use Illuminate\Support\Facades\Session;

class PanelFctConvalController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Alumno_Fct';
    protected $gridFields = ['Nombre'];
    protected $profile = false;

    public function search()
    {
        return AlumnoFct::misConvalidados()->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('fct_convalidacion.delete'));
    }

    public function destroy($id)
    {
        $fct = AlumnoFct::find($id);
        $fct->delete();

        return redirect('/fct');
    }

} 