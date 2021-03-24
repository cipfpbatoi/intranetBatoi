<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Intranet\Entities\Dual;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Fct;
use Intranet\Entities\Profesor;
use mikehaertl\pdftk\Pdf;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;

/**
 * Class DualController
 * @package Intranet\Http\Controllers
 */
class DualController extends IntranetController
{

    use traitImprimir;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Dual';
    /**
     * @var bool
     */
    protected $modal = false;

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $alumno = AlumnoFct::findOrFail($id);
        $elemento = $alumno->Dual;
        $elemento->setInputType('idAlumno', ['type' => 'hidden', 'disableAll' => 'disableAll']);
        $elemento->setInputType('idColaboracion', ['disabled' => 'disabled']);
        $elemento->desde = $alumno->desde;
        $elemento->hasta = $alumno->hasta;
        $elemento->horas = $alumno->horas;
        $elemento->beca = $alumno->beca;
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;

        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $idFct = DB::transaction(function() use ($request, $id) {
                    $alumno = AlumnoFct::findOrFail($id);
                    $elemento = $alumno->Dual;

                    $alumno->desde = FechaInglesa($request['desde']);
                    $alumno->hasta = FechaInglesa($request['hasta']);
                    $alumno->horas = $request['horas'];
                    $alumno->beca = $request['beca'];
                    $alumno->save();
                    $elemento->idInstructor = $request['idInstructor'];
                    $elemento->save();

                    return $elemento->id;
                });

        return $this->redirect();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $idFct = DB::transaction(function() use ($request) {
                    $idAlumno = $request['idAlumno'];
                    $hasta = $request['hasta'];
                    $elemento = Dual::where('idColaboracion', $request->idColaboracion)
                            ->where('asociacion', 3)
                            ->first();

                    if (!$elemento) {
                        $elemento = new Dual();
                        $this->validateAll($request, $elemento);
                        $id = $elemento->fillAll($request);
                    }
                    $elemento->Alumnos()->attach($idAlumno, ['desde' => FechaInglesa($request->desde), 'hasta' => FechaInglesa($hasta), 'horas' => $request->horas, 'beca' => $request->beca]);

                    return $elemento->id;
                });

        return $this->redirect();
    }



}
