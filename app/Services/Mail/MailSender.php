<?php

namespace Intranet\Services\Mail;

use Illuminate\Support\Facades\Mail;
use Intranet\Entities\Activity;
use Intranet\Exceptions\IntranetException;
use Intranet\Mail\DocumentRequest;
use Intranet\Services\Mail\EmailPostSendService;
use Intranet\Services\UI\AppAlert as Alert;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Envia correus a partir d'un MyMail.
 */
class MailSender
{
    /**
     * @var int
     */
    private int $maxMessageBytes;

    /**
     * @var float
     */
    private float $encodedSizeFactor;

    /**
     * @var int
     */
    private int $mimeOverheadBytes;

    /**
     * @param int|null $maxMessageBytes Límit màxim estimat del missatge complet.
     * @param float|null $encodedSizeFactor Factor conservador per estimar l'encoding SMTP/MIME.
     * @param int|null $mimeOverheadBytes Sobrepes fix aproximat de capçaleres i multipart.
     */
    public function __construct(
        ?int $maxMessageBytes = null,
        ?float $encodedSizeFactor = null,
        ?int $mimeOverheadBytes = null
    ) {
        $this->maxMessageBytes = $maxMessageBytes ?? (int) config('mail.max_message_bytes', 18 * 1024 * 1024);
        $this->encodedSizeFactor = $encodedSizeFactor ?? 1.37;
        $this->mimeOverheadBytes = $mimeOverheadBytes ?? 4096;
    }

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
                $resolvedView = $mail->resolveViewForSend();
                $mailable = new DocumentRequest($mail, $resolvedView, $element, $mail->attach);

                $this->assertMessageWithinSizeLimit($mailable);

                try {
                    Mail::to($address, $contact)
                        ->bcc($mail->from)
                        ->send($mailable);
                } catch (TransportExceptionInterface $exception) {
                    throw $this->mapTransportException($exception);
                }

                Alert::info('Enviat correus ' . $mail->subject . ' a ' . $contact);

                if ($mail->register !== null) {
                    Activity::record('email', $element, null, $fecha, $mail->register);
                }

                $this->handlePostSend($mail);
            } else {
                Alert::danger("No s'ha pogut enviar correu a $contact. Comprova email");
            }
        }
    }

    /**
     * Valida que el missatge no supere el límit configurat.
     *
     * @param DocumentRequest $mailable
     * @return void
     */
    private function assertMessageWithinSizeLimit(DocumentRequest $mailable): void
    {
        $estimatedBytes = $this->estimateMessageBytes($mailable, $mailable->attach ?? null);

        if ($estimatedBytes <= $this->maxMessageBytes) {
            return;
        }

        $estimatedMegabytes = number_format($estimatedBytes / 1024 / 1024, 1, ',', '.');
        $limitMegabytes = number_format($this->maxMessageBytes / 1024 / 1024, 1, ',', '.');

        throw new IntranetException(
            "El correu supera el límit estimat de mida ({$estimatedMegabytes} MB > {$limitMegabytes} MB).",
            422,
            "El correu és massa gran per a enviar-lo. Redueix adjunts o lleva imatges incrustades del text.",
            false,
            [
                'estimated_bytes' => $estimatedBytes,
                'max_bytes' => $this->maxMessageBytes,
            ]
        );
    }

    /**
     * Estima la mida final del missatge tenint en compte HTML, adjunts i encoding.
     *
     * @param DocumentRequest $mailable
     * @param array|null $attachments
     * @return int
     */
    private function estimateMessageBytes(DocumentRequest $mailable, ?array $attachments): int
    {
        $htmlBytes = strlen((string) $mailable->render());
        $attachmentBytes = $this->attachmentBytes($attachments);

        return (int) ceil(($htmlBytes + $attachmentBytes) * $this->encodedSizeFactor) + $this->mimeOverheadBytes;
    }

    /**
     * Suma la mida dels fitxers adjunts persistits en `storage`.
     *
     * @param array|null $attachments
     * @return int
     */
    private function attachmentBytes(?array $attachments): int
    {
        if (!$attachments) {
            return 0;
        }

        $bytes = 0;
        $files = array_depth($attachments) > 1 ? $attachments : [$attachments];

        foreach ($files as $file) {
            foreach ($file as $path => $mime) {
                $fullPath = storage_path($path);
                if (is_file($fullPath)) {
                    $bytes += (int) filesize($fullPath);
                }
            }
        }

        return $bytes;
    }

    /**
     * Converteix errors SMTP coneguts en errors funcionals més curts.
     *
     * @param TransportExceptionInterface $exception
     * @return \Throwable
     */
    private function mapTransportException(TransportExceptionInterface $exception): \Throwable
    {
        $message = (string) $exception->getMessage();
        $isMessageTooLarge = str_contains($message, '552')
            && (
                str_contains($message, 'MaxSizeError')
                || str_contains($message, 'message size limits')
                || str_contains($message, '5.3.4')
            );

        if (!$isMessageTooLarge) {
            return $exception;
        }

        return new IntranetException(
            'El servidor SMTP ha rebutjat el correu per excés de mida: ' . $message,
            422,
            "El correu és massa gran per a enviar-lo. Redueix adjunts o lleva imatges incrustades del text.",
            false,
            ['transport_message' => $message],
            $exception
        );
    }

    /**
     * Lança l'esdeveniment definit en sessió, si existeix.
     *
     * @param MyMail $mail
     * @return void
     */
    private function handlePostSend(MyMail $mail): void
    {
        $action = session()->get('email_action');
        if (!$action) {
            return;
        }

        session()->forget('email_action');

        if ($action === 'annexe_individual' || $action === 'Intranet\\Events\\EmailAnnexeIndividual') {
            app(EmailPostSendService::class)->handleAnnexeIndividual($mail->getTo());
        }
    }
}
