<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class DocumentRequest
 * @package Intranet\Mail
 */
class DocumentRequest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $mail;
    /**
     * @var
     */
    public $view;
    public $to;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail,$view,$to)
    {
        $this->mail = $mail;
        $this->view =$view;
        $this->to = $to;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->mail->getFrom(),$this->mail->getFromPerson())->subject($this->mail->getSubject())->view($this->view);
    }
}
