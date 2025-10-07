<?php

namespace Intranet\Listeners;

use Intranet\Events\ReunionCreated;
use Intranet\Entities\Reunion;
use Intranet\Entities\Profesor;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFctAval;
use Illuminate\Support\Facades\Log;

class AsistentesCreate
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReunionCreated  $event
     * @return void
     */
    private function queAlumnes(Reunion $reunion)
    {
        $grupo = $reunion->GrupoClase; // pot ser null

        // Si no hi ha grup, no hi ha alumnes que afegir
        if (!$grupo) {
            Log::warning("AsistentesCreate: Reunion {$reunion->id} sense GrupoClase; no s'assignen alumnes.");
            return collect(); // col·lecció buida
        }

        if ($reunion->extraOrdinaria) {
            if ($grupo->curso == 2) {
                return $grupo->Alumnos
                    ->whereNotIn('nia', hazArray(AlumnoFctAval::misFcts()->titulan()->get(), 'idAlumno'));
            }
            return $grupo->Alumnos;
        }

        return collect();
    }


    /**
     * @param  Reunion  $reunion
     * @return void
     */
    private function assignaAlumnes(Reunion $reunion): void
    {
        foreach ($this->queAlumnes($reunion)?? [] as $alumno) {
            // evitem duplicats al pivot
            if (!$reunion->alumnos()->wherePivot('alumno_nia', $alumno->nia)->exists()) {
                $reunion->alumnos()->attach($alumno->nia, ['capacitats' => 3]);
            }
        }
    }


    /**
     * Handle the event.
     *
     * @param  ReunionCreated  $event
     * @return void
     */

    public function handle(ReunionCreated $event): void
    {
        if (!authUser()) {
            return;
        }

        $reunion = $event->reunion;
        $tipo = $reunion->Tipos();

        if (!$tipo) {
            Log::warning("AsistentesCreate: Reunion {$reunion->id} sense Tipos(); s'ix silenciosament.");
            return;
        }

        switch ($tipo->colectivo) {
            case 'Departamento':
                $profesores = Profesor::Plantilla()
                    ->where('departamento', '=', authUser()->departamento)
                    ->get();
                break;

            case 'Profesor':
                $profesores = Profesor::Plantilla()->get();
                break;

            case 'GrupoTrabajo':
                // Ja uses $reunion->grupo ací (no relació)
                $profesores = Profesor::GrupoT($reunion->grupo)->get();
                break;

            case 'Grupo':
                // Si tenim relació, agafem el codi; si no, fem fallback a camp pla $reunion->grupo
                $codigoGrupo = $reunion->GrupoClase?->codigo ?? $reunion->grupo ?? null;

                if (!$codigoGrupo) {
                    Log::warning("AsistentesCreate: Reunion {$reunion->id} 'Grupo' sense codigo de Grup; no s'assignen profes/alumnes.");
                    $profesores = collect();
                } else {
                    $profesores = Profesor::Grupo($codigoGrupo)->get();
                    $this->assignaAlumnes($reunion);
                }
                break;

            case 'Jefe':
                $profesores = $this->esJefe();
                break;

            default:
                $profesores = collect();
        }

        $this->asignaProfeReunion($profesores, $reunion);
    }

    /**
     * @return array
     */
    private function esJefe(): array
    {
        $profesores = [];
        foreach (Profesor::Activo()->get() as $profesor) {
            if ($profesor->rol % config('roles.rol.jefe_dpto') == 0 ||
                $profesor->rol % config('roles.rol.direccion') == 0) {
                $profesores[] = $profesor;
            }
        }
        return $profesores;
    }

    /**
     * @param  array  $profesores
     * @param  Reunion  $reunion
     * @return void
     */
    private function asignaProfeReunion($profesores, Reunion $reunion): void
    {
        foreach ($profesores as $profe) {

            if (!empty($profe->sustituye_a) && $profe->sustituye_a != ' ') {
                $reunion->profesores()->attach($profe->sustituye_a, ['asiste' => false]);
            }
            if ($profe->fecha_baja == null){
                $reunion->profesores()->attach($profe->dni, ['asiste' => true]);
            } else {
                $reunion->profesores()->attach($profe->dni, ['asiste' => false]);
            }
        }
    }

}
