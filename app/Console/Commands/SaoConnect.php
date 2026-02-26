<?php

namespace Intranet\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\AlumnoFct;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\Automation\SeleniumService;
use Intranet\Sao\SaoSyncAction;
use Intranet\Services\UI\AlertLogger;

class SaoConnect extends Command
{
    protected $signature = 'sao:connect';
    protected $description = 'Connecta SAO per sincronitzar les dades';




    public function handle()
    {
        $envia = config('contacto.avisos.errores') ?? '021652470V';

        try {
            $driver = SeleniumService::loginSAO(
                config('services.selenium.SAO_USER'),
                config('services.selenium.SAO_PASS')
            );

            (new SaoSyncAction())->execute($driver,function () {
                return AlumnoFct::whereNotNull('idSao')
                    ->noHaAcabado()
                    ->haEmpezado()
                    ->activa();
            });

            return Command::SUCCESS;
        } catch (IntranetException $e) {
            avisa($envia, $e->getMessage(), '#', 'SAO');
            Log::channel('sao')->error("Error en la connexió a SAO: " . $e->getMessage());
        } finally {
            if (isset($driver)) {
                $driver->quit();
            }
        }
    }
}
