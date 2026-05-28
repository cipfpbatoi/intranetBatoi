<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Comision\ComisionService;
use Intranet\Http\Controllers\Core\ModalController;
use Intranet\Presentation\Crud\ComisionCrudSchema;

use Illuminate\Http\Request;
use Intranet\UI\Botones\BotonImg;
use Intranet\Support\Fct\DocumentoFctConfig;
use Intranet\Services\Mail\MyMail;
use Intranet\Entities\Activity;
use Intranet\Entities\Comision;
use Intranet\Entities\Fct;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Requests\ComisionRequest;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Http\Traits\Core\SCRUD;
use Intranet\Services\Calendar\CalendarService;
use Intranet\Services\Notifications\ConfirmAndSend;
use Intranet\Services\General\StateService;
use Illuminate\Support\Carbon;


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
    protected $gridFields = ComisionCrudSchema::GRID_FIELDS;
    protected $formFields = ComisionCrudSchema::FORM_FIELDS;
    /**
     * @var string
     */
    protected $model = 'Comision';

    private ?ComisionService $comisionService = null;

    public function __construct(?ComisionService $comisionService = null)
    {
        parent::__construct();
        $this->comisionService = $comisionService;
    }

    private function comisionService(): ComisionService
    {
        if ($this->comisionService === null) {
            $this->comisionService = app(ComisionService::class);
        }

        return $this->comisionService;
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return Comision
     */
    private function findComisionOrFail($id): Comision
    {
        return $this->findModelOrFail(
            Comision::class,
            $id,
            'Comissió no trobada',
            ['comision_id' => $id]
        );
    }


    /**
     * @param ComisionRequest $request
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(ComisionRequest $request)
    {
        $this->authorize('create', Comision::class);
        $request->merge([
            'idProfesor' => $request->idProfesor ?? authUser()->dni,
        ]);

        $id = $this->persist($request);
        $comision = $this->wrapNotFound(
            fn () => $this->comisionService()->findOrFail((int) $id),
            'Comissió no trobada',
            ['comision_id' => $id]
        );

        if ($comision->fct) {
            return $this->detalle($comision->id);
        }

        return $this->confirm($comision->id);
    }

    /**
     * @param ComisionRequest $request
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(ComisionRequest $request, $id)
    {
        $comision = $this->wrapNotFound(
            fn () => $this->comisionService()->findOrFail((int) $id),
            'Comissió no trobada',
            ['comision_id' => $id]
        );
        $this->authorize('update', $comision);
        $this->persist($request, $id);
        return $this->redirect();
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function confirm($id)
    {
        $comision = $this->wrapNotFound(
            fn () => $this->comisionService()->findOrFail((int) $id),
            'Comissió no trobada',
            ['comision_id' => $id]
        );
        $this->authorize('update', $comision);
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
        $manana = new Carbon('tomorrow');
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

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function init($id)
    {
        $comision = $this->findComisionOrFail($id);
        $this->authorize('update', $comision);
        $this->enviarCorreos($comision);
        $stSrv = new StateService($comision);
        $stSrv->putEstado($this->init);

        return $this->redirect();
    }


    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unpaid($id)
    {
        $this->authorize('update', $this->findComisionOrFail($id));
        $this->setEstado($id, 4);
        return back();
    }

    /**
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Contracts\View\View
     */
    public function detalle($id)
    {
        $comision = $this->findComisionOrFail($id);
        $this->authorize('view', $comision);
        $allFcts = $this->buildFctOptions();

        return view('comision.detalle', compact('comision', 'allFcts'));
    }

    /**
     * Redirigix les cridades GET accidentals de createFct al detall.
     *
     * @param int|string $comisionId
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createFctRedirect($comisionId)
    {
        $this->authorize('manageFct', $this->findComisionOrFail($comisionId));

        return redirect()->route('comision.detalle', ['comision' => $comisionId]);
    }

    /**
     * @param Request $request
     * @param int|string $comisionId
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createFct(Request $request, $comisionId)
    {
        $this->authorize('manageFct', $this->findComisionOrFail($comisionId));
        $validated = $this->validate($request, [
            'idFct' => 'required|integer|min:1|exists:fcts,id',
            'hora_ini' => 'required',
        ]);

        $this->comisionService()->attachFct(
            (int) $comisionId,
            (int) $validated['idFct'],
            (string) $validated['hora_ini'],
            $request->boolean('aviso')
        );
        return $this->detalle($comisionId);
    }

    /**
     * @param int|string $comisionId
     * @param int|string $fctId
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFct($comisionId, $fctId)
    {
        $this->authorize('manageFct', $this->findComisionOrFail($comisionId));
        $this->comisionService()->detachFct((int) $comisionId, (int) $fctId);
        return $this->detalle($comisionId);
    }

    private function setEstado($id, int $estado): void
    {
        $this->comisionService()->setEstado((int) $id, $estado);
    }

    /**
     * Retorna opcions de FCT per al selector de detall:
     * una per centre (l'última per id), ordenades pel nom de centre.
     *
     * @return array<int, string>
     */
    private function buildFctOptions(): array
    {
        $all = Fct::esFct()
            ->where(function ($query): void {
                $query->misFcts()->orWhere('cotutor', authUser()->dni);
            })
            ->with('Colaboracion')
            ->orderBy('id')
            ->get();

        return $all
            ->filter(fn (Fct $fct) => (bool) $fct->Colaboracion)
            ->keyBy(fn (Fct $fct) => (string) $fct->Colaboracion->idCentro)
            ->mapWithKeys(fn (Fct $fct) => [$fct->id => $fct->Centro])
            ->sort()
            ->all();
    }

}
