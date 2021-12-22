<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Intranet\Http\Controllers\Auth\PerfilController;
use Intranet\Botones\BotonIcon;
use Jenssegers\Date\Date;
use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;


/**
 * Class AlumnoController
 * @package Intranet\Http\Controllers
 */
class AlumnoController extends PerfilController
{

    use traitImprimir;

    /**
     * @var string
     */
    protected $model = 'Alumno';
    /**
     * @var array
     */
    protected $vista = ['show' => 'perfil', 'edit' => 'perfil'];

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|void
     */
    public function update(Request $request, $id)
    {
        $new = Alumno::find($id);
        parent::update($request, $new);
        return redirect("/alumno_grupo/" . $new->Grupo()->first()->codigo . "/show");
    }

    /**
     * @param $alumno
     * @return mixed
     */
    public function carnet($alumno)
    {
        return $this->hazPdf('pdf.carnet', Alumno::where('nia', $alumno)->get(), [Date::now()->format('Y'), 'Alumnat - Student'], 'portrait', [85.6, 53.98])->stream();
    }

    public function checkFol($id)
    {
        $alumne = Alumno::findOrFail($id);
        $alumne->fol = ($alumne->fol==0)?1:0;
        $alumne->save();
        return back();

    }

//    public function baja($id)
//    {
//        $expediente = new Expediente();
//        $expediente->idAlumno = $id;
//        $expediente->idProfesor = AuthUser()->dni;
//        $expediente->fecha = Hoy();
//        $expediente->tipo = 1;
//        $expediente->estado = 1;
//        $expediente->explicacion = trans("models.accept.Expediente", ['alumno' => $expediente->Alumno->nombre . ' ' . $expediente->Alumno->apellido1 . ' ' . $expediente->Alumno->apellido2, 'profesor' =>
//            $expediente->Profesor->FullName]);
//        $expediente->save();
//        avisa($id, $expediente->explicacion, '#', $expediente->Profesor->FullName);
//        avisa($expediente->idProfesor, $expediente->explicacion, '/expediente/' . $expediente->id . '/edit');
//        return back();
//    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function equipo()
    {
        if (AuthUser()->Grupo) {
            $grupo = AuthUser()->Grupo->count() ? AuthUser()->Grupo->first()->codigo : '';
            $this->panel->setPestana('profile', true, 'profile.equipo', null, null, 1);
            return $this->grid(Profesor::orderBy('apellido1', 'asc')->orderBy('apellido2', 'asc')
                                    ->Grupo($grupo)->get());
        }
        return back();
    }

    /**
     *
     */
    public function iniBotones()
    {
        $this->panel->setBoton('profile', new BotonIcon('alumno.mensaje', ['icon' => 'fa-bell', 'class' => 'mensaje btn-success']));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alerta(Request $request, $id)
    {
        avisa($id, $request->explicacion != '' ? $request->explicacion : 'Te ha dado un toque.');
        return back();
    }



}
