<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\Auth\PerfilController;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonIcon;
use Jenssegers\Date\Date;
use Intranet\Entities\Alumno;
use Intranet\Entities\Profesor;
use Intranet\Entities\Expediente;
use Intranet\Entities\Alumno_grupo;

class AlumnoController extends PerfilController
{

    use traitImprimir;

    protected $model = 'Alumno';
    protected $vista = ['show' => 'perfil', 'edit' => 'perfil'];

    public function update(Request $request, $id)
    {
        $new = Alumno::find($id);
        parent::update($request, $new);
        return redirect("/alumno_grupo/" . $new->Grupo()->first()->codigo . "/show");
    }

    public function carnet($alumno)
    {
        return $this->hazPdf('pdf.carnet', Alumno::where('nia', $alumno)->get(), [Date::now()->format('Y'), 'Alumne - Student'], 'portrait', [85.6, 53.98])->stream();
    }

    public function baja($id)
    {
        $expediente = new Expediente();
        $expediente->idAlumno = $id;
        $expediente->idProfesor = AuthUser()->dni;
        $expediente->fecha = Hoy();
        $expediente->tipo = 1;
        $expediente->estado = 1;
        $expediente->explicacion = trans("models.accept.Expediente", ['alumno' => $expediente->Alumno->nombre . ' ' . $expediente->Alumno->apellido1 . ' ' . $expediente->Alumno->apellido2, 'profesor' =>
            $expediente->Profesor->FullName]);
        $expediente->save();
        avisa($id, $expediente->explicacion, '#', $expediente->Profesor->FullName);
        avisa($expediente->idProfesor, $expediente->explicacion, '/expediente/' . $expediente->id . '/edit');
        return back();
    }

    public function equipo()
    {
        if (AuthUser()->Grupo) {
            $grupo = AuthUser()->Grupo->count() ? AuthUser()->Grupo->first()->codigo : '';
            $this->panel->setPestana('profile', true, 'profile.equipo', null, null, 1);
            return $this->grid(Profesor::orderBy('apellido1', 'asc')->orderBy('apellido2', 'asc')
                                    ->Grupo($grupo)->get());
        }
    }

    public function iniBotones()
    {
        $this->panel->setBoton('profile', new BotonIcon('alumno.mensaje', ['icon' => 'fa-bell', 'class' => 'mensaje btn-success']));
    }

    public function alerta(Request $request, $id)
    {
        avisa($id, $request->explicacion != '' ? $request->explicacion : 'Te ha dado un toque.');
        return back();
    }

}
