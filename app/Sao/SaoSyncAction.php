<?php

namespace Intranet\Sao;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Intranet\Entities\AlumnoFct;
use Intranet\Services\UI\AlertLogger;
use Intranet\Services\Signature\DigitalSignatureService;
use Illuminate\Support\Facades\Log;

/**
 * Acció SAO per sincronitzar dades d'alumnat FCT.
 */
class SaoSyncAction
{
    private RemoteWebDriver $driver;
    private $queryCallback = null;



    public function __construct( $digitalSignatureService = null)
    {

    }

    public function execute($driver, ?callable $queryCallback = null)
    {
        $this->queryCallback = $queryCallback;
        return $this->index($driver);
    }

    public function index($driver)
    {
        $this->driver = $driver;
        try {
            $this->processFcts();
        } catch (Exception $e) {
            report($e);
            Log::error('Error general en la sincronització SAO.', [
                'error' => $e->getMessage(),
            ]);
            AlertLogger::error("Error general en la sincronització: " . $e->getMessage());
        } finally {
            try {
                $this->driver->quit();
            } catch (\Throwable $quitException) {
                report($quitException);
                Log::warning('No s\'ha pogut tancar el driver de SAO en sincronització.', [
                    'error' => $quitException->getMessage(),
                ]);
            }
        }
        return back();
    }

    private function processFcts()
    {
        $alumnesActualitzats = [];

        foreach ($this->getValidFcts() as $fct) {
            try {
                if (!$fct->idSao) {
                    continue;
                }

                $novaHora = $this->obtenirHoresFct($fct->idSao);

                if ($novaHora !== null && $fct->realizadas != $novaHora) {
                    $this->actualitzarFct($fct, $novaHora);
                    $alumnesActualitzats[] = $fct->Alumno->shortName;
                }
            } catch (Exception $e) {
                report($e);
                Log::error('Error sincronitzant FCT de SAO.', [
                    'fct_id' => $fct->id ?? null,
                    'id_sao' => $fct->idSao ?? null,
                    'error' => $e->getMessage(),
                ]);
                AlertLogger::error("Error en la FCT de {$fct->Alumno->shortName}: " . $e->getMessage());
            }
        }

        AlertLogger::info('Fcts sincronitzades: ' . implode(', ', $alumnesActualitzats));
    }

    private function getValidFcts()
    {
        if ($this->queryCallback) {
            return call_user_func($this->queryCallback)->get();
        }

        return AlumnoFct::realFcts()->haEmpezado()->activa()->get();
    }

    private function obtenirHoresFct($idSao): ?int
    {
        try {
            $baseUrl = (string) config('sao.urls.base', 'https://foremp.edu.gva.es');
            $this->driver->navigate()->to("$baseUrl/index.php?accion=11&idFct=$idSao");

            $this->driver->wait(10)->until(
                WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"))
            );

            $detalles = $this->driver->findElement(WebDriverBy::cssSelector("table.tablaDetallesFCT tbody"));
            $dadesHores = $detalles->findElement(WebDriverBy::cssSelector("tr:nth-child(14)"));
            $horas = explode('/', $dadesHores->findElement(WebDriverBy::cssSelector("td:nth-child(4)"))->getText())[0];

            return (int) $horas;
        } catch (Exception $e) {
            throw new Exception("Error obtenint hores per ID SAO $idSao: " . $e->getMessage(), 0, $e);
        }
    }

    private function actualitzarFct($fct, $novaHora): void
    {
        $fct->realizadas = $novaHora;

        try {
            list($diarias, $ultima) = $this->consultaDiario();
            $fct->horas_diarias = (float) $diarias;
            $fct->actualizacion = fechaSao(substr($ultima, 2, 10));
        } catch (Exception $e) {
            report($e);
            Log::warning("Informació incompleta de diari per FCT ID {$fct->id}", [
                'id_sao' => $fct->idSao ?? null,
                'error' => $e->getMessage(),
            ]);
            AlertLogger::info('Informació incompleta per a ' . $fct->Alumno->shortName);
        }

        $fct->save();
    }

    private function consultaDiario()
    {
        $find = false;
        $i = 4;

        do {
            $a = $this->driver->findElements(WebDriverBy::cssSelector("#texto_cont p.diasDelDiario a"));
            if (empty($a) || !isset($a[$i])) {
                throw new Exception("No s'han trobat enllaços de dies al diari.");
            }
            $hores = trim(
                $this->driver->findElement(WebDriverBy::cssSelector("div#diario$i table.tablaDiario tbody tr:nth-child(2) td.celda1:nth-child(4)"))
                    ->getText()
            );

            if ($hores > 0) {
                $find = true;
                $dia = explode(',', $a[$i]->getAttribute('href'))[2];
            }
        } while (!$find && $i-- > 0);

        if ($find) {
            return [$hores, $dia];
        }

        $this->driver->findElement(WebDriverBy::cssSelector("p.celdaInfoAlumno a:nth-child(1)"))->click();
        $this->driver->wait(10)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::cssSelector("#contenido"))
        );

        return $this->consultaDiario();
    }
}
