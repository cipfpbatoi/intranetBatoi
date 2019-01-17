<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Jenssegers\Date\Date;
use Intranet\Entities\Expediente;
use Intranet\Entities\Documento;
use Styde\Html\Facades\Alert;
use Intranet\Entities\TipoExpediente;

class ExpedienteController extends IntranetController
{

    use traitImprimir,
        traitAutorizar;

    protected $gridFields = ['id', 'nomAlum', 'fecha', 'Xtipo', 'Xmodulo', 'situacion'];
    protected $perfil = 'profesor';
    protected $model = 'Expediente';
    protected $modal = true;

    protected function iniBotones()
    {
        $this->panel->setBotonera([]);
        $this->panel->setBoton('index', new BotonBasico('expediente.create'));
        $this->panel->setBoton('grid', new BotonImg('expediente.pdf', ['where' => ['estado', '==', '2']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.delete', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.edit', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.init', ['where' => ['estado', '==', '0','esInforme','==','0']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.pdf', ['where' => ['esInforme', '==', 1]]));
    }

    public function autorizar()
    {
        $this->makeAll(Expediente::where('estado', '1')->get(), 2);
        return back();
    }

    //inicializat a init (normalment 1)
    protected function init($id)
    {
        $expediente = Expediente::find($id);
            // orientacion
        if ($expediente->tipoExpediente->orientacion){
            $mensaje = $expediente->explicacion.' .Grup '.$expediente->Alumno->Grupo->first()->nombre;
            Expediente::putEstado($id, 4, $mensaje);
        }
        else
            Expediente::putEstado($id,1);
        return back();
    }

    protected function pasaOrientacion($id)
    {
        $expediente = Expediente::find($id);
        Expediente::putEstado($id, 5);
        $expediente->fechasolucion = Hoy();
        $expediente->save();

        return back();
    }
    
    public function pdf($id)
    {
        $expediente = Expediente::find($id);
        return self::hazPdf("pdf.expediente.$expediente->tipo",$expediente)->stream();
    }

    public function imprimir()
    {
        if (Expediente::listos()->Count()){
            foreach (TipoExpediente::all() as $tipo) {
                $todos = Expediente::listos()->where('tipo', $tipo->id);
                if ($todos->Count()) {
                    $pdf = $this->hazPdf("pdf.expediente.$tipo->id", $todos);
                    $nom = $this->model . new Date() . '.pdf';
                    $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
                    $doc = Documento::crea(null, ['fichero' => $nomComplet, 'tags' => "listado llistat expediente expedient $tipo->titulo"]);
                    $this->makeAll($todos, '_print');
                    $this->makeLink($todos,$doc);
                    $pdf->save(storage_path('/app/' . $nomComplet));
                    return response()->download(storage_path('/app/' . $nomComplet), $nom);
                }
            } 
        }
        else 
        {
            Alert::info(trans('messages.generic.empty'));
            return back();
        }
    }
    
}
