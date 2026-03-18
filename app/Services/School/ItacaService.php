<?php


namespace Intranet\Services\School;


use Google\Service\AndroidPublisher\ActivateBasePlanRequest;
use Intranet\Entities\Actividad;
use Facebook\WebDriver\WebDriverBy;
use Intranet\Exceptions\IntranetException;
use Intranet\Services\Automation\SeleniumService;
use Intranet\Services\UI\AppAlert as Alert;

class ItacaService
{
    private $ss;

    public function __construct(string $dni, string $password, $selenium = null, bool $validateDriver = true)
    {
        if ($selenium) {
            $this->ss = $selenium;
            if ($validateDriver && !$this->ss->getDriver()) {
                throw new IntranetException(
                    "Error al iniciar la sessió a ITACA",
                    500,
                    "Error al iniciar la sessió a ITACA"
                );
            }
            return;
        }

        try {
            $this->ss = new SeleniumService($dni, $password);
            if (!$this->ss->getDriver()) {
                throw new IntranetException(
                    "Error al iniciar la sessió a ITACA",
                    500,
                    "Error al iniciar la sessió a ITACA"
                );
            }
        } catch (\Throwable $e) {
            throw new IntranetException(
                "Error al iniciar la sessió a ITACA",
                500,
                "Error al iniciar la sessió a ITACA",
                true,
                [],
                $e
            );
        }
    }

    public function close()
    {
        $this->ss->quit();
    }

    public function goToLlist()
    {
        $this->ss->getDriver()->get('https://itaca3.edu.gva.es/itaca3-gad/');
        $this->closeNoticias();
        $this->ss->waitAndClick("//span[contains(text(),'Gestión')]");
        $this->ss->waitAndClick("//span[contains(text(),'Personal')]");
        $this->ss->waitAndClick("//span[contains(text(),'Listado Personal')]");
    }

    public function processActivitat(Actividad $activitat): bool
    {
        return true;
    }

    private function closeNoticias()
    {
        try {
            $remainingWindows = [];
            $retryCount = 0;
            while (true) {
                $elements = $this->ss->getDriver()->findElements(WebDriverBy::cssSelector('.z-window-close.imc--bt-terciari'));
                if (empty($elements) && empty($remainingWindows)) break;
                foreach ($elements as $element) {
                    try {
                        $element->click();
                        usleep(500000);
                    } catch (\Throwable $e) {
                        \Log::info('No s\'ha pogut tancar una finestra emergent.', [
                            'exception' => $e->getMessage(),
                        ]);
                        $remainingWindows[] = $element;
                    }
                }
                $elements = $remainingWindows;
                $remainingWindows = [];
                if (++$retryCount > 10) break;
            }
        } catch (\Throwable $e) {
            \Log::warning('Error en tancar les finestres emergents de SAO.', [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
