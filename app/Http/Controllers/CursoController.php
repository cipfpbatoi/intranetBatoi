<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Curso;
use Intranet\Entities\AlumnoCurso;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;

class CursoController extends IntranetController
{

    use traitImprimir;

    
    protected $model = 'Curso';
    protected $gridFields = ['id', 'titulo', 'estado', 'fecha_inicio','Alumno'];

    public function detalle($id)
    {
        return redirect()->route('alumnocurso.show', ['grupo' => $id]);
    }

    public function pdf($id)
    {
        $curso = Curso::find($id);
        if (haVencido($curso->fecha_fin)){
            $todos = AlumnoCurso::Curso($id)->Finalizado()->get();
            return self::hazPdf('pdf.alumnos.manipulador',$todos,$curso)->stream();
        }
        else return self::imprime($id);
    }

    public function indexAlumno()
    {
        $todos = Curso::where('activo', 1)
                ->get();
        $this->iniAluBotones();
        return $this->grid($todos);
    }

    protected function iniAluBotones()
    {
        $this->panel->setPestana('profile', true);
        $this->panel->setBothBoton('alumnocurso.register', ['class' => 'btn-success authorize'], true);
        $this->panel->setBothBoton('alumnocurso.unregister', ['class' => 'btn-danger unauthorize'], true);
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['detalle', 'edit']);
        $this->panel->setBoton('grid',new BotonImg('curso.pdf',['where' => ['alumnos','>',0]]));
        $this->panel->setBoton('grid', new BotonImg('curso.delete', ['where' => ['activo', '==', 0]]));
        $this->panel->setBoton('grid', new BotonImg('curso.active'));
    }
}
