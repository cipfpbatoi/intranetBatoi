<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Hora;
use Intranet\Entities\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonIcon;
use Jenssegers\Date\Date;
use Intranet\Entities\Falta_itaca;
use Intranet\Entities\Documento;

class FaltaItacaController extends IntranetController
{
    use traitAutorizar,traitImprimir;
    
    protected $perfil = 'profesor';
    protected $model = 'Falta_itaca';
    
    public function index()
    {
        Session::forget('redirect');
        $profesor = AuthUser();
        $horarios = Horario::Profesor($profesor->dni)->get();
        $horas = Hora::all();
        return view('falta.itaca', compact('profesor','horarios', 'horas'));
    }
    
    public function imprime_birret(Request $request)
    {
        $desde = new Date($request->desde);
        $hasta = new Date($request->hasta);
        //$todos = Falta_itaca::all();
        $todos = Falta_itaca::where([
                        ['estado', '2'],
                        ['dia', '>=', $desde->format('Y-m-d')],
                        ['dia', '<=', $hasta->format('Y-m-d')]
                    ])->orderBy('idProfesor')->orderBy('dia')->get();
        if ($request->mensual == 'on') {
            $nom = 'Birret' . $desde->format('F') . '.pdf';
            $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
            if ($doc = Documento::where('fichero',$nomComplet)->first()){
                unlink(storage_path('app/' . $doc->fichero));
                $doc->delete();
            }
            $doc = Documento::crea(null, ['fichero' => $nomComplet, 'tags' => "Birret listado llistat autorizacion autorizacio"]);
            $this->makeLink($todos, $doc);
            return $this->hazPdf("pdf.birret", $todos)
                            ->save(storage_path('/app/' . $nomComplet))
                            ->download($nom);
        } else {
            return $this->hazPdf("pdf.birret", $todos)->stream();
        }
    }
}
