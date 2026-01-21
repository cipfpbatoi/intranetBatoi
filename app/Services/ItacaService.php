<?php


namespace Intranet\Services;


use Google\Service\AndroidPublisher\ActivateBasePlanRequest;
use Intranet\Entities\Actividad;
use Intranet\Entities\Falta_itaca;
use Intranet\Entities\Hora;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Intranet\Exceptions\IntranetException;
use Styde\Html\Facades\Alert;

/**
 * Servei ItacaService.
 */
class ItacaService
{
    private SeleniumService $ss;

    public function __construct(string $dni, string $password)
    {
        $this->ss = new SeleniumService($dni, $password);
        if (!$this->ss->getDriver()) {
            throw new IntranetException("Error al iniciar la sessió a ITACA");
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
    public function processFalta(Falta_itaca $falta): bool
    {
        try {
            $this->ss->fill(WebDriverBy::cssSelector('.itaca-grid.texto-busqueda.z-textbox'), $falta->idProfesor);
            $this->ss->waitAndClick("//button[contains(text(),'Buscar')]");
            sleep(1);
            $element = $this->ss->getDriver()->findElement(WebDriverBy::xpath("//div[contains(text(),'$falta->idProfesor')]"));
            (new WebDriverActions($this->ss->getDriver()))->contextClick($element)->perform();
            $this->ss->waitAndClick("//span[contains(text(),'Faltas docente')]");
            sleep(1);

            $fechaActual = date('d/m/Y');
            $this->ss->fill(WebDriverBy::xpath("//input[@value='$fechaActual']"), $falta->dia);
            $this->ss->waitAndClick("//button[contains(text(),'Cambiar Fecha')]");
            sleep(2);

            $diaSemana = date('N', strtotime($falta->dia)) + 1;
            $hora = Hora::find($falta->sesion_orden);
            $textHora = $hora->hora_ini.' - '.$hora->hora_fin;
            $expresionXPath = "//table//tr/td[$diaSemana]//div[starts-with(@title, '$textHora')]";
            $this->ss->waitAndClick($expresionXPath);
            sleep(1);
            $this->ss->waitAndClick("//button[contains(text(),'Impartido por titular')]");
            sleep(1);

            $checkboxLabel = $this->ss->getDriver()->findElement(WebDriverBy::xpath('//label[contains(text(), "Clase impartida por el profesor titular.")]'));
            $checkboxId = $checkboxLabel->getAttribute('for');
            $checkbox = $this->ss->getDriver()->findElement(WebDriverBy::id($checkboxId));
            if (!$checkbox->isSelected()) {
                $checkbox->click();
            }
            $this->ss->waitAndClick("//button[contains(text(),'Guardar')]");
            $this->ss->waitAndClick("//button[contains(text(),'Aceptar')]");
            $this->ss->waitAndClick(WebDriverBy::className('z-icon-times'));

            $falta->estado = 4;
            $falta->save();
            return true;
        } catch (\Exception $e) {
            Alert::danger("{$e->getMessage()} {$falta->Profesor->shortName} {$falta->dia} {$falta->sesion_orden}");
            return false;
        }
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
                    } catch (\Exception $e) {
                        $remainingWindows[] = $element;
                    }
                }
                $elements = $remainingWindows;
                $remainingWindows = [];
                if (++$retryCount > 10) break;
            }
        } catch (\Exception $e) {}
    }
}
