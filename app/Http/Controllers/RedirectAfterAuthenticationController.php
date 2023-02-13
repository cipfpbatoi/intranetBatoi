<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;


class RedirectAfterAuthenticationController extends Controller
{
    /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        return redirect()->route($request->accion, ['password' => $request->password]);
    }
}
