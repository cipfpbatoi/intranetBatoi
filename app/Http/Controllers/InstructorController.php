<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Instructor\InstructorWorkflowService;
use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Centro;
use Intranet\Entities\Instructor;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Presentation\Crud\InstructorCrudSchema;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;


/**
 * Class InstructorController
 * @package Intranet\Http\Controllers
 */
class InstructorController extends IntranetController
{
    private ?InstructorWorkflowService $instructorWorkflowService = null;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Instructor';
    /**
     * @var array
     */
    protected $titulo = [];
    /**
     * @var array
     */
    protected $gridFields = InstructorCrudSchema::GRID_FIELDS;
    protected $formFields = InstructorCrudSchema::FORM_FIELDS;
    /**
     * @var bool
     */
    protected $modal = false;
    
    use Imprimir;

    private function instructors(): InstructorWorkflowService
    {
        if ($this->instructorWorkflowService === null) {
            $this->instructorWorkflowService = app(InstructorWorkflowService::class);
        }

        return $this->instructorWorkflowService;
    }

    /**
     *
     */
    public function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('instructor.edit'));
        $this->panel->setBoton('grid', new BotonImg('instructor.show'));
        $this->panel->setBoton('grid', new BotonImg('instructor.pdf'));
        $this->panel->setBoton('grid', new BotonImg('instructor.delete', [
            'data-confirm' => 'Segur que vols eliminar este instructor?',
        ]));
    }

    /**
     * @return mixed
     */
    public function search()
    {
        return $this->instructors()->searchForTutorFcts();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id)
    {
        $empresa = $this->instructors()->empresaIdFromInstructor($id);
        return redirect("empresa/$empresa/detalle");
    }



    /**
     * @param $centro
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function crea($centro)
    {
        return parent::create();
    }

    /**
     * @param $id
     * @param $empresa
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edita($id, $empresa)
    {
        return parent::edit($id);
    }

    /**
     * @param Request $request
     * @param $id
     * @param $centro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function guarda(Request $request, $id, $centro)
    {
        try {
            parent::update($request, $id);
        } catch (\Illuminate\Database\QueryException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                Alert::danger("Ja existeix un instructor amb aquest DNI.");
                return back()->withInput();
            }
            throw $e;
        }
        return $this->showEmpresa(Centro::find($centro)->idEmpresa);
    }

    private function showEmpresa($id)
    {
        $colaboracion = Session::get('colaboracion')??null;
        if ($colaboracion) {
            Session::put('pestana', 4);
            return redirect()->action('ColaboracionController@show', ['colaboracion'=> $colaboracion]);
        } else {
            Session::put('pestana', 2);
            return redirect()->action('EmpresaController@show', ['empresa' => $id]);
        }
    }

    /**
     * @param Request $request
     * @param $centro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function almacena(Request $request, $centro)
    {
        $empresaId = $this->instructors()->upsertAndAttachToCentro(
            $request,
            $centro,
            fn ($req) => parent::store($req)
        );

        return $this->showEmpresa($empresaId);
    }

    /**
     * @param $id
     * @param $centro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, $centro)
    {
        $empresaId = $this->instructors()->detachFromCentroAndDeleteIfOrphan(
            $id,
            $centro,
            fn ($instructorId) => parent::destroy($instructorId)
        );

        return $this->showEmpresa($empresaId);
    }

    /**
     * @param $id
     * @param $idCentro
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function copy($id, $idCentro)
    {
        $instructor = Instructor::findOrFail($id);
        $centro = Centro::findOrFail($idCentro);
        $posibles = hazArray($centro->Empresa->centros, 'id', ['nombre', 'direccion'], '-');

        return view('instructor.copy', compact('instructor', 'posibles', 'centro'));
    }

    /**
     * @param Request $request
     * @param $id
     * @param $idCentro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toCopy(Request $request, $id, $idCentro)
    {
        $empresaId = $this->instructors()->copyInstructorToCentro(
            $id,
            $idCentro,
            $request->centro,
            (string) $request->accion
        );

        return $this->showEmpresa($empresaId);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
   public function pdf($id)
    {
        $instructor = Instructor::findOrFail($id);

        if ($instructor->surnames == '') {
            Alert::danger("Completa les dades de l'instructor");
            return redirect("/instructor");
        }

        // 🔒 Assegura Collection (mai null) i evita N+1 per a Colaboracion
        $fcts = $instructor->Fcts()->with('Colaboracion')->get();

        if ($fcts->isEmpty()) {
            Alert::danger("Aquest instructor no té cap FCT associada.");
            return redirect("/instructor");
        }

        // 🔒 Calcula la data posterior de forma segura
        $fecha = $this->instructors()->ultimaFecha($fcts) ?? new Date(); // fallback si totes tenen 'hasta' buit

        $secretario = cargo('secretario');
        $director   = cargo('director');

        $dades = [
            'date'         => FechaString($fecha, 'ca'),
            'fecha'        => FechaString($fecha, 'es'),
            'consideracion'=> ($secretario && $secretario->sexo === 'H') ? 'En' : 'Na',
            'secretario'   => $secretario?->FullName ?? '',
            'centro'       => config('contacto.nombre'),
            'poblacion'    => config('contacto.poblacion'),
            'provincia'    => config('contacto.provincia'),
            'director'     => $director?->FullName ?? '',
            'instructor'   => $instructor,
        ];

        if ($fcts->count() == 1) {
            $pdf = $this->hazPdf('pdf.fct.instructor', $fcts->first(), $dades);
        } else {
            // 🔒 Conjunt de centres únics
            $centros = $fcts->pluck('Colaboracion.idCentro')->filter()->unique()->values()->all();
            $pdf = $this->hazPdf('pdf.fct.instructors', $centros, $dades);
        }

        return $pdf->stream();
    }

}
