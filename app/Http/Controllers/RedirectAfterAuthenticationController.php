<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Requests\PasswordRequest;
use Intranet\Services\SeleniumService;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Str;
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

        try {
            $driver = SeleniumService::loginSAO(authUser()->dni, $request->password, $caps);
            return $this->executeAction($className, $driver, $request);
        } catch (Throwable $exception) {
            Alert::info($exception->getMessage());
            $driver?->close();
            return back();
        }
    }

    private function resolveClassName(string $action): string
    {
        return 'Intranet\\Sao\\' . Str::ucfirst($action);
    }

    private function executeAction(string $className, $driver, PasswordRequest $request)
    {
        $reflection = new ReflectionMethod($className, 'index');
        $parameters = [$driver, $request->toArray()];

        if ($request->hasFile('file')) {
            $parameters[] = $request->file('file');
        }

        return $reflection->isStatic()
            ? $className::index(...$parameters)
            : app($className)->index(...$parameters);
    }
}
