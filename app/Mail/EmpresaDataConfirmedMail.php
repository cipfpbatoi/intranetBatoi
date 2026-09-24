<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Intranet\Entities\EmpresaDataConfirmation;

/**
 * Correu que informa el tutor que l'empresa ha confirmat les seues dades.
 */
class EmpresaDataConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public EmpresaDataConfirmation $confirmation;
    public string $tutorName;

    /**
     * Crea l'avís de confirmació per al tutor.
     */
    public function __construct(EmpresaDataConfirmation $confirmation, string $tutorName)
    {
        $this->confirmation = $confirmation;
        $this->tutorName = $tutorName;
    }

    /**
     * Configura l'assumpte i la plantilla de l'avís.
     */
    public function build(): self
    {
        return $this
            ->subject('Dades d’empresa confirmades: ' . $this->confirmation->empresa->nombre)
            ->view('email.fct.empresa-data-confirmed');
    }
}
