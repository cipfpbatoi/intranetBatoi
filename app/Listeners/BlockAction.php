<?php

namespace Intranet\Listeners;

use Intranet\Events\PreventAction;
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

    private function usuario()
    {
        if (auth('profesor')->user()) {
            return auth()->user()->dni;
        }
        if (auth('alumno')->user()) {
            return auth('alumno')->user()->nia;
        }
        return null;
    }

    public function handle(PreventAction $event)
    {
        if ($event->owner) {
            if (!userIsAllow($event->autorizados)) {
                if (! ($usuario = $this->usuario())) {
                    Alert::danger(trans('messages.generic.notAllowed'));
                    return false;
                }
                if ($usuario != $event->owner) {
                    Alert::danger(trans('messages.generic.notAllowed'));
                    return false;
                }
            }
        } else {
            Alert::danger(trans('messages.generic.notAllowed'));
            return false;
        }
    }
}
