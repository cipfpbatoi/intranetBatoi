<?php

namespace Intranet\Http\Controllers;


use Illuminate\Support\Facades\Mail;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Componentes\Pdf;
use Intranet\Entities\Adjunto;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Entities\Documento;
use Intranet\Entities\Fct;
use Intranet\Entities\Profesor;
use Intranet\Entities\FctConvalidacion;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Intranet\Http\PrintResources\A1ENResource;
use Intranet\Http\PrintResources\A2ENResource;
use Intranet\Http\PrintResources\A3ENResource;
use Intranet\Http\PrintResources\A5Resource;
use Intranet\Http\PrintResources\ConformidadAlumnadoResource;
use Intranet\Http\PrintResources\ConformidadTutoriaResource;
use Intranet\Mail\DocumentRequest;
use Intranet\Services\FDFPrepareService;
use Intranet\Services\FormBuilder;
use Intranet\Http\PrintResources\AutorizacionDireccionResource;
use Intranet\Http\PrintResources\ExempcioResource;
use mikehaertl\pdftk\Pdf as pdftk;


class FctAlumnoController extends IntranetController
{
    use traitImprimir,traitDropZone;

    const ROLES_ROL_TUTOR = 'roles.rol.tutor';
    protected $perfil = 'profesor';
    protected $model = 'AlumnoFct';
    protected $gridFields = ['Nombre', 'Centro', 'Instructor', 'desde',  'horasRealizadas','hasta', 'finPracticas'];
    protected $profile = false;
    protected $titulo = [];
    protected $parametresVista = ['modal' => ['extended', 'saoPassword', 'loading']];
    protected $modal = true;


    public function search()
    {
        return AlumnoFctAval::misFcts()->esAval()->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.delete',
                ['where' => [
                    'hasta','posterior',hace(1),
                ]]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.edit',
                ['where' => [
                    'asociacion', '==', '1',
                    'hasta','posterior',hoy()
                ]]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.pdf',
                ['where' => ['asociacion', '<', '3']]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.pdf',
                ['where' => ['asociacion', '==', '3']]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.auth',
                ['img' => 'fa-file-zip-o', 'where' => ['autorizacion', '==', '1']]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'fct.link',
                ['where' => ['asociacion', '<', 3]]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.A5',
                ['img' => 'fa-hand-o-up', 'where' => ['asociacion', '==', '1']]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.A1',
                ['img' => 'fa-file-zip-o', 'where' => ['asociacion', '==', '2']]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.email',
                [
                    'img' => 'fa-envelope',
                    'where' =>
                        [
                            'asociacion', '==', 1,
                            'actualizacion', '<', hace(7),
                            'desde', 'anterior', hace(7),
                            'hasta','posterior',hoy()
                        ]
                ]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "sao.post",
                ['class' => 'btn-success download', 'roles' => config(self::ROLES_ROL_TUTOR)]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "fct.print",
                ['class' => 'btn-warning selecciona', 'roles' => config(self::ROLES_ROL_TUTOR)]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "fct.create",
                ['class' => 'btn-info', 'roles' => config(self::ROLES_ROL_TUTOR)]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "alumnofct.convalidacion",
                ['class' => 'btn-info convalidacion', 'roles' => config(self::ROLES_ROL_TUTOR)]
            )
        );
        $this->setQualityB();
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "fct",
                ['class' => 'btn-link', 'roles' => config(self::ROLES_ROL_TUTOR)]
            )
        );
        Session::put('redirect', 'FctAlumnoController@index');

    }

    /**
     *
     */
    private function setQualityB(): void
    {
        $find = Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento', 'FCT')
            ->where('curso', Curso())->first();
        if (!$find) {
            $documents = Adjunto::where('route', "profesor/".AuthUser()->dni)->count();
            $fcts = Fct::misFcts()->where('correoInstructor', 0)->count();
            if ($documents || $fcts) {
                $this->panel->setBoton(
                    'index',
                    new BotonBasico(
                        "fct.dropzone.".AuthUser()->dni,
                        ['class' => 'btn-warning', 'roles' => config(self::ROLES_ROL_TUTOR)]
                    )
                );
            } else {
                $this->panel->setBoton(
                    'index',
                    new BotonBasico(
                        "fct.upload",
                        ['class' => 'btn-warning', 'roles' => config(self::ROLES_ROL_TUTOR)]
                    )
                );
            }
        } else {
            $this->panel->setBoton(
                'index',
                new BotonBasico(
                    "documento.$find->id.edit",
                    ['class' => 'btn-warning', 'roles' => config(self::ROLES_ROL_TUTOR)]
                )
            );
        }
    }

    //

    public function nuevaConvalidacion()
    {
        $elemento = new FctConvalidacion();
        $formulario = new FormBuilder(
            $elemento,
            [
                'idAlumno' => ['type' => 'select'],
                'asociacion' => ['type' => 'hidden'],
                'horas' => ['type' => 'text'],
            ]
        );
        $modelo = $this->model;
        return view($this->chooseView('create'), compact('formulario', 'modelo'));
    }

    public function storeConvalidacion(Request $request)
    {
        DB::transaction(function () use ($request) {
            $idAlumno = $request['idAlumno'];
            $elementos = FctConvalidacion::where('idColaboracion', $request->idColaboracion)
                ->where('asociacion', $request->asociacion)
                ->get();
            $elemento = $elementos->first() ?? null;

            if (!$elemento) {
                $elemento = new FctConvalidacion();
                $this->validateAll($request, $elemento);
                $elemento->fillAll($request);
            }

            $elemento->Alumnos()->attach(
                $idAlumno,
                [
                    'desde' => FechaInglesa(Hoy()),
                    'horas' => $request->horas,
                    'calificacion' => 2,
                    'correoAlumno' => 1
                ]
            );

            return $elemento->id;
        });

        return $this->redirect();
    }

    public function show($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        return redirect("/fct/$fct->idFct/show");
    }

    public function pdf($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        if ($fct->asociacion == 1) {
            return self::preparePdf($id)->stream();
        }
        if ($fct->asociacion == 3) {
            return response()->file(FDFPrepareService::exec(new ExempcioResource(AlumnoFct::find($id))));
        }
    }

    public function A5($id)
    {
        return response()->file(FDFPrepareService::exec(new A5Resource(AlumnoFct::find($id))));
    }

    public function A1($id)
    {

        $fct = AlumnoFct::find($id);
        $nameFile = storage_path("tmp/AN_EN{$fct->Alumno->shorName}.zip");
        if (file_exists($nameFile)) {
            unlink($nameFile);
        }
        $zip = new \ZipArchive();
        $zip->open($nameFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFile(FDFPrepareService::exec(new A1ENResource(AlumnoFct::find($id))), 'AIEN.pdf');
        $zip->addFile(FDFPrepareService::exec(new A2ENResource(AlumnoFct::find($id))), 'AIIEN.pdf');
        $zip->addFile(FDFPrepareService::exec(new A3ENResource(AlumnoFct::find($id))), 'AIIIEN.pdf');
        $zip->close();
        return response()->download($nameFile);
    }

    public function auth($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        $folder = storage_path("tmp/auth$id/");
        $zipFile = storage_path("tmp/auth_".$fct->Alumno->dualName.".zip");
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        // Genere els tres documents
        $zip->addFile(
            FDFPrepareService::exec(new AutorizacionDireccionResource($fct)),
            '9_Autoritzacio_direccio_situacions_excepcionals.pdf'
        );
        $zip->addFile(
            FDFPrepareService::exec(new ConformidadTutoriaResource($fct)),
            '10_Conformitat_tutoria.pdf'
        );
        $zip->addFile(
            FDFPrepareService::exec(new ConformidadAlumnadoResource($fct)),
            '11_Conformitat_alumnat.pdf'
        );
        $zip->close();
        deleteDir($folder);

        return response()->download($zipFile);
    }


    public static function preparePdf($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        $secretario = Profesor::find(config(fileContactos().'.secretario'));
        $director = Profesor::find(config(fileContactos().'.director'));
        $dades = [
            'date' => FechaString($fct->hasta),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName
        ];
        return Pdf::hazPdf('pdf.fct.certificatsFCT', [$fct], $dades);
    }

    /**
     * public function email($id)
     * {
     * // CARREGANT DADES
     * $elemento = AlumnoFct::findOrFail($id);
     *
     *
     * // MANE ELS TREBALLS
     * if ($elemento->Alumno->email != '' && config('curso.enquestesAutomatiques')){
     * $remitente = ['email' => AuthUser()->email, 'nombre' => AuthUser()->FullName, 'id' => AuthUser()->dni];
     * dispatch(new SendEmail($elemento->Alumno->email, $remitente, 'email.fct.alumno', $elemento));
     * Alert::info('Correu enviat');
     * return back();
     * }
     *
     * Alert::info("L'alumne no té correu. Revisa-ho");
     * return back();
     * }
     */

    public function pg0301($id)
    {
        $fct = AlumnoFct::find($id);
        $fct->pg0301 = $fct->pg0301 ? 0 : 1;
        $fct->save();
        return redirect()->action('PanelPG0301Controller@indice', ['id' => $fct->Grup]);
    }

    public function email($id)
    {
        $fct = AlumnoFct::findOrFail($id);
        $alumno = $fct->Alumno;
        Mail::to($alumno->email)
            ->bcc(authUser()->email)
            ->send(new DocumentRequest(
                [
                    'from' => authUser()->email,
                    'fromPerson' => authUser()->fullName,
                    'subject' => 'Diari de FCT'
                ],
                'email.fct.advise',
                $fct
            ));
        return back();
    }
}
