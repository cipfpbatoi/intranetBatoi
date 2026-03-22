<?php

namespace Intranet\Sao\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intranet\Services\Automation\SeleniumService;
use Intranet\Services\Signature\DigitalSignatureService;
use ReflectionMethod;
use Throwable;

/**
 * Gestiona el cicle de vida de Selenium per a accions SAO.
 */
class SaoRunner
{
    /**
     * Executa una acció SAO amb login previ i tancament garantit de sessió.
     *
     * @param string $className Classe d'acció SAO (`Intranet\Sao\...`).
     * @param string $dni Identificador d'usuari per al login SAO.
     * @param string $password Contrasenya SAO.
     * @param array<string, mixed> $requestData Dades del formulari.
     * @param mixed $caps Capacitats Selenium opcionals.
     * @param UploadedFile|null $file Fitxer opcional del formulari.
     * @return mixed
     * @throws Throwable
     */
    public function run(
        string $className,
        string $dni,
        string $password,
        array $requestData,
        mixed $caps = null,
        ?UploadedFile $file = null
    ): mixed {
        $driver = null;

        try {
            $driver = SeleniumService::loginSAO($dni, $password, $caps);

            return $this->executeAction($className, $driver, $requestData, $file);
        } finally {
            if ($driver) {
                try {
                    $driver->quit();
                } catch (Throwable $quitException) {
                    Log::warning('No s\'ha pogut tancar la sessio Selenium en SaoRunner', [
                        'error' => $quitException->getMessage(),
                        'action' => $className,
                    ]);
                }
            }
        }
    }

    /**
     * Resol i executa el mètode `index` de l'acció SAO.
     *
     * @param string $className
     * @param mixed $driver
     * @param array<string, mixed> $requestData
     * @param UploadedFile|null $file
     * @return mixed
     */
    private function executeAction(
        string $className,
        mixed $driver,
        array $requestData,
        ?UploadedFile $file = null
    ): mixed {
        $reflection = new ReflectionMethod($className, 'index');
        $digitalSignatureService = new DigitalSignatureService();
        $parameters = [$driver, $requestData];

        if ($file !== null) {
            $parameters[] = $file;
        }

        return $reflection->isStatic()
            ? $className::index(...$parameters)
            : (new $className($digitalSignatureService))->index(...$parameters);
    }
}

