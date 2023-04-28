<?php

namespace Intranet\Console\Commands;

use Doctrine\DBAL\Query\QueryException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Console\Command;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\SeleniumService;


class SaoConnect extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sao:connect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Connecta SAO per sincronitzar les dades';


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
                         ->haEmpezado()
                         ->activa()
                         ->get() as $fct) {
                try {
                    if ($fct->idSao) {
                        $driver->navigate()->to("https://foremp.edu.gva.es/index.php?accion=11&idFct=$fct->idSao");
                        sleep(1);
                        $detalles = $driver->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"));
                        $dadesHores = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(14)"));
                        $horas = explode(
                            '/',
                            $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText()
                        )[0];
                        if ($fct->realizadas != $horas) {
                            $fct->realizadas = (int) $horas;
                            list($diarias,$ultima) =
                                $this->consultaDiario(
                                    $driver,
                                    $driver->findElement(WebDriverBy::cssSelector("#contenido"))
                                );
                            $fct->horas_diarias = (float)$diarias;
                            $fct->actualizacion = fechaSao(substr($ultima, 2, 10));
                            $fct->save();
                            $alumnes[] = $fct;
                        }
                    }
                } catch (NoSuchElementException $e) {
                    avisa(
                        $fct->Fct->cotutor??$envia,
                        'No trobada informació en el SAO de '.$fct->Alumno->shortName.' :'.$e->getMessage(),
                        '#',
                        'SAO'
                    );
                } catch (QueryException $e) {
                    avisa($envia, $e->getMessage(), '#', 'SAO');
                }
            }
            foreach ($alumnes as $alumne) {
                avisa($alumne->fct->cotutor??$envia, "Actualitzades hores de {$alumne->Alumno->fullName}", '#', 'SAO');
            }
        } catch (IntranetException $e) {
            avisa($envia, $e->getMessage(), '#', 'SAO');
        }
        if (isset($driver)) {
            $driver->close();
        }
    }

    private function consultaDiario(RemoteWebDriver $driver, \Facebook\WebDriver\Remote\RemoteWebElement $contenido)
    {
        $find = false;
        $i=4;
        do {
            $a = $contenido->findElements(WebDriverBy::cssSelector("#texto_cont p.diasDelDiario a"));
            $hores = trim(
                $contenido->findElement(
                    WebDriverBy::
                    cssSelector("div#diario$i table.tablaDiario tbody tr:nth-child(2) td.celda1:nth-child(4)")
                )->getText());
            if ($hores > 0) {
                $find = true;
                $dia = explode(',', $a[$i]->getAttribute('href'))[2];
            }
        } while (!$find && $i-- >0);
        if ($find) {
            return array($hores, $dia);
        } else {
            $driver->findElement(WebDriverBy::cssSelector("p.celdaInfoAlumno a:nth-child(1)"))->click();
            sleep(1);
            return $this->consultaDiario($driver, $driver->findElement(WebDriverBy::cssSelector("#contenido")));
        }
    }

}
