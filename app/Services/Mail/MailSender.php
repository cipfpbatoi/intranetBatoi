<?php

namespace Intranet\Services\Mail;

use Illuminate\Support\Facades\Mail;
use Intranet\Entities\Activity;
use Intranet\Mail\DocumentRequest;
use Styde\Html\Facades\Alert;

/**
 * Envia correus a partir d'un MyMail.
 */
class MailSender
{
    /**
     * Envia el correu a tots els receptors.
     *
     * @param MyMail $mail
     * @param mixed $fecha
     * @return void
     */
    public function send(MyMail $mail, $fecha = null)
    {
        $elements = $mail->getTo();

        if (is_iterable($elements)) {
            foreach ($elements as $element) {
                $this->sendMail($mail, $element, $fecha);
            }
        } else {
            $this->sendMail($mail, $elements, $fecha);
        }

        session()->forget('attach');
    }

    /**
     * Envia un correu a un receptor.
     *
     * @param MyMail $mail
     * @param object $element
     * @param mixed $fecha
     * @return void
     */
    private function sendMail(MyMail $mail, $element, $fecha)
    {
        $contact = $element->contact ?? $element->contacto ?? 'A qui corresponga';

        if (isset($element)) {
            $address = $element->mail ?? $element->email;
            if (filter_var($address, FILTER_VALIDATE_EMAIL)) {
                Mail::to($address, $contact)
                    ->bcc($mail->from)
                    ->send(new DocumentRequest($mail, $mail->resolveViewForSend(), $element, $mail->attach));

                Alert::info('Enviat correus ' . $mail->subject . ' a ' . $contact);

                if ($mail->register !== null) {
                    Activity::record('email', $element, null, $fecha, $mail->register);
                }

                $this->sendEvent($mail);
            } else {
                Alert::danger("No s'ha pogut enviar correu a $contact. Comprova email");
            }
        }
    }

    /**
     * Lança l'esdeveniment definit en sessió, si existeix.
     *
     * @param MyMail $mail
     * @return void
     */
    private function sendEvent(MyMail $mail)
    {
        if (session()->has('email_action')) {
            $event = session()->get('email_action');
            event(new $event($mail->getTo()));
            session()->forget('email_action');
        }
    }
}
