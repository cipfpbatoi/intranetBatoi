<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Grupo;
use Intranet\Entities\Horario;
use Intranet\Entities\Ciclo;
use DB;
use Intranet\Botones\BotonImg;
use Intranet\Entities\Alumno;
use Jenssegers\Date\Date;
use Intranet\Entities\Curso;
use Styde\Html\Facades\Alert;
use Intranet\Jobs\SendEmail;
/**
 * Class GrupoController
 * @package Intranet\Http\Controllers
 */
class GrupoController extends IntranetController
{

    const DIRECCION ='roles.rol.direccion';

    use traitImprimir;

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
    protected $gridFields = ['codigo', 'nombre', 'Xtutor', 'Xciclo','XDual','Torn'];



    /**
     * @return \Illuminate\Database\Eloquent\Collection|Grupo[]|mixed
     */
    protected function search(){

        return esRol(AuthUser()->rol,config(self::DIRECCION)) ?
                Grupo::with('Ciclo')->with('Tutor')->with('TutorDual')->with('Tutor.Sustituye')->get():
                Grupo::with('Ciclo')->MisGrupos()->get();
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
        $this->panel->setBoton('grid', new BotonImg('grupo.carnet', ['roles' => [config(self::DIRECCION), config('roles.rol.tutor')]]));
        $this->panel->setBoton('grid', new BotonImg('grupo.edit', ['roles' => config(self::DIRECCION)]));
        $this->panel->setBoton('grid',new BotonImg('equipo.grupo',['img' => 'fa-graduation-cap']));

        if (AuthUser()->xdepartamento == 'Fol' && date('Y-m-d') > config('curso.certificatFol')){
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
        $horario = Horario::HorarioGrupo($id);
        $titulo = Grupo::findOrFail($id)->nombre;
        return view('horario.grupo', compact('horario', 'titulo'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asigna()
    {
        $todos = Grupo::all();
        foreach ($todos as $uno) {
            if ($uno->ciclo == ''){
                $ciclo = Ciclo::select('id')
                        ->where('codigo', '=', substr($uno->codigo, 1, 4))
                        ->first();
                if ($ciclo) {
                    $uno->idCiclo = $ciclo->id;
                    $uno->save();
                }
            }
        }
        return back();
    }

    /**
     * @param $grupo
     * @return mixed
     */
    public function pdf($grupo)
    {
        return $this->hazPdf('pdf.alumnos.fotoAlumnos',AlumnoGrupo::where('idGrupo',$grupo)->orderBy('subGrupo')->orderBy('posicion','desc')->OrderBy('apellido1')
            ->OrderBy('apellido2')->get()->groupBy('subGrupo'), Grupo::find($grupo))->stream();
    }

    /**
     * @param $grupo
     * @return mixed

    public function fse($grupo)
    {
        return $this->hazPdf('pdf.reunion.actaFSE',$this->alumnos($grupo), Grupo::find($grupo) )->stream();
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

    /**
     * @param $grupo
     * @return mixed
     */
    public function certificados($grupo)
    {
        $grupo = Grupo::find($grupo);
        $datos['ciclo'] = $grupo->Ciclo;
        $remitente = ['email' => cargo('secretario')->email, 'nombre' => cargo('secretario')->FullName];
        $count = 0;
        foreach ($grupo->Alumnos as $alumno){
            if ($alumno->fol == 1){
                $id = $alumno->nia;
                if (file_exists(storage_path("tmp/fol_$id.pdf"))){
                    unlink(storage_path("tmp/fol_$id.pdf"));
                }
                self::hazPdf('pdf.alumnos.'.$grupo->Ciclo->normativa,[$alumno],cargaDatosCertificado($datos),'portrait')->save(storage_path("tmp/fol_$id.pdf"));
                $attach = ["tmp/fol_$id.pdf" => 'application/pdf'];
                dispatch(new SendEmail($alumno->email, $remitente, 'email.fol', $alumno , $attach));
                $count++;
            }
        }
        $grupo->fol = 2;
        $grupo->save();
        if ($count){
            Alert::info("$count Correus enviats");
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
        return $this->hazPdf('pdf.alumnos.'.$grupo->Ciclo->normativa,Alumno::where('nia',$alumno)->get(),cargaDatosCertificado($datos),'portrait')->stream();
    }

    public function checkFol($id)
    {
        $grupo = Grupo::findOrFail($id);
        $grupo->fol = ($grupo->fol==0)?1:0;
        $grupo->save();
        return back();

    }

}
