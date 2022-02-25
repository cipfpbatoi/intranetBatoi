<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Intranet\Botones\BotonImg;
use Intranet\Componentes\DocumentoFct;
use Intranet\Componentes\MyMail;
use Intranet\Http\Requests\ComisionRequest;
use Intranet\Services\CalendarService;
use Intranet\Services\StateService;
use \PDF;
use Intranet\Entities\Comision;
use Intranet\Entities\Fct;
use Intranet\Entities\Activity;
use Jenssegers\Date\Date;


/**
 * Class ComisionController
 * @package Intranet\Http\Controllers
 */
class ComisionController extends ModalController
{

    use traitImprimir,  traitSCRUD,
        traitAutorizar;


    /**
     * @var array
     */
    protected $gridFields = ['id', 'descripcion', 'desde','total', 'situacion'];
    /**
     * @var string
     */
    protected $model = 'Comision';



    public function store(ComisionRequest $request)
    {
        $new = new Comision();
        $new->fillAll($request);
        if ($new->fct) {
            return $this->detalle($new->id);
            //return redirect()->route('comision.detalle', ['comision' => $new->id]);
        }
        return $this->redirect();
    }

    public function update(ComisionRequest $request, $id)
    {
        Comision::findOrFail($id)->fillAll($request);
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


    protected function createWithDefaultValues( $default=[]){
        $manana = new Date('tomorrow');
        $manana->addHours(8);
        if (Fct::misFcts()->count()){
            $fct = 1;
            $servicio = "Visita a Empreses per FCT: ";
        }
        else{
            $fct = 0;
            $servicio = "Visita a Empreses: ";
        }
        $comision = new Comision([
                'idProfesor'=>AuthUser()->dni,
                'desde'=>$manana,
                'hasta'=>$manana,
                'fct'=>$fct,
                'servicio'=>$servicio
        ]);
        if (!$fct) {
            $comision->deleteInputType('fct');
        }
        return $comision;
    }

    private function enviarCorreos($comision){
        foreach ($comision->Fcts as $fct){
            if ($fct->pivot->aviso){
                $this->sendEmail($fct,$comision->desde);

            }
            Activity::record('visita', $fct, null, $comision->desde, 'Visita Empresa');
        }

    }

    private function sendEmail($elemento,$fecha)
    {
        if (file_exists(storage_path("tmp/visita_$elemento->id.ics"))){
            unlink(storage_path("tmp/visita_$elemento->id.ics"));
        }

        $ini = buildFecha($fecha,$elemento->pivot->hora_ini);
        $fin = buildFecha($fecha,$elemento->pivot->hora_ini);
        $fin->add(new \DateInterval("PT30M"));

        file_put_contents(storage_path("tmp/visita_$elemento->id.ics"), CalendarService::render($ini,$fin,'Visita del Tutor CIPFPBatoi','Seguimiento Fct',$elemento->Centro)->render());
        $attach = [ "tmp/visita_$elemento->id.ics" => 'text/calendar'];
        $documento = new DocumentoFct('visitaComision');
        $documento->fecha = $fecha;


        $mail = new MyMail($elemento,$documento->view,$documento->email,$attach,'visita');
        $mail->send($fecha);

    }

    protected function init($id)
    {
        $comision = Comision::find($id);
        $this->enviarCorreos($comision);
        $stSrv = new StateService($comision);
        $stSrv->putEstado($this->init);

        return back();
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function payment()
    {
        return $this->imprimir('payments',4,5,'landscape',false);
    }

    public function printAutoritzats()
    {
        return $this->imprimir('comisionsServei');
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
        $all = Fct::misFcts()->distinct()->esFct()->get();
        $allFcts = collect();
        foreach ($all as $fct){
            $allFcts[$fct->Colaboracion->idCentro] = $fct;
        }
        $allCol = Fct::misFctsColaboracion()->distinct()->esFct()->get();
        foreach ($allCol as $fct){
            $allFcts[$fct->Colaboracion->idCentro] = $fct;
        }
        $allFcts = hazArray($allFcts,'id','Centro');
        asort($allFcts);
        return view('comision.detalle', compact('comision', 'allFcts'));
    }

    public function createFct(Request $request, $comision_id)
    {
        $comision = Comision::find($comision_id);
        $aviso = isset($request->aviso)?1:0;
        $comision->fcts()->syncWithoutDetaching([$request->idFct => ['hora_ini' => $request->hora_ini ,'aviso' => $aviso]]);
        return $this->detalle($comision_id);
    }

    public function deleteFct($comision_id,$fct_id)
    {
        $comision = Comision::find($comision_id);
        $comision->fcts()->detach($fct_id);
        return $this->detalle($comision_id);
    }

}
