<?php

namespace Intranet\Sao\Actions;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intranet\Sao\A2;
use Intranet\Sao\Annexes;
use Intranet\Sao\Compara;
use Intranet\Sao\Importa;
use Intranet\Sao\Sync;
use Intranet\Services\Signature\DigitalSignatureService;
use RuntimeException;

/**
 * Entrypoint unificat per a les operacions SAO.
 */
class SAOAction implements SaoActionInterface
{
    private DigitalSignatureService $digitalSignatureService;

    public function __construct(?DigitalSignatureService $digitalSignatureService = null)
    {
        $this->digitalSignatureService = $digitalSignatureService ?? app(DigitalSignatureService::class);
    }

    /**
     * Retorna les capacitats Firefox necessàries per a descàrregues SAO.
     *
     * Manté compatibilitat amb el flux antic delegant en la configuració d'A2.
     *
     * @return mixed
     */
    public static function setFireFoxCapabilities(): mixed
    {
        return A2::setFireFoxCapabilities();
    }

    /**
     * @inheritdoc
     */
    public function index(RemoteWebDriver $driver, array $requestData, ?UploadedFile $file = null): mixed
    {
        $action = strtolower((string) ($requestData['accion'] ?? ''));

        return match ($action) {
            'a2' => (new A2($this->digitalSignatureService))->index($driver, $requestData, $file),
            'importa' => Importa::index($driver),
            'compara' => Compara::index($driver),
            'sync' => (new Sync())->index($driver),
            'annexes' => (new Annexes())->index($driver),
            default => $this->executeLegacyAction($action, $driver, $requestData, $file),
        };
    }

    /**
     * Manté compatibilitat amb accions SAO legacy no migrades.
     *
     * @param string $action
     * @param RemoteWebDriver $driver
     * @param array<string, mixed> $requestData
     * @param UploadedFile|null $file
     * @return mixed
     */
    private function executeLegacyAction(
        string $action,
        RemoteWebDriver $driver,
        array $requestData,
        ?UploadedFile $file = null
    ): mixed {
        $className = 'Intranet\\Sao\\' . Str::ucfirst($action);

        if (!class_exists($className)) {
            throw new RuntimeException("No existeix cap accio SAO registrada per a '$action'");
        }

        if ($file !== null) {
            return (new $className($this->digitalSignatureService))->index($driver, $requestData, $file);
        }

        return (new $className($this->digitalSignatureService))->index($driver, $requestData);
    }
}
