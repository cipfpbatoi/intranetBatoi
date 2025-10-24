<?php

namespace Intranet\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{

    public function handle($request, Closure $next, $role)
    {
        $user = Auth::user();
        if (!$user) {
            // No autenticat -> al login amb missatge
            return redirect()->route('login');
        }
        if (userIsNameAllow($role) || isAdmin()) {
            return $next($request);
        }
        else {
            abort(404, 'No estas autoritzat');
        }

    }

}
