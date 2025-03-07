<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Requests\PasswordRequest;
use Intranet\Services\SeleniumService;
use Styde\Html\Facades\Alert;


class RedirectAfterAuthenticationController extends Controller
{


    public function __invoke(PasswordRequest $request)
    {
        $className = 'Intranet\Sao\\' . ucfirst($request->accion);

        if (!class_exists($className)) {
            throw new \Exception("La classe $className no existeix.");
        }

        $caps = method_exists($className, 'setFireFoxCapabilities') ? $className::setFireFoxCapabilities() : null;

        try {
            $driver = SeleniumService::loginSAO(authUser()->dni, $request->password, $caps);

            $reflection = new \ReflectionMethod($className, 'index');
            // Si la classe té un mètode estàtic `handle`, el cridem directament
            if ($reflection->isStatic()) {
                if ($request->hasFile('file')) {
                    return $className::index($driver, $request->toArray(), $request->file('file'));
                }
                return $className::index($driver, $request->toArray());
            }

            // Si no té `handle` estàtic, la instanciem i cridem el mètode corresponent
            $classInstance = app($className);
            if ($request->hasFile('file')) {
                return $classInstance->index($driver, $request->toArray(), $request->file('file'));
            }
            return $classInstance->index($driver, $request->toArray());

        } catch (\Throwable $exception) {
            Alert::info($exception->getMessage());
            if (isset($driver)) {
                $driver->close();
            }
            return back();
        }
    }
}
