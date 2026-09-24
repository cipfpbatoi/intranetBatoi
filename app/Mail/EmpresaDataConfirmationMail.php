<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Intranet\Entities\EmpresaDataConfirmation;

/**
 * Correu amb l'enllaç públic per a confirmar les dades de l'empresa.
 */
class EmpresaDataConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public EmpresaDataConfirmation $confirmation;
    public string $url;
    public string $tutorName;
    public string $tutorEmail;

    /**
     * Crea el correu de confirmació.
     */
    public function __construct(
        EmpresaDataConfirmation $confirmation,
        string $url,
        string $tutorName,
        string $tutorEmail
    )
    {
        $this->confirmation = $confirmation;
        $this->url = $url;
        $this->tutorName = $tutorName;
        $this->tutorEmail = $tutorEmail;
    }

    /**
     * Configura l'assumpte i la plantilla del missatge.
     */
    public function build(): self
    {
        return $this
            ->subject('Confirmació de dades per a la formació en empresa')
            ->from($this->tutorEmail, $this->tutorName)
            ->replyTo($this->tutorEmail, $this->tutorName)
            ->view('email.fct.empresa-data-confirmation');
    }
}
