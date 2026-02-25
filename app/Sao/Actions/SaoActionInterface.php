<?php

namespace Intranet\Sao\Actions;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Http\UploadedFile;

/**
 * Contracte base per a una acció SAO executable amb un driver Selenium.
 */
interface SaoActionInterface
{
    /**
     * Executa l'acció SAO.
     *
     * @param RemoteWebDriver $driver Driver Selenium autenticat.
     * @param array<string, mixed> $requestData Dades del formulari d'entrada.
     * @param UploadedFile|null $file Fitxer opcional del formulari.
     * @return mixed
     */
    public function index(RemoteWebDriver $driver, array $requestData, ?UploadedFile $file = null): mixed;
}

