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
    public $elemento;
    /**
     * @var
     */
    public $de;
    /**
     * @var
     */
    public $view;
    /**
     * @var
     */
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($elemento,$from,$subject,$view)
    {
        $this->elemento = $elemento;
        $this->de = $from;
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
        return $this->from($this->de)->subject($this->subject)->view($this->view);
    }
}
