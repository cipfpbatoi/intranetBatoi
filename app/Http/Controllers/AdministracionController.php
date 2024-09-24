<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;


use Illuminate\Http\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Intranet\Entities\Adjunto;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Espacio;
use Intranet\Entities\Empresa;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Grupo;
use Intranet\Entities\IpGuardia;

use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\VoteAnt;
use Intranet\Entities\Programacion;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\Setting;
use Intranet\Mail\CertificatAlumneFct;
use Intranet\Mail\Comunicado;
use Intranet\Services\ImageService;
use Intranet\Services\SeleniumService;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Storage;
use Intranet\Jobs\SendEmail;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Poll\Vote;
use Intranet\Entities\Fct;
use Illuminate\Http\Request;
use Intranet\Entities\Centro;
use Symfony\Component\Mime\Exception\RfcComplianceException;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class AdministracionController extends Controller
{
    const DIRECTORIO_GESTOR = 'gestor/Empresa/';

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function simplifica()
    {
        if (Session::get('completa')) {
            Session::forget('completa');
        } else {
            Session::put('completa', 1);
        }
        return back();
    }

    /**
     * @param $lang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lang($lang)
    {
        Session::put('lang', $lang);
        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function allApiToken()
    {
        $remitente = ['nombre' => 'Intranet', 'email' => config('contacto.host.email')];
        foreach (Profesor::Activo()->get() as $profesor) {
             try {
                Mail::to($profesor->email)->send(new Comunicado(  $remitente, $profesor,'email.apitoken'  ));
            } catch (RfcComplianceException $e) {
            }
        }
        Alert::info('Correus enviats');
        return back();
    }

    public function cleanCache()
    {
        Alert::info(system('php ./../artisan cache:clear'));
        return back();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function nuevoCursoIndex()
    {
        return view('nuevo.curso');
    }

    private function esborrarProgramacions()
    {
        Programacion::where('curso', '!=', CursoAnterior())->delete();
    }



    private function esborrarEnquestes()
    {
        foreach (Poll::all() as $poll) {
            if (!$poll->remains) {
                $poll->delete();
            }
        }
    }


    private function ferVotsPermanents()
    {
        foreach (Vote::all() as $vote) {
            if ($vote->idPoll == 13 && $fct = Fct::find($vote->idOption1)) {

                $newVote = new VoteAnt([
                    'option_id' => $vote->option_id,
                    'idColaboracion' => $fct->idColaboracion,
                    'value' => $vote->value,
                    'text' => $vote->text,
                    'curs' => CursoAnterior()
                ]);
                $newVote->save();
            }
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */



    protected function nuevoCurso(Request $request)
    {
         if ($request->Vots) {
            $this->ferVotsPermanents();
        }
        if ($request->Auxiliars){
            $tables = ['actividades', 'comisiones', 'cursos', 'expedientes', 'faltas', 'faltas_itaca', 'faltas_profesores',
                'grupos_trabajo', 'guardias',  'incidencias', 'notifications', 'ordenes_trabajo', 'reservas',
                'resultados',   'tutorias_grupos', 'activities',
                'autorizaciones', 'votes' , 'activities', 'failed_jobs'   ];
            foreach ($tables as $tabla) {
                DB::table($tabla)->delete();
            }
        }

        if ($request->Dual) {
            // inicialitza dels col·laboracions
            Colaboracion::where('tutor', '!=', '')->update(['tutor'=>'']);
            Colaboracion::where('estado', '>', 1)->update(['estado' => 1]);

            // inicialitza professors
            Profesor::whereNotNull('fecha_baja')->update(['fecha_baja' => null]);

            //$this->esborrarEnquestes();


            // certificats de fol
            foreach (AlumnoGrupo::with('Grupo')->with('Alumno')->get() as $algr) {
                if ($algr->curso == 2 && $algr->fol > 0) {
                    $alumno = $algr->Alumno;
                    $alumno->fol = 0;
                    $alumno->save();
                }
            }
            foreach (Grupo::all() as $grupo) {
                $grupo->fol = 0;
                $grupo->save();
            }

            // preservar dual
            Adjunto::moveAndPreserveDualFiles();
        }

        if ($request->Esborrat){
            $tables = [
                  'horarios', 'reuniones',  'alumno_resultados','alumnos_grupos',
                 'alumno_fcts','fcts'  ];
            foreach ($tables as $tabla) {
                DB::table($tabla)->delete();
            }
        }

         return back();
    }

    /**
     * @param $fichero
     * @param $enlace
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function help($fichero, $enlace)
    {
        return view('intranet.readme', ['elemento' => mdFind($fichero, $enlace)]);
    }

    /**
     * @param $VersionAntigua
     */
    public static function exe_actualizacion($VersionAntigua)
    {
        foreach (config('constants.version') as $version) {
            if ($version > $VersionAntigua) {
                AdministracionController::$version();
            }
        }
    }


    public static function v3_00()
    {
        Alert::info('Version 3.00');
        $a = config('contacto');
        foreach ($a as $key => $value) {
            if ($value != '') {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $set = new Setting(['collection' => 'contacto','key' => $key.'.'.$k, 'value' => $v]);
                        $set->save();
                    }
                } else {
                    $set = new Setting(['collection' => 'contacto','key' => $key, 'value' => $value]);
                    $set->save();
                }
            }
        }
        $a = config('avisos');
        foreach ($a as $key => $value) {
            if (! is_array($value) && $value != '') {
                $set = new Setting(['collection' => 'avisos','key' => $key, 'value' => $value]);
                $set->save();
            }
        }
        $a = config('variables');
        foreach ($a as $key => $value) {
            if ( $value != '') {
                if (is_array($value) && $key == 'ipGuardias') {
                    foreach ($value as $k => $v) {
                        $ip = new IpGuardia(['ip' => $v['ip'],'codOcup' => $v['codOcup']]);
                        $ip->save();
                    }
                } else {
                    $set = new Setting(['collection' => 'variables', 'key' => $key, 'value' => $value]);
                    $set->save();
                }
            }
        }
        return back();
    }

    public static function v3_01()
    {
       $fcts = AlumnoFct::all();
       foreach ($fcts as $fct) {
           $grupo = $fct->Alumno->Grupo->first() ?? null;
           if ($grupo) {
               if ($grupo->curso == 2) {
                   $fct->idProfesor = $grupo->tutor;
               } else {
                   $fct->idProfesor = $grupo->tutorDual;
               }
               $fct->save();
           }
       }
        Alert::info('Version 3.01');
    }

    public function consulta() {
        $alumnos = Alumno::all();
        $success = 0;

        foreach ($alumnos as $alumno) {
            // Eliminar el prefix si cal
            if (strpos($alumno->foto, 'fotos/') === 0) {
                 $alumno->foto = substr($alumno->foto, strlen('fotos/'));
                 $alumno->save();
                 $success++;
            }
        }

        return response()->json([
            'message' => "$success canvis "
        ]);
    }


    public static function v2_01()
    {
        // partxe per actualitzar professors sense token

        Alert::info('Version 2.01');


    }


    public function importaAnexoI()
    {
        $canvis = 0;
        $nous = 0;
        $malament = 0;
        foreach (Empresa::all() as $elemento) {
            if (isset($elemento->fichero) &&
                strpos($elemento->fichero, '2018-2019') &&
                (file_exists(storage_path('/app/'.$elemento->fichero)))
            ) {
                Storage::put(self::DIRECTORIO_GESTOR.$elemento->cif.'.pdf', Storage::get($elemento->fichero));
                $elemento->fichero = self::DIRECTORIO_GESTOR.$elemento->cif.'.pdf';
                $elemento->save();
                $canvis++;
            } else {
                if (file_exists(storage_path('app/'.self::DIRECTORIO_GESTOR . $elemento->cif.'.pdf'))) {
                    $elemento->fichero = self::DIRECTORIO_GESTOR . $elemento->cif.'.pdf';
                    $elemento->save();
                    $nous++;
                } else {
                    $elemento->fichero = '';
                    $elemento->save();
                    $malament++;
                }
            }

        }
        Alert::info($canvis.' canviats,'.$nous.' nous,'.$malament.' esborrats');
        return back();
    }



    public static function centres_amb_mateixa_adreça()
    {
        echo "<p><strong>Empreses amb centres amb la mateixa adreça</strong></p>";
        foreach (
            Centro::select('idEmpresa')
                ->groupBy('idEmpresa', 'direccion')
                ->havingRaw('COUNT(*) > 1')
                ->get() as $item
        ) {
            echo "<br/>$item->idEmpresa: ".Empresa::find($item->idEmpresa)->nombre;
        }
    }





    public function showDoor()
    {
        $doors = Espacio::whereNotNull('dispositivo')->get();
        return view('espai.show', compact('doors'));
    }

    public function secure(Request $request)
    {
        $user = config('variables.domotica.user');
        $pass =  config('variables.domotica.pass');
        if (esrol(AuthUser()->rol, config('roles.rol.administrador'))) {
            $link = str_replace('{dispositivo}', $request->dispositivo, config('variables.ipDomotica')).'/secure';
            $response = Http::withBasicAuth($user, $pass)->accept('application/json')->post($link, ['args'=>[]]);
            if ($response->successful()) {
                $missatge = 'Porta tancada';
            } else {
                $missatge = "No s'ha pogut tancar la porta: ".$response->status();
            }
        } else {
            $missatge = 'Usuari no autoritzat';
        }
        $doors = Espacio::whereNotNull('dispositivo')->get();
        return view('espai.show', compact('missatge', 'doors'));
    }

    /*public function consulta()
    {
        $alumnosPendientes = AlumnoFct::esErasmus()->get();

            foreach ($alumnosPendientes as $alumno) {
                try {
                    Mail::to($alumno->Alumno->email)->send(new CertificatAlumneFct($alumno));
                    $alumno->correoAlumno = 1;
                    $alumno->save();
                } catch (Exception $e) {
                    Alert::info($e->getMessage());
                }
            }
    }
    */
}
