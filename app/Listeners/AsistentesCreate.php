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
                $profesores = Profesor::GrupoT($event->reunion->grupo)->get();
            }
            if ($event->reunion->Tipos()['colectivo'] == 'Grupo') {
                $grupo = Grupo::QTutor(AuthUser()->dni)->get()->first();
                $profesores = Profesor::Grupo($grupo->codigo)->get();
            }
            if ($event->reunion->Tipos()['colectivo'] == '') {
                $grupo = Grupo::QTutor(AuthUser()->dni)->get()->first();
                $profesores = [];
            }
            if ($event->reunion->Tipos()['colectivo'] == 'Jefe'){
                $todos = Profesor::Activo()->get();
                $profesores = [];
                foreach ($todos as $uno){
                    if ($uno->rol % config('roles.rol.jefe_dpto') == 0 || $uno->rol % config('roles.rol.direccion')== 0) 
                        $profesores[] = $uno;
                }
            }

            $reunion = Reunion::findOrFail($event->reunion->id);
            foreach ($profesores as $profe) {
                if ($profe->Sustituye) $reunion->profesores()->attach($profe->Sustituye->dni,['asiste'=>true]);
                else $reunion->profesores()->attach($profe->dni,['asiste'=>true]);
            }

        }
    }

}
