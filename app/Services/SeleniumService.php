<?php
namespace Intranet\Services;

use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverWait;
use Intranet\Exceptions\IntranetException;
use Intranet\Exceptions\SeleniumException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SeleniumService
{
    private $driver;

    public function __construct( $dni, $password)
    {
        $this->driver = self::loginItaca($dni, $password);
    }

    /**
     * @param  mixed  $desiredCapabilities
     * @return RemoteWebDriver
     * @throws SeleniumException
     */
    private static function getDriverSelenium(mixed $desiredCapabilities=null): RemoteWebDriver
    {
        try {
            if ($desiredCapabilities == null) {
                if (config('services.selenium.firefox_path')) {
                     $desiredCapabilities = DesiredCapabilities::firefox()->setCapability(FirefoxOptions::CAPABILITY,
                        ['binary' => config('services.selenium.firefox_path')]);
                } else {
                    $desiredCapabilities = DesiredCapabilities::firefox();
                }
            }
            $driver = RemoteWebDriver::create('http://'.(config('services.selenium.url')), $desiredCapabilities,10000,200000);
        } catch (\Exception $e) {
            throw new SeleniumException('No s\'ha pogut connectar al servidor de Selenium'.$e->getMessage());
        }
        return $driver;
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
        $driver = self::getDriverSelenium($desiredCapabilities);
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
        $driver = self::getDriverSelenium();
        $dni = substr($dni, -9);
        $driver->get(config('services.selenium.itaca'));
        $driver->findElement(WebDriverBy::id('form1:j_username')) // find usuario
        ->sendKeys($dni);
        $driver->findElement(WebDriverBy::id('form1:j_password'))
            ->sendKeys($password);
        $driver->findElement(WebDriverBy::cssSelector('input[value="Entrar"]'))
            ->click();
        sleep(1);
        try {
            $driver->findElement(WebDriverBy::xpath("//dt[contains(@class, 'error') and span[contains(text(), 'La contraseña no es válida')]]"));
            $driver->close();
            throw new IntranetException('Password no vàlid. Has de ficarl el de l\'ITACA');
        } catch (\Exception $e) {
        }
        return $driver;
    }


    public static function restartSelenium()
    {
        $process = Process::fromShellCommandline("echo '".config('services.selenium.SELENIUM_ROOT_PASS')
            ."'  | ssh intranet@172.16.9.10 'sudo -S /sbin/reboot'");
        try {
            $process->mustRun();
            echo $process->getOutput();
        } catch (ProcessFailedException $exception) {
            echo $exception->getMessage();
        }
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
            sleep(1);
            $this->closeNoticias();
            $this->waitAndClick("//span[contains(text(),'Gestión')]");
            $this->waitAndClick("//span[contains(text(),'Personal')]");
            $this->waitAndClick("//span[contains(text(),'Listado Personal')]");
        } catch (\Exception $e) {
            $this->driver->quit();
            throw new IntranetException($e->getMessage());
        }
    }

    private function closeNoticias()
    {
        try {
            $elements = $this->driver->findElements(WebDriverBy::cssSelector('.z-window-close.imc--bt-terciari'));
            foreach ($elements??[] as $element) {
                $element->click();
            }
        } catch (\Exception $e) {
            // No pasa res
        }
    }


}
