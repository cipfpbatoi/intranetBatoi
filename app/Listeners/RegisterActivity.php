<?php

namespace Intranet\Listeners;

use Intranet\Events\ActivityReport;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Intranet\Entities\Activity;
use Illuminate\Support\Facades\Auth;

class RegisterActivity
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
     * @param  ActivityReport  $event
     * @return void
     */
    public function handle(ActivityReport $event)
    {
        if (AuthUser()) {
            if (!$event->model->exists)
                $mensaje = 'delete';
            else {
                if ($event->model->wasRecentlyCreated)
                    $mensaje = 'create';
                else
                    $mensaje = 'update';
            }
            Activity::record($mensaje, $event->model);
        }
    }

}
