<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Requests\PasswordRequest;
use Intranet\Services\Signature\DigitalSignatureService;
use Intranet\Services\Automation\SeleniumService;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Throwable;
use ReflectionMethod;
use Exception;

class RedirectAfterAuthenticationController extends Controller
{
    public function __invoke(PasswordRequest $request)
    {
        $className = $this->resolveClassName($request->accion);

        if (!class_exists($className)) {
            throw new Exception("La classe $className no existeix.");
        }

        $caps = method_exists($className, 'setFireFoxCapabilities')
            ? $className::setFireFoxCapabilities()
            : null;

        $driver = null;

        try {
            $driver = SeleniumService::loginSAO(authUser()->dni, $request->password, $caps);
            return $this->executeAction($className, $driver, $request);
        } catch (Throwable $exception) {
            Alert::info($exception->getMessage());
        } finally {
            if ($driver) {
                try {
                    $driver->quit();
                } catch (Throwable $quitException) {
                    Log::warning('No s\'ha pogut tancar la sessio Selenium en finally', [
                        'error' => $quitException->getMessage(),
                    ]);
                }
            }
        }
        return back();

    }

    private function resolveClassName(string $action): string
    {
        return 'Intranet\\Sao\\' . Str::ucfirst($action);
    }

    private function executeAction(string $className, $driver, PasswordRequest $request)
    {
        $reflection = new ReflectionMethod($className, 'index');
        $ds = new DigitalSignatureService();
        $parameters = [$driver, $request->toArray()];

        if ($request->hasFile('file')) {
            $parameters[] = $request->file('file');
        }
        return $reflection->isStatic()
            ? $className::index(...$parameters)
            : (new $className($ds))->index(...$parameters);
    }
}
