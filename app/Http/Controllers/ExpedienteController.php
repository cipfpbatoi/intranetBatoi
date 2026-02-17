<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Expediente;
use Intranet\Entities\TipoExpediente;
use Intranet\Http\Requests\ExpedienteRequest;
use Intranet\Presentation\Crud\ExpedienteCrudSchema;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Http\Traits\Core\DropZone;
use Intranet\Services\General\GestorService;
use Intranet\Services\General\StateService;
use Intranet\Services\School\ExpedienteWorkflowService;
use Styde\Html\Facades\Alert;


/**
 * Class ExpedienteController
 * @package Intranet\Http\Controllers
 */
class ExpedienteController extends ModalController
{

    use Imprimir,DropZone,
        Autorizacion;

    /**
     * @var array
     */
    protected $gridFields = ExpedienteCrudSchema::GRID_FIELDS;
    /**
     * @var string
     */
    protected $model = 'Expediente';
    protected $profile = false;
    protected $formFields = ExpedienteCrudSchema::FORM_FIELDS;



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
        app(ExpedienteWorkflowService::class)->authorizePending();
        return back();
    }

    //inicializat a init (normalment 1)

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function init($id)
    {
        if (!app(ExpedienteWorkflowService::class)->init($id)) {
            return back()->with('error', 'Expedient no trobat.');
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
        if (!app(ExpedienteWorkflowService::class)->passToOrientation($id)) {
            return back()->with('error', 'Expedient no trobat.');
        }

        return back();
    }

    protected function assigna($id,Request $request){
        if (!app(ExpedienteWorkflowService::class)->assignCompanion($id, $request->idAcompanyant)) {
            return back()->with('error', 'Expedient no trobat.');
        }

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
                    $pdf = self::hazPdf("pdf.expediente.$tipo->vista", $todos);

                    // Nom del fitxer
                    //$nom = "Expediente_" . $tipo->titulo . "_" . now()->format('Ymd_His') . ".pdf";
                    $nom = "Expediente_" . Str::slug($tipo->titulo, '_') . "_" . now()->format('Ymd_His') . ".pdf";

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
