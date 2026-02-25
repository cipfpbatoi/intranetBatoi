<?php

namespace Intranet\Sao\Actions;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intranet\Sao\SaoAnnexesAction;
use Intranet\Sao\SaoComparaAction;
use Intranet\Sao\SaoDocumentsAction;
use Intranet\Sao\SaoImportaAction;
use Intranet\Sao\SaoSyncAction;
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
     * Manté compatibilitat amb el flux antic delegant en la configuració de SaoDocumentsAction.
     *
     * @return mixed
     */
    public static function setFireFoxCapabilities(): mixed
    {
        return SaoDocumentsAction::setFireFoxCapabilities();
    }

    /**
     * @inheritdoc
     */
    public function index(RemoteWebDriver $driver, array $requestData, ?UploadedFile $file = null): mixed
    {
        $action = strtolower((string) ($requestData['accion'] ?? ''));

        return match ($action) {
            'a2' => (new SaoDocumentsAction($this->digitalSignatureService))->index($driver, $requestData, $file),
            'importa' => SaoImportaAction::index($driver),
            'compara' => SaoComparaAction::index($driver),
            'sync' => (new SaoSyncAction())->index($driver),
            'annexes' => (new SaoAnnexesAction())->index($driver),
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
