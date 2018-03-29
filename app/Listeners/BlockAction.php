<?php

namespace Intranet\Listeners;

use Intranet\Events\PreventAction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;
use Styde\Html\Facades\Alert;

class BlockAction
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
     * @param  PreventAction  $event
     * @return void
     */
    public function handle(PreventAction $event)
    {
        if ($event->clau){
            if (!UserisAllow($event->autorizados)) {
                if (auth('profesor')->user()) $usuario = auth()->user()->dni;
                else if (auth('alumno')->user()) $usuario = auth('alumno')->user()->nia;
                    else {
                        Alert::danger(trans('messages.generic.notAllowed'));
                        return false;
                    }
                if ($usuario != $event->clau) {
                     Alert::danger(trans('messages.generic.notAllowed'));
                    return false;
                }
            }
        }
        else 
        {
            Alert::danger(trans('messages.generic.notAllowed'));
            return false;
        }
    }
}
