<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DocumentRequest extends Mailable
{
    use Queueable, SerializesModels;
    
    public $elemento;
    public $email;
    public $document;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($elemento,$from,$subject,$view)
    {
        $this->elemento = $elemento;
        $this->from = $from;
        $this->view =$view;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from($this->from)
            ->subject($this->subject)
            ->view($this->view);
    }
}
