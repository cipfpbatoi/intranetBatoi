<?php

namespace Intranet\Http\Controllers;

use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Falta_profesor;
use Intranet\Entities\Profesor;
use Intranet\Entities\Actividad;
use Intranet\Entities\Comision;
use Intranet\Entities\Falta;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Intranet\Services\FitxatgeService;
use Jenssegers\Date\Date;
use Carbon\Carbon;
use Intranet\Entities\Horario;
use Intranet\Componentes\Pdf;
use Styde\Html\Facades\Alert;

class PanelPresenciaController extends BaseController
{
    
    protected $model = 'Profesor';
    protected $gridFields = ['Xdepartamento', 'NameFull', 'email'];
    protected $vista = ['index' => 'llist.ausencia'];
    
    
    public function indice($dia = null)
    {
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $dia = $dia ? $dia : Hoy();
        $fdia = new Date($dia);
        $this->panel->dia = $fdia->toDateString();
        $this->panel->anterior = $fdia->subDay()->toDateString();
        $this->panel->posterior = $fdia->addDays(2)->toDateString();
        $this->panel->setBoton('grid', new BotonImg('fichar.delete', [], 'direccion', $this->panel->dia));
        $this->panel->setBoton('grid', new BotonImg('fichar.email', [], 'direccion', $this->panel->dia));
        
        return $this->grid(Profesor::whereIn('dni', self::noHanFichado($dia))->get());
    }

    public function email($usuario, $dia)
    {
        // Busca el/la professor/a pel DNI
        $profesor = Profesor::where('dni', $usuario)->first();

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
        $fitxatgeService->fitxaDiaManual($usuario, $dia);
        return back();
    }

    public static function noHanFichado($dia)
    {
        $profesores = Profesor::select('dni')->Activo()->get();
        
        // mira qui no ha fitxat
        $noHanFichado = [];
        foreach ($profesores as $profesor) {
            if (Falta_profesor::haFichado($dia, $profesor->dni)->count() == 0) {
                if (Horario::Profesor($profesor->dni)->Dia(nameDay(new Date($dia)))->count() > 1) {
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
        $comisiones = Comision::Dia($dia)->get();
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
