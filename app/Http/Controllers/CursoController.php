<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Curso;
use Intranet\Entities\AlumnoCurso;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use DB;
use Intranet\Entities\Documento;
use Jenssegers\Date\Date;

class CursoController extends IntranetController
{

    use traitImprimir;

    
    protected $model = 'Curso';
    protected $gridFields = ['id', 'titulo', 'estado', 'fecha_inicio','Alumno'];

    public function detalle($id)
    {
        return redirect()->route('alumnocurso.show', ['grupo' => $id]);
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
        $this->panel->setBoton('grid',new BotonImg('curso.saveFile'),
                ['where' => ['fecha_fin','anterior',Hoy(),'activo', '==', 0]]);
    }
    
    public function saveFile($id)
    {
        $elemento = Curso::find($id);
        if ($elemento->fichero != '')
            $nomComplet = $elemento->fichero;
        else {
            $nom = 'Curso_' . $elemento->id . '.pdf';
            $directorio = 'gestor/' . Curso() . '/' . $this->model;
            $nomComplet = $directorio . '/' . $nom;
            if (!file_exists(storage_path('/app/' . $nomComplet))){
                $todos = AlumnoCurso::Curso($id)->Finalizado()->get();
                self::hazPdf('pdf.alumnos.manipulador',$todos,$elemento)->save(storage_path('/app/' . $nomComplet));
            }
        }
        $elemento->archivada = 1;
        $elemento->fichero = $nomComplet;
        DB::transaction(function () use ($elemento) {
            Documento::crea($elemento, ['propietario' => $elemento->profesorado,
                'tipoDocumento' => 'Curso',
                'descripcion' => $elemento->titulo,
                'tags' => 'Curs',
                'fichero' => $elemento->fichero,
                'supervisor' => AuthUser()->shortName,
                'created_at' => new Date($elemento->fecha_fin),
                'rol' => config('constants.rol.direccion')]);
            $elemento->save();
        });
        return back();
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
    
}
