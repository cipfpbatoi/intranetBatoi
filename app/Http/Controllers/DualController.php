<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Intranet\Entities\Dual;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonImg;
use Intranet\Botones\Panel;
use Intranet\Entities\Documento;
use Intranet\Entities\AlumnoFct;
use Jenssegers\Date\Date;
use Intranet\Entities\Fct;

class DualController extends IntranetController
{

    use traitImprimir;
    
    protected $perfil = 'profesor';
    protected $model = 'Dual';
    protected $modal = false;

    public function edit($id)
    {
        $alumno = AlumnoFct::findOrFail($id);
        $elemento = $alumno->Dual;
        $elemento->setInputType('idAlumno', ['type' => 'hidden', 'disableAll' => 'disableAll']);
        $elemento->setInputType('idColaboracion', ['disabled' => 'disabled']);
        $elemento->desde = $alumno->desde;
        $elemento->hasta = $alumno->hasta;
        $elemento->horas = $alumno->horas;
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;

        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }

    public function update(Request $request, $id)
    {
        $idFct = DB::transaction(function() use ($request, $id) {
                    $alumno = AlumnoFct::findOrFail($id);
                    $elemento = $alumno->Dual;

                    $alumno->desde = FechaInglesa($request['desde']);
                    $alumno->hasta = FechaInglesa($request['hasta']);
                    $alumno->horas = $request['horas'];
                    $alumno->save();
                    $elemento->idInstructor = $request['idInstructor'];
                    $elemento->save();

                    return $elemento->id;
                });

        return $this->redirect();
    }

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
                    $elemento->Alumnos()->attach($idAlumno, ['desde' => FechaInglesa($request->desde), 'hasta' => FechaInglesa($hasta), 'horas' => $request->horas]);

                    return $elemento->id;
                });

        return $this->redirect();
    }

    public function anexeVI()
    {
        $fct = AlumnoFct::misDual()->get();
        $empresas = Fct::misFcts()->esDual()->count();
        $grupo = $fct->first()->Alumno->Grupo->first();
        $alumnos = $grupo->Alumnos->where('sexo','H')->count();
        $alumnas = $grupo->Alumnos->where('sexo','M')->count();
        $dualH = 0; $dualM = 0;
        foreach ($fct as $dual){
            if ($dual->Alumno->sexo == 'H') $dualH++; else $dualM++;
        }
        $dades = ['date' => Hoy(),
            'grupo' => $grupo,
            'alumnos' => $alumnos,
            'alumnas' => $alumnas,
            'dualH' => $dualH,
            'dualM' => $dualM,
            'empresas' => $empresas,
        ];

        $pdf = $this->hazPdf('dual.anexe_vi', $fct, $dades, 'portrait', 'a4', 10);
        return $pdf->stream();
    }

}
