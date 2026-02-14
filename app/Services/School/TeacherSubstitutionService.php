<?php

namespace Intranet\Services\School;

use Illuminate\Support\Facades\DB;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Asistencia;
use Intranet\Entities\Expediente;
use Intranet\Entities\Grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Resultado;
use Intranet\Entities\Reunion;
use Intranet\Entities\Profesor;
use Jenssegers\Date\Date;

/**
 * Gestiona lògica d'alta/baixa de professorat amb cadena de substitucions.
 */
class TeacherSubstitutionService
{
    private ProfesorService $profesorService;
    private HorarioService $horarioService;

    public function __construct(?ProfesorService $profesorService = null, ?HorarioService $horarioService = null)
    {
        $this->profesorService = $profesorService ?? app(ProfesorService::class);
        $this->horarioService = $horarioService ?? app(HorarioService::class);
    }

    /**
     * Marca un professor com de baixa en una data.
     *
     * @param string $idProfesor
     * @param string $fecha
     * @return void
     */
    public function markLeave(string $idProfesor, string $fecha): void
    {
        $profe = $this->profesorService->find($idProfesor);
        if (!$profe) {
            return;
        }

        $profe->fecha_baja = new Date($fecha);
        $profe->save();
    }

    /**
     * Reactiva un professor i reverteix canvis dels substituts en cadena.
     *
     * @param string $idProfesor
     * @return void
     */
    public function reactivate(string $idProfesor): void
    {
        DB::transaction(function () use ($idProfesor): void {
            $original = $this->profesorService->find($idProfesor);
            if (!$original) {
                return;
            }

            $original->fecha_baja = null;
            $profesor = $original;

            while ($profesor->Sustituye) {
                $this->changeWithSubstitute($original, $profesor->Sustituye);
                $profesor = $profesor->Sustituye;
            }

            $original->save();
        });
    }

    /**
     * Mou càrrega docent/administrativa del substitut al professor original.
     *
     * @param Profesor $profesorAlta
     * @param Profesor $sustituto
     * @return void
     */
    private function changeWithSubstitute(Profesor $profesorAlta, Profesor $sustituto): void
    {
        // Canvi d'horari.
        if ($this->horarioService->byProfesor($profesorAlta->dni)->count() == 0) {
            $this->horarioService->reassignProfesor($sustituto->dni, $profesorAlta->dni);
        } else {
            $this->horarioService->deleteByProfesor($sustituto->dni);
        }

        // Assistència a reunions.
        foreach (Asistencia::where('idProfesor', $sustituto->dni)->get() as $asistencia) {
            $this->markAssistenceMeetings($profesorAlta->dni, $asistencia);
        }

        // Tota la feina del substitut passa al substituït.
        Reunion::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profesorAlta->dni]);
        Grupo::where('tutor', $sustituto->dni)->update(['tutor' => $profesorAlta->dni]);
        Programacion::where('profesor', $sustituto->dni)->update(['profesor' => $profesorAlta->dni]);
        Expediente::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profesorAlta->dni]);
        Resultado::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profesorAlta->dni]);
        AlumnoFct::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profesorAlta->dni]);

        $sustituto->sustituye_a = ' ';
        $sustituto->activo = 0;
        $sustituto->save();
    }

    /**
     * Marca assistència pendent del professor reactiu a una reunió.
     *
     * @param string $dniProfesor
     * @param Asistencia $meeting
     * @return void
     */
    private function markAssistenceMeetings(string $dniProfesor, Asistencia $meeting): void
    {
        if (Asistencia::where('idProfesor', $dniProfesor)->where('idReunion', $meeting->idReunion)->count() == 0) {
            Reunion::find($meeting->idReunion)?->profesores()->syncWithoutDetaching([$dniProfesor => ['asiste' => 0]]);
        }
    }
}
