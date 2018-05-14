<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Profesor;
use Intranet\Entities\GrupoTrabajo;
use Intranet\Entities\Miembro;
use Response;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Session;

class GrupoTrabajoController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'GrupoTrabajo';
    protected $gridFields = ['literal',  'objetivos'];
    protected $modal = true;

    
    protected function seach()
    {
        return GrupoTrabajo::MisGruposTrabajo()->get();
    }

    public function detalle($id)
    {
//        $tGrupos = Grupo::pluck('nombre', 'codigo')->toArray();
        $Profesores = Profesor::select('apellido1', 'apellido2', 'nombre', 'dni')
                ->OrderBy('apellido1')
                ->OrderBy('apellido2')
                ->get();
        foreach ($Profesores as $profesor) {
            $tProfesores[$profesor->dni] = $profesor->apellido1 . ' ' . $profesor->apellido2 . ',' . $profesor->nombre;
        }

        $Gt = GrupoTrabajo::find($id);
        $sProfesores = $Gt->profesores()->orderBy('apellido1')->orderBy('apellido2')->get(['dni', 'apellido1', 'apellido2', 'nombre', 'coordinador']);

        // $sGrupos = $Actividad->grupos()->get(['codigo', 'nombre']);
        return view('grupotrabajo.miembros', compact('Gt', 'tProfesores', 'sProfesores'));
    }

    public function altaProfesor(Request $request, $gt_id)
    {
        $ExtGrupo = Miembro::create($request->all());
        return redirect()->route('grupotrabajo.detalle', ['grupotrabajo' => $gt_id]);
    }

    public function borrarProfesor($gt_id, $profesor_id)
    {
        Miembro::where('idGrupoTrabajo', '=', $gt_id)
                ->where('idProfesor', '=', $profesor_id)
                ->where('coordinador',0)
                ->delete();
        
        return redirect()->route('grupotrabajo.detalle', ['grupo_trabajo' => $gt_id]);
    }

    public function coordinador($grupo_id, $profesor_id)
    {
        $coordActual = Miembro::where('idGrupoTrabajo', '=', $grupo_id)
                ->where('coordinador', '=', '1')
                ->first();
        if ($coordActual) {
            $coordActual->coordinador = 0;
            $coordActual->save();
        }
        $coordActual = Miembro::where('idGrupoTrabajo', '=', $grupo_id)
                ->where('idProfesor', '=', $profesor_id)
                ->first();
        $coordActual->coordinador = 1;
        $coordActual->save();
        return redirect()->route('grupotrabajo.detalle', ['grupotrabajo' => $grupo_id]);
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create'], ['delete']);
        $this->panel->setBoton('grid', new BotonImg('#', ['img' => 'fa-pencil', 'class' => 'editGrupo', 'text' => 'edita']));
        $this->panel->setBoton('grid', new BotonImg('grupotrabajo.detalle', ['img' => 'fa-group', 'text' => 'participantes']));
    }

}
