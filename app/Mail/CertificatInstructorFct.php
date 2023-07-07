<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Intranet\Componentes\Pdf;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\FctController;
use Illuminate\Support\Facades\Log;
use Intranet\Http\PrintResources\CertificatInstructorResource;
use Intranet\Services\FDFPrepareService;


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
        $emitent = $this->fct->encarregat;
        $pdf = FDFPrepareService::exec(
            new CertificatInstructorResource($this->fct));
        $view = $this->view("email.fct.certificadoInstructor")
            ->from($emitent->email, $emitent->fullName)
            ->replyTo($emitent->email, $emitent->fullName)
            ->cc($emitent->email, $emitent->fullName)
            ->attach($pdf, ['as'=>'certificadoInstructor.pdf','mime' => 'application/pdf']);
        if (count($this->fct->Colaboradores)) {
            $id = $this->fct->id;
            if (file_exists(storage_path("tmp/certificatIFct_$id.pdf"))) {
                unlink(storage_path("tmp/certificatIFct_$id.pdf"));
            }
            $pdf = $this->certificatColaboradors();
            $pdf->save(storage_path("tmp/certificatIFct_$id.pdf"));
            $view = $view->attach(
                storage_path("tmp/certificatIFct_$id.pdf"),
                ['as' => 'certificadoColaboradores.pdf', 'mime' => 'application/pdf']
            );
        }
        Log::notice("Enviat correu certificat ".$this->fct->Instructor->nombre);
        return $view;
    }

    public function certificatColaboradors()
    {
        $secretario = Profesor::find(config('avisos.secretario'));
        $director = Profesor::find(config('avisos.director'));
        $dades = ['date' => FechaString(hoy(), 'ca'),
            'fecha' => FechaString(hoy(), 'es'),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName,
        ];

        return Pdf::hazPdf('pdf.fct.certificatColaborador', $this->fct, $dades);
    }
}
