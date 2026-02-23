<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\AlumnoFct\AlumnoFctService;
use Intranet\Http\Controllers\Core\ModalController;
use Intranet\Presentation\Crud\AlumnoFctCrudSchema;


use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonImg;
use Intranet\Services\Document\PdfService;
use Intranet\Entities\Adjunto;
use Intranet\Entities\Documento;
use Intranet\Entities\Fct;
use Intranet\Entities\FctConvalidacion;
use Intranet\Http\PrintResources\A1ENResource;
use Intranet\Http\PrintResources\A2ENResource;
use Intranet\Http\PrintResources\A3ENResource;
use Intranet\Http\PrintResources\A5Resource;
use Intranet\Http\PrintResources\AutorizacionDireccionResource;
use Intranet\Http\PrintResources\AVIIIResource;
use Intranet\Http\PrintResources\AVIResource;
use Intranet\Http\PrintResources\ConformidadAlumnadoResource;
use Intranet\Http\PrintResources\ConformidadTutoriaResource;
use Intranet\Http\PrintResources\ExempcioResource;
use Intranet\Http\PrintResources\NotificacioInspeccioResource;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Http\Traits\Core\DropZone;
use Intranet\Mail\DocumentRequest;
use Intranet\Services\Document\FDFPrepareService;
use Intranet\Services\UI\FormBuilder;
use Styde\Html\Facades\Alert;


class FctAlumnoController extends ModalController
{
    use Imprimir,DropZone;

    private ?AlumnoFctService $alumnoFctService = null;

    const ROLES_ROL_TUTOR = 'roles.rol.tutor';
    protected $perfil = 'profesor';
    protected $model = 'AlumnoFct';
    protected $redirect = 'FctAlumnoController@index';
    protected $gridFields = AlumnoFctCrudSchema::GRID_FIELDS;
    protected $formFields = AlumnoFctCrudSchema::FORM_FIELDS;
    protected $profile = false;
    protected $titulo = [];
    protected $parametresVista = ['modal' => ['extended', 'saoPassword', 'loading','signatura']];
    public function __construct(?AlumnoFctService $alumnoFctService = null)
    {
        parent::__construct();
        $this->alumnoFctService = $alumnoFctService;
    }

    private function alumnoFcts(): AlumnoFctService
    {
        if ($this->alumnoFctService === null) {
            $this->alumnoFctService = app(AlumnoFctService::class);
        }

        return $this->alumnoFctService;
    }


    public function search()
    {
        return $this->alumnoFcts()->totesFcts();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.show',
                ['img' => 'fa-plus', 'where' => ['asociacion', '<', '2'],'text'=>'Vore més']
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.show',
                ['img' => 'fa-plus', 'where' => ['asociacion', '>', '2'],'text'=>'Vore més']
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.edit',
                ['where' => ['asociacion', '<', '2'],'text'=>'Canviar dates']
            )
        );

        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.edit',
                ['where' => ['asociacion', '>', '2'],'text'=>'Canviar dates']
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.pdf',
                ['where' => ['asociacion', '==', '2']]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.email',
                [
                    'img' => 'fa-send-o',
                    'where' =>
                        [
                            'asociacion', '<', 2,
                            'actualizacion', '<', hace(7),
                            'desde', 'anterior', hace(7),
                            'hasta','posterior',hoy(),
                            'realizadas','<',380
                        ]
                ]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.email',
                [
                    'img' => 'fa-send-o',
                    'where' =>
                        [
                            'asociacion', '>', 2,
                            'actualizacion', '<', hace(7),
                            'desde', 'anterior', hace(7),
                            'hasta','posterior',hoy(),
                            'realizadas','<',380
                        ]
                ]
            )
        );
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.importa',
                [
                    'img' => 'fa-download',
                    'where' =>
                        [
                            'asociacion', '==', 3,
                        ]
                ]
            )
        );

        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'alumnofct.delete',
                ['where' => [
                    'hasta','posterior',hace(1),
                    'realizadas','==',0
                ]]
            )
        );


        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "alumnofct.convalidacion",
                ['class' => 'btn-info convalidacion', 'roles' => config(self::ROLES_ROL_TUTOR)]
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
                'signatura',
                [
                    'class' => 'btn-danger',
                    'text' => 'Signatures Digitals',
                    'roles' => config(self::ROLES_ROL_TUTOR),
                ]
            )
        );
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "fct.print",
                ['class' => 'btn-warning selecciona', 'roles' => config(self::ROLES_ROL_TUTOR)]
            )
        );

        $this->setQualityB();
        $this->panel->setBoton(
            'index',
            new BotonBasico(
                "fct",
                [
                    'class' => 'btn-dark',
                    'roles' => config(self::ROLES_ROL_TUTOR),
                    'text' => 'Contactes'
                ]
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
            $fcts = Fct::misFcts()->count();
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

    public function days($id)
    {
        $alumnoFct = $this->alumnoFcts()->find((int) $id);

        return view('fct.days',compact('alumnoFct'));
    }

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
        return view('intranet.create', compact('formulario', 'modelo'));
    }

    public function unlink($id)
    {
        $elemento = $this->alumnoFcts()->find((int) $id);
        abort_unless($elemento !== null, 404);
        $elemento->idSao = null;
        $elemento->save();
        return redirect()->back();
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
                $this->validateByModelRules($request, $elemento);
                $elemento->fillAll($request);
            }

            $elemento->Alumnos()->attach(
                $idAlumno,
                [
                    'desde' => FechaInglesa(Hoy()),
                    'horas' => $request->horas,
                    'calificacion' => 2,
                    'correoAlumno' => 1,
                    'idProfesor' => authUser()->dni
                ]
            );

            return $elemento->id;
        });

        return $this->redirect();
    }

    public function update(Request $request, $id)
    {
        $elemento = $this->alumnoFcts()->findOrFail((int) $id);
        $this->validateByModelRules($request, $elemento);
        $this->persist($request, $id);
        return $this->redirect();
    }

    public function show($id)
    {
        $fct = $this->alumnoFcts()->findOrFail((int) $id);
        return redirect("/fct/$fct->idFct/show");
    }

    private function validateByModelRules(Request $request, $elemento): void
    {
        $rules = method_exists($elemento, 'getRules') ? $elemento->getRules() : [];
        if (!empty($rules)) {
            $this->validate($request, $rules);
        }
    }

    public function pdf($id)
    {
        $fct = $this->alumnoFcts()->findOrFail((int) $id);
        if ($fct->asociacion === 1 || $fct->asociacion > 3) {
            return self::preparePdf($id)->stream();
        }
        if ($fct->asociacion == 2) {
            return response()->file(FDFPrepareService::exec(new ExempcioResource($fct)));
        }
        if ($fct->asociacion == 3){
            return response()->file(FDFPrepareService::exec(new AVIIIResource($fct)));
        }

    }

    public function Signatura($id, $num)
    {
        $fct = $this->alumnoFcts()->findOrFail((int) $id);
        return response()->file($fct->routeFile($num));
    }

    public function Valoratiu($id)
    {
        return response()->file(FDFPrepareService::exec(new A5Resource($this->alumnoFcts()->findOrFail((int) $id))));
    }


    public function AVI($id)
    {
        return response()->file(FDFPrepareService::exec(new AVIResource($this->alumnoFcts()->findOrFail((int) $id))));
    }



    public function AEng($id)
    {

        $fct = $this->alumnoFcts()->findOrFail((int) $id);
        $nameFile = storage_path("tmp/AN_EN{$fct->Alumno->shorName}.zip");
        if (file_exists($nameFile)) {
            unlink($nameFile);
        }
        $zip = new \ZipArchive();
        $zip->open($nameFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFile(FDFPrepareService::exec(new A1ENResource($fct)), 'AIEN.pdf');
        $zip->addFile(FDFPrepareService::exec(new A2ENResource($fct)), 'AIIEN.pdf');
        $zip->addFile(FDFPrepareService::exec(new A3ENResource($fct)), 'AIIIEN.pdf');
        $zip->close();
        return response()->download($nameFile);
    }


    public function auth($id)
    {
        $fct = $this->alumnoFcts()->findOrFail((int) $id);
        $folder = storage_path("tmp/auth$id/");
        $zipFile = storage_path("tmp/auth_".$fct->Alumno->dualName.".zip");
        if (!file_exists($folder)) {
            if (!mkdir($folder, 0777, true) && !is_dir($folder)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $folder));
            }
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

    public function AutDual($id)
    {
        return response()->file(FDFPrepareService::exec(new NotificacioInspeccioResource($this->alumnoFcts()->findOrFail((int) $id))));
    }

    public static function preparePdf($id)
    {
        $fct = app(AlumnoFctService::class)->findOrFail((int) $id);
        $secretario = cargo('secretario');
        $director = cargo('director');
        $dades = [
            'date' => FechaString($fct->hasta),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName
        ];
        return app(PdfService::class)->hazPdf('pdf.fct.certificatsFCT', [$fct], $dades);
    }

    /**
     * public function email($id)
     * {
     * // CARREGANT DADES
     * $elemento = AlumnoFct::findOrFail($id);
     *
     *
     * // MANE ELS TREBALLS
     * if ($elemento->Alumno->email != '' && config('variables.enquestesAutomatiques')){
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
        $fct = $this->alumnoFcts()->findOrFail((int) $id);
        $fct->pg0301 = $fct->pg0301 ? 0 : 1;
        $fct->save();
        return redirect()->action('PanelPG0301Controller@indice', ['id' => $fct->Grup]);
    }

    public function email($id)
    {
        $fct = $this->alumnoFcts()->findOrFail((int) $id);
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
        Alert::info('Correu enviat a ' . $alumno->fullName);
        return back();
    }

    public function importa($id){
        $fct = $this->alumnoFcts()->findOrFail((int) $id);
        $dni = $fct->Alumno->dni;
        $annexos = Adjunto::where('route', 'like', "dual/$dni")->get();
        if ($annexos->isEmpty()){
            Alert::info('No hi ha fitxers a importar');
            return back();
        }
        foreach ($annexos as $annex){
            $annex->route = "alumnofctaval/$id";
            $annex->save();
        }
        Alert::info('Fitxers importats');
        return back();
    }

}
