<?php
namespace Intranet\Services;




use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Intranet\Exceptions\IntranetException;

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
}
