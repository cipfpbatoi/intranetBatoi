<?php

namespace Intranet\Http\Middleware;

use Closure;

class Role
{

    public function handle($request, Closure $next, $role)
    {
        if (UserisNameAllow($role) || isAdmin()) {
            return $next($request);
        }
        else {
            abort(404, 'No estas autoritzat');
        }

    }

}
