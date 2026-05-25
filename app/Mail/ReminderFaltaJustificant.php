<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Intranet\Entities\Falta;

/**
 * Correu de recordatori per aportar justificant d'una falta de professorat.
 */
class ReminderFaltaJustificant extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Falta
     */
    public Falta $falta;

    /**
     * @param Falta $falta
     */
    public function __construct(Falta $falta)
    {
        $this->falta = $falta;
    }

    /**
     * Construix el correu amb assumpte i vista.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Recordatori de justificant d\'absència')
            ->view('email.reminderFaltaJustificant', [
                'falta' => $this->falta,
            ]);
    }
}
