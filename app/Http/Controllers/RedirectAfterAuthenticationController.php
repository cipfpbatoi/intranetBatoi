<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Requests\PasswordRequest;
use Intranet\Sao\Actions\SAOAction;
use Intranet\Sao\Support\SaoRunner;
use Intranet\Services\UI\AppAlert as Alert;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Orquestra l'execució d'accions SAO després de validar la contrasenya.
 */
class RedirectAfterAuthenticationController extends Controller
{
    private SaoRunner $saoRunner;

    public function __construct(?SaoRunner $saoRunner = null)
    {
        $this->saoRunner = $saoRunner ?? app(SaoRunner::class);
    }

    public function __invoke(PasswordRequest $request)
    {
        $className = SAOAction::class;

        $caps = method_exists($className, 'setFireFoxCapabilities')
            ? $className::setFireFoxCapabilities()
            : null;

        try {
            return $this->saoRunner->run(
                $className,
                (string) authUser()->dni,
                (string) $request->password,
                $request->toArray(),
                $caps,
                $request->file('file')
            );
        } catch (Throwable $exception) {
            report($exception);
            Log::warning('Error en executar acció després d\'autenticació SAO.', [
                'dni' => authUser()->dni ?? null,
                'error' => $exception->getMessage(),
            ]);
            Alert::info($exception->getMessage());
        }
        return back();

    }
}
