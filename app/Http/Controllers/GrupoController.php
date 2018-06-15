<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Intranet\Entities\Horario;
use Illuminate\Support\Facades\Auth;
use DB;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Alumno;
use Jenssegers\Date\Date;
use Intranet\Entities\Curso;
use Illuminate\Support\Facades\Session;

class GrupoController extends IntranetController
{

    use traitImprimir;

    protected $perfil = 'profesor';
    protected $model = 'Grupo';
    protected $gridFields = ['codigo', 'nombre', 'Xtutor', 'Xciclo'];
    //protected $modal = true;

    
    protected function search(){
//        if (esRol(AuthUser()->rol,config('constants.rol.direccion')))
//            $this->panel->addGridField('Acta');
        return esRol(AuthUser()->rol,config('constants.rol.direccion')) ?
                Grupo::all():
                Grupo::MisGrupos()->get();
    }

    public function detalle($id)
    {
        return redirect()->route('alumnogrupo.index', ['grupo' => $id]);
    }


    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['pdf', 'horario']);
        //$this->panel->setBoton('index', new BotonBasico('grupo.asigna', ['roles' => config('constants.rol.administrador')]));
        $this->panel->setBoton('grid', new BotonImg('grupo.detalle', ['img' => 'fa-group']));
        $this->panel->setBoton('grid', new BotonImg('grupo.carnet', ['roles' => [config('constants.rol.direccion'), config('constants.rol.tutor')]]));
        $this->panel->setBoton('grid', new BotonImg('grupo.edit', ['roles' => config('constants.rol.direccion')]));
        $this->panel->setBoton('grid',new BotonImg('equipo.grupo',['img' => 'fa-graduation-cap']));
        $this->panel->setBoton('grid',new BotonImg('grupo.fse',['img' => 'fa-euro']));
        //$this->panel->setBoton('grid',new BotonImg('direccion.acta',['img' => 'fa-file-word-o','roles' => config('constants.rol.direccion'),'where' => ['acta_pendiente','==','1']]));
        $this->panel->setBoton('grid',new BotonImg('direccion.fol',['img' => 'fa-file-word-o','roles' => config('constants.rol.direccion')]));
        $cursos = Curso::Activo()->get();
        foreach ($cursos as $curso) {
            if (($curso->aforo == 0) || ($curso->NAlumnos < $curso->aforo * config('constants.reservaAforo')))
                $this->panel->setBoton('grid', new BotonImg("alumnocurso.registerGrupo/" . $curso->id, ['text' => trans('messages.generic.register') . $curso->titulo, 'img' => 'fa-institution']));
        }
    }

    protected function horario($id)
    {
        $horario = Horario::HorarioGrupo($id);
        $titulo = Grupo::findOrFail($id)->nombre;
        return view('horario.grupo', compact('horario', 'titulo'));
    }

    public function asigna()
    {
        $todos = Grupo::all();
        foreach ($todos as $uno) {
            if ($uno->ciclo == ''){
                $ciclo = \Intranet\Entities\Ciclo::select('id')
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

    public function pdf($grupo)
    {
        return $this->hazPdf('pdf.alumnos.fotoAlumnos',$this->alumnos($grupo), Grupo::find($grupo))->stream();
    }
    public function fse($grupo)
    {
        return $this->hazPdf('pdf.alumnos.fse',$this->alumnos($grupo), Grupo::find($grupo) )->stream();
    }
    public function carnet($grupo)
    {
        return $this->hazPdf('pdf.carnet', $this->alumnos($grupo), [Date::now()->format('Y'), 'Alumnes - Student'], 'portrait', [85.6, 53.98])->stream();
    }
    public function certificados($grupo)
    {
        $datos['ciclo'] = Grupo::find($grupo)->Ciclo;    
        return $this->hazPdf('pdf.alumnos.'.Grupo::find($grupo)->Ciclo->normativa, $this->alumnos($grupo),$this->cargaDatosCertificado($datos),'portrait')->stream();
    }
    public function certificado($alumno)
    {
        $grupo = Alumno::findOrFail($alumno)->Grupo->first();
        $datos['ciclo'] = $grupo->Ciclo;  
        return $this->hazPdf('pdf.alumnos.'.$grupo->Ciclo->normativa,Alumno::where('nia',$alumno)->get(),$this->cargaDatosCertificado($datos),'portrait')->stream();
    }
    
    private function alumnos($grupo){
        return Alumno::QGrupo($grupo)
                ->OrderBy('apellido1')
                ->OrderBy('apellido2')
                ->get();
    }
    

    

}
