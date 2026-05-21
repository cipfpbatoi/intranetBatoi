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
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Controlador del panell de presència per a revisar fitxatges pendents.
 */
class PanelPresenciaController extends BaseController
{
    /**
     * Model associat al panell.
     *
     * @var string
     */
    protected $model = 'Profesor';

    /**
     * Camps visibles en el llistat de professorat pendent de fitxar.
     *
     * @var array<int, string>
     */
    protected $gridFields = ['Xdepartamento', 'NameFull', 'email'];

    /**
     * Vistes específiques del panell.
     *
     * @var array<string, string>
     */
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
    
    /**
     * Mostra el llistat de professorat que no ha fitxat en un dia concret.
     *
     * @param string|null $dia Data rebuda com a paràmetre de ruta.
     * @return \Illuminate\Contracts\View\View
     */
    public function indice($dia = null)
    {
        Gate::authorize('manageAttendance', Profesor::class);
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $dia = $this->diaSeleccionat($dia);
        $dnisIncidencia = $this->dnisConIncidenciaDia($dia);
        $fdia = new Carbon($dia);
        $this->panel->dia = $fdia->toDateString();
        $this->panel->anterior = $fdia->subDay()->toDateString();
        $this->panel->posterior = $fdia->addDays(2)->toDateString();
        $this->panel->setBoton('grid', new BotonImg('fichar.delete', [], 'direccion', $this->panel->dia));
        $this->panel->setBoton('grid', new BotonImg('fichar.email', [], 'direccion', $this->panel->dia));

        $profesores = self::profesores()->byDnis(array_values(self::noHanFichado($dia)))
            ->map(static function ($profesor) use ($dnisIncidencia) {
                if (in_array((string) $profesor->dni, $dnisIncidencia, true)) {
                    $profesor->class = trim(((string) ($profesor->class ?? '')) . ' text-danger');
                }

                return $profesor;
            });

        return $this->grid($profesores);
    }

    /**
     * Resol la data de consulta prioritzant la ruta i acceptant `?dia=YYYY-MM-DD`.
     *
     * @param string|null $dia Data rebuda per la ruta.
     * @return string
     */
    protected function diaSeleccionat(?string $dia): string
    {
        $candidata = $dia ?: request()->query('dia');

        if (is_string($candidata) && $this->esDiaValid($candidata)) {
            return $candidata;
        }

        return Hoy();
    }

    /**
     * Comprova que la data tinga format ISO i represente un dia real.
     *
     * @param string $dia
     * @return bool
     */
    private function esDiaValid(string $dia): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dia)) {
            return false;
        }

        [$any, $mes, $jorn] = array_map('intval', explode('-', $dia));

        return checkdate($mes, $jorn, $any);
    }

    /**
     * Envia un avís al professorat que té fitxatge pendent.
     *
     * @param string $usuario DNI del professorat.
     * @param string $dia Data del fitxatge pendent.
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * Registra manualment el fitxatge d'un professor per al dia indicat.
     *
     * @param string $usuario DNI del professorat.
     * @param string $dia Data a regularitzar.
     * @param \Intranet\Services\HR\FitxatgeService $fitxatgeService Servei de fitxatge.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteDia($usuario, $dia, FitxatgeService $fitxatgeService)
    {
        Gate::authorize('manageAttendance', Profesor::class);
        $fitxatgeService->fitxaDiaManual($usuario, $dia);
        return back();
    }

    /**
     * Calcula el professorat actiu que no ha fitxat i no té justificació registrada.
     *
     * @param string $dia Data de consulta.
     * @return array<string, string>
     */
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

        return $noHanFichado;
    }

    /**
     * Retorna els DNIs amb incidència visual al panell de presència.
     *
     * Inclou comissions, absències i activitats fora del centre.
     *
     * @param string $dia Data de consulta.
     * @return array<int, string>
     */
    private function dnisConIncidenciaDia(string $dia): array
    {
        $dnis = [];

        $actividades = Actividad::Dia($dia)->where('fueraCentro', '=', 1)->with('profesores:dni')->get();
        foreach ($actividades as $actividad) {
            foreach ($actividad->profesores as $profesor) {
                $dnis[] = (string) $profesor->dni;
            }
        }

        $comisiones = self::comisions()->byDay($dia);
        foreach ($comisiones as $comision) {
            $dnis[] = (string) $comision->idProfesor;
        }

        $faltas = Falta::Dia($dia)->pluck('idProfesor')->all();
        foreach ($faltas as $dni) {
            $dnis[] = (string) $dni;
        }

        return array_values(array_unique($dnis));
    }
    
}
