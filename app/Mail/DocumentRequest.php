<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Headers;


/**
 * Class DocumentRequest
 * @package Intranet\MyMail
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
    public $elemento;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail, $view, $elemento, $attach=null)
    {
        $this->mail = $mail;
        $this->view = $view;
        $this->elemento = $elemento;
        $this->attach = $attach;
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'Disposition-Notification-To' => $this->mail->from??$this->mail['from'],
            ],
        );
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $vista =  $this->from(
            $this->mail->from??$this->mail['from'],
            $this->mail->fromPerson??$this->mail['fromPerson']
        )->replyTo(
            $this->mail->from??$this->mail['from'],
            $this->mail->fromPerson??$this->mail['fromPerson']
        )->subject(
            $this->mail->subject??$this->mail['subject']
        )->view($this->view);


        if (isset($this->attach)) {
            if (array_depth($this->attach) > 1) {
                foreach ($this->attach as $file) {
                    foreach ($file as $index => $value) {
                        $vista = $vista->attach(storage_path($index), ['mime' => $value]);
                    }
                }
            } else {
                foreach ($this->attach as $index => $value) {
                    $vista = $vista->attach(storage_path($index), ['mime' => $value]);
                }
            }
        }
        return $vista;
    }
}
