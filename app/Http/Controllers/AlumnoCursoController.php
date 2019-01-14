<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Intranet\Entities\AlumnoCurso;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Curso;
use Intranet\Botones\BotonImg;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Alumno;

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
        $actual->finalizado = $actual->finalizado ? 0 : 1;
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
        $actual = AlumnoCurso::where('id', $id)->get();
        $curso = Curso::find($actual->first()->idCurso);
        if (haVencido($curso->fecha_fin)) {
            return self::hazPdf('pdf.alumnos.manipulador', $actual, $curso)->stream();
        } else
            return self::imprime($id);
    }

    public function registerG($grupo, $id)
    {
        $alumnos = AlumnoGrupo::where('idGrupo', $grupo)->get();
        foreach ($alumnos as $alumno) {
            $this->register($id, $alumno->idAlumno, false);
        }
        return back();
    }

    public function registerA($alumno, $id)
    {
        return $this->register($id, $alumno);
    }

    public function register($id, $alumno = null, $redirect = 1)
    {
        $curso = Curso::find($id);
        $alumno = $alumno ? Alumno::find($alumno) : AuthUser();
        $existe = AlumnoCurso::where('idCurso', $id)
                ->where('idAlumno', $alumno->nia)
                ->count();
        if ($curso && !$existe) {
            if ($curso->aforo == 0 || $curso->NAlumnos < $curso->aforo) {
                $alumno->Curso()->attach($id, ['registrado' => 'S', 'finalizado' => 0]);
                $mensaje = 'Has sigut registrat/ada al curs ';
            } else
            if ($curso->NAlumnos < $curso->aforo * config('variables.reservaAforo')) {
                $alumno->Curso()->attach($id, ['registrado' => 'R', 'finalizado' => 0]);
                $mensaje = "Estas en llista d'espera al curs ";
            } else
                $mensaje = 'No queden places al curs ';
            avisa($alumno->nia, $mensaje);
        }
        if ($redirect)
            return back();
    }

    public function unregister($id, $alumno = null, $redirect = 1)
    {
        $curso = Curso::find($id);
        $alumno = $alumno ? $alumno : AuthUser()->nia;
        $existe = AlumnoCurso::where('idCurso', $id)
                ->where('idAlumno', $alumno)
                ->count();

        if ($curso && $existe) {
            $registro = AlumnoCurso::where('idCurso', $id)
                    ->where('idAlumno', $alumno)
                    ->first();
            $tipoRegistro = $registro->registrado;
            $registro->delete();
            if ($curso->aforo && $tipoRegistro == 'S') {
                $registro = AlumnoCurso::where('idCurso', $id)
                        ->where('registrado', 'R')
                        ->orderBy('id')
                        ->first();
                if ($registro) {
                    $registro->registrado = 'S';
                    $registro->save();
                    avisa($registro->idAlumno, 'Has sigut registrat/ada al curs ');
                }
            }
            avisa($alumno, 'Esborrat/ada del curs ');
        }
        if ($redirect) return back();
    }
}
