<?php

declare(strict_types=1);

namespace Intranet\Application\AssumpteParticular;

use Illuminate\Support\Facades\Log;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\AssumpteParticularMailDelivery;
use Intranet\Entities\Profesor;
use Intranet\Jobs\SendAssumpteParticularMail;
use Throwable;

/** Registra i posa en cua els correus derivats de transicions confirmades. */
class AssumpteParticularNotificationService
{
    /** Avisa la directora configurada d'una petició excepcional. */
    public function urgent(AssumpteParticular $peticio): void
    {
        $this->registrar($peticio, AssumpteParticularMailDelivery::URGENT, (string) config('avisos.director'));
    }

    /** Comunica al professor la motivació d'una denegació. */
    public function denegada(AssumpteParticular $peticio): void
    {
        $this->registrar($peticio, AssumpteParticularMailDelivery::DENEGADA, (string) $peticio->idProfesor);
    }

    /** Avisa el professor amb un enllaç autenticat a la resolució. */
    public function autoritzada(AssumpteParticular $peticio): void
    {
        $this->registrar($peticio, AssumpteParticularMailDelivery::AUTORITZADA, (string) $peticio->idProfesor);
    }

    /** Crea una sola entrada per transició i deixa els errors de correu fora del flux de domini. */
    private function registrar(AssumpteParticular $peticio, string $tipus, string $dni): void
    {
        try {
            $destinatari = $dni !== '' ? Profesor::query()->find($dni) : null;
            $email = trim((string) ($destinatari?->email ?? ''));
            $valid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
            $registre = AssumpteParticularMailDelivery::query()->firstOrCreate(
                [
                    'curs' => (string) $peticio->curs,
                    'peticio_id' => (int) $peticio->getKey(),
                    'tipus' => $tipus,
                ],
                [
                    'destinatari_dni' => $dni !== '' ? $dni : null,
                    'destinatari_email' => $valid ? $email : null,
                    'estat' => $valid ? AssumpteParticularMailDelivery::PENDENT : AssumpteParticularMailDelivery::ERROR,
                    'error' => $valid ? null : 'destinatari_sense_email_valid',
                ]
            );

            if (!$registre->wasRecentlyCreated) {
                return;
            }
            if (!$valid) {
                Log::warning('Avís d’assumpte particular sense destinatari vàlid', [
                    'peticio_id' => $peticio->getKey(),
                    'tipus' => $tipus,
                ]);
                return;
            }

            try {
                SendAssumpteParticularMail::dispatch((int) $registre->getKey());
            } catch (Throwable $exception) {
                $registre->forceFill([
                    'estat' => AssumpteParticularMailDelivery::ERROR,
                    'error' => 'error_cua',
                ])->save();
                Log::error('No s’ha pogut posar en cua un avís d’assumpte particular', [
                    'peticio_id' => $peticio->getKey(),
                    'tipus' => $tipus,
                    'error_class' => $exception::class,
                ]);
            }
        } catch (Throwable $exception) {
            Log::error('No s’ha pogut registrar un avís d’assumpte particular', [
                'peticio_id' => $peticio->getKey(),
                'tipus' => $tipus,
                'error_class' => $exception::class,
            ]);
        }
    }
}
