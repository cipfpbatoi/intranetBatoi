<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Jenssegers\Date\Date;
use \PDF;
use Intranet\Entities\Comision;
use Intranet\Http\Controllers\BaseController;

class ComisionController extends IntranetController
{

    use traitImprimir,
        traitNotificar,
        traitAutorizar;

    protected $gridFields = ['id', 'servicio', 'desde','total', 'situacion'];
    protected $perfil = 'profesor';
    protected $model = 'Comision';
    protected $modal = true;
    
     protected function iniBotones()
     {
         $this->panel->setBotonera(['create'],['show']);
         $this->panel->setBoton('grid', new BotonImg('comision.delete', ['where' => ['estado', '>=', '0', 'estado', '<', '2']]));
         $this->panel->setBoton('grid', new BotonImg('comision.edit', ['where' => ['estado', '>=', '0', 'estado', '<', '2']]));
         $this->panel->setBothBoton('comision.cancel', ['where' => ['estado', '>=', '2', 'estado', '<', '4']]);
         $this->panel->setBothBoton('comision.unpaid', ['where' => ['estado', '==', '3']]);
         $this->panel->setBothBoton('comision.init', ['where' => ['estado', '==', '0']]);
         $this->panel->setBothBoton('comision.notification', ['where' => ['estado', '>', '0', 'hasta', 'posterior', Hoy()]]);
    }

    public function payment()
    {
        return $this->imprimir('payments',4,5,'landscape');
    }
    
    public function paid($id)
    {
        $elemento = $this->class::findOrFail($id);
        $elemento->estado = 5;
        $elemento->save();
    }
    public function unpaid($id)
    {
        $elemento = $this->class::findOrFail($id);
        $elemento->estado = 4;
        $elemento->save();
        return back();
    }
    
    public function autorizar(){
        $this->makeAll(Comision::where('estado','1')->get(),2 );
        return back();
    }  

}
