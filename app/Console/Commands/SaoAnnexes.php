<?php

namespace Intranet\Console\Commands;


use Exception;
use Illuminate\Console\Command;
use Intranet\Entities\AlumnoFct;
use Intranet\Sao\Annexes;
use Intranet\Services\SeleniumService;


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
     * @return mixed
     */

    public function handle()
    {
        $envia = config('contacto.avisos.errores') ?? '021652470V';

        try {
            $driver = SeleniumService::loginSAO(
                config('services.selenium.SAO_USER'),
                config('services.selenium.SAO_PASS')
            );
             (new Annexes($driver))->execute(function ( ) {
                return AlumnoFct::whereNotNull('idSao')->noHaAcabado()->where('beca',0)->where('pg0301', 0)->activa();
            });
            return Command::SUCCESS;
        } catch (Exception $e) {
            avisa($envia, $e->getMessage(), '#', 'SAO');
        } finally {
            if (isset($driver)) {
                $driver->quit();
            }
        }
    }
}
