<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Intranet\Entities\AlumnoCurso;
use Intranet\Entities\Alumno_grupo;
use Intranet\Entities\Curso;
use Intranet\Botones\BotonImg;
use Illuminate\Support\Facades\Session;

class AlumnoCursoController extends IntranetController
{

    use traitImprimir;

    
    protected $model = 'AlumnoCurso';
    protected $gridFields = ['idAlumno', 'nombre', 'finalizado', 'registrado'];

    public function search()
    {
        return AlumnoCurso::where('idCurso', '=', $this->search)->get();
    }
    
    public function active($id)
    {
        $actual = AlumnoCurso::find($id);
        if ($actual->finalizado)
            $actual->finalizado = 0;
        else
            $actual->finalizado = 1;
        $actual->save();
        return back();
    }

    public function destroy($id)
    {
        $registro = AlumnoCurso::find($id);
        $this->unregister($registro->idCurso, $registro->idAlumno, 0);
        $registro->delete();
        return back();
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera([], ['active']);
        $this->panel->setBoton('grid', new BotonImg('alumnocurso.delete', ['where' => ['finalizado', '==', 0]]));
        $this->panel->setBoton('grid', new BotonImg('alumnocurso.pdf', ['where' => ['finalizado', '==', 1]]));
    }
    public function pdf($id)
    {
        $actual = AlumnoCurso::where('id',$id)->get();
        $curso = Curso::find($actual->first()->idCurso);
        if (haVencido($curso->fecha_fin)){
            return self::hazPdf('pdf.alumnos.manipulador',$actual,$curso)->stream();
        }
        else return self::imprime($id);
    }

    public function register($id, $alumno = null, $redirect = 1)
    {
        $curso = Curso::find($id);
        $alumno = $alumno ? $alumno : AuthUser()->nia;
        if ($curso) {
            $existe = AlumnoCurso::where('idCurso', $id)
                    ->where('idAlumno', $alumno)
                    ->count();
            if (!$existe) {
                $new = new AlumnoCurso();
                $new->idAlumno = $alumno;
                $new->idCurso = $id;
                $new->finalizado = 0;
                if ($curso->aforo == 0)
                    $new->registrado = 'S';
                else {
                    if ($curso->Cuantos() < $curso->aforo)
                        $new->registrado = 'S';
                    else {
                        if ($curso->Cuantos() < $curso->aforo * config('constants.reservaAforo'))
                            $new->registrado = 'R';
                        else
                            $new->registrado = 'N';
                    }
                }
                if ($new->registrado != 'N')
                    $new->save();
                avisa($alumno, $this->mensaje($new->registrado));
            }
        }

        if ($redirect)
            return back();
    }

    public function registerG($grupo, $id)
    {
        $alumnos = Alumno_grupo::where('idGrupo', $grupo)->get();
        foreach ($alumnos as $alumno) {
            $this->register($id, $alumno->idAlumno, false);
        }
        return back();
    }

    public function registerA($alumno, $id)
    {
        return $this->register($id, $alumno);
    }

    public function unregister($id, $alumno = null, $redirect = 1)
    {
        $curso = Curso::find($id);
        $alumno = $alumno ? $alumno : AuthUser()->nia;

        if ($curso) {
            $existe = AlumnoCurso::where('idCurso', $id)
                    ->where('idAlumno', $alumno)
                    ->count();
            if ($existe) {
                $registro = AlumnoCurso::where('idCurso', $id)
                        ->where('idAlumno', $alumno)
                        ->first();
                $tipoRegistro = $registro->registrado;
                $registro->delete();
                if ($curso->aforo) {
                    $registro = AlumnoCurso::where('idCurso', $id)
                            ->where('registrado', 'R')
                            ->orderBy('id')
                            ->first();
                    if ($registro) {
                        $registro->registrado = 'S';
                        $registro->save();
                        avisa($registro->idAlumno, $this->mensaje('S'));
                    }
                }
                avisa($alumno, $this->mensaje('E'));
            }
        }
        if ($redirect)
            return back();
    }

    private function mensaje($tipoMensaje)
    {
        switch ($tipoMensaje) {
            case 'S' : $mensaje = 'Has sigut registrat/ada al curs ';
                break;
            case 'R' : $mensaje = "Estas en llista d'espera al curs ";
                break;
            case 'N' : $mensaje = 'No queden places al curs ';
                break;
            case 'E' : $mensaje = 'Esborrat/ada del curs ';
        }
        return $mensaje;
    }

}
