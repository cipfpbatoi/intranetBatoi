<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Falta;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use \DB;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Illuminate\Http\Request;
use Intranet\Entities\Profesor;
use Intranet\Entities\Documento;
use PDF;

class FaltaController extends IntranetController
{

    use traitImprimir,
        traitNotificar,
        traitCRUD,
        traitAutorizar;

    protected $perfil = 'profesor';
    protected $model = 'Falta';
    protected $gridFields = ['id', 'desde', 'hasta', 'motivo', 'situacion'];
    protected $modal = true;

    
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('falta.delete', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('falta.edit', ['where' => ['estado', '<', '3']]));
        $this->panel->setBoton('grid', new BotonImg('falta.init', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('falta.notification', ['where' => ['estado', '>', '0', 'hasta', 'posterior', Ayer()]]));
        $this->panel->setBoton('grid', new BotonImg('falta.document', ['where' => ['fichero', '!=', '']]));
    }

    
    public function store(Request $request)
    {
        $request->baja = isset($request->baja)?$request->baja:0;
        if ($request->baja) {
            $request->hora_ini = null;
            $request->hora_fin = null;
            $request->hasta = '';
            $request->dia_completo = 1;
            $request->estado = 5;
            Profesor::Baja($request->idProfesor, $request->desde);
            parent::realStore($request);
        } else {
            $dia_completo = $request->dia_completo == '' ? '0' : '1';
            $request->hora_ini = $dia_completo ? null : $request->hora_ini;
            $request->hora_fin = $dia_completo ? null : $request->hora_fin;
            $request->hasta = esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta;
            $id = parent::realStore($request);
            if (UserisAllow(config('constants.rol.direccion'))) $this->init($id); // si es direcció autoritza
        }
        return $this->redirect();
    }

    public function update(Request $request, $id)
    {
        $request->dia_completo = isset($request->dia_completo)?1:0;
        $request->hora_ini = $request->dia_completo ? null : $request->hora_ini;
        $request->hora_fin = $request->dia_completo ? null : $request->hora_fin;
        $request->hasta = esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta;
        $elemento = Falta::find(parent::realStore($request,$id));
        if ($elemento->estado == 1 && $elemento->fichero) Falta::putEstado($id,2); // si estava enviat i he pujat fitxer
        return $this->redirect();
    }

    public function init($id)
    {
        $elemento = Falta::findOrFail($id);
        if ($elemento->fichero) Falta::putEstado($id,2);
        else Falta::putEstado($id,1);
        
        return $this->redirect();
    }

    public function alta($id)
    {
        $elemento = Falta::findOrFail($id);
        DB::transaction(function() use ($elemento){
            $elemento->estado = 3;
            $elemento->hasta = new Date();
            $elemento->baja = 0;
            $elemento->save();
            // quita la  baja del profesor
            Profesor::Baja($elemento->idProfesor);
        });
        return back()->with('pestana', $elemento->estado);
    }
    
    public function imprime_falta(Request $request)
    {
        if ($request->mensual == 'on') {
            $nom = 'Falta' . new Date() . '.pdf';
            $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
            Documento::crea(null, ['fichero' => $nomComplet, 'tags' => "Falta listado llistat autorizacion autorizacio"]);

            // pendientes pasan a ser impresas
            // todas las faltas hasta la fecha no impresas y comunicadas
            $pendientes = Falta::where([
                        ['estado', '>', '0'],
                        ['estado', '<', '4'],
                        ['hasta', '<=', new Date($request->hasta)]
                    ])->get();
            // faltas que empiezan entre las fechas
            // faltas que acaban entre las fechas
            // faltas de larga duracion
            $todos = Falta::where([
                        ['estado', '>', '0'],
                        ['estado', '<', '4'],
                        ['desde', '>=', new Date($request->desde)],
                        ['desde', '<=', new Date($request->hasta)]
                    ])
                    ->orwhere([
                        ['estado', '>', '0'],
                        ['estado', '<', '4'],
                        ['hasta', '>=', new Date($request->desde)],
                        ['hasta', '<=', new Date($request->hasta)]
                    ])
                    ->orwhere([['estado', '=', '5'],
                        ['desde', '<=', new Date($request->hasta)]])
                    ->orderBy('idProfesor')
                    ->orderBy('desde')
                    ->get();
            $this->makeAll($pendientes, '_print');
            return $this->hazPdf("pdf.faltas", $todos)
                            ->save(storage_path('/app/' . $nomComplet))
                            ->download($nom);
        } else {
            $todos = Falta::where('estado', '>', '0')
                    ->where('estado', '<', '5')
                    ->whereBetween('desde', [new Date($request->desde), new Date($request->hasta)])
                    ->orWhereBetween('hasta', [new Date($request->desde), new Date($request->hasta)])
                    ->orwhere([['estado', '=', '5'],
                        ['desde', '<=', new Date($request->hasta)]])
                    ->orderBy('idProfesor')
                    ->orderBy('desde')
                    ->get();
            return $this->hazPdf("pdf.faltas", $todos)->stream();
        }
    }
}
