<?php

namespace Intranet\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Routing\Redirector;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::user();
        if (!$user) {
            // Redirigeix com a convidat per evitar bucles
            return redirect()->guest(route('login'))->with('error', 'Has d’iniciar sessió.');
        }
        if (userIsNameAllow($role) || isAdmin()) {
            $response = $next($request);
        } else {
            abort(SymfonyResponse::HTTP_FORBIDDEN, 'No estàs autoritzat.');
        }

       return $this->normalizeRedirector($response, $request);
    }

    private function normalizeRedirector($response, $request)
    {
        if ($response instanceof Redirector) {
            // Logueja per a trobar l’origen real
            Log::warning('Redirector detectat i normalitzat en pipeline', [
                'route' => optional($request->route())->getName(),
                'path'  => $request->path(),
            ]);
            return $response->toResponse($request);
        }
        return $response;
    }


}

