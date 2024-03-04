<?php

namespace Intranet\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Intranet\Services\NavigationService;

class CustomBackMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('back')) {
            NavigationService::dropFromHistory();
        } else {
            NavigationService::addToHistory($request->fullUrl());
        }
        return $next($request);
    }
}
