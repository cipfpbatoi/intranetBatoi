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
use Intranet\Botones\Panel;

/**
 * Class ComisionController
 * @package Intranet\Http\Controllers
 */
class ComisionController extends IntranetController
{

    use traitImprimir,
        traitNotificar,
        traitAutorizar;

    /**
     * @var array
     */
    protected $gridFields = ['id', 'servicio', 'desde','total', 'situacion'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Comision';
    /**
     * @var bool
     */
    protected $modal = true;


    /**
     *
     */
    protected function iniBotones()
     {
         $this->panel->setBotonera(['create'],['show']);
         $this->panel->setBoton('grid', new BotonImg('comision.delete', ['where' => ['estado', '>=', '0', 'estado', '<', '2']]));
         $this->panel->setBoton('grid', new BotonImg('comision.edit', ['where' => ['estado', '>=', '0', 'estado', '<', '2']]));
         $this->panel->setBothBoton('comision.cancel', ['where' => ['estado', '>=', '2', 'estado', '<', '4']]);
         $this->panel->setBothBoton('comision.unpaid', ['where' => ['estado', '==', '3','total','>',0]]);
         $this->panel->setBothBoton('comision.init', ['where' => ['estado', '==', '0']]);
         $this->panel->setBothBoton('comision.notification', ['where' => ['estado', '>', '0', 'hasta', 'posterior', Hoy()]]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function payment()
    {
        return $this->imprimir('payments',4,5,'landscape',false);
    }

    /**
     * @param $id
     */
    public function paid($id)
    {
        $elemento = Comision::findOrFail($id);
        $elemento->estado = 5;
        $elemento->save();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unpaid($id)
    {
        $elemento = Comision::findOrFail($id);
        $elemento->estado = 4;
        $elemento->save();
        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function autorizar(){
        $this->makeAll(Comision::where('estado','1')->get(),2 );
        return back();
    }  

}
