<?php

namespace Intranet\Http\Controllers\Direccion\Actividad;

use Intranet\Entities\Actividad;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\Calendar\GoogleCalendarService;
use Intranet\Services\General\StateService;

/**
 * Autorització massiva d'activitats des del panell de Direcció.
 */
class AuthorizeController extends Controller
{
    /**
     * Autoritza totes les activitats pendents i sincronitza calendari si toca.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke()
    {
        $this->authorize('create', Actividad::class);

        $activitats = Actividad::where('estado', 1)->get();
        $credentialsPath = (string) env('services.calendar.calendarCredentialsPath');

        if ($credentialsPath !== '' && file_exists(storage_path($credentialsPath))) {
            $calendar = new GoogleCalendarService();

            foreach ($activitats as $activitat) {
                $assistents = $activitat->profesores()->select('email')->get()->toArray();
                $calendar->addEvent(
                    $activitat->name,
                    $activitat->descripcion,
                    $activitat->desde,
                    $activitat->hasta,
                    $assistents
                );
            }

            $calendar->saveEvents();
        }

        StateService::makeAll($activitats, 2);

        return back();
    }
}
