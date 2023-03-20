<?php

namespace Intranet\Http\Controllers;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Intranet\Services\SeleniumService;
use Intranet\Services\SignaturaService;
use Styde\Html\Facades\Alert;
use Facebook\WebDriver\Remote\RemoteWebElement;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class SaoSignaturaController
{

    public function index($password)
    {
        if (!SignaturaService::exists(AuthUser()->dni)) {
            Alert::danger('No tens cap signatura associada. Ves al perfil i afegeix-la');
            return redirect(route('alumnofct.index'));
        } else {
            $driver = SeleniumService::loginSAO(AuthUser()->dni, $password);
            try {
                $this->signa($driver);
                $driver->findElement(WebDriverBy::cssSelector("a.enlacePag"))->click();
                sleep(1);
                $this->signa($driver);
                $driver->close;
            } catch (Exception $e) {
                $driver->close();
            }
        }
        return redirect(route('alumnofct.index'));
    }

    /**
     * @param  RemoteWebDriver  $driver
     * @param  array  $dades
     * @return array
     */
    private function signa(RemoteWebDriver $driver)
    {
        $table = $driver->findElements(WebDriverBy::cssSelector("tr"));
        $pages = $this->getPages($table);
        foreach ($pages as $page) {
            try {

                $driver->navigate()->to($page);
                sleep(1);
                $signa = $this->clickCheckBox($driver, "Anexo2", true);
                if ($this->clickCheckBox($driver, "Anexo3", true) || $signa) {
                    $this->signBox($driver);
                }
            } catch (NoSuchElementException $e) {
                Alert::info($e->getMessage());
            }
        }
    }

    private function getIdSao(RemoteWebElement $tr)
    {
        $enlace = $tr->findElement(WebDriverBy::cssSelector("a[title='Detalles FCT']"));
        return explode("'", $enlace->getAttribute('href'))[1];
    }

    private function clickCheckBox(RemoteWebDriver $driver, $element, $signAlreadySigned = false):bool
    {
        try {
            $driver->findElement(WebDriverBy::id("chivato".$element));
            if ($signAlreadySigned) {
                return $this->click($driver, $element);
            }
            return false;
        } catch (NoSuchElementException $e) {
            return $this->click($driver, $element);
        }
    }

    /**
     * @param  RemoteWebDriver  $driver
     * @return void
     */
    private function signBox(RemoteWebDriver $driver): void
    {
        try {
            $fileInput = $driver->findElement(WebDriverBy::id('file'));
            $fileInput->setFileDetector(new LocalFileDetector());
            $fileInput->sendKeys(SignaturaService::getFile(AuthUser()->dni));
            sleep(1);
            $driver->findElement(WebDriverBy::id('submitfichero'))->click();
            sleep(1);
            $form = $driver->findElement(WebDriverBy::id('firma'));
            $form->submit();
        } catch (NoSuchElementException $e) {
            Alert::info($e->getMessage());
        }

    }

    /**
     * @param  array  $table
     * @return array
     */
    private function getPages(array $table): array
    {
        $pages = [];
        foreach ($table as $index => $tr) {
            if ($index) { //el primer és el titol i no cal iterar-lo
                $idSao = $this->getIdSao($tr);
                $pages[$index] = "https://foremp.edu.gva.es/inc/fcts/firmas_fct.php?id=$idSao";
            }
        }
        return $pages;
    }

    /**
     * @param  RemoteWebDriver  $driver
     * @param $element
     * @return bool
     * @throws NoSuchElementException
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    private function click(RemoteWebDriver $driver, $element): bool
    {
        $driver->findElement(WebDriverBy::id("quieroFirmar$element"))->click();
        try {
            $alert = $driver->wait(1)->until(
                WebDriverExpectedCondition::alertIsPresent()
            );
            $alert->accept();
        } catch (\Exception $e) {

        }
        return true;
    }
}
