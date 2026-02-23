<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Fct\FctCertificateService;
use Intranet\Application\Fct\FctService;
use Intranet\Http\Controllers\Core\IntranetController;
use Intranet\Presentation\Crud\FctCrudSchema;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Colaborador;
use Intranet\Entities\Fct;
use Intranet\Http\PrintResources\AVIIAResource;
use Intranet\Http\PrintResources\AVIIBResource;
use Intranet\Http\PrintResources\CertificatInstructorResource;
use Intranet\Http\Requests\ColaboradorRequest;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Services\Document\FDFPrepareService;
use Intranet\Services\UI\FormBuilder;
use Styde\Html\Facades\Alert;


/**
 * Class FctController
 * @package Intranet\Http\Controllers
 */
class FctController extends IntranetController
{
    private ?FctCertificateService $fctCertificateService = null;
    private ?FctService $fctService = null;


    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Fct';
    /**
     * @var array
     */
    protected $gridFields = FctCrudSchema::GRID_FIELDS;
    protected $formFields = FctCrudSchema::FORM_FIELDS;
    /**
     * @var
     */
    protected $grupo;
    /**
     * @var array
     */

    protected $parametresVista = ['modal' => ['contactoAl']];




    /**
     * @var bool
     */
    protected $modal = false;

    use Imprimir;

    public function __construct(?FctService $fctService = null)
    {
        parent::__construct();
        $this->fctService = $fctService;
    }

    private function fcts(): FctService
    {
        if ($this->fctService === null) {
            $this->fctService = app(FctService::class);
        }

        return $this->fctService;
    }

    private function certificates(): FctCertificateService
    {
        if ($this->fctCertificateService === null) {
            $this->fctCertificateService = app(FctCertificateService::class);
        }

        return $this->fctCertificateService;
    }


    public function edit($id=null)
    {
        $formulario = new FormBuilder($this->fcts()->findOrFail($id), ['idInstructor' => ['type'=>'select']]);
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('formulario', 'modelo'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->fcts()->setInstructor($id, (string) $request->idInstructor);
        return $this->redirect();
    }


    public function certificat($id)
    {
        $fct = $this->fcts()->findOrFail($id);
        /*if ($fct->asociacion == 4){
            $nameFile = storage_path("tmp/Dual_AVII_{$fct->id}.zip");
            if (file_exists($nameFile)) {
                unlink($nameFile);
            }
            $zip = new \ZipArchive();
            $zip->open($nameFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $zip->addFile(FDFPrepareService::exec(new AVIIAResource(Fct::find($id))), 'AVIIa_Certificat_empresa.pdf');
            $zip->addFile(FDFPrepareService::exec(new AVIIBResource(Fct::find($id))), 'AVIIb_Certificat_instructor.pdf');
            $zip->close();
            return response()->download($nameFile);
        }*/

        return response()->file(FDFPrepareService::exec(
            new CertificatInstructorResource($this->fcts()->findOrFail($id))));

    }

    public static function certificatColaboradores($id)
    {
        $fct = app(FctService::class)->findOrFail($id);
        return app(FctCertificateService::class)->streamColaboradorCertificate($fct);
    }





    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            $fct = $this->fcts()->findBySignature(
                (string) $request->idColaboracion,
                (string) $request->asociacion,
                (string) $request->idInstructor
            );

            if (!$fct) {
                $model = new Fct();
                $this->validateAll($request, $model);
                $fct = $this->fcts()->createFromRequest($request);
            }

            $this->fcts()->attachAlumnoFromStoreRequest($fct, $request);
        } catch (\Exception $e) {
            Alert::warning("L'alumne {$request['idAlumno']} ja té una Fct oberta amb eixa empresa ");
        }
        
        return $this->redirect();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $activa = Session::get('pestana') ? Session::get('pestana') : 1;
        Session::put('pestana', 1);
        $fct = $this->fcts()->findOrFail($id);
        $instructores = $fct->Colaboradores->pluck('dni');

        return view('fct.show', compact('fct', 'activa', 'instructores'));
    }




    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if (Session::get('pestana')) {
            $empresa = $this->fcts()->empresaIdByFct($id);
            $this->fcts()->deleteFct($id);
            Session::put('pestana', 3);
            return redirect()->action('EmpresaController@show', ['empresa' => $empresa]);
        }

        return parent::destroy($id);
    }

    /**
     * @param $idFct
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nouAlumno($idFct, Request $request)
    {
        $this->fcts()->attachAlumnoSimple($idFct, $request);
        
        return back();
    }

    /**
     * @param $idFct
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nouFctAlumno(Request $request)
    {
        if (isset($request->idInstructor)) {
            $this->store($request);
        } else {
            Alert::danger('No hi ha instructor.No puc generar la FCT');
        }

        return back();
    }

    /**
     * @param $idFct
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nouInstructor($idFct, ColaboradorRequest $request)
    {
        $colaborador = new Colaborador([
            'idInstructor'=>$request->idInstructor,
            'name'=>$request->name,
            'horas'=> $request->horas
        ]);
       $this->fcts()->addColaborador($idFct, $colaborador);
       Session::put('pestana', 5);
       return back();
    }

    /**
     * @param $idFct
     * @param $idInstructor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteInstructor($idFct, $idInstructor)
    {
       $this->fcts()->deleteColaborador($idFct, $idInstructor);
       Session::put('pestana', 5);
       return back();
    }

    /**
     * @param $idFct
     * @param $idAlumno
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alumnoDelete($idFct, $idAlumno)
    {
       $this->fcts()->detachAlumno($idFct, $idAlumno);
       return back();
    }

    /**
     * @param $idFct
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function modificaHoras($idFct, Request $request)
    {
        $this->fcts()->updateColaboradorHoras($idFct, $request->except('_token'));
        return back();
    }

    public function cotutor(Request $request, $idFct)
    {
        $this->fcts()->setCotutor($idFct, $request->cotutor ?? null);

        return back();
    }



}
