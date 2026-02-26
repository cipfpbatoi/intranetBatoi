<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Comision\ComisionService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\BaseController;

use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Actividad;
use Intranet\Entities\Falta;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Gate;
use Intranet\Services\HR\FitxatgeService;
use Illuminate\Support\Carbon;
use Styde\Html\Facades\Alert;

class PanelPresenciaController extends BaseController
{
    protected $model = 'Profesor';
    protected $gridFields = ['Xdepartamento', 'NameFull', 'email'];
    protected $vista = ['index' => 'llist.ausencia'];

    private static function comisions(): ComisionService
    {
        return app(ComisionService::class);
    }

    private static function profesores(): ProfesorService
    {
        return app(ProfesorService::class);
    }

    private static function horarios(): HorarioService
    {
        return app(HorarioService::class);
    }
    
    
    public function indice($dia = null)
    {
        Gate::authorize('manageAttendance', Profesor::class);
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $dia = $dia ? $dia : Hoy();
        $fdia = new Carbon($dia);
        $this->panel->dia = $fdia->toDateString();
        $this->panel->anterior = $fdia->subDay()->toDateString();
        $this->panel->posterior = $fdia->addDays(2)->toDateString();
        $this->panel->setBoton('grid', new BotonImg('fichar.delete', [], 'direccion', $this->panel->dia));
        $this->panel->setBoton('grid', new BotonImg('fichar.email', [], 'direccion', $this->panel->dia));
        
        return $this->grid(self::profesores()->byDnis(array_values(self::noHanFichado($dia))));
    }

    public function email($usuario, $dia)
    {
        Gate::authorize('manageAttendance', Profesor::class);
        // Busca el/la professor/a pel DNI
        $profesor = self::profesores()->find((string) $usuario);

        if (!$profesor || empty($profesor->email)) {
            Alert::warning('No s\'ha pogut enviar el correu. El professor no existeix o no té correu electrònic assignat.');
            return back();
        }

        // Format amable de la data (ca_ES)
        $dataFormatejada = Carbon::parse($dia)->locale('ca')->isoFormat('dddd D [de] MMMM [de] YYYY');

        // Cos del missatge (si no vols Blade)
        $cos = "Hola {$profesor->nombre},\n\n"
            . "Hem vist que no has fitxat el dia {$dataFormatejada}. "
            . "Pots confirmar-nos si ha sigut un oblit o, pel contrari, no vas vindre al centre?\n\n"
            . "Salutacions,\nCIPFP Batoi";

        // Envia
        Mail::raw($cos, function ($message) use ($profesor, $dataFormatejada) {
            $message->to($profesor->email, $profesor->nombre);

            // Assumpte
            $message->subject("Fitxatge pendent — {$dataFormatejada}");

            // Remitent i Reply-To (el que demanes)
            $message->from('03012165.info@edu.gva.es', 'CIPFP Batoi - Caporalia');
            $message->replyTo('03012165.info@edu.gva.es', 'CIPFP Batoi - Caporalia');
        });

        return back();
    }


    public function deleteDia($usuario, $dia, FitxatgeService $fitxatgeService)
    {
        Gate::authorize('manageAttendance', Profesor::class);
        $fitxatgeService->fitxaDiaManual($usuario, $dia);
        return back();
    }

    public static function noHanFichado($dia)
    {
        $fitxatgeService = app(FitxatgeService::class);
        $profesores = self::profesores()->activosOrdered();
        
        // mira qui no ha fitxat
        $noHanFichado = [];
        foreach ($profesores as $profesor) {
            if (!$fitxatgeService->hasFichado($dia, (string) $profesor->dni)) {
                if (self::horarios()->countByProfesorAndDay((string) $profesor->dni, nameDay(new Carbon($dia))) > 1) {
                    $noHanFichado[$profesor->dni] = $profesor->dni;
                }
            }
        }


        // comprova que no estigues d'activitat
        $actividades = Actividad::Dia($dia)->where('fueraCentro','=',1)->get();
        foreach ($actividades as $actividad) {
            foreach ($actividad->profesores as $profesor) {
                if (in_array($profesor->dni, $noHanFichado)) {
                    unset($noHanFichado[$profesor->dni]);
                }
            }
        }

        // comprova que no està de comissió
        $comisiones = self::comisions()->byDay($dia);
        foreach ($comisiones as $comision) {
            if (in_array($comision->idProfesor, $noHanFichado)) {
                unset($noHanFichado[$comision->idProfesor]);
            }
        }

        // compova que no tinga falta
        $faltas = Falta::Dia($dia)->get();
        foreach ($faltas as $falta) {
            if (in_array($falta->idProfesor, $noHanFichado)) {
                unset($noHanFichado[$falta->idProfesor]);
            }
        }
        
        return $noHanFichado;
    }
    
}
