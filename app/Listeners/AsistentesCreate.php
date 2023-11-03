<?php

namespace Intranet\Listeners;

use Intranet\Events\ReunionCreated;
use Intranet\Entities\Reunion;
use Intranet\Entities\Profesor;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFctAval;

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


    private function queAlumnes($reunion)
    {
        $grupo = $reunion->GrupoClase;

        if ($reunion->extraOrdinaria) {
            if ($grupo->curso == 2) {
                return $grupo
                    ->Alumnos
                    ->whereNotIn('nia', hazArray(AlumnoFctAval::misFcts()->titulan()->get(), 'idAlumno'));
            } else {
                return $grupo->Alumnos;
            }
        }
        return [];
    }

    private function assignaAlumnes($reunion)
    {
        foreach ($this->queAlumnes($reunion) as $alumno) {
            $reunion->alumnos()->attach($alumno->nia, ['capacitats' => 3]);
        }
    }


    public function handle(ReunionCreated $event)
    {
        if (authUser()) {
            $reunion = $event->reunion;
            $tipo = $reunion->Tipos();
            switch ($tipo->colectivo) {
                case 'Departamento' :
                    $profesores = Profesor::Plantilla()->where('departamento', '=', authUser()->departamento)->get();
                    break;
                case 'Profesor' :
                    $profesores = Profesor::Plantilla()->get();
                    break;
                case 'GrupoTrabajo':
                    $profesores = Profesor::GrupoT($reunion->grupo)->get();
                    break;
                case 'Grupo':
                    $profesores = Profesor::Grupo($reunion->GrupoClase->codigo)->get();
                    $this->assignaAlumnes($reunion);
                    break;
                case 'Jefe' :
                    $profesores = $this->esJefe();
                    break;
                default:
                    $profesores = [];
            }
            $this->asignaProfeReunion($profesores, $reunion);
        }
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
