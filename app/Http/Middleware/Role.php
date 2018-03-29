<?php

namespace Intranet\Http\Middleware;

use Closure;

class Role
{

    public function handle($request, Closure $next, $role)
    {
        if (!UserisNameAllow($role))
            abort(404);
        return $next($request);
    }

}
