<?php

declare(strict_types=1);

namespace Intranet\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Intranet\Entities\ImportRun;
use Intranet\Http\Controllers\ImportController;
use Intranet\Http\Controllers\TeacherImportController;
use Throwable;

class RunImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 1200;

    public function __construct(private readonly int $importRunId)
    {
    }

    public function handle(): void
    {
        $previousMaxExecutionTime = ini_get('max_execution_time');
        @ini_set('max_execution_time', '0');

        $importRun = ImportRun::find($this->importRunId);
        if (!$importRun) {
            return;
        }

        $importRun->status = 'running';
        $importRun->progress = 5;
        $importRun->started_at = now();
        $importRun->message = 'Processant importació';
        $importRun->save();

        try {
            $absolutePath = storage_path('app/' . ltrim((string) $importRun->file_path, '/'));
            if (!is_file($absolutePath)) {
                throw new \RuntimeException("No existeix el fitxer d'importació: {$absolutePath}");
            }

            $options = is_array($importRun->options) ? $importRun->options : [];
            $request = Request::create('/import/async/run', 'POST', $options);

            if ($importRun->type === 'general') {
                $controller = app(ImportController::class);
                $controller->run($absolutePath, $request);

                if (($options['primera'] ?? null) === 'on' || ($options['primera'] ?? null) === true) {
                    $controller->asignarTutores();
                }
            } elseif ($importRun->type === 'teacher') {
                $controller = app(TeacherImportController::class);
                $controller->run($absolutePath, $request);
            } else {
                throw new \RuntimeException('Tipus d\'importació no suportat.');
            }

            $importRun->status = 'done';
            $importRun->progress = 100;
            $importRun->finished_at = now();
            $importRun->message = 'Importació completada';
            $importRun->save();
        } catch (Throwable $e) {
            $importRun->status = 'failed';
            $importRun->failed_at = now();
            $importRun->message = 'Importació fallida';
            $importRun->error = mb_substr($e->getMessage(), 0, 1000);
            $importRun->save();

            throw $e;
        } finally {
            if ($previousMaxExecutionTime !== false) {
                @ini_set('max_execution_time', (string) $previousMaxExecutionTime);
            }
        }
    }
}
