<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AvalFct extends Mailable
{

    use Queueable,
        SerializesModels;

    public $fct;
    private $quien;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fct,$quien)
    {
        $this->fct = $fct;
        $this->quien = $quien;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view("email.fct.$this->quien");
    }

}
