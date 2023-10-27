<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Requests\PasswordRequest;
use Intranet\Services\SeleniumService;
use Styde\Html\Facades\Alert;


class RedirectAfterAuthenticationController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(PasswordRequest $request)
    {
        $class = 'Intranet\Sao\\'. ucfirst($request->accion);
        if (method_exists($class, 'setFireFoxCapabilities')) {
            $caps = $class::setFireFoxCapabilities();
        }
        try {
            $driver = SeleniumService::loginSAO(AuthUser()->dni, $request->password, $caps??null);
            if ($request->hasFile('file')) {
                return $class::index($driver, $request->toArray(),$request->file('file'));
            } else {
                return $class::index($driver, $request->toArray());
            }

        } catch (\Throwable $exception) {
            Alert::info($exception->getMessage());
            if (isset($driver)) {
                $driver->close();
            }
            return back();
        }
    }
}
