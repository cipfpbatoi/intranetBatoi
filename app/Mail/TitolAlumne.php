<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Intranet\Http\Controllers\FctAlumnoController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Profesor;


class TitolAlumne extends Mailable
{

    use Queueable,
        SerializesModels;

    public $fct;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fct)
    {
        $this->fct = $fct;
    }

    /**
     * Build the message.
     *
     * @return $this
    */
    public function build()
    {
        Log::notice("Enviat correu Titol ".$this->fct->Alumno->fullName);
        return $this->view("email.fct.titol");
    }






}
