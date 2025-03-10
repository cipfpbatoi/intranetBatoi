<?php
namespace Intranet\Sao;

use Exception;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Intranet\Entities\Adjunto;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Services\AlertLogger;
use Intranet\Services\AttachedFileService;
use Intranet\Entities\Signatura;

class Annexes
{
    private $driver;

    private $queryCallback = null;

    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    public function execute(callable $queryCallback = null)
    {
        $this->queryCallback = $queryCallback;
        return $this->index();
    }

    public function index()
    {
        try {
            $this->processFcts();
        } catch (Exception $e) {
            AlertLogger::danger($e->getMessage());
        } finally {
            $this->driver->quit();
        }
        return back();
    }

    private function processFcts()
    {
         foreach ($this->getValidFcts() as $fct) {
            if (!$this->isAnnexDownloaded($fct)) {
                try {
                    $this->downloadAnnex($fct);
                } catch (Exception $e) {
                    AlertLogger::danger("Error en descarregar annexes de {$fct->Alumno->fullName}: " .$e->getMessage());
                }
            }
        }
    }

    private function getValidFcts()
    {
        if ($this->queryCallback) {
            return call_user_func($this->queryCallback)->get();
        }

        return AlumnoFctAval::realFcts()
            ->where('beca', 0)
            ->whereNotNull('idSao')
            ->activa()
            ->get();

    }

    private function isAnnexDownloaded($fct): bool
    {
        return Adjunto::where('size', 1024)
            ->where('route', "alumnofctaval/{$fct->id}")
            ->exists();
    }


    private function downloadAnnex($fct)
    {

        AlertLogger::info("Intentant descarregar annexes per a {$fct->Alumno->fullName}");

        // Navegar a la pàgina
        $url = "https://foremp.edu.gva.es/inc/fcts/documentos_fct.php?id={$fct->idSao}&documento=2";
        $this->driver->navigate()->to($url);
        sleep(2);

        // Comprovar si el botó de descàrrega existeix
        $downloadButtons = $this->driver->findElements(WebDriverBy::cssSelector(".botonSelec[value='Descargar']"));
        if (count($downloadButtons) === 0) {
            throw new Exception("No s'ha trobat el botó de descàrrega per a {$fct->Alumno->fullName} a la URL: $url");
        }

        // Obtenir el nom del fitxer
        $name = trim(
            $this->driver->findElement(
                WebDriverBy::cssSelector("table.tablaListadoFCTs tbody tr:nth-child(2) td:nth-child(1)")
            )->getText()
        );

        // Obtenir l'enllaç de descàrrega
        $onclick = $downloadButtons[0]->getAttribute('onclick');
        if (!$onclick) {
            throw new Exception("No s'ha pogut obtenir l'atribut 'onclick' per a {$fct->Alumno->fullName}");
        }

        $cut = explode("'", $onclick);
        if (!isset($cut[1])) {
            throw new Exception("No s'ha pogut extraure l'enllaç de descàrrega per a {$fct->Alumno->fullName}");
        }

        $downloadLink = "https://foremp.edu.gva.es/" . $cut[1];
        $this->saveAnnex($name, $downloadLink, $fct);
        $this->deleteSignatures($fct);
        $this->closePopup( );

        AlertLogger::success("Annex descarregat correctament per a {$fct->Alumno->fullName}");
    }


    private function saveAnnex($name, $downloadLink, $fct)
    {
        $dniTutor = $fct->Alumno->tutor[0]['dni']  ?? null;

        AttachedFileService::saveLink(
            $name,
            $downloadLink,
            'SAO:Annexe II i III',
            'zip',
            "alumnofctaval/{$fct->id}",
            $dniTutor
        );
    }

    private function deleteSignatures($fct)
    {
        foreach (Signatura::where('idSao', $fct->idSao)->get() as $signatura) {
            $signatura->deleteFile();
            $signatura->delete();
        }
    }

    private function closePopup( )
    {
        $this->driver->findElement(WebDriverBy::cssSelector(".botonSelec[value='Cerrar']"))->click();
        sleep(1);
    }

}
