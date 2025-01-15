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


class CertificatAlumneFct extends Mailable
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
        $emitent = $this->fct->Tutor;
        if (is_null($emitent)) {
            $emitent = Profesor::find(config('contacto.email'));
        }

        if (file_exists(storage_path("tmp/certificatFct_$id.pdf"))) {
            unlink(storage_path("tmp/certificatFct_$id.pdf"));
        }
        $pdf = FctAlumnoController::preparePdf($id);
        $pdf->save(storage_path("tmp/certificatFct_$id.pdf"));
        Log::notice("Enviat correu certificat ".$this->fct->Alumno->fullName);
        return $this->view("email.fct.certificadoAlumno")
            ->from($emitent->email, $emitent->fullName)
            ->replyTo($emitent->email, $emitent->fullName)
            ->cc($emitent->email, $emitent->fullName)
            ->attach(
                storage_path("tmp/certificatFct_$id.pdf"),
                [
                    'as'=>'certificatFCT.pdf',
                    'mime' => 'application/pdf'
                ]
            );
    }
 }
