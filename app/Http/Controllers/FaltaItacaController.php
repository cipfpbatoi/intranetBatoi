<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Hora;
use Intranet\Entities\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Services\GestorService;
use Intranet\Services\StateService;
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
        return view('falta.itaca', compact('profesor', 'horarios', 'horas'));
    }
    
    public static function printReport($request)
    {
        $elementos = self::findElements($request->desde, $request->hasta);

        if ($request->mensual != 'on') {
            return self::hazPdf("pdf.comunicacioBirret", $elementos)->stream();
        }

        self::deleteFile($nomComplet = self::nameFile($request->desde));
        $gestor = new GestorService();

        $doc = $gestor->save(['fichero' => $nomComplet, 'tags' => "Birret listado llistat autorizacion autorizacio"]);
        self::makeLink($elementos, $doc);
        return self::hazPdf("pdf.birret", $elementos)
                        ->save(storage_path('/app/' . $nomComplet))
                        ->download($nomComplet);

    }

    private static function deleteFile(String $nomComplet)
    {
        if ($doc = Documento::where('fichero', $nomComplet)->first()) {
            unlink(storage_path('app/' . $doc->fichero));
            $doc->delete();
        }
    }

    private static function findElements(String $desde, String $hasta)
    {
        return Falta_itaca::where([
            ['estado', '2'],
            ['dia', '>=', FechaInglesa($desde)],
            ['dia', '<=',FechaInglesa($hasta)]
        ])->join('profesores', 'profesores.dni', '=', 'faltas_itaca.idProfesor')
            ->orderBy('profesores.apellido1')
            ->orderBy('profesores.apellido2')
            ->orderBy('profesores.nombre')
            ->orderBy('dia')->get();
    }

    private static function nameFile(String $desde)
    {
        $fecha = new Date($desde);
        return 'gestor/' . Curso() . '/informes/' . 'Birret' . $fecha->format('M') . '.pdf';
    }

    public function resolve($id)
    {
        $falta = Falta_itaca::find($id);

        $faltesDia = Falta_itaca::where('idProfesor', $falta->idProfesor)->
            where('dia', FechaInglesa($falta->dia))->where('estado', 1)->get();
        foreach ($faltesDia as $falta_hora) {
            $staSer = new StateService($falta_hora);
            $staSer->resolve();
        }
        return $this->follow(1, 1);
    }

    public function refuse($id, Request $request)
    {
        $falta = Falta_itaca::find($id);

        $faltesDia = Falta_itaca::where('idProfesor', $falta->idProfesor)->
            where('dia', FechaInglesa($falta->dia))->get();
        foreach ($faltesDia as $falta_hora) {
            $staSer = new StateService($falta_hora);
            $staSer->refuse($request->explicacion);
        }
        return $this->follow(2, 1);
    }
}
