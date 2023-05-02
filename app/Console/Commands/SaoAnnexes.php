<?php

namespace Intranet\Console\Commands;


use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Console\Command;
use Intranet\Entities\Adjunto;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\AttachedFileService;
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
        $envia = config('contacto.avisos.errores')??'021652470V';
        try {
            $driver = SeleniumService::loginSAO(
                config('services.selenium.SAO_USER'),
                config('services.selenium.SAO_PASS')
            );
            $alumnes = [];

            foreach (AlumnoFctAval::whereNotNull('idSao')
                         ->noHaAcabado()
                         ->where('beca', 0)
                         ->where('pg0301', 0)
                         ->activa()
                         ->get() as $fct) {
                try {
                    $find = Adjunto::where('size', 1024)->where('route', 'alumnofctaval/'.$fct->id)->count();
                    if (!$find) {
                        $driver->navigate()->to(
                            "https://foremp.edu.gva.es/inc/fcts/documentos_fct.php?id={$fct->idSao}&documento=2"
                        );
                        sleep(1);
                        try {
                            $name = trim(
                                $driver->findElement(
                                    WebDriverBy::cssSelector(
                                        "table.tablaListadoFCTs tbody tr:nth-child(2) td:nth-child(1)"
                                    )
                                )->getText()
                            );
                            $onclick = $driver->findElement(
                                WebDriverBy::cssSelector(".botonSelec[value='Descargar']")
                            )->getAttribute('onclick');
                            $cut1 = explode("'", $onclick);
                            AttachedFileService::saveLink(
                                $name,
                                "https://foremp.edu.gva.es/".$cut1[1],
                                'SAO:Annexe II i III',
                                'zip',
                                "alumnofctaval/$fct->id",
                                $fct->Alumno->tutor[0]->dni
                            );
                            $alumnes[] = $fct->Alumno;
                        } catch (Exception $e) {
                            // No trobats els annexes no es fa res
                        }
                        try {
                            $driver->findElement(WebDriverBy::cssSelector(".botonSelec[value='Cerrar']"))->click();
                            sleep(1);
                        } catch (Exception $e) {
                            avisa(
                                $fct->Fct->cotutor ?? $envia,
                                'Fct de '.$fct->Alumno->shortName." no trobada. Esborra-la de la intranet",
                                '#',
                                'SAO'
                            );
                        }
                    }
                } catch (NoSuchElementException $e) {
                avisa(
                    $fct->Fct->cotutor??$envia,
                    'No trobada informació en el SAO del annexes de '.$fct->Alumno->shortName,
                    '#',
                    'SAO'
                );
                } catch (IntranetException $e) {
                    avisa($envia, $e->getMessage(), '#', 'SAO');
                }
            }
            foreach ($alumnes as $alumne) {
                avisa($alumne->tutor[0]->dni ?? $envia, "Baixats annexes de {$alumne->fullName}", '#', 'SAO');
            }
        } catch (IntranetException $e) {
                avisa($envia, $e->getMessage(), '#', 'SAO');
        }
        if (isset($driver)) {
            $driver->quit();
        }
    }
}
