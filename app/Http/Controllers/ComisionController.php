<?php

namespace Intranet\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Intranet\UI\Botones\BotonImg;
use Intranet\Support\Fct\DocumentoFctConfig;
use Intranet\Services\Mail\MyMail;
use Intranet\Entities\Activity;
use Intranet\Entities\Comision;
use Intranet\Entities\Fct;
use Intranet\Http\Requests\ComisionRequest;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Imprimir;
use Intranet\Http\Traits\Core\SCRUD;
use Intranet\Services\Calendar\CalendarService;
use Intranet\Services\Notifications\ConfirmAndSend;
use Intranet\Services\General\StateService;
use Jenssegers\Date\Date;


/**
 * Class ComisionController
 * @package Intranet\Http\Controllers
 */
class ComisionController extends ModalController
{

    use Imprimir,  SCRUD,
        Autorizacion;


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
        $request->merge([
            'idProfesor' => $request->idProfesor ?? authUser()->dni,
        ]);
         
        $new->fillAll($request);
        if ($new->fct) {
            return $this->detalle($new->id);
        }
        return $this->confirm($new->id);
    }

    public function update(ComisionRequest $request, $id)
    {
        Comision::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

    public function confirm($id)
    {
        $comision = Comision::findOrFail($id);
        if ($comision->estado == 0) {
            return ConfirmAndSend::render($this->model, $id, 'Enviar a direcció i correus confirmació');
        }
        return $this->redirect();
    }



    /**
     *
     */
    protected function iniBotones()
     {
         $this->panel->setBotonera(['create']);
         $this->panel->setBothBoton('comision.detalle', ['where' => ['estado', '<', '2','fct','==',1,'estado','>',-1]]);
         $this->panel->setBoton(
             'grid',
             new BotonImg('comision.edit', ['where' => ['estado', '>=', '0', 'estado', '<', '2']])
         );
         $this->panel->setBoton(
             'grid',
             new BotonImg('comision.delete', ['where' => ['estado', '>=', '0', 'estado', '<', '2']])
         );
         $this->panel->setBothBoton(
             'comision.cancel',
             ['class'=>'confirm','where' => ['estado', '>=', '2', 'estado', '<', '4']]
         );
         $this->panel->setBothBoton('comision.unpaid', ['where' => ['estado', '==', '3','total','>',0]]);
         $this->panel->setBothBoton('comision.init', ['where' => ['estado', '==', '0','desde','posterior',Hoy()]]);
         $this->panel->setBothBoton(
             'comision.notification',
             ['where' => ['estado', '>', '0', 'hasta', 'posterior', Hoy()]]
         );
    }


    protected function createWithDefaultValues($default=[])
    {
        $manana = new Date('tomorrow');
        $manana->addHours(8);
        if (Fct::misFcts()->count()) {
            $fct = 1;
            $servicio = "Visita a Empreses per FCT: ";
        } else {
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
        if (AuthUser()->dni == config('avisos.director')) {
            $comision->setInputType('idProfesor', ["type" => "select"]);
        }
        return $comision;
    }

    private function enviarCorreos($comision)
    {
        foreach ($comision->Fcts as $fct) {
            if ($fct->pivot->aviso) {
                $this->sendEmail($fct, $comision->desde);
            }
            Activity::record('visita', $fct, null, $comision->desde, 'Visita Empresa');
        }

    }

    private function sendEmail($elemento, $fecha)
    {

        if (file_exists(storage_path("tmp/visita_$elemento->id.ics"))) {
            unlink(storage_path("tmp/visita_$elemento->id.ics"));
        }

        $ini = buildFecha($fecha, $elemento->pivot->hora_ini);
        $fin = buildFecha($fecha, $elemento->pivot->hora_ini);
        $fin->add(new \DateInterval("PT30M"));

        file_put_contents(
            storage_path("tmp/visita_$elemento->id.ics"),
            CalendarService::render(
                $ini,
                $fin,
                'Visita del Tutor CIPFPBatoi',
                'Seguimiento Fct',
                $elemento->Centro
            )->render());
        $attach = [ "tmp/visita_$elemento->id.ics" => 'text/calendar'];
        $documento = new DocumentoFctConfig('visitaComision');
        $documento->fecha = $fecha;
        $elemento->desde = $fecha;


        $mail = new MyMail($elemento, $documento->view, $documento->email, $attach, 'visita');
        $mail->send($fecha);

    }

    protected function init($id)
    {
        $comision = Comision::find($id);
        $this->enviarCorreos($comision);
        $stSrv = new StateService($comision);
        $stSrv->putEstado($this->init);

        return $this->redirect();
    }


    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function payment()
    {
        return $this->imprimir('payments', 6, 5, 'landscape', false);
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
    public function autorizar()
    {
        StateService::makeAll(Comision::where('estado', '1')->get(), 2);
        return back();
    }

    public function detalle($id)
    {
        $comision = Comision::find($id);
        $all = Fct::esFct()
            ->misFcts()
            ->orWhere('cotutor', authUser()->dni)
            ->with('Colaboracion')
            ->distinct()
            ->orderBy('id')
            ->get();
        $allFcts = collect();
        foreach ($all as $fct) {
            if (!$fct->Colaboracion) {
                continue;
            }
            $allFcts[$fct->Colaboracion->idCentro] = $fct;
        }
        $allFcts = hazArray($allFcts, 'id', 'Centro');
        asort($allFcts);
        return view('comision.detalle', compact('comision', 'allFcts'));
    }

    public function createFct(Request $request, $comisionId)
    {
        $comision = Comision::find($comisionId);
        $aviso = isset($request->aviso)?1:0;
        $comision->fcts()
            ->syncWithoutDetaching([$request->idFct => ['hora_ini' => $request->hora_ini ,'aviso' => $aviso]]);
        return $this->detalle($comisionId);
    }

    public function deleteFct($comisionId, $fctId)
    {
        $comision = Comision::find($comisionId);
        $comision->fcts()->detach($fctId);
        return $this->detalle($comisionId);
    }

}
