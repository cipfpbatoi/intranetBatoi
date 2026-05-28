<?php

namespace Intranet\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


/**
 * Mailable genèric per enviar una vista amb dades de document.
 */
class DocumentRequest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Dades de configuració del correu.
     *
     * @var array<string, mixed>|object
     */
    public $mail;

    /**
     * Vista Blade del cos del correu.
     *
     * @var string
     */
    public $view;

    /**
     * Element passat a la vista.
     *
     * @var mixed
     */
    public $elemento;

    /**
     * Fitxers adjunts opcionals.
     *
     * @var mixed
     */
    public $attach;

    /**
     * Crea una nova instància del missatge.
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

    /*
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'Disposition-Notification-To' => $this->mail->from??$this->mail['from'],
            ],
        );
    }
    */
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $vista =  $this->from(
            $this->mailValue('from'),
            $this->mailValue('fromPerson')
        )->replyTo(
            $this->mailValue('from'),
            $this->mailValue('fromPerson')
        )->subject(
            $this->mailValue('subject')
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

    /**
     * Llig una propietat de configuració tant si arriba com objecte com si arriba com array.
     *
     * @param string $key
     * @return mixed
     */
    private function mailValue(string $key): mixed
    {
        if (is_array($this->mail)) {
            return $this->mail[$key] ?? null;
        }

        return $this->mail->{$key} ?? null;
    }
}
