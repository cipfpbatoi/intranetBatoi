<?php
namespace Intranet\Services;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverWait;
use Intranet\Exceptions\IntranetException;

class SeleniumService
{
    private $driver;

    public function __construct( $dni, $password)
    {
        $this->driver = self::loginItaca($dni, $password);
    }

    /**
     * @return RemoteWebDriver|null
     */
    public function getDriver(): ?RemoteWebDriver
    {
        return $this->driver;
    }

    public function quit()
    {
        $this->driver->quit();
    }

    /**
     * @param $password
     * @return RemoteWebDriver
     * @throws IntranetException
     */
    public static function loginSAO($dni, $password, $desiredCapabilities=null): RemoteWebDriver
    {
        try {
            $desiredCapabilities = $desiredCapabilities??DesiredCapabilities::firefox();
            $driver = RemoteWebDriver::create(config('services.selenium.url'), $desiredCapabilities);
        } catch (\Exception $e) {
            throw new IntranetException('No s\'ha pogut connectar al servidor de Selenium');
        }
        $driver->get(config('services.selenium.SAO'));
        $dni = substr($dni, -9);
        $driver->findElement(WebDriverBy::name('usuario')) // find usuario
        ->sendKeys($dni);
        $driver->findElement(WebDriverBy::name('password'))
            ->sendKeys($password);
        $driver->findElement(WebDriverBy::cssSelector('.botonform'))
            ->click();
        $driver->get(config('services.selenium.SAO').'?op=2&subop=0');
        sleep(1);
        $name = $driver->findElement(WebDriverBy::cssSelector('.botonform'))->getAttribute('name');
        if ($name === 'login') {
            $driver->close();
            throw new IntranetException('Password no vàlid. Has de ficarl el del SAO');
        } else {
            return $driver;
        }
    }

    /**
     * @param  RemoteWebDriver  $driver
     * @return void
     * @throws \Facebook\WebDriver\Exception\UnknownErrorException
     */
    public static function loginItaca($dni, $password): RemoteWebDriver
    {
        try {
            $desiredCapabilities = $desiredCapabilities??DesiredCapabilities::firefox();
            $driver = RemoteWebDriver::create(config('services.selenium.url'), $desiredCapabilities);
        } catch (\Exception $e) {
            throw new IntranetException('No s\'ha pogut connectar al servidor de Selenium');
        }
        $dni = substr($dni, -9);
        $driver->get(config('services.selenium.itaca'));
        $driver->findElement(WebDriverBy::id('form1:j_username')) // find usuario
        ->sendKeys($dni);
        $driver->findElement(WebDriverBy::id('form1:j_password'))
            ->sendKeys($password);
        $driver->findElement(WebDriverBy::name('form1:j_id47'))
            ->click();
        sleep(1);
        return $driver;

    }

    public function fill($selector, $keys, $driver = null)
    {
        $driver = $driver ?? $this->driver;
        $formulari = $driver->findElement($selector);
        $formulari->clear();
        $formulari->sendKeys($keys);
        $formulari->sendKeys(WebDriverKeys::ENTER);
    }

    public function waitAndClick($xpath, $driver = null)
    {
        $driver = $driver ?? $this->driver;
        $element = is_string($xpath)?WebDriverBy::xpath($xpath):$xpath;
        $wait = new WebDriverWait($this->driver, 10);
        $wait->until(WebDriverExpectedCondition::elementToBeClickable($element));
        $driver->findElement($element)->click();
    }

    public function gTPersonalLlist()
    {
        try {
            $this->driver->get('https://itaca3.edu.gva.es/itaca3-gad/');
            $this->waitAndClick("//span[contains(text(),'Gestión')]");
            $this->waitAndClick("//span[contains(text(),'Personal')]");
            $this->waitAndClick("//span[contains(text(),'Listado Personal')]");
        } catch (\Exception $e) {
            $this->driver->quit();
            throw new IntranetException($e->getMessage());
        }
    }


}
