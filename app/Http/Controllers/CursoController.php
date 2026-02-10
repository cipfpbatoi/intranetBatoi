<?php

namespace Intranet\Http\Controllers;

use DB;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\AlumnoCurso;
use Intranet\Entities\Curso;
use Intranet\Http\Requests\CursoRequest;
use Intranet\Http\Traits\Imprimir;
use Intranet\Jobs\SendEmail;
use Intranet\Services\General\GestorService;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;

/**
 * Class CursoController
 * @package Intranet\Http\Controllers
 */
class CursoController extends ModalController
{

    use Imprimir;


    /**
     * @var string
     */
    protected $model = 'Curso';
    /**
     * @var array
     */
    protected $gridFields = ['id', 'titulo', 'estado', 'fecha_inicio','NAlumnos'];


    public function store(CursoRequest $request)
    {
        $new = new Curso();
        $new->fillAll($request);
        return $this->redirect();
    }

    public function update(CursoRequest $request, $id)
    {
        Curso::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }
    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function detalle($id)
    {
        return redirect()->route('alumnocurso.show', ['grupo' => $id]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function indexAlumno()
    {
        $this->iniAluBotones();
        return $this->grid(Curso::where('activo', 1) ->get());
    }

    /**
     *
     */
    protected function iniAluBotones()
    {
        $this->panel->setPestana('profile', true);
        $this->panel->setBothBoton('alumnocurso.register', ['class' => 'btn-success authorize'], true);
        $this->panel->setBothBoton('alumnocurso.unregister', ['class' => 'btn-danger unauthorize'], true);
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['detalle', 'edit']);
        $this->panel->setBoton('grid',new BotonImg('curso.pdf',['where' => ['NAlumnos','>',0,'fecha_fin','posterior',Hoy()]]));
        $this->panel->setBoton('grid',new BotonImg('curso.email',['where' => ['NAlumnos','>',0,'fecha_fin','anterior',Hoy()]]));
        $this->panel->setBoton('grid', new BotonImg('curso.delete', ['where' => ['activo', '==', 0,'archivada','==',0]]));
        $this->panel->setBoton('grid', new BotonImg('curso.active',['where'=>['archivada','==',0]]));
        $this->panel->setBoton('grid',new BotonImg('curso.saveFile',
              ['where' => ['fecha_fin','anterior',Hoy(),'activo', '==', 0,'archivada','==',0]]));
        $this->panel->setBoton('grid',new BotonImg('curso.shows',
            ['img' => 'fa-file-pdf-o','where' => ['archivada','==',1]]));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveFile($id)
    {
        $elemento = $this->makeReport($id);
        DB::transaction(function () use ($elemento) {
            $gestor = new GestorService($elemento);
            $gestor->save(['propietario' => $elemento->profesorado,
                'tipoDocumento' => 'Curso',
                'descripcion' => $elemento->titulo,
                'tags' => 'Curs',
                'fichero' => $elemento->fichero,
                'supervisor' => AuthUser()->shortName,
                'created_at' => new Date($elemento->fecha_fin),
                'rol' => config('roles.rol.direccion')]);
            $elemento->archivada = 1;
            $elemento->save();
        });
        return back();
    }

    /**
     * @param $id
     * @return mixed
     */
    private function makeReport($id)
    {
        $curso = Curso::find($id);
        if ($curso->fichero == ''){
            $nomComplet = 'gestor/' . Curso() . '/' . $this->model. '/' .'Curso_' . $curso->id . '.pdf';
            $curso->fichero = $nomComplet;
            if (!file_exists(storage_path('/app/' . $nomComplet))){
                self::hazPdf('pdf.alumnos.manipuladores',$curso->Asistentes, $curso)->save(storage_path('/app/' . $nomComplet));
            }

        }
        return $curso;
    }


    public function document($id)
    {
        $elemento = Curso::findOrFail($id);
        if ($elemento->link) {
            return response()->file(storage_path('app/' . $elemento->fichero));
        }
        Alert::danger(trans("messages.generic.nodocument"));
        return back();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function pdf($id)
    {
        $elemento = $this->class::findOrFail($id);
        $informe = 'pdf.' . strtolower($this->model);
        $pdf = self::hazPdf($informe, $elemento, null, 'portrait');
        return $pdf->stream();
    }

    public function email($id)
    {
        $curso = Curso::findOrFail($id);
        $remitente = ['email' => cargo('director')->email, 'nombre' => cargo('director')->FullName];
        foreach ($curso->Asistentes as $alumno){
            $id = $alumno->pivot->id;
            if (file_exists(storage_path("tmp/Curs_$id.pdf"))){
                unlink(storage_path("tmp/Curs_$id.pdf"));
            }
            self::hazPdf('pdf.alumnos.manipulador', $alumno, $curso)->save(storage_path("tmp/Curs_$id.pdf"));
            $attach = ["tmp/Curs_$id.pdf" => 'application/pdf'];
            dispatch(new SendEmail($alumno->email, $remitente, 'email.certificado', AlumnoCurso::find($id), $attach));
        }
        Alert::info('Correus enviats');
        return back();

    }

    /*
    * active ($id)
    * canvia la variable activo del elemento (alumnocurso,curso,menu)
    */
    public function active($id)
    {
        $elemento = $this->class::findOrFail($id);
        if ($elemento->activo) {
            $elemento->activo = false;
        } else {
            $elemento->activo = true;
        }
        $elemento->save();
        return $this->redirect();
    }
    
}
