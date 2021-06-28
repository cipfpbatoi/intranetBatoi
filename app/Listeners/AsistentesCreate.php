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

        if ($reunion->extraOrdinaria) {
            if ($grupo->curso == 2) {
                return $grupo->Alumnos->whereNotIn('nia', hazArray(AlumnoFctAval::misFcts()->titulan()->get(), 'idAlumno'));
            } else {
                return $grupo->Alumnos;
            }
        }
        return [];
    }

    private function assignaAlumnes($reunion)
    {
        //$capacitat = $reunion->avaluacioFinal?0:3;

        foreach ($this->queAlumnes($reunion) as $alumno)
            $reunion->alumnos()->attach($alumno->nia,['capacitats'=>3]);
    }


    public function handle(ReunionCreated $event)
    {
        if (AuthUser()) {
            $reunion = $event->reunion;
            $tipo = $reunion->Tipos();
            if ($tipo->colectivo == 'Departamento') {
                $profesores = Profesor::Activo()->where('departamento', '=', AuthUser()->departamento)->get();
            }
            if ($tipo->colectivo == 'Profesor') {
                $profesores = Profesor::Activo()->get();
            }
            if ($tipo->colectivo == 'GrupoTrabajo') {
                $profesores = Profesor::GrupoT($reunion->grupo)->get();
            }
            if ($tipo->colectivo == 'Grupo') {
                $profesores = Profesor::Grupo($reunion->GrupoClase->codigo)->get();
                $this->assignaAlumnes($reunion);
            }
            if ($tipo->colectivo == '') {
                $profesores = [];
            }
            if ($tipo->colectivo == 'Jefe'){
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
