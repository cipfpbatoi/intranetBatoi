<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Intranet\Botones\BotonImg;
use Intranet\Botones\Mail;
use \PDF;
use Intranet\Entities\Comision;
use Intranet\Entities\Fct;
use Intranet\Entities\Activity;


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
    protected $gridFields = ['id', 'descripcion', 'desde','total', 'situacion'];
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


    public function store(Request $request)
    {
        $id = $this->realStore($request);
        if (Comision::find($id)->fct)
            return redirect()->route('comision.detalle', ['comision' => $id]);
        return $this->redirect();
    }

    /**
     *
     */
    protected function iniBotones()
     {
         $this->panel->setBotonera(['create']);
         $this->panel->setBothBoton('comision.detalle', ['where' => ['estado', '<', '2','fct','==',1,'estado','>',-1]]);
         $this->panel->setBoton('grid', new BotonImg('comision.edit', ['where' => ['estado', '>=', '0', 'estado', '<', '2']]));
         $this->panel->setBoton('grid', new BotonImg('comision.delete', ['where' => ['estado', '>=', '0', 'estado', '<', '2']]));
         $this->panel->setBothBoton('comision.cancel', ['where' => ['estado', '>=', '2', 'estado', '<', '4']]);
         $this->panel->setBothBoton('comision.unpaid', ['where' => ['estado', '==', '3','total','>',0]]);
         $this->panel->setBothBoton('comision.init', ['where' => ['estado', '==', '0']]);
         $this->panel->setBothBoton('comision.notification', ['where' => ['estado', '>', '0', 'hasta', 'posterior', Hoy()]]);
    }

    private function enviarCorreos($comision){
        foreach ($comision->Fcts as $fct)
            if ($fct->pivot->aviso)  $this->sendEmail($fct,$comision->desde);
            else Activity::record('visita', $fct, null, $comision->desde, 'Visita Empresa');

    }

    private function sendEmail($elemento,$fecha)
    {
        if (file_exists(storage_path("tmp/visita_$elemento->id.ics")))
            unlink(storage_path("tmp/visita_$elemento->id.ics"));

        $ini = buildFecha($fecha,$elemento->pivot->hora_ini);
        $fin = buildFecha($fecha,$elemento->pivot->hora_ini);
        $fin->add(new \DateInterval("PT30M"));

        file_put_contents(storage_path("tmp/visita_$elemento->id.ics"), $this->build_ics($ini,$fin,'Visita del Tutor CIPFPBatoi','Seguimiento Fct',$elemento->Centro)->render());
        $attach = [ "tmp/visita_$elemento->id.ics" => 'text/calendar'];

        $mail = new Mail($elemento,$fecha,'Visita Empresa','email.fct.confirm',AuthUser()->email,AuthUser()->fullName,null,true,$attach,'visita');
        $mail->send($fecha);

    }

    protected function init($id)
    {

        $this->enviarCorreos(Comision::find($id));
        $this->class::putEstado($id,$this->init);
        return back();
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

    public function detalle($id)
    {
        $comision = Comision::find($id);
        $allFcts = hazArray(Fct::misFctsColaboracion()->esFct()->get(),'id','Centro');

        return view('comision.detalle', compact('comision', 'allFcts'));
    }

    public function createFct(Request $request, $comision_id)
    {
        $comision = Comision::find($comision_id);
        $aviso = isset($request->aviso)?1:0;
        $comision->fcts()->syncWithoutDetaching([$request->idFct => ['hora_ini' => $request->hora_ini ,'aviso' => $aviso]]);
        return redirect()->route('comision.detalle', ['comision' => $comision_id]);
    }

    public function deleteFct($comision_id,$fct_id)
    {
        $comision = Comision::find($comision_id);
        $comision->fcts()->detach($fct_id);
        return redirect()->route('comision.detalle', ['comision' => $comision_id]);
    }

}
