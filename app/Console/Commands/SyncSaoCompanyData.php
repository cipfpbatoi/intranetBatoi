<?php

declare(strict_types=1);

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Intranet\Application\Empresa\SaoCompanyDataUpdater;
use Intranet\Entities\AlumnoFct;
use Intranet\Exceptions\IntranetException;
use Intranet\Sao\SaoCompanyDataReader;
use Intranet\Services\Automation\SeleniumService;

/**
 * Sincronitza dades d'empresa i centre des de SAO omplint només camps buits.
 */
class SyncSaoCompanyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sao:sync-company-data
        {--dry-run : Mostra què faria sense escriure en la base de dades}
        {--all : Inclou FCT finalitzades}
        {--limit= : Limita el nombre de FCT processades}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ompli camps buits d\'empreses i centres amb dades SAO';

    /**
     * Execute the console command.
     *
     * @param SaoCompanyDataReader $reader
     * @param SaoCompanyDataUpdater $updater
     * @return int
     */
    public function handle(SaoCompanyDataReader $reader, SaoCompanyDataUpdater $updater): int
    {
        $envia = config('contacto.avisos.errores') ?? '021652470V';
        $dryRun = (bool) $this->option('dry-run');
        $processed = 0;
        $updatedEmpresa = 0;
        $updatedCentro = 0;
        $errors = 0;

        try {
            $driver = SeleniumService::loginSAO(
                config('services.selenium.SAO_USER'),
                config('services.selenium.SAO_PASS')
            );

            foreach ($this->queryFcts()->get() as $alumnoFct) {
                $processed++;
                $fct = $alumnoFct->Fct;
                $centro = $fct?->Colaboracion?->Centro;
                $empresa = $centro?->Empresa;

                if (!$fct || !$centro || !$empresa) {
                    continue;
                }

                try {
                    $data = $reader->readFromFct($driver, (string) $alumnoFct->idSao, $centro->idSao);
                    $result = $updater->fillMissing($empresa, $centro, $data, $dryRun);
                    $updatedEmpresa += $result['empresa'];
                    $updatedCentro += $result['centro'];
                } catch (\Throwable $exception) {
                    $errors++;
                    report($exception);
                    Log::channel('sao')->warning('Error sincronitzant dades empresa/centre des de SAO.', [
                        'alumno_fct_id' => $alumnoFct->id ?? null,
                        'id_sao' => $alumnoFct->idSao ?? null,
                        'error' => $exception->getMessage(),
                    ]);
                }
            }

            $prefix = $dryRun ? '[dry-run] ' : '';
            $this->info($prefix . 'FCT processades: ' . $processed);
            $this->info($prefix . 'Camps empresa omplits: ' . $updatedEmpresa);
            $this->info($prefix . 'Camps centre omplits: ' . $updatedCentro);
            $this->info($prefix . 'Errors: ' . $errors);

            return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
        } catch (IntranetException $exception) {
            avisa($envia, $exception->getMessage(), '#', 'SAO');
            report($exception);
            Log::channel('sao')->error('Error en la connexió a SAO per sincronitzar empresa/centre.', [
                'error' => $exception->getMessage(),
            ]);

            return Command::FAILURE;
        } finally {
            if (isset($driver)) {
                try {
                    $driver->quit();
                } catch (\Throwable $exception) {
                    Log::channel('sao')->warning('No s\'ha pogut tancar la sessió Selenium de SAO.', [
                        'error' => $exception->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Construeix la consulta de FCT amb id SAO a revisar.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function queryFcts()
    {
        $query = AlumnoFct::query()
            ->whereNotNull('idSao')
            ->where('beca', 0)
            ->with('Fct.Colaboracion.Centro.Empresa');

        if (!$this->option('all')) {
            $query->noHaAcabado();
        }

        $limit = $this->option('limit');
        if ($limit !== null && $limit !== '') {
            $query->limit((int) $limit);
        }

        return $query;
    }
}
