<?php

namespace Intranet\Console\Commands;


use Illuminate\Console\Command;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\SeleniumService;
use Intranet\Sao\Sync;
use Intranet\Services\AlertLogger;

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

            (new Sync( ))->execute($driver,function () {
                return AlumnoFctAval::whereNotNull('idSao')
                    ->noHaAcabado()
                    ->haEmpezado()
                    ->activa();
            });

            return Command::SUCCESS;
        } catch (IntranetException $e) {
            avisa($envia, $e->getMessage(), '#', 'SAO');
            AlertLogger::error("Error en la connexió a SAO: " . $e->getMessage());
        } finally {
            if (isset($driver)) {
                $driver->quit();
            }
        }
    }
}
