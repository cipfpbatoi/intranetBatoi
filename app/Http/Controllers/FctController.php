<?php

namespace Intranet\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Intranet\Componentes\Pdf;
use Intranet\Entities\Colaborador;
use Intranet\Entities\Fct;
use Intranet\Entities\Profesor;
use Intranet\Http\PrintResources\AVIIAResource;
use Intranet\Http\PrintResources\AVIIBResource;
use Intranet\Http\PrintResources\CertificatInstructorResource;
use Intranet\Http\Requests\ColaboradorRequest;
use Intranet\Http\Traits\Imprimir;
use Intranet\Services\FDFPrepareService;
use Intranet\Services\FormBuilder;
use Styde\Html\Facades\Alert;


/**
 * Class FctController
 * @package Intranet\Http\Controllers
 */
class FctController extends IntranetController
{


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
    protected $gridFields = ['Centro','Contacto','Lalumnes','Nalumnes','sendCorreo'];
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



    public function edit($id=null)
    {
        $formulario = new FormBuilder(Fct::findOrFail($id), ['idInstructor' => ['type'=>'select']]);
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
        $fct = Fct::findOrFail($id);
        $fct->idInstructor = $request->idInstructor;
        $fct->save();
        return $this->redirect();
    }


    public function certificat($id)
    {
        $fct = Fct::findOrFail($id);
        if ($fct->asociacion == 4){
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
        }

        return response()->file(FDFPrepareService::exec(
            new CertificatInstructorResource(Fct::findOrFail($id))));

    }

    public static function certificatColaboradores($id)
    {
        $fct = Fct::findOrFail($id);
        $secretario = Profesor::find(config('avisos.secretario'));
        $director = Profesor::find(config('avisos.director'));
        $dades = ['date' => FechaString(hoy(), 'ca'),
            'fecha' => FechaString(hoy(), 'es'),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName,
        ];
        return Pdf::hazPdf('pdf.fct.certificatColaborador', $fct, $dades)->stream();
    }





    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        DB::transaction(function () use ($request) {
            $idAlumno = $request['idAlumno'];
            $fct = Fct::where('idColaboracion', $request->idColaboracion)
                    ->where('asociacion', $request->asociacion)
                    ->where('idInstructor', $request->idInstructor)
                    ->first();

            if (!$fct) {
                $fct = new Fct();
                $this->validateAll($request, $fct);
                $fct->fillAll($request);
            }
            try {
                $fct->Alumnos()->attach(
                    $idAlumno,
                    [
                        'desde'=> FechaInglesa($request->desde),
                        'hasta'=>FechaInglesa($request->hasta),
                        'horas'=>$request->horas,
                        'autorizacion'=>$request->autorizacion??0
                    ]
                );
            } catch (\Exception $e) {
               Alert::warning("L'alumne $idAlumno ja té una Fct oberta amb eixa empresa ");
            }

            return $fct;
        });
        
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
        $fct = Fct::findOrFail($id);
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
            $empresa = Fct::find($id)->Colaboracion->Centro->idEmpresa;
            parent::destroy($id);
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
        
        $fct = Fct::find($idFct);
        $fct->Alumnos()->attach(
            $request->idAlumno,
            [
                'calificacion'=>0,
                'calProyecto'=>0,
                'actas'=>0,
                'insercion'=>0,
                'desde'=> FechaInglesa($request->desde),
                'hasta'=> FechaInglesa($request->hasta),
                'horas'=>$request->horas
            ]
        );
        
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
       $fct = Fct::find($idFct);
       $fct->Colaboradores()->save($colaborador);
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
       Colaborador::where('idFct', $idFct)->where('idInstructor', $idInstructor)->delete();
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
       $fct = Fct::find($idFct);
       $fct->Alumnos()->detach($idAlumno);
       return back();
    }

    /**
     * @param $idFct
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function modificaHoras($idFct, Request $request)
    {
        $fct = Fct::find($idFct);
        foreach ($request->except('_token') as $dni => $horas) {
            $fct->Colaboradores()->where('idInstructor', $dni)->update(['horas'=>$horas]);
        }
        return back();
    }

    public function cotutor(Request $request, $idFct)
    {
        DB::transaction(function () use ($request, $idFct){
            // Desactiva les restriccions de clau forana
            Schema::disableForeignKeyConstraints();

            // Realitza les operacions de guardat aquí
            $fct = Fct::find($idFct);
            if ($fct) {
                $fct->cotutor = $request->cotutor??null;
                $fct->save();
            }

            // Reactiva les restriccions de clau forana
            Schema::enableForeignKeyConstraints();
        });

        return back();
    }



}
