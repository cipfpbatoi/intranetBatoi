<?php

namespace Intranet\Http\Controllers;

use DB;
use Intranet\Componentes\Pdf as PDF;
use Intranet\Entities\AlumnoCurso;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Curso;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Alumno;
use Styde\Html\Facades\Alert;

class AlumnoCursoController extends IntranetController
{

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
        $actual = AlumnoCurso::where('id', $id)->first();
        $curso = Curso::find($actual->first()->idCurso);
        if (haVencido($curso->fecha_fin)) {
            return PDF::hazPdf('pdf.alumnos.manipulador', $actual, $curso)->stream();
        }
        else {
            return back();
        }
    }

    public function registerGrup($grupo, $id)
    {
        $alumnos = AlumnoGrupo::where('idGrupo', $grupo)->get();
        foreach ($alumnos as $alumno) {
            $this->register($id, $alumno->idAlumno);
        }
        return back();
    }

    public function registerAlumn($alumno, $id)
    {
        $this->register($id, $alumno);
        return back();
    }

    public function register($id, $alumno = null)
    {
        $alumno = $alumno ? Alumno::find($alumno) : AuthUser();
        $curso = Curso::find($id);

        if ($curso && AlumnoCurso::where('idCurso', $id)->where('idAlumno', $alumno->nia)->count()==0){
            $mensaje = $this->getRegister($alumno,$curso);
            Alert::info($alumno->FullName.': '.$mensaje);
            avisa($alumno->nia,$mensaje);
        }
        else {
            Alert::danger($alumno->FullName . ': Ja estas registrat');
        }
    }

    private function getRegister($alumno,$curso){
        if ($curso->aforo == 0 || $curso->NAlumnos < $curso->aforo) {
            $alumno->Curso()->attach($curso->id, ['registrado' => 'S', 'finalizado' => 0]);
            return 'Has sigut registrat/ada al curs ';
        }
        if ($curso->NAlumnos < $curso->aforo * config('variables.reservaAforo')) {
            $alumno->Curso()->attach($curso->id, ['registrado' => 'R', 'finalizado' => 0]);
            return "Estas en llista d'espera al curs ";
        }
        return 'No queden places al curs ';
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
        if ($redirect) {
            return back();
        }
    }
}
