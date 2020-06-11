<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Intranet\Http\Controllers\ReunionController;

class AvalAlumne extends Mailable
{

    use Queueable,
        SerializesModels;

    public $aR;
    public $informe;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($aR,$informe)
    {
        $this->aR = $aR;
        $this->informe = $informe;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pdf = ReunionController::preparePdf($this->informe,$this->aR);
        $id = $this->aR->idAlumno;
        $pdf->save(storage_path("tmp/certificatAvalAlumne_$id.pdf"));
        Log::notice("Enviat correu avalució alumne ".$this->aR->Alumno->email);
        return $this->view("email.avalAlumne",['aR'=> $this->aR])->attach(storage_path("tmp/certificatAvalAlumne_$id.pdf"),['as'=>'ResultatsAvaluacio.pdf','mime' => 'application/pdf']);;
    }

}
