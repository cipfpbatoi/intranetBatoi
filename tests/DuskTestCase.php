<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use InvalidArgumentException;
use Laravel\Dusk\TestCase as BaseTestCase;

/**
 * Base Dusk test case per a proves E2E del projecte.
 */
abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Crea el driver remot de navegador (Selenium Grid).
     */
    protected function driver(): RemoteWebDriver
    {
        $seleniumUrl = (string) (env('DUSK_DRIVER_URL')
            ?: env('SELENIUM_URL')
            ?: 'http://127.0.0.1:9515');
        $browser = strtolower((string) env('DUSK_BROWSER', 'chrome'));
        $capabilities = $this->makeCapabilities($browser);

        return RemoteWebDriver::create($seleniumUrl, $capabilities);
    }

    /**
     * Genera capabilities segons navegador disponible al Grid.
     *
     * @param string $browser
     * @return \Facebook\WebDriver\Remote\DesiredCapabilities
     */
    protected function makeCapabilities(string $browser): DesiredCapabilities
    {
        if ($browser === 'firefox') {
            $options = (new FirefoxOptions())->addArguments([
                '--width=1920',
                '--height=1080',
            ]);

            $capabilities = DesiredCapabilities::firefox();
            $capabilities->setCapability(FirefoxOptions::CAPABILITY, $options);

            return $capabilities;
        }

        if ($browser === 'chrome') {
            $options = (new ChromeOptions())->addArguments([
                '--disable-dev-shm-usage',
                '--no-sandbox',
                '--window-size=1920,1080',
            ]);

            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

            return $capabilities;
        }

        throw new InvalidArgumentException("DUSK_BROWSER no suportat: {$browser}");
    }
}
