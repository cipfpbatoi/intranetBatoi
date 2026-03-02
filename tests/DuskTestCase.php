<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use InvalidArgumentException;
use Illuminate\Support\Facades\URL;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;

/**
 * Base Dusk test case per a proves E2E del projecte.
 */
abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Força URL base i esquema per evitar redirects HTTPS no vàlids en Dusk.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $baseUrl = (string) (env('DUSK_APP_URL') ?: env('APP_URL') ?: 'http://localhost');
        $baseUrl = rtrim($baseUrl, '/');
        $scheme = parse_url($baseUrl, PHP_URL_SCHEME) ?: 'http';

        config(['app.url' => $baseUrl]);
        config(['dusk.domain' => null]);
        Browser::$baseUrl = $baseUrl;
        URL::forceRootUrl($baseUrl);
        URL::forceScheme($scheme);
    }

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
            $capabilities->setCapability('acceptInsecureCerts', true);

            return $capabilities;
        }

        if ($browser === 'chrome') {
            $options = (new ChromeOptions())->addArguments([
                '--disable-dev-shm-usage',
                '--no-sandbox',
                '--window-size=1920,1080',
                '--ignore-certificate-errors',
            ]);

            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
            $capabilities->setCapability('acceptInsecureCerts', true);

            return $capabilities;
        }

        throw new InvalidArgumentException("DUSK_BROWSER no suportat: {$browser}");
    }
}
