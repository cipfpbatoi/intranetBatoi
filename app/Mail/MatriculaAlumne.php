<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Intranet\Http\Controllers\ReunionController;

class MatriculaAlumne extends Mailable
{

    use Queueable,
        SerializesModels;

    public $aR;
    public $vista;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($aR,$vista)
    {
        $this->aR = $aR;
        $this->vista = $vista;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::notice("Enviat correu matrícula alumne ".$this->aR->Alumno->email);
        return $this->view($this->vista,['aR'=> $this->aR]);
    }

}
