<?php

declare(strict_types=1);

namespace Intranet\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\AssumpteParticularMailDelivery;
use Intranet\Entities\Profesor;
use Intranet\Mail\AssumpteParticularAvis;
use Throwable;

/** Envia i audita un avís després de la transició de domini. */
class SendAssumpteParticularMail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    /** Retards progressius per a errors SMTP temporals. */
    public array $backoff = [60, 300];

    /** Identifica l'entrada persistent que s'ha de lliurar. */
    public function __construct(public readonly int $deliveryId)
    {
        $this->afterCommit();
    }

    /** Envia com a màxim una vegada per entrada, llevat d'un error de confirmació extern. */
    public function handle(): void
    {
        $registre = DB::transaction(function (): ?AssumpteParticularMailDelivery {
            $registre = AssumpteParticularMailDelivery::query()->lockForUpdate()->find($this->deliveryId);
            if ($registre === null || $registre->estat === AssumpteParticularMailDelivery::ENVIADA) {
                return null;
            }
            if ($registre->estat === AssumpteParticularMailDelivery::ENVIANT
                && $registre->last_attempt_at?->greaterThan(now()->subMinutes(5))) {
                $this->release(60);
                return null;
            }
            if (blank($registre->destinatari_email)) {
                $registre->forceFill([
                    'estat' => AssumpteParticularMailDelivery::ERROR,
                    'error' => 'destinatari_sense_email_valid',
                ])->save();
                return null;
            }

            $registre->forceFill([
                'estat' => AssumpteParticularMailDelivery::ENVIANT,
                'intents' => $registre->intents + 1,
                'last_attempt_at' => now(),
                'error' => null,
            ])->save();

            return $registre;
        });

        if ($registre === null) {
            return;
        }

        try {
            $peticio = AssumpteParticular::query()
                ->where('id', $registre->peticio_id)
                ->where('curs', $registre->curs)
                ->with('profesor')
                ->firstOrFail();
            $correu = new AssumpteParticularAvis($peticio, $registre->tipus);
            if ($registre->tipus === AssumpteParticularMailDelivery::URGENT) {
                $capEstudis = Profesor::query()->find((string) config('avisos.jefeEstudios'));
                $copia = trim((string) ($capEstudis?->email ?? ''));
                if (filter_var($copia, FILTER_VALIDATE_EMAIL) !== false
                    && $copia !== $registre->destinatari_email) {
                    $correu->cc($copia);
                }
            }
            Mail::to($registre->destinatari_email)->send($correu);
            $registre->forceFill([
                'estat' => AssumpteParticularMailDelivery::ENVIADA,
                'sent_at' => now(),
                'error' => null,
            ])->save();
        } catch (Throwable $exception) {
            $registre->forceFill([
                'estat' => AssumpteParticularMailDelivery::ERROR,
                'error' => class_basename($exception),
            ])->save();
            Log::warning('Ha fallat un correu d’assumptes particulars', [
                'delivery_id' => $registre->getKey(),
                'tipus' => $registre->tipus,
                'error_class' => $exception::class,
            ]);
            throw $exception;
        }
    }
}
