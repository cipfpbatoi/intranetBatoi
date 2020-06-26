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
    
    public static function printReport(Request $request)
    {
        $elementos = self::findElements($request->desde,$request->hasta);

        if ($request->mensual != 'on') {
            return self::hazPdf("pdf.birret", $elementos)->stream();
        }

        self::deleteFile($nomComplet = self::nameFile($request->desde));

        $doc = Documento::crea(null, ['fichero' => $nomComplet, 'tags' => "Birret listado llistat autorizacion autorizacio"]);
        self::makeLink($elementos, $doc);
        return self::hazPdf("pdf.birret", $elementos)
                        ->save(storage_path('/app/' . $nomComplet))
                        ->download($nomComplet);

    }
    private static function deleteFile(String $nomComplet){
        if ($doc = Documento::where('fichero',$nomComplet)->first()){
            unlink(storage_path('app/' . $doc->fichero));
            $doc->delete();
        }
    }
    private static function findElements(String $desde,String $hasta){
        return Falta_itaca::where([
            ['estado', '2'],
            ['dia', '>=', FechaInglesa($desde)],
            ['dia', '<=',FechaInglesa($hasta)]
        ])->join('profesores','profesores.dni','=','faltas_itaca.idProfesor')
            ->orderBy('profesores.apellido1')
            ->orderBy('profesores.apellido2')
            ->orderBy('profesores.nombre')
            ->orderBy('dia')->get();
    }

    private static function nameFile(String $desde){
        $fecha = new Date($desde);
        return 'gestor/' . Curso() . '/informes/' . 'Birret' . $fecha->format('M') . '.pdf';
    }
}
