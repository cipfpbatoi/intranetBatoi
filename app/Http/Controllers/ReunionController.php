<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\ModalController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Asistencia;
use Intranet\Entities\Documento;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Reunion;
use Intranet\Services\Document\TipoReunionService;
use Intranet\Exceptions\IntranetException;
use Intranet\Http\Requests\OrdenReunionStoreRequest;
use Intranet\Http\Requests\ReunionStoreRequest;
use Intranet\Http\Requests\ReunionUpdateRequest;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Jobs\SendEmail;
use Intranet\Presentation\Crud\ReunionCrudSchema;
use Intranet\Services\Calendar\CalendarService;
use Intranet\Services\UI\FormBuilder;
use Intranet\Services\General\GestorService;
use Intranet\Services\Calendar\MeetingOrderGenerateService;
use Intranet\Services\School\ReunionService;
use Jenssegers\Date\Date;
use Response;
use Styde\Html\Facades\Alert;
use function dispatch;

class ReunionController extends ModalController
{
    private ?GrupoService $grupoService = null;
    private ?ReunionService $reunionService = null;

    use Imprimir;

    const REUNION_UPDATE = 'reunion.update';
    protected $perfil = 'profesor';
    protected $model = 'Reunion';
    protected $gridFields = ReunionCrudSchema::GRID_FIELDS;
    protected $formFields = ReunionCrudSchema::FORM_FIELDS;
    protected $parametresVista = [  'modal' => ['password']];

    public function __construct(?GrupoService $grupoService = null, ?ReunionService $reunionService = null)
    {
        parent::__construct();
        $this->grupoService = $grupoService;
        $this->reunionService = $reunionService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function reunionService(): ReunionService
    {
        if ($this->reunionService === null) {
            $this->reunionService = app(ReunionService::class);
        }

        return $this->reunionService;
    }

    protected function search()
    {
        return Reunion::MisReuniones()->get();
    }

    protected function createWithDefaultValues( $default=[]){
        return new Reunion(['idProfesor'=>AuthUser()->dni,'curso'=>Curso()]);
    }

    public function store(ReunionStoreRequest $request)
    {
        $elemento = DB::transaction(function() use ($request) {
            $id = $this->persist($request);
            $elemento = Reunion::findOrFail($id);
            $service = new MeetingOrderGenerateService($elemento);
            $service->exec();
            return $elemento;
        });

        if ($elemento->fichero != '') {
            return back();
        }
        return redirect()->route(self::REUNION_UPDATE, ['reunion' => $elemento->id]);
    }


    public function edit($id = null)
    {
        $elemento = Reunion::findOrFail($id);
        if ($elemento->fichero != '') {
            $formulario = new FormBuilder($elemento, $this->formFields);
            $modelo = $this->model;
            return view('intranet.edit', compact('formulario', 'modelo'));
        }

        $ordenes = OrdenReunion::where('idReunion', '=', $id)->get();
        $activos = app(ProfesorService::class)->activosOrdered();
        $tProfesores = hazArray($activos, 'dni', 'FullName');
        $sProfesores = $elemento
            ->profesores()
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->get(['dni', 'apellido1', 'apellido2', 'nombre']);
        $formulario = new FormBuilder($elemento,[
            'idProfesor' => ['type' => 'hidden'],
            'tipo' => ['type' => 'hidden'],
            'numero' => ['type' => 'select'],
            'grupo' => ['type' => 'hidden'],
            'curso' => ['disabled' => 'disabled'],
            'fecha' => ['type' => 'datetime'],
            'descripcion' => ['type' => 'textarea'],
            'objetivos' => ['type' => 'textarea'],
            'idEspacio' => ['type' => 'select'],
            'fichero' => ['type' => 'file'],
        ]);
        $modelo = $this->model;
        if ($elemento->informe){
            $select = $elemento->isSemi?'auxiliares.promocionaSemi':'auxiliares.promociona';
            $sAlumnos = $elemento->alumnos()->orderBy('apellido1')->orderBy('apellido2')->get();
            $tAlumnos = $this->tAlumnos($elemento,hazArray($sAlumnos,'nia'));
            return view(
                'reunion.asistencia',
                compact(
                    'formulario',
                    'modelo',
                    'tProfesores',
                    'sProfesores',
                    'ordenes',
                    'tAlumnos',
                    'sAlumnos',
                    'select'
                )
            );
        }
        return view('reunion.asistencia', compact('formulario', 'modelo', 'tProfesores', 'sProfesores', 'ordenes'));

    }

    public function update(ReunionUpdateRequest $request, $id)
    {
        $this->persist($request, $id);
        return $this->redirect();
    }

    private function tAlumnos($reunion,$sAlumnos){
        $grupo = $reunion->grupoClase;
        return hazArray($grupo->Alumnos->whereNotIn('nia',$sAlumnos),'nia','nameFull');
    }


    public function altaProfesor(Request $request, $reunion_id)
    {
        $reunion = Reunion::findOrFail($reunion_id);
        $this->reunionService()->addProfesor($reunion, (string) $request->idProfesor);
        return redirect()->route(self::REUNION_UPDATE, ['reunion' => $reunion_id]);
    }

    public function borrarProfesor($reunion_id, $profesor_id)
    {

        $reunion = Reunion::findOrFail($reunion_id);
        $this->reunionService()->removeProfesor($reunion, (string) $profesor_id);
        return redirect()->route(self::REUNION_UPDATE, ['reunion' => $reunion_id]);
    }

    public function borrarAlumno($reunion_id, $alumno_id)
    {

        $reunion = Reunion::findOrFail($reunion_id);
        $this->reunionService()->removeAlumno($reunion, (string) $alumno_id);
        return redirect()->route(self::REUNION_UPDATE, ['reunion' => $reunion_id]);
    }

    public function altaAlumno(Request $request, $reunion_id)
    {
        $reunion = Reunion::findOrFail($reunion_id);
        $this->reunionService()->addAlumno($reunion, (string) $request->idAlumno, (int) $request->capacitats);
        return redirect()->route(self::REUNION_UPDATE, ['reunion' => $reunion_id]);
    }

    public function altaOrden(OrdenReunionStoreRequest $request, $reunion_id)
    {
        if (!is_numeric($request->orden )) {
            $max = OrdenReunion::where('idReunion', '=', $reunion_id)->max('orden');
            $request->merge(['orden' => $max + 1]);
        }
        OrdenReunion::create($request->all());
        return redirect()->route(self::REUNION_UPDATE, ['reunion' => $reunion_id]);
    }


    public function borrarOrden($reunion_id, $orden_id)
    {
        $orden = OrdenReunion::find($orden_id);

        if (!$orden) {
            Alert::danger("No s'ha trobat l'ordre de reunió #{$orden_id}.");
            return redirect()->route(self::REUNION_UPDATE, ['reunion' => $reunion_id]);
        }

        try {
            $orden->delete();
            Alert::success("S'ha eliminat correctament l'ordre de reunió #{$orden_id}.");
        } catch (\Exception $e) {
            Alert::danger("No s'ha pogut eliminar l'ordre #{$orden_id}.");
        }

        return redirect()->route(self::REUNION_UPDATE, ['reunion' => $reunion_id]);
    }

    public function notify($id)
    {
        $elemento = Reunion::findOrFail($id);
        $this->reunionService()->notify($elemento);
        return back();
    }



    public function email($id)
    {
        $elemento = Reunion::findOrFail($id);
        //esborra fitxer si ja estaven
        if (file_exists(storage_path("tmp/Reunion_$id.pdf"))) {
            unlink(storage_path("tmp/Reunion_$id.pdf"));
        }
        if (file_exists(storage_path("tmp/invita_$id.ics"))) {
            unlink(storage_path("tmp/invita_$id.ics"));
        }
        //guarda fitxers i construix variable
        $this->construye_pdf($id)->save(storage_path("tmp/Reunion_$id.pdf"));
        if (!haVencido($elemento->fecha)) {
            file_put_contents(storage_path("tmp/invita_$id.ics"), CalendarService::build($elemento)->render());
            $attach = ["tmp/Reunion_$id.pdf" => 'application/pdf', "tmp/invita_$id.ics" => 'text/calendar'];
        } else {
            $attach = ["tmp/Reunion_$id.pdf" => 'application/pdf'];
        }
        
        $asistentes = Asistencia::where('idReunion', '=', $id)->get();
        $remitente = ['email' => $elemento->Responsable->email, 'nombre' => $elemento->Responsable->FullName];
        foreach ($asistentes as $asistente) {
            if (!haVencido($elemento->fecha)) {
                dispatch(
                    new SendEmail(
                        $asistente->Profesor->email,
                        $remitente,
                        'email.convocatoria',
                        $elemento,
                        $attach
                    )
                );
            }  else {
                dispatch(new SendEmail($asistente->Profesor->email, $remitente, 'email.reunion', $elemento, $attach));
            }
        }
        Alert::info('Correus en qua.Prompte arribaran al seu destinari');
        return back();
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['pdf']);
        $actual = AuthUser()->dni;
        $this->panel->setBoton('grid',
            new BotonImg('reunion.edit',
                ['img' => 'fa-pencil', 'where' => ['idProfesor', '==', $actual, 'archivada', '==', '0']]
            )
        );
        $this->panel->setBoton('grid',
            new BotonImg('reunion.delete',
                ['where' => ['idProfesor', '==', $actual, 'archivada', '==', '0']]
            )
        );
        $this->panel->setBoton('grid',
            new BotonImg('reunion.notification',
                ['where' => ['idProfesor', '==', $actual, 'fichero', '==', '', 'archivada', '==', '0']]
            )
        );
        $this->panel->setBoton('grid',
            new BotonImg('reunion.email',
                ['where' => ['idProfesor', '==', $actual, 'fichero', '==', '']]
            )
        );
        $this->panel->setBoton('grid',
            new BotonImg('reunion.ics',
                ['img' => 'fa-calendar', 'where' => ['fecha', 'posterior', Date::yesterday()]]
            )
        );
        $this->panel->setBoton('grid',
            new BotonImg('reunion.saveFile',
                [
                    'where' => [
                        'idProfesor', '==', $actual,
                        'archivada', '==', '0',
                        'fecha', 'anterior', Date::yesterday()
                    ]
                ]
            )
        );
        $this->panel->setBoton('grid',
            new BotonImg('reunion.deleteFile',
                [
                    'img' => 'fa-unlock',
                    'where' => [
                        'idProfesor', '==', $actual,
                        'archivada', '==', '1',
                        'fecha', 'anterior', Date::yesterday()
                    ]
                ]
            )
        );

    }

    public function pdf($id)
    {
        $elemento = Reunion::find($id);
        if (!$elemento) {
            Alert::danger("No s'ha trobat la reunió #$id");
            return back();
        }
        if ($elemento->fichero != '') {
            if (file_exists(storage_path('/app/' . $elemento->fichero))) {
                return response()->file(storage_path('/app/' . $elemento->fichero));
            }

            Alert::message('No trobe fitxer', 'danger');
            return back();
        }

        if ($elemento->archivada) {
            $this->saveFile($id);
        }

        return $this->construye_pdf($id)->stream();
    }

    private function actaCompleta(Reunion $reunion)
    {
        if ($reunion->tipo == 7) {
            foreach ($reunion->ordenes as $orden) {
                if (empty($orden->resumen)) {
                    throw new IntranetException("Tots els punts han de completar-se en una reunió d'avaluació");
                }
            }
        }
    }

    public function saveFile($id)
    {
        try {
            $elemento = $this->class::find($id);
            if ($elemento->fichero != '') {
                $nomComplet = $elemento->fichero;
            } else {
                $this->actaCompleta($elemento);
                $nom = 'Acta_' . $elemento->id . '.pdf';
                $directorio = 'gestor/' . Curso() . '/' . $this->model;
                $nomComplet = $directorio . '/' . $nom;
                if (!file_exists(storage_path('/app/' . $nomComplet))) {
                    $this->construye_pdf($id)->save(storage_path('/app/' . $nomComplet));
                }
            }
            $elemento->archivada = 1;
            $elemento->fichero = $nomComplet;
            DB::transaction(function () use ($elemento) {
                $gestor = new GestorService($elemento);
                $gestor->save(['propietario' => $elemento->Creador->FullName,
                    'tipoDocumento' => 'Acta',
                    'descripcion' => $elemento->descripcion,
                    'fichero' => $elemento->fichero,
                    'supervisor' => $elemento->Creador->FullName,
                    'grupo' => str_replace(' ', '_', $elemento->Xgrupo),
                    'tags' => TipoReunionService::find($elemento->tipo)->vliteral,
                    'created_at' => new Date($elemento->fecha),
                    'rol' => config('roles.rol.profesor')]);
                $elemento->save();
            });
        } catch (IntranetException $e){
                Alert::warning($e->getMessage());
        }
        return back();
    }

    public function deleteFile(Request $request,$id)
    {
        if ($request->pass == date('mdy')) {
            $elemento = $this->class::find($id);
            $document = Documento::where('tipoDocumento','Acta')
                ->where('curso',Curso())
                ->where('idDocumento',$elemento->id)
                ->first();
            if ($elemento->fichero != '' && $document) {
                DB::transaction(function () use ($elemento, $document) {
                    $nom = $elemento->fichero;
                    $document->delete();
                    $elemento->archivada = 0;
                    $elemento->fichero = '';
                    $elemento->save();
                    unlink(storage_path('/app/' . $nom));
                });
            }
        }
        return back();
    }

    public function listado($dia = null)
    {
        foreach ($this->grupos()->all() as $grupo) {
            foreach (config('auxiliares.reunionesControlables') as $tipo => $howMany) {
                $reuniones[$grupo->nombre][$tipo] = Reunion::Convocante($grupo->tutor)->Tipo($tipo)->Archivada()->get();
            }
        }
        return view('reunion.control', compact('reuniones'));
    }

    public function avisaFaltaActa(Request $request)
    {
        $cont = 0;
        if ($request->quien) {
            $grupos = $this->grupos()->byCurso((int) $request->quien);
        }
        else {
            $grupos = $this->grupos()->all();
        }
        
        foreach ($grupos as $grupo) {
            if (!Reunion::Convocante($grupo->tutor)
                ->Tipo($request->tipo)
                ->Numero($request->numero)
                ->Archivada()
                ->count()) {
                $texto = 'Et falta per fer i/o arxivar la reunio ' . TipoReunionService::find($request->tipo)->vliteral . ' ';
                $texto .= $request->numero > 0 ? config('auxiliares.numeracion')[$request->numero] : '';
                avisa($grupo->tutor, $texto);
                $cont++;
            }
        }
        Alert::info($cont . ' Avisos enviats');
        return back();
    }

    private function construye_pdf($id)
    {
        $elemento = Reunion::findOrFail($id);
        $hoy = new Date($elemento->fecha);
        $elemento->dia = FechaString($hoy);
        $elemento->hora = $hoy->format('H:i');
        $hoy = new Date($elemento->updated_at);
        $elemento->hoy = haVencido($elemento->fecha) ? $elemento->dia : FechaString($hoy);
        return $this->hazPdf(
            $this->informe($elemento),
            OrdenReunion::where('idReunion', '=', $id)->get(),
            $elemento,
            'portrait',
            'a4'
        );
    }

    private function informe($elemento)
    {
        $tipo_reunion = TipoReunionService::find($elemento->tipo);
        return haVencido($elemento->fecha) ?
            'pdf.reunion.'.$tipo_reunion->acta:
            'pdf.reunion.'.$tipo_reunion->convocatoria;
    }


    public static function preparePdf($informe, $aR)
    {
        $hoy = new Date();
        $elemento = FechaString($hoy,'ca');
        return self::hazPdf($informe, $aR,$elemento ,'portrait','a4');
    }


}
