<?php

namespace Intranet\Http\Middleware;

use Closure;

class RoleMiddleware
{

    public function handle($request, Closure $next, $role)
    {
        if (userIsNameAllow($role) || isAdmin()) {
            return $next($request);
        }
        else {
            abort(404, 'No estas autoritzat');
        }

    }

}
