<?php

namespace Intranet\Sao\Support;

use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Utilitats bàsiques de navegació per al flux SAO.
 */
class SaoNavigator
{
    /**
     * Torna a la pantalla principal de SAO i aplica una xicoteta espera.
     *
     * @param RemoteWebDriver $driver
     * @param int|null $sleepSeconds
     * @return void
     */
    public function backToMain(RemoteWebDriver $driver, ?int $sleepSeconds = null): void
    {
        $driver->get((string) config('sao.urls.main', 'https://foremp.edu.gva.es/index.php?op=2&subop=0'));
        sleep($sleepSeconds ?? (int) config('sao.navigation.sleep_seconds', 1));
    }
}
