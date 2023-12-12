<?php
namespace Intranet\Services;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Intranet\Exceptions\IntranetException;
use Intranet\Exceptions\SeleniumException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SeleniumService
{

    /**
     * @param $password
     * @return RemoteWebDriver
     * @throws IntranetException
     */
    public static function loginSAO($dni, $password, $desiredCapabilities=null): RemoteWebDriver
    {
        try {
            $desiredCapabilities = $desiredCapabilities??DesiredCapabilities::firefox();
            $driver = RemoteWebDriver::create('http://'.(config('services.selenium.url')), $desiredCapabilities);
        } catch (\Exception $e) {
            throw new SeleniumException('No s\'ha pogut connectar al servidor de Selenium');
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
            $driver = RemoteWebDriver::create('http://'.config('services.selenium.url'), $desiredCapabilities);

        } catch (\Exception $e) {
            throw new SeleniumException('No s\'ha pogut connectar al servidor de Selenium');
        }
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
}
