<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Profesor\ProfesorService;
use Illuminate\Http\Request;
use Intranet\Http\Requests\AlumnoUpdateRequest;
use Intranet\Services\Document\PdfService;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Colaboracion;
use Intranet\Http\Controllers\Auth\PerfilController;
use Intranet\UI\Botones\BotonIcon;
use Jenssegers\Date\Date;
use Intranet\Entities\Alumno;


/**
 * Class AlumnoController
 * @package Intranet\Http\Controllers
 */
class AlumnoController extends PerfilController
{
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
        $this->validate($request, (new AlumnoUpdateRequest())->rules());
        $new = Alumno::find($id);

        parent::update($request, $new);

        return redirect()->route('alumnogrupo.index', ['grupo' => $new->Grupo()->first()->codigo]);
    }

    /**
     * @param $alumno
     * @return mixed
     */
    public function carnet($alumno)
    {
        return app(PdfService::class)->hazPdf('pdf.carnet', Alumno::where('nia', $alumno)->get(), [Date::now()->format('Y'), 'Alumnat - Student'], 'portrait', [85.6, 53.98])->stream();
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
            return $this->grid(app(ProfesorService::class)->byGrupo((string) $grupo));
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
