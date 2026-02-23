<?php
namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Application\Grupo\GrupoWorkflowService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Http\Controllers\Core\IntranetController;
use Intranet\Presentation\Crud\GrupoCrudSchema;

use Illuminate\Http\Request;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Curso;
use Intranet\Http\Traits\Core\Imprimir;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;

/**
 * Class GrupoController
 * @package Intranet\Http\Controllers
 */
class GrupoController extends IntranetController
{
    private ?GrupoWorkflowService $grupoWorkflowService = null;
    private ?HorarioService $horarioService = null;
    private ?GrupoService $grupoService = null;

    const DIRECCION ='roles.rol.direccion';
    const TUTOR ='roles.rol.tutor';
    const ORIENTADOR ='roles.rol.orientador';


    use Imprimir;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Grupo';
    /**
     * @var array
     */
    protected $gridFields = GrupoCrudSchema::GRID_FIELDS;
    protected $formFields = GrupoCrudSchema::FORM_FIELDS;
    protected $parametresVista = ['modal' => [  'selAlumGrup']];

    private function horarios(): HorarioService
    {
        if ($this->horarioService === null) {
            $this->horarioService = app(HorarioService::class);
        }

        return $this->horarioService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    private function workflows(): GrupoWorkflowService
    {
        if ($this->grupoWorkflowService === null) {
            $this->grupoWorkflowService = app(GrupoWorkflowService::class);
        }

        return $this->grupoWorkflowService;
    }




    /**
     * @return \Illuminate\Database\Eloquent\Collection|Grupo[]|mixed
     */
    protected function search(){

        return esRol(AuthUser()->rol,config(self::DIRECCION)) || esRol(AuthUser()->rol,config(self::ORIENTADOR))  ?
                $this->grupos()->allWithTutorAndCiclo():
                $this->grupos()->misGruposWithCiclo();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function detalle($id)
    {
        return redirect()->route('alumnogrupo.index', ['grupo' => $id]);
    }


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['pdf', 'horario']);
        $this->panel->setBoton('grid', new BotonImg('grupo.detalle', ['img' => 'fa-group']));
        $this->panel->setBoton('grid', new BotonImg('grupo.carnet', ['roles' => config(self::DIRECCION)]));
        $this->panel->setBoton('grid', new BotonImg('grupo.carnet', ['roles' => config(self::TUTOR),'where'=>['tutor','==',AuthUser()->dni]]));
        $this->panel->setBoton('grid', new BotonImg('grupo.edit', ['roles' => config(self::DIRECCION)]));
        $this->panel->setBoton('grid',new BotonImg('equipo.grupo',['img' => 'fa-graduation-cap']));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'grupo.list',
                    [
                         'img' => 'fa-file-excel-o',
                        'class' => 'selecciona',
                     ]
            )
        );



        if (AuthUser()->xdepartamento === 'Fol' && date('Y-m-d') > config('variables.certificatFol')) {
            $this->panel->setBoton('grid',new BotonImg('grupo.fol',['img' => 'fa-square-o','where'=>['fol','==', 0]]));
            $this->panel->setBoton('grid',new BotonImg('grupo.fol',['img' => 'fa-check','where'=>['fol','==', 1]]));
        }

        $this->panel->setBoton('grid',new BotonImg('direccion.fol',
            ['img' => 'fa-file-word-o','roles' => config(self::DIRECCION),'where'=>['fol','==', 1]]));
        $cursos = Curso::Activo()->get();
        foreach ($cursos as $curso) {
            if (($curso->aforo == 0) || ($curso->NAlumnos < $curso->aforo * config('variables.reservaAforo'))){
                $this->panel->setBoton('grid', new BotonImg("alumnocurso.registerGrupo/" . $curso->id, ['text' => trans('messages.generic.register') . $curso->titulo, 'img' => 'fa-institution']));

            }
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function horario($id)
    {
        $horario = $this->horarios()->semanalByGrupo((string) $id);
        $grupo = $this->grupos()->find((string) $id);
        abort_unless($grupo !== null, 404);
        $titulo = $grupo->nombre;
        return view('horario.grupo', compact('horario', 'titulo'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asigna()
    {
        $this->workflows()->assignMissingCiclo();
        return back();
    }

    /**
     * @param $grupo
     * @return mixed
     */
    public function pdf($grupo)
    {
        return $this->hazPdf(
            'pdf.alumnos.fotoAlumnos',
            AlumnoGrupo::where('idGrupo', $grupo)->orderBy('subGrupo')->orderBy('posicion', 'desc')->get()->groupBy('subGrupo'),
            $this->grupos()->find((string) $grupo)
        )->stream();
    }

    /**
     * @param $grupo
     * @return mixed

    public function fse($grupo)
    {
        return $this->hazPdf('pdf.reunion.actaFSE',$this->alumnos($grupo), $this->grupos()->find((string) $grupo))->stream();
    }*/

    /**
     * @param $grupo
     * @return mixed
     */
    public function carnet($grupo)
    {
        return $this->hazPdf('pdf.carnet', Alumno::QGrupo($grupo)
            ->OrderBy('apellido1')
            ->OrderBy('apellido2')
            ->get(), [Date::now()->format('Y'), 'Alumnat - Student'], 'portrait', [85.6, 53.98])->stream();
    }

    public function list(Request $request)
    {
        return response($this->workflows()->selectedStudentsPlainText($request->toArray()), 200)
            ->header('Content-Type', 'text/plain');
    }

    /*
    public function list($idGrupo)
    {
        $grupo = $this->grupos()->find((string) $idGrupo);
        $alumnos = hazArray($grupo->Alumnos->sortBy('nameFull'),'nameFull');
        $gr = array('grupo' => $grupo->codigo.' - '.$grupo->nombre);
        $columna = array_merge($gr,$alumnos);
        $xls = new ExcelService(storage_path('/tmp/'.$idGrupo.'.xlsx'));
        $xls->render($columna);
        return response()->download(
            storage_path('/tmp/'.$idGrupo.'.xlsx'), // Ajusta el camí segons la teva estructura
            'alumnes_'.$idGrupo.'.xlsx', // Aquest serà el nom del fitxer per al client
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'] // Aquest és el Content-Type per a fitxers .xlsx
        );
    }
    */

    /**
     * @param $grupo
     * @return mixed
     */
    public function certificados($grupo)
    {
        $grupoModel = $this->grupos()->find((string) $grupo);
        if (!$grupoModel) {
            Alert::danger('Grup no trobat');
            return back();
        }

        try {
            $result = $this->workflows()->sendFolCertificates(
                $grupoModel,
                function ($alumno, $datos, $tmpPath) use ($grupoModel): void {
                    self::hazPdf(
                        'pdf.alumnos.' . $grupoModel->Ciclo->normativa,
                        [$alumno],
                        cargaDatosCertificado($datos),
                        'portrait'
                    )->save($tmpPath);
                }
            );
        } catch (\Exception) {
            echo 'No hi ha connexió amb el servidor de matrícules';
            exit();
        }

        foreach ($result['errors'] as $error) {
            Alert::danger($error);
        }

        if ($result['sent']) {
            Alert::info("{$result['sent']} Correus enviats");
        } else {
            Alert::info("Cap Correu enviat");
        }
        return back();
    }

    /**
     * @param $alumno
     * @return mixed
     */
    public function certificado($alumno)
    {
        $grupo = Alumno::findOrFail($alumno)->Grupo->first();
        $datos['ciclo'] = $grupo->Ciclo;  
        return $this->hazPdf('pdf.alumnos.'.$grupo->Ciclo->normativa, Alumno::where('nia',$alumno)->get(),cargaDatosCertificado($datos),'portrait')->stream();
    }

    public function checkFol($id)
    {
        $grupo = $this->grupos()->find((string) $id);
        abort_unless($grupo !== null, 404);
        $grupo->fol = ($grupo->fol==0)?1:0;
        $grupo->save();
        return back();

    }

}
