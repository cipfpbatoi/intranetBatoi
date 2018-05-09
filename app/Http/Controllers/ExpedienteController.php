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

class ExpedienteController extends IntranetController
{

    use traitImprimir,traitCRUD,traitAutorizar;

    protected $gridFields = ['id', 'nomAlum', 'fecha', 'Xtipo', 'Xmodulo', 'situacion'];
    protected $perfil = 'profesor';
    protected $model = 'Expediente';
    protected $modal = true;
    

    protected function iniBotones()
    {
        $this->panel->setBotonera([]);
        $this->panel->setBoton('index', new BotonBasico('expediente.create', ['roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('grid', new BotonImg('expediente.pdf', ['where' => ['estado', '==', '2']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.delete', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.edit', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('expediente.init', ['where' => ['estado', '==', '0']]));
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
        if ($expediente->tipo == 4)
            Expediente::putEstado($id,4);
        else
            Expediente::putEstado($id,1);
        return back();
    }
    protected function pasaOrientacion($id)
    {
        Expediente::putEstado($id,5);
        $expediente = Expediente::find($id);
        $expediente->fechasolucion = Hoy();
        $expediente->save();
        
        return back();
    }

    public function imprimir()
    {
        if ($this->class::listos()->Count())
            foreach (config('constants.tipoExpediente') as $index => $valor) {
                $todos = $this->class::listos()->where('tipo', $index);
                if ($todos->Count()) {
                    $pdf = $this->hazPdf("pdf.expediente.$index", $todos);
                    $nom = $this->model . new Date() . '.pdf';
                    $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
                    Documento::crea(null, ['fichero' => $nomComplet, 'tags' => "listado llistat expediente expediente $valor"]);
                    $this->makeAll($todos, '_print');
                    $pdf->save(storage_path('/app/' . $nomComplet));
                    return response()->download(storage_path('/app/' . $nomComplet), $nom);
                }
            } 
        else 
        {
            Alert::info(trans('messages.generic.empty'));
            return back();
        }
    }

}
