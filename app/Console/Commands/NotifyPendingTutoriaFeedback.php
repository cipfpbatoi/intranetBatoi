<?php

declare(strict_types=1);

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Intranet\Application\Tutoria\TutoriaFeedbackService;
use Throwable;

/**
 * Notifica els tutors que tenen feedback de tutories pendent.
 */
class NotifyPendingTutoriaFeedback extends Command
{
    /** @var string */
    protected $signature = 'tutories:notifica-feedback-pendent';

    /** @var string */
    protected $description = 'Notifica els tutors amb feedback de tutories pendent';

    /**
     * Executa la comprovació diària de feedback pendent.
     */
    public function handle(TutoriaFeedbackService $service): int
    {
        try {
            $sent = $service->notifyPending(Carbon::today());
            $this->info("S'han enviat {$sent} avisos de feedback pendent.");

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            Log::error('Error notificant feedback de tutories pendent.', [
                'exception' => $exception->getMessage(),
            ]);

            return self::FAILURE;
        }
    }
}
