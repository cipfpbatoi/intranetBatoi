<?php

namespace Intranet\Http\Controllers;


use Illuminate\Http\Request;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Http\Requests\ExpedienteRequest;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Imprimir;
use Intranet\Services\GestorService;
use Intranet\Services\StateService;
use Styde\Html\Facades\Alert;


/**
 * Class ExpedienteController
 * @package Intranet\Http\Controllers
 */
class ExpedienteController extends ModalController
{

    use Imprimir,traitDropZone,
        Autorizacion;

    /**
     * @var array
     */
    protected $gridFields = ['id', 'nomAlum', 'fecha', 'Xtipo', 'Xmodulo', 'situacion'];
    /**
     * @var string
     */
    protected $model = 'Expediente';
    protected $profile = false;



    public function store(ExpedienteRequest $request)
    {
        $new = new Expediente();
        $new->fillAll($request);
        return $this->redirect();
    }


    public function update(ExpedienteRequest $request, $id)
    {
        Expediente::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }



    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('expediente.pdf', ['where' => ['estado', '==', '2']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.delete', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.edit', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.show', ['where' => ['estado', '>', '2']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.link', ['where' => ['annexo','!=',0]]));
        $this->panel->setBoton('grid', new BotonImg('expediente.init', ['where' => ['estado', '==', '0','esInforme','==','0']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.pdf', ['where' => ['esInforme', '==', 1]]));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function autorizar()
    {
        StateService::makeAll(Expediente::where('estado', '1')->get(), 2);
        return back();
    }

    //inicializat a init (normalment 1)

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function init($id)
    {
        $expediente = Expediente::find($id);
        $staSrv = new StateService($expediente);
            // orientacion
        if ($expediente->tipoExpediente->orientacion >= 1){
            $mensaje = $expediente->explicacion.' .Grup '.$expediente->Alumno->Grupo->first()->nombre;
            $staSrv->putEstado(4, $mensaje);
        } else {
            $staSrv->putEstado(1);
        }
        return back();
    }

    protected function createWithDefaultValues($default = [])
    {
        return new Expediente(['idProfesor'=>AuthUser()->dni]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function pasaOrientacion($id)
    {
        $expediente = Expediente::find($id);
        $staSrv = new StateService($expediente);
        $staSrv->putEstado(5);
        $expediente->fechasolucion = Hoy();
        $expediente->save();

        return back();
    }

    protected function assigna($id,Request $request){
        $expediente = Expediente::find($id);
        $staSrv = new StateService($expediente);
        $expediente->idAcompanyant = $request->idAcompanyant;
        $expediente->fechasolucion = Hoy();
        $expediente->save();
        $staSrv->putEstado(5,"Assignat professor Acompanyant ".$expediente->Acompanyant->fullName);

        return back();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function pdf($id)
    {
        $expediente = Expediente::find($id);
        $dades[] = $expediente;
        $vista = $expediente->TipoExpediente->vista;

        return self::hazPdf("pdf.expediente.$vista",$dades)->stream();
    }



    /**
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function imprimir()
    {
        $expedientes = Expediente::listos()->get();

        if ($expedientes->count()) {
            foreach (TipoExpediente::all() as $tipo) {
                $todos = $expedientes->where('tipo', $tipo->id);

                if ($todos->count()) {
                    // Generem el PDF
                    $pdf = $this->hazPdf("pdf.expediente.$tipo->vista", $todos);

                    // Nom del fitxer
                    $nom = "Expediente_" . $tipo->titulo . "_" . now()->format('Ymd_His') . ".pdf";
                    $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
                    $tags = "listado llistat expediente expedient $tipo->titulo";

                    // Guardem el document
                    $gestor = new GestorService();
                    $doc = $gestor->save(['fichero' => $nomComplet, 'tags' => $tags]);

                    // Modifiquem l'estat de tots els elements
                    StateService::makeAll($todos, '_print');

                    // Enllacem els elements amb el document
                    StateService::makeLink($todos, $doc);

                    // Guardem i descarreguem el PDF
                    $pdf->save(storage_path('/app/' . $nomComplet));
                    return response()->download(storage_path('/app/' . $nomComplet), $nom);
                }
            }
        }

        Alert::info(trans('messages.generic.empty'));
        return back();
    }


    /*
    * show($id) return vista
    * busca en model de dades i el mostra amb vista show
    */

    public function show($id)
    {
        $elemento = Expediente::findOrFail($id);
        $modelo = $this->model;
        return view('expediente.show', compact('elemento', 'modelo'));
    }


    
}
