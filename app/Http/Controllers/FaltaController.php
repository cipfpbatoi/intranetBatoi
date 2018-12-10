<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Falta;
use Intranet\Entities\Profesor;
use Intranet\Entities\Documento;
use Intranet\Entities\Horario;
use Intranet\Entities\Asistencia;
use Intranet\Entities\Reunion;
use Intranet\Entities\Grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Expediente;
use Intranet\Entities\Resultado;

use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;
use \DB;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Illuminate\Http\Request;
use PDF;

class FaltaController extends IntranetController
{

    use traitImprimir,
        traitNotificar,
        traitAutorizar;

    protected $perfil = 'profesor';
    protected $model = 'Falta';
    protected $gridFields = ['id', 'desde', 'hasta', 'motivo', 'situacion','observaciones'];
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
            DB::transaction(function() use ($request){
                $request->hora_ini = null;
                $request->hora_fin = null;
                $request->hasta = '';
                $request->dia_completo = 1;
                $request->estado = 5;
                $this->tramitaBajaProfesor($request->idProfesor, $request->desde);
                parent::realStore($request);
            });
        } else {
            $dia_completo = $request->dia_completo == '' ? '0' : '1';
            $request->hora_ini = $dia_completo ? null : $request->hora_ini;
            $request->hora_fin = $dia_completo ? null : $request->hora_fin;
            $request->hasta = esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta;
            $id = parent::realStore($request);
            if (UserisAllow(config('roles.rol.direccion'))) $this->init($id); // si es direcció autoritza
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
            $this->tramitaBajaProfesor($elemento->idProfesor);
        });
        return back()->with('pestana', $elemento->estado);
    }
    
    public function imprime_falta(Request $request)
    {
        if ($request->mensual == 'on') {
            $nom = 'Falta' . new Date() . '.pdf';
            $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
            $doc = Documento::crea(null, ['fichero' => $nomComplet, 'tags' => "Ausència Ausencia Llistat listado Professorado Profesorat Mensual"]);

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
            $this->makeLink($todos, $doc);
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
    
    private function tramitaBajaProfesor($id, $fecha = null)
    {
        $profe = Profesor::find($id);
        // Baixa
        if ($fecha){
            $profe->fecha_baja = new Date($fecha);
            $profe->save();
        }
        // Alta
        else {
            DB::transaction(function() use ($profe) {
                $profe->fecha_baja = null;
                $profe->save();
                if ($sustituto = $profe->Sustituye) {
                        //canvi d'horari
                    if (Horario::profesor($profe->dni)->count()==0)
                        Horario::where('idProfesor',$sustituto->dni)->update(['idProfesor'=> $profe->dni]);
                    else
                        Horario::where('idProfesor',$sustituto->dni)->delete();
                               // asistència a reunions
                    foreach (Asistencia::where('idProfesor',$sustituto->dni)->get() as $asistencia){
                        if (Asistencia::where('idProfesor', $profe->dni)->where('idReunion', $asistencia->idReunion)->count() == 0){
                            Reunion::find($asistencia->idReunion)->profesores()->syncWithoutDetaching([$profe->dni=>['asiste'=>0]]);
                        }
                    }
                            // tota la feina del substitut pasa al subtituit
                    Reunion::where('idProfesor',$sustituto->dni)->update(['idProfesor'=>$profe->dni]);
                    Grupo::where('tutor',$sustituto->dni)->update(['tutor'=>$profe->dni]);
                    Programacion::where('idProfesor',$sustituto->dni)->update(['idProfesor' => $profe->dni]);
                    Expediente::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profe->dni]);
                    Resultado::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profe->dni]);
                    
                    $sustituto->sustituye_a = ' ';
                    $sustituto->activo = 0;
                    $sustituto->save();
                }
            });
        }
    }
}
