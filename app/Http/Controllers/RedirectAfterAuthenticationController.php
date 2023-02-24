<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Requests\PasswordRequest;


class RedirectAfterAuthenticationController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(PasswordRequest $request)
    {
        return redirect()->route($request->accion, ['password' => $request->password]);
    }
}
