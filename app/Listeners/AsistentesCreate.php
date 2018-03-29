<?php

namespace Intranet\Listeners;

use Intranet\Events\ReunionCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Reunion;
use Intranet\Entities\Profesor;
use Intranet\Entities\Grupo;
use Intranet\Entities\Miembro;

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
    public function handle(ReunionCreated $event)
    {
        if (AuthUser()) {
            if ($event->reunion->Tipos()['colectivo'] == 'Departamento') {
                $profesores = Profesor::Activo()->where('departamento', '=', AuthUser()->departamento)->get();
            }
            if ($event->reunion->Tipos()['colectivo'] == 'Profesor') {
                $profesores = Profesor::Activo()->get();
            }
            if ($event->reunion->Tipos()['colectivo'] == 'GrupoTrabajo') {
                $profesores = Miembro::where('idGrupoTrabajo', '=', $event->reunion->grupo)->get();
            }
            if ($event->reunion->Tipos()['colectivo'] == 'Grupo') {
                $grupo = Grupo::QTutor(AuthUser()->dni)->get()->first();
                $profesores = $grupo->getProfesores();
            }
            if ($event->reunion->Tipos()['colectivo'] == '') {
                $grupo = Grupo::QTutor(AuthUser()->dni)->get()->first();
                $profesores = [];
            }
            if ($event->reunion->Tipos()['colectivo'] == 'Jefe'){
                $todos = Profesor::Activo()->get();
                $profesores = [];
                foreach ($todos as $uno){
                    if ($uno->rol % config('constants.rol.jefe_dpto') == 0 || $uno->rol % config('constants.rol.direccion')== 0) $profesores[] = $uno;
                }
            }

            $reunion = Reunion::findOrFail($event->reunion->id);
            foreach ($profesores as $profesor) {
                $id = isset($profesor->dni) ? $profesor->dni : $profesor->idProfesor;
                $profe = Profesor::find($id);
                if ($profe->Sustituye) $reunion->profesores()->attach($profe->Sustituye->dni,['asiste'=>true]);
                else $reunion->profesores()->attach($id,['asiste'=>true]);
            }
//            $creador = Asistencia::where('idProfesor', AuthUser()->dni)->where('idReunion',$event->reunion->id)->first();
//            if (!isset($creador->asiste)){
//                $a = new Asistencia;
//                $a->idProfesor = AuthUser()->dni;
//                $a->idReunion = $event->reunion->id;
//                $a->asiste = true;
//                $a->save(); 
//            }
        }
    }

}
