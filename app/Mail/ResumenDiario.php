<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResumenDiario extends Mailable
{

    use Queueable,
        SerializesModels;

    public $notificaciones;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($notificaciones)
    {
        $this->notificaciones = $notificaciones;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.resumenDiario')->subject("Resumen dels missatges del dia " . Hoy() . " : ");
    }

}
