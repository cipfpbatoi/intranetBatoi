<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Intranet\Http\Controllers\FctController;
use Illuminate\Support\Facades\Log;



class CertificatInstructorFct extends Mailable
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
        $id = $this->fct->id;
        if (file_exists(storage_path("tmp/certificatIFct_$id.pdf")))
            unlink(storage_path("tmp/certificatIFct_$id.pdf"));
        $pdf = FctController::preparePdf($this->fct,Hoy(),$this->fct->Colaboracion->Ciclo->horasFct);
        $pdf->save(storage_path("tmp/certificatIFct_$id.pdf"));
        Log::notice("Enviat correu certificat ".$this->fct->Instructor->nombre);
        return $this->view("email.fct.certificadoInstructor")->attach(storage_path("tmp/certificatIFct_$id.pdf"),['as'=>'certificatFCT.pdf','mime' => 'application/pdf']);
    }






}
