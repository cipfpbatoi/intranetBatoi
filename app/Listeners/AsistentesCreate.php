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


    private function queAlumnes($reunion){
        $grupo = $reunion->GrupoClase;

        if ($reunion->avaluacioFinal) return $grupo->Alumnos;

        if ($reunion->extraOrdinaria) {
            if ($grupo->curso == 2)
                return $grupo->Alumnos->whereNotIn('nia', hazArray(AlumnoFctAval::misFcts()->titulan()->get(), 'idAlumno'));
            if ($grupo->isSemi)
                return $grupo->Alumnos;
            if ($actaFinal = Reunion::actaFinal($reunion->idProfesor)->first())
                return $actaFinal->noPromocionan;
            return $grupo->Alumnos;
        }
    }

    private function assignaAlumnes($reunion)
    {
        $capacitat = $reunion->avaluacioFinal?0:3;

        foreach ($this->queAlumnes($reunion) as $alumno)
            $reunion->alumnos()->attach($alumno->nia,['capacitats'=>$capacitat]);
    }


    public function handle(ReunionCreated $event)
    {
        if (AuthUser()) {
            $reunion = $event->reunion;
            if ($reunion->Tipos()['colectivo'] == 'Departamento') {
                $profesores = Profesor::Activo()->where('departamento', '=', AuthUser()->departamento)->get();
            }
            if ($reunion->Tipos()['colectivo'] == 'Profesor') {
                $profesores = Profesor::Activo()->get();
            }
            if ($reunion->Tipos()['colectivo'] == 'GrupoTrabajo') {
                $profesores = Profesor::GrupoT($reunion->grupo)->get();
            }
            if ($reunion->Tipos()['colectivo'] == 'Grupo') {
                $profesores = Profesor::Grupo($reunion->GrupoClase->codigo)->get();
                $this->assignaAlumnes($reunion);
            }
            if ($reunion->Tipos()['colectivo'] == '') {
                $profesores = [];
            }
            if ($reunion->Tipos()['colectivo'] == 'Jefe'){
                $todos = Profesor::Activo()->get();
                $profesores = [];
                foreach ($todos as $uno){
                    if ($uno->rol % config('roles.rol.jefe_dpto') == 0 || $uno->rol % config('roles.rol.direccion')== 0) 
                        $profesores[] = $uno;
                }
            }
            foreach ($profesores as $profe) {
                if ($profe->Sustituye) $reunion->profesores()->attach($profe->Sustituye->dni,['asiste'=>true]);
                else $reunion->profesores()->attach($profe->dni,['asiste'=>true]);
            }
        }
    }

}
