<?php

namespace Intranet\Sao;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Intranet\Entities\Adjunto;
use Intranet\Entities\AlumnoFct;
use Intranet\Services\UI\AlertLogger;
use Intranet\Services\Document\AttachedFileService;
use Intranet\Entities\Signatura;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Acció SAO per descarregar i enllaçar annexos.
 */
class SaoAnnexesAction
{
    /**
     * Driver Selenium autenticat en SAO.
     *
     * @var RemoteWebDriver
     */
    private RemoteWebDriver $driver;

    /**
     * Consulta opcional per acotar les FCT a processar.
     *
     * @var callable|null
     */
    private $queryCallback = null;

    /**
     * Comptadors de l'última execució.
     *
     * @var array<string, int>
     */
    private array $summary = [
        'processed' => 0,
        'skipped' => 0,
        'downloaded' => 0,
        'errors' => 0,
    ];

    /**
     * Crea l'acció de descàrrega d'annexos SAO.
     *
     * @param mixed $digitalSignatureService Servei legacy no usat en aquesta acció.
     */
    public function __construct($digitalSignatureService = null)
    {

    }

    /**
     * Executa la descàrrega d'annexos amb una consulta opcional.
     *
     * @param RemoteWebDriver $driver
     * @param callable|null $queryCallback
     * @return mixed
     */
    public function execute($driver, ?callable $queryCallback = null)
    {
        $this->queryCallback = $queryCallback;
        return $this->index($driver);
    }

    /**
     * Executa el procés i torna a la pantalla anterior.
     *
     * @param RemoteWebDriver $driver
     * @return mixed
     */
    public function index($driver)
    {
        $this->driver = $driver;
        try {
            $this->processFcts();
        } catch (Throwable $e) {
            $this->summary['errors']++;
            report($e);
            Log::channel('sao')->error('Error en l\'acció de descàrrega d\'annexos SAO.', [
                'error' => $e->getMessage(),
            ]);
            AlertLogger::error($e->getMessage());
            $this->publishSummary();
        } finally {
            try {
                $this->driver->quit();
            } catch (Throwable $quitException) {
                Log::channel('sao')->warning('No s\'ha pogut tancar el driver de SAO en annexos.', [
                    'error' => $quitException->getMessage(),
                ]);
            }
        }
        return back();
    }

    /**
     * Retorna els comptadors de l'última execució.
     *
     * @return array<string, int>
     */
    public function summary(): array
    {
        return $this->summary;
    }

    /**
     * Processa totes les FCT candidates i publica un resum observable.
     *
     * @return void
     */
    private function processFcts(): void
    {
        foreach ($this->getValidFcts() as $fct) {
            $this->summary['processed']++;

            if ($this->isAnnexDownloaded($fct)) {
                $this->summary['skipped']++;
                continue;
            }

            try {
                $this->downloadAnnex($fct);
                $this->summary['downloaded']++;
            } catch (Throwable $e) {
                $this->summary['errors']++;
                report($e);
                Log::channel('sao')->warning('Error descarregant annex d\'una FCT en SAO.', [
                    'fct_id' => $fct->id ?? null,
                    'alumne' => $fct->Alumno->fullName ?? null,
                    'error' => $e->getMessage(),
                ]);
                AlertLogger::error("Error en descarregar annexes de {$fct->Alumno->fullName}: " . $e->getMessage());
            }
        }

        $this->publishSummary();
    }

    /**
     * Obté les FCT candidates a revisar.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getValidFcts()
    {
        if ($this->queryCallback) {
            return call_user_func($this->queryCallback)->get();
        }

        return AlumnoFct::realFcts()
            ->where('beca', 0)
            ->whereNotNull('idSao')
            ->activa()
            ->get();

    }

    /**
     * Determina si la FCT ja té annexos SAO descarregats.
     *
     * @param AlumnoFct $fct
     * @return bool
     */
    private function isAnnexDownloaded($fct): bool
    {
        return Adjunto::where('size', 1024)
            ->where('route', "alumnofctaval/{$fct->id}")
            ->exists();
    }


    /**
     * Descarrega i registra l'annex d'una FCT.
     *
     * @param AlumnoFct $fct
     * @return void
     * @throws Exception
     */
    private function downloadAnnex($fct): void
    {

        AlertLogger::info("Intentant descarregar annexes per a {$fct->Alumno->fullName}");

        // Navegar a la pàgina
        $baseUrl = (string) config('sao.urls.base', 'https://foremp.edu.gva.es');
        $url = "$baseUrl/inc/fcts/documentos_fct.php?id={$fct->idSao}&documento=2";
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

        $downloadLink = rtrim($baseUrl, '/') . '/' . ltrim($cut[1], '/');
        $this->saveAnnex($name, $downloadLink, $fct);
        $this->deleteSignatures($fct);
        $this->closePopup();

        AlertLogger::info("Annex descarregat correctament per a {$fct->Alumno->fullName}");
    }


    /**
     * Guarda l'enllaç de l'annex descarregat.
     *
     * @param string $name
     * @param string $downloadLink
     * @param AlumnoFct $fct
     * @return void
     */
    private function saveAnnex($name, $downloadLink, $fct): void
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

    /**
     * Elimina signatures pendents associades a la FCT descarregada.
     *
     * @param AlumnoFct $fct
     * @return void
     */
    private function deleteSignatures($fct): void
    {
        foreach (Signatura::where('idSao', $fct->idSao)->get() as $signatura) {
            $signatura->deleteFile();
            $signatura->delete();
        }
    }

    /**
     * Tanca la finestra de descàrrega de SAO.
     *
     * @return void
     */
    private function closePopup(): void
    {
        $this->driver->findElement(WebDriverBy::cssSelector(".botonSelec[value='Cerrar']"))->click();
        sleep(1);
    }

    /**
     * Escriu i mostra el resum de l'execució.
     *
     * @return void
     */
    private function publishSummary(): void
    {
        $message = sprintf(
            'Annexos SAO: %d FCT revisades, %d descarregats, %d ja existien, %d errors.',
            $this->summary['processed'],
            $this->summary['downloaded'],
            $this->summary['skipped'],
            $this->summary['errors']
        );

        Log::channel('sao')->info($message, $this->summary);

        if ($this->summary['errors'] > 0) {
            AlertLogger::warning($message);
            return;
        }

        AlertLogger::info($message);
    }

}
