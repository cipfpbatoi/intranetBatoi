<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\AlumnoFct;
use Intranet\Sao\SaoAnnexesAction;
use Intranet\Services\Automation\SeleniumService;
use Throwable;

/**
 * Comandament per descarregar annexos SAO pendents.
 */
class SaoAnnexes extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sao:annexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connecta SAO per sincronitzar annexes';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle(): int
    {
        $envia = config('contacto.avisos.errores') ?? '021652470V';


        try {
            $driver = SeleniumService::loginSAO(
                config('services.selenium.SAO_USER'),
                config('services.selenium.SAO_PASS')
            );
            $action = new SaoAnnexesAction();
            $action->execute($driver, function () {
                return AlumnoFct::whereNotNull('idSao')
                    ->haEmpezado()
                    ->noHaAcabado()
                    ->where('beca', 0)
                    ->where('pg0301', 0)
                    ->whereNull('calificacion')
                    ->where('correoAlumno', 0);
            });

            $driver = null;
            $this->printSummary($action->summary());

            return $action->summary()['errors'] > 0 ? Command::FAILURE : Command::SUCCESS;
        } catch (Throwable $e) {
            avisa($envia, $e->getMessage(), '#', 'SAO');
            report($e);
            Log::channel('sao')->error("Error en sincronització d'annexes SAO: " . $e->getMessage());
            $this->error("Error en sincronització d'annexes SAO: " . $e->getMessage());
            return Command::FAILURE;
        } finally {
            if (isset($driver)) {
                try {
                    $driver->quit();
                } catch (Throwable $quitException) {
                    Log::channel('sao')->warning('No s\'ha pogut tancar la sessió Selenium de SAO en annexos.', [
                        'error' => $quitException->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Mostra per consola el resum del procés.
     *
     * @param array<string, int> $summary
     * @return void
     */
    private function printSummary(array $summary): void
    {
        $this->info('FCT revisades: ' . $summary['processed']);
        $this->info('Annexos descarregats: ' . $summary['downloaded']);
        $this->info('FCT que ja tenien annexos: ' . $summary['skipped']);

        if ($summary['errors'] > 0) {
            $this->warn('Errors: ' . $summary['errors']);
            return;
        }

        $this->info('Errors: 0');
    }
}
